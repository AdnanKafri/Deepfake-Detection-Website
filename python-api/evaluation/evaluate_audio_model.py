from __future__ import annotations

import argparse
import shutil
import subprocess
import sys
import tempfile
import wave
from pathlib import Path

import librosa
import numpy as np


SCRIPT_DIR = Path(__file__).resolve().parent
PROJECT_ROOT = SCRIPT_DIR.parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))
if str(SCRIPT_DIR) not in sys.path:
    sys.path.insert(0, str(SCRIPT_DIR))

from common import (  # noqa: E402
    build_condition_reports,
    build_report,
    ensure_dir,
    fake_score_from_prediction,
    load_manifest,
    normalize_prediction,
    parse_comma_separated_numbers,
    render_report,
    write_csv_rows,
    write_json,
)
from core.audio_analyzer import AudioAnalyzer  # noqa: E402


FAKE_SEGMENT_CONFIDENCE_THRESHOLD = 0.75
FAKE_SEGMENT_COUNT_THRESHOLD = 3


def write_wav(path: Path, waveform: np.ndarray, sample_rate: int) -> None:
    clipped = np.clip(waveform, -1.0, 1.0)
    pcm = (clipped * 32767).astype(np.int16)
    with wave.open(str(path), "wb") as handle:
        handle.setnchannels(1)
        handle.setsampwidth(2)
        handle.setframerate(sample_rate)
        handle.writeframes(pcm.tobytes())


def load_audio(path: Path, sample_rate: int = 16000) -> tuple[np.ndarray, int]:
    waveform, sr = librosa.load(str(path), sr=sample_rate, mono=True)
    return waveform, sr


def add_noise_by_snr(waveform: np.ndarray, snr_db: float) -> np.ndarray:
    signal_power = np.mean(np.square(waveform)) + 1e-12
    noise_power = signal_power / (10 ** (snr_db / 10))
    noise = np.random.normal(0.0, np.sqrt(noise_power), size=waveform.shape)
    return np.clip(waveform + noise, -1.0, 1.0)


def roundtrip_resample(waveform: np.ndarray, source_sr: int, intermediate_sr: int) -> np.ndarray:
    downsampled = librosa.resample(waveform, orig_sr=source_sr, target_sr=intermediate_sr)
    return librosa.resample(downsampled, orig_sr=intermediate_sr, target_sr=source_sr)


def apply_clipping(waveform: np.ndarray, threshold: float) -> np.ndarray:
    return np.clip(waveform, -threshold, threshold)


def save_noise_variant(source_path: Path, target_path: Path, snr_db: float) -> None:
    waveform, sr = load_audio(source_path)
    write_wav(target_path, add_noise_by_snr(waveform, snr_db), sr)


def save_resample_variant(source_path: Path, target_path: Path, intermediate_sr: int) -> None:
    waveform, sr = load_audio(source_path)
    transformed = roundtrip_resample(waveform, sr, intermediate_sr)
    write_wav(target_path, transformed, sr)


def save_clipped_variant(source_path: Path, target_path: Path, threshold: float) -> None:
    waveform, sr = load_audio(source_path)
    write_wav(target_path, apply_clipping(waveform, threshold), sr)


def save_mp3_variant(source_path: Path, target_path: Path, bitrate_kbps: int) -> None:
    ffmpeg_path = shutil.which("ffmpeg")
    if not ffmpeg_path:
        raise RuntimeError("ffmpeg is not installed or not available on PATH.")

    waveform, sr = load_audio(source_path)
    with tempfile.TemporaryDirectory(prefix="audio_eval_mp3_") as temp_dir_raw:
        temp_dir = Path(temp_dir_raw)
        intermediate_wav = temp_dir / "source.wav"
        write_wav(intermediate_wav, waveform, sr)

        command = [
            ffmpeg_path,
            "-y",
            "-i",
            str(intermediate_wav),
            "-codec:a",
            "libmp3lame",
            "-b:a",
            f"{bitrate_kbps}k",
            str(target_path),
        ]
        completed = subprocess.run(command, capture_output=True, text=True)
        if completed.returncode != 0:
            raise RuntimeError(completed.stderr.strip() or "ffmpeg audio transcode failed.")


