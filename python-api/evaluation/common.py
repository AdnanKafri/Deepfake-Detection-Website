from __future__ import annotations

import csv
import json
from collections import Counter, defaultdict
from pathlib import Path
from typing import Any


POSITIVE_LABEL = "FAKE"
NEGATIVE_LABEL = "REAL"
SUPPORTED_PREDICTIONS = {POSITIVE_LABEL, NEGATIVE_LABEL}
PROJECT_ROOT = Path(__file__).resolve().parents[2]

_LABEL_ALIASES = {
    "fake": POSITIVE_LABEL,
    "deepfake": POSITIVE_LABEL,
    "manipulated": POSITIVE_LABEL,
    "spoof": POSITIVE_LABEL,
    "1": POSITIVE_LABEL,
    "real": NEGATIVE_LABEL,
    "authentic": NEGATIVE_LABEL,
    "genuine": NEGATIVE_LABEL,
    "bonafide": NEGATIVE_LABEL,
    "bona_fide": NEGATIVE_LABEL,
    "0": NEGATIVE_LABEL,
}

_PREDICTION_ALIASES = {
    **_LABEL_ALIASES,
    "unknown": "UNKNOWN",
    "inconclusive": "INCONCLUSIVE",
    "review": "REVIEW",
    "error": "ERROR",
}


def clamp(value: float, min_value: float = 0.0, max_value: float = 1.0) -> float:
    return max(min_value, min(max_value, value))


def safe_float(value: Any, default: float | None = None) -> float | None:
    try:
        return float(value)
    except (TypeError, ValueError):
        return default


def normalize_label(value: Any) -> str:
    if value is None:
        raise ValueError("Label is missing.")

    normalized = str(value).strip().lower().replace("-", "_").replace(" ", "_")
    if normalized not in _LABEL_ALIASES:
        raise ValueError(
            f"Unsupported label '{value}'. Use one of: real, fake, 0, 1, authentic, deepfake."
        )
    return _LABEL_ALIASES[normalized]


def normalize_prediction(value: Any) -> str | None:
    if value is None:
        return None

    normalized = str(value).strip().lower().replace("-", "_").replace(" ", "_")
    if normalized in _PREDICTION_ALIASES:
        return _PREDICTION_ALIASES[normalized]

    upper = str(value).strip().upper()
    if upper in {"UNKNOWN", "INCONCLUSIVE", "REVIEW", "ERROR"}:
        return upper
    return None


def label_to_int(value: str) -> int:
    return 1 if normalize_label(value) == POSITIVE_LABEL else 0


def fake_score_from_prediction(prediction: Any, confidence: Any) -> float | None:
    normalized_prediction = normalize_prediction(prediction)
    normalized_confidence = safe_float(confidence)
    if normalized_prediction not in SUPPORTED_PREDICTIONS or normalized_confidence is None:
        return None

    normalized_confidence = clamp(normalized_confidence)
    if normalized_prediction == POSITIVE_LABEL:
        return normalized_confidence
    return 1.0 - normalized_confidence


def parse_comma_separated_numbers(value: str, cast_type=float) -> list[Any]:
    if not value:
        return []
    return [cast_type(item.strip()) for item in value.split(",") if item.strip()]


def ensure_dir(path: str | Path) -> Path:
    output_path = Path(path)
    output_path.mkdir(parents=True, exist_ok=True)
    return output_path


def resolve_data_path(manifest_path: Path, raw_path: str) -> Path:
    path = Path(raw_path)
    if path.is_absolute():
        return path.resolve()

    candidate_paths = [
        (manifest_path.parent / path).resolve(),
        (PROJECT_ROOT / path).resolve(),
    ]
    for candidate in candidate_paths:
        if candidate.exists():
            return candidate

    return candidate_paths[0]


def _load_csv_manifest(path: Path) -> list[dict[str, Any]]:
    with path.open("r", encoding="utf-8-sig", newline="") as handle:
        reader = csv.DictReader(handle)
        rows = []
        for index, row in enumerate(reader, start=1):
            rows.append({"row_number": index, **row})
        return rows


