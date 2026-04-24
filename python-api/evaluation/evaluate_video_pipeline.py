from __future__ import annotations

import argparse
import shutil
import subprocess
import sys
import tempfile
from pathlib import Path

import cv2
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
from core.media_analyzer import MediaAnalyzer  # noqa: E402


def even_dimension(value: int) -> int:
    return max(2, value - (value % 2))


def rewrite_video(
    source_path: Path,
    target_path: Path,
    *,
    resize_scale: float = 1.0,
    blur_sigma: float = 0.0,
    noise_std: float = 0.0,
    frame_stride: int = 1,
) -> None:
    capture = cv2.VideoCapture(str(source_path))
    if not capture.isOpened():
        raise RuntimeError(f"Could not open video file: {source_path}")

    fps = capture.get(cv2.CAP_PROP_FPS) or 25.0
    width = int(capture.get(cv2.CAP_PROP_FRAME_WIDTH))
    height = int(capture.get(cv2.CAP_PROP_FRAME_HEIGHT))
    output_width = even_dimension(int(width * resize_scale))
    output_height = even_dimension(int(height * resize_scale))
    output_fps = max(fps / max(frame_stride, 1), 1.0)

    writer = cv2.VideoWriter(
        str(target_path),
        cv2.VideoWriter_fourcc(*"mp4v"),
        output_fps,
        (output_width, output_height),
    )

    if not writer.isOpened():
        capture.release()
        raise RuntimeError(f"Could not create output video: {target_path}")

    frame_index = 0
    kernel_size = 0
    if blur_sigma > 0:
        kernel_size = int(max(3, round(blur_sigma * 6)))
        if kernel_size % 2 == 0:
            kernel_size += 1

    try:
        while True:
            success, frame = capture.read()
            if not success:
                break

            if frame_index % max(frame_stride, 1) != 0:
                frame_index += 1
                continue

            processed = frame
            if resize_scale != 1.0:
                processed = cv2.resize(processed, (output_width, output_height), interpolation=cv2.INTER_AREA)
            else:
                processed = cv2.resize(processed, (output_width, output_height), interpolation=cv2.INTER_LINEAR)

            if kernel_size > 0:
                processed = cv2.GaussianBlur(processed, (kernel_size, kernel_size), blur_sigma)

            if noise_std > 0:
                noise = np.random.normal(0.0, noise_std, size=processed.shape)
                processed = np.clip(processed.astype(np.float32) + noise, 0, 255).astype(np.uint8)

            writer.write(processed)
            frame_index += 1
    finally:
        capture.release()
        writer.release()


def transcode_with_ffmpeg(source_path: Path, target_path: Path, crf: int) -> None:
    ffmpeg_path = shutil.which("ffmpeg")
    if not ffmpeg_path:
        raise RuntimeError("ffmpeg is not installed or not available on PATH.")

    command = [
        ffmpeg_path,
        "-y",
        "-i",
        str(source_path),
        "-an",
        "-c:v",
        "libx264",
        "-preset",
        "medium",
        "-crf",
        str(crf),
        str(target_path),
    ]
    completed = subprocess.run(command, capture_output=True, text=True)
    if completed.returncode != 0:
        raise RuntimeError(completed.stderr.strip() or "ffmpeg video transcode failed.")


def build_conditions(args: argparse.Namespace) -> tuple[list[tuple[str, object]], list[str]]:
    conditions: list[tuple[str, object]] = [("clean", None)]
    warnings: list[str] = []

    if not args.run_robustness:
        return conditions, warnings

    for scale in parse_comma_separated_numbers(args.resize_scales, float):
        conditions.append((f"resize_{str(scale).replace('.', '_')}", lambda src, dst, s=scale: rewrite_video(src, dst, resize_scale=s)))
    for sigma in parse_comma_separated_numbers(args.blur_sigmas, float):
        conditions.append((f"blur_{str(sigma).replace('.', '_')}", lambda src, dst, b=sigma: rewrite_video(src, dst, blur_sigma=b)))
    for sigma in parse_comma_separated_numbers(args.noise_stds, float):
        conditions.append((f"noise_{str(sigma).replace('.', '_')}", lambda src, dst, n=sigma: rewrite_video(src, dst, noise_std=n)))
    for stride in parse_comma_separated_numbers(args.frame_strides, int):
        conditions.append((f"frame_stride_{stride}", lambda src, dst, fs=stride: rewrite_video(src, dst, frame_stride=fs)))

    if shutil.which("ffmpeg"):
        for crf in parse_comma_separated_numbers(args.compression_crfs, int):
            conditions.append((f"compression_crf_{crf}", lambda src, dst, c=crf: transcode_with_ffmpeg(src, dst, c)))
    else:
        warnings.append("ffmpeg was not found, so H.264 compression robustness conditions were skipped.")

    return conditions, warnings