def analyze_audio_pipeline(
    analyzer: AudioAnalyzer,
    audio_path: Path,
    segment_duration: float,
    fake_threshold: float,
) -> dict:
    waveform, sr = librosa.load(str(audio_path), sr=16000, mono=True)
    voiced_regions = librosa.effects.split(waveform, top_db=20)
    segment_length = int(sr * segment_duration)
    min_segment_length = int(sr * 0.5)

    all_predictions = []
    segment_index = 0
    for start, end in voiced_regions:
        region = waveform[start:end]
        for chunk_start in range(0, len(region), segment_length):
            chunk = region[chunk_start : chunk_start + segment_length]
            if len(chunk) < min_segment_length:
                continue

            features = analyzer._preprocess_segment(chunk)
            if features is None:
                continue

            probability_fake = float(analyzer.model.predict(features, verbose=0)[0][0])
            prediction = "FAKE" if probability_fake > 0.5 else "REAL"
            confidence = probability_fake if prediction == "FAKE" else (1 - probability_fake)

            segment_index += 1
            all_predictions.append(
                {
                    "segment": segment_index,
                    "prediction": prediction,
                    "confidence": float(confidence),
                    "raw_prob": probability_fake,
                }
            )

    if not all_predictions:
        return {
            "prediction": "UNKNOWN",
            "confidence": 0.0,
            "segments_analyzed": 0,
            "segments": [],
            "override_reason": "No valid segments to analyze.",
            "secondary_override_reason": None,
        }

    final_prediction = None
    override_reason = None
    secondary_override_reason = None

    for prediction in all_predictions:
        if prediction["raw_prob"] > fake_threshold:
            final_prediction = "FAKE"
            override_reason = (
                f"Segment {prediction['segment']} exceeded model FAKE threshold "
                f"({prediction['raw_prob']:.2f} > {fake_threshold})."
            )
            break

    if final_prediction is None:
        high_confidence_fakes = [
            prediction
            for prediction in all_predictions
            if prediction["prediction"] == "FAKE"
            and prediction["confidence"] > FAKE_SEGMENT_CONFIDENCE_THRESHOLD
        ]
        if len(high_confidence_fakes) >= FAKE_SEGMENT_COUNT_THRESHOLD:
            final_prediction = "FAKE"
            culprit_segments = ", ".join(
                f"#{segment['segment']} ({segment['confidence']:.2f})"
                for segment in high_confidence_fakes
            )
            secondary_override_reason = (
                f"Override triggered by {len(high_confidence_fakes)} FAKE segments with confidence > "
                f"{FAKE_SEGMENT_CONFIDENCE_THRESHOLD}. Segments: {culprit_segments}."
            )

    if final_prediction is None:
        fake_votes = sum(1 for prediction in all_predictions if prediction["prediction"] == "FAKE")
        real_votes = len(all_predictions) - fake_votes
        final_prediction = "FAKE" if fake_votes > real_votes else "REAL"
        if fake_votes == real_votes:
            final_prediction = "FAKE" if np.mean([p["raw_prob"] for p in all_predictions]) >= 0.5 else "REAL"

    return {
        "prediction": final_prediction,
        "confidence": round(float(np.mean([prediction["confidence"] for prediction in all_predictions])), 2),
        "segments_analyzed": len(all_predictions),
        "segments": all_predictions,
        "override_reason": override_reason,
        "secondary_override_reason": secondary_override_reason,
    }


def build_conditions(args: argparse.Namespace) -> tuple[list[tuple[str, object]], list[str]]:
    conditions: list[tuple[str, object]] = [("clean", None)]
    warnings: list[str] = []

    if not args.run_robustness:
        return conditions, warnings

    for snr in parse_comma_separated_numbers(args.noise_snrs, float):
        conditions.append((f"noise_snr_{str(snr).replace('.', '_')}", lambda src, dst, s=snr: save_noise_variant(src, dst, s)))
    for sample_rate in parse_comma_separated_numbers(args.roundtrip_sample_rates, int):
        conditions.append(
            (f"roundtrip_sr_{sample_rate}", lambda src, dst, sr=sample_rate: save_resample_variant(src, dst, sr))
        )
    for threshold in parse_comma_separated_numbers(args.clipping_thresholds, float):
        conditions.append(
            (f"clipping_{str(threshold).replace('.', '_')}", lambda src, dst, t=threshold: save_clipped_variant(src, dst, t))
        )

    if shutil.which("ffmpeg"):
        for bitrate in parse_comma_separated_numbers(args.mp3_bitrates, int):
            conditions.append((f"mp3_{bitrate}k", lambda src, dst, b=bitrate: save_mp3_variant(src, dst, b)))
    else:
        warnings.append("ffmpeg was not found, so MP3 compression robustness conditions were skipped.")

    return conditions, warnings


