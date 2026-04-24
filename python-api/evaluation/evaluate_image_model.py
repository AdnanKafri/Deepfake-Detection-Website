from __future__ import annotations

import argparse
import sys
import tempfile
from pathlib import Path

import numpy as np
from PIL import Image, ImageFilter


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


def save_jpeg_variant(source_path: Path, target_path: Path, quality: int) -> None:
    with Image.open(source_path) as image:
        rgb = image.convert("RGB")
        rgb.save(target_path, format="JPEG", quality=quality, optimize=True)


def save_resize_variant(source_path: Path, target_path: Path, scale: float) -> None:
    with Image.open(source_path) as image:
        rgb = image.convert("RGB")
        original_size = rgb.size
        resized_size = (
            max(8, int(rgb.width * scale)),
            max(8, int(rgb.height * scale)),
        )
        degraded = rgb.resize(resized_size, Image.Resampling.BICUBIC).resize(
            original_size, Image.Resampling.BICUBIC
        )
        degraded.save(target_path, format="JPEG", quality=95, optimize=True)


def save_blur_variant(source_path: Path, target_path: Path, radius: float) -> None:
    with Image.open(source_path) as image:
        rgb = image.convert("RGB")
        blurred = rgb.filter(ImageFilter.GaussianBlur(radius=radius))
        blurred.save(target_path, format="JPEG", quality=95, optimize=True)


def save_noise_variant(source_path: Path, target_path: Path, sigma: float) -> None:
    with Image.open(source_path) as image:
        rgb = image.convert("RGB")
        image_array = np.asarray(rgb).astype(np.float32)
        noise = np.random.normal(loc=0.0, scale=sigma, size=image_array.shape)
        noisy = np.clip(image_array + noise, 0, 255).astype(np.uint8)
        Image.fromarray(noisy).save(target_path, format="JPEG", quality=95, optimize=True)


def build_conditions(args: argparse.Namespace) -> list[tuple[str, object]]:
    conditions: list[tuple[str, object]] = [("clean", None)]
    if not args.run_robustness:
        return conditions

    for quality in parse_comma_separated_numbers(args.jpeg_qualities, int):
        conditions.append((f"jpeg_q{quality}", lambda src, dst, q=quality: save_jpeg_variant(src, dst, q)))
    for scale in parse_comma_separated_numbers(args.resize_scales, float):
        conditions.append(
            (f"resize_{str(scale).replace('.', '_')}", lambda src, dst, s=scale: save_resize_variant(src, dst, s))
        )
    for radius in parse_comma_separated_numbers(args.blur_radii, float):
        conditions.append((f"blur_{str(radius).replace('.', '_')}", lambda src, dst, r=radius: save_blur_variant(src, dst, r)))
    for sigma in parse_comma_separated_numbers(args.noise_stds, float):
        conditions.append((f"noise_{str(sigma).replace('.', '_')}", lambda src, dst, n=sigma: save_noise_variant(src, dst, n)))
    return conditions


def run_image_inference(analyzer: MediaAnalyzer, image_path: Path) -> dict:
    result = analyzer.analyze_image(str(image_path))
    details = result.get("details", {})
    prediction = normalize_prediction(result.get("prediction"))
    confidence = result.get("confidence")
    return {
        "predicted_label": prediction or "UNKNOWN",
        "confidence": confidence,
        "fake_score": fake_score_from_prediction(prediction, confidence),
        "faces_detected": details.get("faces_detected"),
        "real_faces": details.get("real_faces"),
        "fake_faces": details.get("fake_faces"),
        "reason": details.get("reason"),
    }


def main() -> None:
    parser = argparse.ArgumentParser(description="Evaluate the deployed image deepfake pipeline.")
    parser.add_argument("--manifest", required=True, help="CSV/JSON/JSONL manifest with at least path,label columns.")
    parser.add_argument(
        "--output-dir",
        default=str(SCRIPT_DIR / "results" / "image"),
        help="Directory where predictions and summary files will be written.",
    )
    parser.add_argument("--run-robustness", action="store_true", help="Also evaluate robustness variants.")
    parser.add_argument("--jpeg-qualities", default="95,75,50", help="JPEG qualities for robustness evaluation.")
    parser.add_argument("--resize-scales", default="0.75,0.5", help="Resize-down-then-up scales for robustness evaluation.")
    parser.add_argument("--blur-radii", default="1.0,2.0", help="Gaussian blur radii for robustness evaluation.")
    parser.add_argument("--noise-stds", default="5,10", help="Gaussian noise standard deviations in pixel space.")
    args = parser.parse_args()

    samples = load_manifest(args.manifest)
    output_dir = ensure_dir(args.output_dir)
    analyzer = MediaAnalyzer()
    conditions = build_conditions(args)
    records: list[dict] = []

    with tempfile.TemporaryDirectory(prefix="image_eval_") as temp_dir_raw:
        temp_dir = Path(temp_dir_raw)

        for sample in samples:
            source_path = Path(sample["path"])
            for condition_name, transform_fn in conditions:
                evaluated_path = source_path
                if transform_fn is not None:
                    evaluated_path = temp_dir / f"{sample['sample_id']}__{condition_name}.jpg"
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
                    "faces_detected": None,
                    "real_faces": None,
                    "fake_faces": None,
                    "reason": None,
                    "error": None,
                }

                try:
                    record.update(run_image_inference(analyzer, evaluated_path))
                except Exception as exc:
                    record["error"] = str(exc)

                records.append(record)

    summary = {
        "modality": "image",
        "manifest": str(Path(args.manifest).resolve()),
        "model_file": str((PROJECT_ROOT / "models" / "fine_tuned_epoch_25.pt").resolve()),
        "robustness_enabled": args.run_robustness,
        "conditions": [name for name, _ in conditions],
        "overall": build_report(records),
        "by_condition": build_condition_reports(records),
    }

    write_csv_rows(output_dir / "predictions.csv", records)
    write_json(output_dir / "summary.json", summary)
    print(render_report("Image evaluation summary", summary["overall"], summary["by_condition"]))


if __name__ == "__main__":
    main()