def _load_json_manifest(path: Path) -> list[dict[str, Any]]:
    data = json.loads(path.read_text(encoding="utf-8"))
    if isinstance(data, dict):
        data = data.get("samples", [])
    if not isinstance(data, list):
        raise ValueError("JSON manifest must contain a list of samples or a {'samples': [...]} object.")
    return [{"row_number": index, **row} for index, row in enumerate(data, start=1)]


def _load_jsonl_manifest(path: Path) -> list[dict[str, Any]]:
    rows: list[dict[str, Any]] = []
    with path.open("r", encoding="utf-8") as handle:
        for index, line in enumerate(handle, start=1):
            line = line.strip()
            if not line:
                continue
            rows.append({"row_number": index, **json.loads(line)})
    return rows


def load_manifest(manifest_path: str | Path) -> list[dict[str, Any]]:
    path = Path(manifest_path).resolve()
    if not path.exists():
        raise FileNotFoundError(f"Manifest file not found: {path}")

    suffix = path.suffix.lower()
    if suffix == ".csv":
        raw_rows = _load_csv_manifest(path)
    elif suffix == ".json":
        raw_rows = _load_json_manifest(path)
    elif suffix == ".jsonl":
        raw_rows = _load_jsonl_manifest(path)
    else:
        raise ValueError("Manifest must be .csv, .json, or .jsonl")

    samples: list[dict[str, Any]] = []
    for row in raw_rows:
        raw_sample_path = row.get("path")
        raw_label = row.get("label")
        if not raw_sample_path or raw_label is None:
            raise ValueError(
                f"Manifest row {row['row_number']} is missing required fields. Expected at least 'path' and 'label'."
            )

        sample_id = row.get("id") or row.get("sample_id") or f"sample_{row['row_number']:04d}"
        condition = row.get("condition") or "clean"
        notes = row.get("notes")
        resolved_path = resolve_data_path(path, raw_sample_path)

        samples.append(
            {
                "sample_id": str(sample_id),
                "path": str(resolved_path),
                "label": normalize_label(raw_label),
                "condition": str(condition),
                "notes": notes,
            }
        )
    return samples


def safe_div(numerator: float, denominator: float) -> float:
    if denominator == 0:
        return 0.0
    return numerator / denominator


def compute_binary_metrics(y_true: list[int], y_pred: list[int]) -> dict[str, Any]:
    tp = sum(1 for truth, pred in zip(y_true, y_pred) if truth == 1 and pred == 1)
    tn = sum(1 for truth, pred in zip(y_true, y_pred) if truth == 0 and pred == 0)
    fp = sum(1 for truth, pred in zip(y_true, y_pred) if truth == 0 and pred == 1)
    fn = sum(1 for truth, pred in zip(y_true, y_pred) if truth == 1 and pred == 0)
    total = len(y_true)

    accuracy = safe_div(tp + tn, total)
    precision = safe_div(tp, tp + fp)
    recall = safe_div(tp, tp + fn)
    f1 = safe_div(2 * precision * recall, precision + recall)
    specificity = safe_div(tn, tn + fp)
    balanced_accuracy = (recall + specificity) / 2 if total else 0.0

    return {
        "sample_count": total,
        "tp": tp,
        "tn": tn,
        "fp": fp,
        "fn": fn,
        "accuracy": round(accuracy, 4),
        "precision": round(precision, 4),
        "recall": round(recall, 4),
        "f1_score": round(f1, 4),
        "specificity": round(specificity, 4),
        "balanced_accuracy": round(balanced_accuracy, 4),
    }


def _rank_scores(scores: list[float]) -> list[float]:
    indexed_scores = sorted(enumerate(scores), key=lambda item: item[1])
    ranks = [0.0] * len(scores)
    current_rank = 1
    start = 0

    while start < len(indexed_scores):
        end = start
        while end + 1 < len(indexed_scores) and indexed_scores[end + 1][1] == indexed_scores[start][1]:
            end += 1

        average_rank = (current_rank + current_rank + (end - start)) / 2
        for index in range(start, end + 1):
            original_index = indexed_scores[index][0]
            ranks[original_index] = average_rank

        current_rank += end - start + 1
        start = end + 1

    return ranks