def main() -> None:
    parser = argparse.ArgumentParser(description="Evaluate the deployed segmented audio deepfake pipeline.")
    parser.add_argument("--manifest", required=True, help="CSV/JSON/JSONL manifest with at least path,label columns.")
    parser.add_argument(
        "--output-dir",
        default=str(SCRIPT_DIR / "results" / "audio"),
        help="Directory where predictions and summary files will be written.",
    )
    parser.add_argument("--segment-duration", type=float, default=2.5, help="Segment duration in seconds.")
    parser.add_argument("--fake-threshold", type=float, default=0.6, help="Segment-level FAKE override threshold.")
    parser.add_argument("--run-robustness", action="store_true", help="Also evaluate robustness variants.")
    parser.add_argument("--noise-snrs", default="20,10", help="Additive noise conditions expressed as SNR in dB.")
    parser.add_argument(
        "--roundtrip-sample-rates",
        default="8000",
        help="Resample down and back up through these intermediate sample rates.",
    )
    parser.add_argument("--clipping-thresholds", default="0.85,0.65", help="Hard clipping thresholds.")
    parser.add_argument("--mp3-bitrates", default="128,64", help="MP3 bitrates for compression robustness.")
    args = parser.parse_args()

    samples = load_manifest(args.manifest)
    output_dir = ensure_dir(args.output_dir)
    analyzer = AudioAnalyzer()
    conditions, warnings = build_conditions(args)
    records: list[dict] = []

    with tempfile.TemporaryDirectory(prefix="audio_eval_") as temp_dir_raw:
        temp_dir = Path(temp_dir_raw)

        for sample in samples:
            source_path = Path(sample["path"])
            for condition_name, transform_fn in conditions:
                evaluated_path = source_path
                if transform_fn is not None:
                    suffix = ".mp3" if condition_name.startswith("mp3_") else ".wav"
                    evaluated_path = temp_dir / f"{sample['sample_id']}__{condition_name}{suffix}"
                    transform_fn(source_path, evaluated_path)

                record = {
                    "sample_id": sample["sample_id"],
                    "source_path": str(source_path),
                    "evaluated_path": str(evaluated_path),
                    "condition": condition_name,
                    "true_label": sample["label"],
                    "predicted_label": "ERROR",
                    "confidence": None,
                    "fake_score": None,
                    "segments_analyzed": None,
                    "fake_segments": None,
                    "real_segments": None,
                    "mean_segment_fake_probability": None,
                    "max_segment_fake_probability": None,
                    "override_reason": None,
                    "secondary_override_reason": None,
                    "error": None,
                }

                try:
                    result = analyze_audio_pipeline(
                        analyzer,
                        evaluated_path,
                        segment_duration=args.segment_duration,
                        fake_threshold=args.fake_threshold,
                    )
                    prediction = normalize_prediction(result.get("prediction"))
                    raw_probabilities = [segment["raw_prob"] for segment in result.get("segments", [])]
                    fake_segments = sum(
                        1 for segment in result.get("segments", []) if segment["prediction"] == "FAKE"
                    )
                    real_segments = sum(
                        1 for segment in result.get("segments", []) if segment["prediction"] == "REAL"
                    )
                    record.update(
                        {
                            "predicted_label": prediction or "UNKNOWN",
                            "confidence": result.get("confidence"),
                            "fake_score": fake_score_from_prediction(prediction, result.get("confidence")),
                            "segments_analyzed": result.get("segments_analyzed"),
                            "fake_segments": fake_segments,
                            "real_segments": real_segments,
                            "mean_segment_fake_probability": round(float(np.mean(raw_probabilities)), 4)
                            if raw_probabilities
                            else None,
                            "max_segment_fake_probability": round(float(max(raw_probabilities)), 4)
                            if raw_probabilities
                            else None,
                            "override_reason": result.get("override_reason"),
                            "secondary_override_reason": result.get("secondary_override_reason"),
                        }
                    )
                except Exception as exc:
                    record["error"] = str(exc)

                records.append(record)

    summary = {
        "modality": "audio",
        "manifest": str(Path(args.manifest).resolve()),
        "model_file": str((PROJECT_ROOT / "models" / "best_lstm_model_v3.keras").resolve()),
        "segment_duration": args.segment_duration,
        "fake_threshold": args.fake_threshold,
        "robustness_enabled": args.run_robustness,
        "conditions": [name for name, _ in conditions],
        "warnings": warnings,
        "overall": build_report(records),
        "by_condition": build_condition_reports(records),
    }

    write_csv_rows(output_dir / "predictions.csv", records)
    write_json(output_dir / "summary.json", summary)
    print(render_report("Audio evaluation summary", summary["overall"], summary["by_condition"]))
    if warnings:
        print("\nWarnings:")
        for warning in warnings:
            print(f"  - {warning}")


if __name__ == "__main__":
    main()
