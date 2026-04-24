<?php

namespace App\Jobs;

use App\Models\Analysis;
use App\Services\DeepfakeDetectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 360;

    protected int $analysisId;

    public function __construct(int $analysisId)
    {
        $this->analysisId = $analysisId;
    }

    public function handle(): void
    {
        /** @var Analysis|null $analysis */
        $analysis = Analysis::find($this->analysisId);

        if (!$analysis) {
            return;
        }

        if ($this->shouldSkip($analysis)) {
            return;
        }

        try {
            if (!$analysis->file_path || !Storage::exists($analysis->file_path)) {
                throw new \RuntimeException('Uploaded file was not found for processing.');
            }

            $analysis->forceFill([
                'status' => Analysis::STATUS_PROCESSING,
                'started_at' => $analysis->started_at ?? Carbon::now(),
                'completed_at' => null,
                'error_message' => null,
            ])->save();

            /** @var DeepfakeDetectionService $service */
            $service = app(DeepfakeDetectionService::class);
            $absolutePath = Storage::path($analysis->file_path);
            $result = $service->analyzeStoredFile($absolutePath, $analysis->file_name, $analysis->file_type);
            $mainResult = $result['result'] ?? $result;

            if (!is_array($mainResult) || !isset($mainResult['type'])) {
                throw new \RuntimeException('Invalid response received from FastAPI.');
            }

            DB::transaction(function () use ($analysis, $result, $mainResult) {
                $analysis->details()->delete();

                $analysis->forceFill([
                    'prediction' => $this->normalizePrediction($mainResult['prediction'] ?? null),
                    'confidence' => isset($mainResult['confidence']) ? (float) $mainResult['confidence'] : null,
                    'result_json' => json_encode($result),
                    'status' => Analysis::STATUS_COMPLETED,
                    'completed_at' => Carbon::now(),
                    'error_message' => null,
                ])->save();

                $this->storeDetails($analysis, $mainResult['details'] ?? []);
            });
        } catch (\Throwable $e) {
            Log::error('ProcessMediaJob failed', [
                'analysis_id' => $this->analysisId,
                'error' => $e->getMessage(),
            ]);

            $analysis->forceFill([
                'status' => Analysis::STATUS_FAILED,
                'completed_at' => Carbon::now(),
                'error_message' => $e->getMessage(),
            ])->save();
        }
    }

    protected function shouldSkip(Analysis $analysis): bool
    {
        return $analysis->status === Analysis::STATUS_COMPLETED
            && !empty($analysis->result_json)
            && !empty(json_decode($analysis->result_json, true));
    }

    protected function storeDetails(Analysis $analysis, array $details): void
    {
        if ($analysis->file_type === 'video' && isset($details['frame_images']) && is_array($details['frame_images'])) {
            foreach ($details['frame_images'] as $index => $frame) {
                $prediction = $this->normalizePrediction($frame['prediction'] ?? null);

                if (!$prediction) {
                    continue;
                }

                $analysis->details()->create([
                    'segment_index' => $index,
                    'prediction' => $prediction,
                    'confidence' => isset($frame['confidence']) ? (float) $frame['confidence'] : 0,
                    'original_image_path' => $frame['original_path'] ?? null,
                    'cropped_face_path' => $frame['cropped_face_path'] ?? null,
                    'extra_json' => json_encode($frame),
                ]);
            }

            return;
        }

        if ($analysis->file_type === 'image' && isset($details['face_details']) && is_array($details['face_details'])) {
            foreach ($details['face_details'] as $index => $face) {
                $prediction = $this->normalizePrediction($face['prediction'] ?? null);

                if (!$prediction) {
                    continue;
                }

                $analysis->details()->create([
                    'segment_index' => $index,
                    'prediction' => $prediction,
                    'confidence' => isset($face['confidence']) ? (float) $face['confidence'] : 0,
                    'original_image_path' => $face['original_path'] ?? null,
                    'cropped_face_path' => $face['cropped_face_path'] ?? null,
                    'extra_json' => json_encode($face),
                ]);
            }

            return;
        }

        if ($analysis->file_type === 'audio' && isset($details['segments']) && is_array($details['segments'])) {
            foreach ($details['segments'] as $index => $segment) {
                $prediction = $this->normalizePrediction($segment['prediction'] ?? null);

                if (!$prediction) {
                    continue;
                }

                $analysis->details()->create([
                    'segment_index' => $index,
                    'prediction' => $prediction,
                    'confidence' => isset($segment['confidence']) ? (float) $segment['confidence'] : 0,
                    'extra_json' => json_encode($segment),
                ]);
            }
        }
    }

    protected function normalizePrediction(?string $prediction): ?string
    {
        $prediction = strtoupper((string) $prediction);

        return in_array($prediction, ['REAL', 'FAKE'], true) ? $prediction : null;
    }
}