def compute_roc_auc(y_true: list[int], scores: list[float]) -> float | None:
    if len(set(y_true)) < 2:
        return None

    positive_count = sum(y_true)
    negative_count = len(y_true) - positive_count
    if positive_count == 0 or negative_count == 0:
        return None

    ranks = _rank_scores(scores)
    positive_rank_sum = sum(rank for rank, truth in zip(ranks, y_true) if truth == 1)
    auc = (positive_rank_sum - (positive_count * (positive_count + 1) / 2)) / (positive_count * negative_count)
    return round(float(auc), 4)


def metrics_at_threshold(y_true: list[int], scores: list[float], threshold: float) -> dict[str, Any]:
    y_pred = [1 if score >= threshold else 0 for score in scores]
    metrics = compute_binary_metrics(y_true, y_pred)
    metrics["threshold"] = round(float(threshold), 4)
    return metrics


def sweep_thresholds(y_true: list[int], scores: list[float]) -> dict[str, Any] | None:
    if not y_true or len(set(y_true)) < 2:
        return None

    candidate_thresholds = {round(index / 100, 2) for index in range(0, 101)}
    candidate_thresholds.update(round(score, 4) for score in scores)

    best_metrics = None
    for threshold in sorted(candidate_thresholds):
        metrics = metrics_at_threshold(y_true, scores, threshold)
        if best_metrics is None:
            best_metrics = metrics
            continue

        current_key = (metrics["f1_score"], metrics["balanced_accuracy"], metrics["accuracy"])
        best_key = (best_metrics["f1_score"], best_metrics["balanced_accuracy"], best_metrics["accuracy"])
        if current_key > best_key:
            best_metrics = metrics

    default_metrics = metrics_at_threshold(y_true, scores, 0.5)
    return {
        "roc_auc": compute_roc_auc(y_true, scores),
        "metrics_at_0_50": default_metrics,
        "best_f1_threshold": best_metrics["threshold"] if best_metrics else None,
        "best_f1_metrics": best_metrics,
    }


def build_report(records: list[dict[str, Any]]) -> dict[str, Any]:
    total_samples = len(records)
    error_counter: Counter[str] = Counter()
    covered_true: list[int] = []
    covered_pred: list[int] = []
    score_true: list[int] = []
    score_values: list[float] = []
    conservative_correct = 0
    covered_samples = 0
    unknown_predictions = 0

    for record in records:
        true_label = normalize_label(record["true_label"])
        predicted_label = normalize_prediction(record.get("predicted_label"))

        if record.get("error"):
            error_counter[str(record["error"])] += 1

        if predicted_label in SUPPORTED_PREDICTIONS:
            covered_samples += 1
            if predicted_label == true_label:
                conservative_correct += 1

            covered_true.append(label_to_int(true_label))
            covered_pred.append(1 if predicted_label == POSITIVE_LABEL else 0)

            score = safe_float(record.get("fake_score"))
            if score is not None:
                score_true.append(label_to_int(true_label))
                score_values.append(clamp(score))
        else:
            unknown_predictions += 1

    report: dict[str, Any] = {
        "total_samples": total_samples,
        "covered_samples": covered_samples,
        "coverage": round(safe_div(covered_samples, total_samples), 4),
        "unknown_or_failed_samples": unknown_predictions,
        "error_count": sum(error_counter.values()),
        "conservative_accuracy": round(safe_div(conservative_correct, total_samples), 4),
        "class_distribution": {
            NEGATIVE_LABEL: sum(1 for record in records if normalize_label(record["true_label"]) == NEGATIVE_LABEL),
            POSITIVE_LABEL: sum(1 for record in records if normalize_label(record["true_label"]) == POSITIVE_LABEL),
        },
        "top_errors": dict(error_counter.most_common(10)),
    }

    if covered_true:
        report["covered_metrics"] = compute_binary_metrics(covered_true, covered_pred)
    else:
        report["covered_metrics"] = None

    if score_values:
        report["score_analysis"] = {
            "mean_fake_score_for_real": round(
                safe_div(
                    sum(score for truth, score in zip(score_true, score_values) if truth == 0),
                    sum(1 for truth in score_true if truth == 0),
                ),
                4,
            ),
            "mean_fake_score_for_fake": round(
                safe_div(
                    sum(score for truth, score in zip(score_true, score_values) if truth == 1),
                    sum(1 for truth in score_true if truth == 1),
                ),
                4,
            ),
            "threshold_sweep": sweep_thresholds(score_true, score_values),
        }
    else:
        report["score_analysis"] = None

    return report