def run_video_inference(analyzer: MediaAnalyzer, video_path: Path, frames_to_sample: int) -> dict:
    result = analyzer.analyze_video(str(video_path), frames_to_sample=frames_to_sample, save_frames=False)
    details = result.get("details", {})
    prediction = normalize_prediction(result.get("prediction"))
    confidence = result.get("confidence")
    return {
        "predicted_label": prediction or "UNKNOWN",
        "confidence": confidence,
        "fake_score": fake_score_from_prediction(prediction, confidence),
        "frames_analyzed": details.get("frames_analyzed"),
        "frames_with_faces": details.get("frames_with_faces"),
        "real_frames": details.get("real_frames"),
        "fake_frames": details.get("fake_frames"),
        "reason": details.get("reason"),
    }


def main() -> None:
    parser = argparse.ArgumentParser(description="Evaluate the deployed frame-based video deepfake pipeline.")
    parser.add_argument("--manifest", required=True, help="CSV/JSON/JSONL manifest with at least path,label columns.")
    parser.add_argument(
        "--output-dir",
        default=str(SCRIPT_DIR / "results" / "video"),
        help="Directory where predictions and summary files will be written.",
    )
    parser.add_argument("--frames-to-sample", type=int, default=10, help="Number of frames sampled per video.")
    parser.add_argument("--run-robustness", action="store_true", help="Also evaluate robustness variants.")
    parser.add_argument("--resize-scales", default="0.75,0.5", help="Video resize scales for robustness evaluation.")
    parser.add_argument("--blur-sigmas", default="1.0", help="Gaussian blur sigma values for robustness evaluation.")
    parser.add_argument("--noise-stds", default="8", help="Per-frame Gaussian noise standard deviation.")
    parser.add_argument("--frame-strides", default="2", help="Frame skipping factors for robustness evaluation.")
    parser.add_argument(
        "--compression-crfs",
        default="28,35",
        help="H.264 CRF levels for compression robustness. Requires ffmpeg on PATH.",
    )
    args = parser.parse_args()

    samples = load_manifest(args.manifest)
    output_dir = ensure_dir(args.output_dir)
    analyzer = MediaAnalyzer()
    conditions, warnings = build_conditions(args)
    records: list[dict] = []

    with tempfile.TemporaryDirectory(prefix="video_eval_") as temp_dir_raw:
        temp_dir = Path(temp_dir_raw)

        for sample in samples:
            source_path = Path(sample["path"])
            for condition_name, transform_fn in conditions:
                evaluated_path = source_path
                if transform_fn is not None:
                    evaluated_path = temp_dir / f"{sample['sample_id']}__{condition_name}.mp4"
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
                    "frames_analyzed": None,
                    "frames_with_faces": None,
                    "real_frames": None,
                    "fake_frames": None,
                    "reason": None,
                    "error": None,
                }

                try:
                    record.update(run_video_inference(analyzer, evaluated_path, args.frames_to_sample))
                except Exception as exc:
                    record["error"] = str(exc)

                records.append(record)

    summary = {
        "modality": "video",
        "manifest": str(Path(args.manifest).resolve()),
        "model_file": str((PROJECT_ROOT / "models" / "fine_tuned_epoch_25.pt").resolve()),
        "frames_to_sample": args.frames_to_sample,
        "robustness_enabled": args.run_robustness,
        "conditions": [name for name, _ in conditions],
        "warnings": warnings,
        "overall": build_report(records),
        "by_condition": build_condition_reports(records),
    }

    write_csv_rows(output_dir / "predictions.csv", records)
    write_json(output_dir / "summary.json", summary)
    print(render_report("Video evaluation summary", summary["overall"], summary["by_condition"]))
    if warnings:
        print("\nWarnings:")
        for warning in warnings:
            print(f"  - {warning}")


if __name__ == "__main__":
    main()
