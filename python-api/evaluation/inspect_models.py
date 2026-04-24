from __future__ import annotations

import argparse
import json
import zipfile
from pathlib import Path
from typing import Any


PROJECT_ROOT = Path(__file__).resolve().parent.parent
MODELS_DIR = PROJECT_ROOT / "models"


def inspect_keras_model(path: Path) -> dict[str, Any]:
    details: dict[str, Any] = {
        "path": str(path),
        "exists": path.exists(),
    }
    if not path.exists():
        return details

    details["file_size_mb"] = round(path.stat().st_size / (1024 * 1024), 2)
    with zipfile.ZipFile(path, "r") as archive:
        details["archive_files"] = archive.namelist()

        if "metadata.json" in archive.namelist():
            details["metadata"] = json.loads(archive.read("metadata.json"))

        if "config.json" in archive.namelist():
            config = json.loads(archive.read("config.json"))
            details["keras_class"] = config.get("class_name")
            details["compile_config"] = config.get("compile_config", {})
            details["layers"] = []
            for layer in config.get("config", {}).get("layers", []):
                layer_config = layer.get("config", {})
                details["layers"].append(
                    {
                        "class_name": layer.get("class_name"),
                        "name": layer_config.get("name"),
                        "units": layer_config.get("units"),
                        "activation": layer_config.get("activation"),
                        "batch_shape": layer_config.get("batch_shape"),
                        "return_sequences": layer_config.get("return_sequences"),
                    }
                )
    return details


def inspect_pytorch_model(path: Path) -> dict[str, Any]:
    details: dict[str, Any] = {
        "path": str(path),
        "exists": path.exists(),
    }
    if not path.exists():
        return details

    details["file_size_mb"] = round(path.stat().st_size / (1024 * 1024), 2)

    try:
        with zipfile.ZipFile(path, "r") as archive:
            members = archive.namelist()
            details["archive_file_count"] = len(members)
            details["archive_head"] = members[:25]
    except zipfile.BadZipFile:
        details["archive_file_count"] = None
        details["archive_head"] = []

    try:
        import torch

        loaded = torch.load(path, map_location="cpu")
        details["torch_loaded_type"] = type(loaded).__name__
        if isinstance(loaded, dict):
            details["top_level_keys"] = list(loaded.keys())[:25]
            state_dict = loaded.get("state_dict", loaded)
            if isinstance(state_dict, dict):
                weight_keys = list(state_dict.keys())
                details["state_dict_key_count"] = len(weight_keys)
                details["state_dict_head"] = weight_keys[:20]
                shape_preview = {}
                for key in weight_keys[:10]:
                    value = state_dict[key]
                    shape_preview[key] = list(value.shape) if hasattr(value, "shape") else str(type(value))
                details["state_dict_shapes"] = shape_preview
    except Exception as exc:
        details["torch_load_error"] = str(exc)

    return details


def main() -> None:
    parser = argparse.ArgumentParser(description="Inspect saved deepfake model artifacts.")
    parser.add_argument(
        "--output",
        default=str(Path(__file__).resolve().parent / "model_inspection.json"),
        help="Where to write the inspection report as JSON.",
    )
    args = parser.parse_args()

    report = {
        "project_root": str(PROJECT_ROOT),
        "models_dir": str(MODELS_DIR),
        "image_model": inspect_pytorch_model(MODELS_DIR / "fine_tuned_epoch_25.pt"),
        "audio_model": inspect_keras_model(MODELS_DIR / "best_lstm_model_v3.keras"),
        "cannot_be_inferred_from_weights_alone": [
            "Training accuracy, validation accuracy, precision, recall, F1-score, or ROC-AUC",
            "Which dataset or dataset split was used",
            "Class balance, data leakage, or augmentation policy",
            "Threshold calibration quality and real-world robustness",
            "Whether the saved label mapping matches the current inference code",
        ],
    }

    output_path = Path(args.output).resolve()
    output_path.write_text(json.dumps(report, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"Wrote model inspection report to: {output_path}")


if __name__ == "__main__":
    main()