def build_condition_reports(records: list[dict[str, Any]]) -> dict[str, dict[str, Any]]:
    grouped: defaultdict[str, list[dict[str, Any]]] = defaultdict(list)
    for record in records:
        grouped[str(record.get("condition") or "clean")].append(record)

    return {condition: build_report(group_records) for condition, group_records in grouped.items()}


def serialize_for_json(data: Any) -> Any:
    if isinstance(data, Path):
        return str(data)
    if isinstance(data, dict):
        return {key: serialize_for_json(value) for key, value in data.items()}
    if isinstance(data, list):
        return [serialize_for_json(item) for item in data]
    return data


def write_json(path: str | Path, payload: Any) -> None:
    path = Path(path)
    path.write_text(json.dumps(serialize_for_json(payload), indent=2, ensure_ascii=False), encoding="utf-8")


def write_csv_rows(path: str | Path, rows: list[dict[str, Any]]) -> None:
    path = Path(path)
    fieldnames: list[str] = []
    seen_fields: set[str] = set()
    for row in rows:
        for key in row.keys():
            if key not in seen_fields:
                seen_fields.add(key)
                fieldnames.append(key)

    with path.open("w", encoding="utf-8", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=fieldnames)
        writer.writeheader()
        for row in rows:
            writer.writerow({key: serialize_for_json(value) for key, value in row.items()})


def render_report(title: str, summary: dict[str, Any], by_condition: dict[str, dict[str, Any]] | None = None) -> str:
    lines = [
        title,
        f"  Samples: {summary['total_samples']}",
        f"  Coverage: {summary['coverage']:.2%}",
        f"  Conservative accuracy: {summary['conservative_accuracy']:.2%}",
    ]

    covered_metrics = summary.get("covered_metrics")
    if covered_metrics:
        lines.extend(
            [
                f"  Covered accuracy: {covered_metrics['accuracy']:.2%}",
                f"  Precision: {covered_metrics['precision']:.2%}",
                f"  Recall: {covered_metrics['recall']:.2%}",
                f"  F1-score: {covered_metrics['f1_score']:.2%}",
            ]
        )

    score_analysis = summary.get("score_analysis") or {}
    threshold_sweep = score_analysis.get("threshold_sweep") if score_analysis else None
    if threshold_sweep and threshold_sweep.get("best_f1_metrics"):
        lines.append(
            f"  Best F1 threshold: {threshold_sweep['best_f1_threshold']} "
            f"(F1={threshold_sweep['best_f1_metrics']['f1_score']:.2%}, "
            f"Accuracy={threshold_sweep['best_f1_metrics']['accuracy']:.2%})"
        )

    if by_condition:
        lines.append("  By condition:")
        for condition, condition_summary in sorted(by_condition.items()):
            metrics = condition_summary.get("covered_metrics") or {}
            lines.append(
                f"    - {condition}: coverage={condition_summary['coverage']:.2%}, "
                f"accuracy={metrics.get('accuracy', 0.0):.2%}, "
                f"f1={metrics.get('f1_score', 0.0):.2%}"
            )

    return "\n".join(lines)
