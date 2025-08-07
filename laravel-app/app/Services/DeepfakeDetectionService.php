<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class DeepfakeDetectionService
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.deepfake.api_url', 'http://localhost:8000');
    }

    public function analyzeImage(UploadedFile $file)
    {
        return $this->sendFileToApi($file, 'image');
    }

    public function analyzeVideo(UploadedFile $file, $framesToSample = 5, $saveFrames = false)
    {
        return $this->sendFileToApi($file, 'video', [
            'frames_to_sample' => $framesToSample,
            'save_frames' => $saveFrames
        ]);
    }

    public function analyzeAudio(UploadedFile $file)
    {
        return $this->sendFileToApi($file, 'audio');
    }

    protected function sendFileToApi(UploadedFile $file, string $fileType, array $extraParams = [])
    {
        try {
            $params = array_merge([
                'file_type' => $fileType
            ], $extraParams);

            // timeout أطول للتحليل الصوتي المعقد
            $timeout = ($fileType === 'audio') ? 300 : 120; // 5 دقائق للصوت، دقيقتان للباقي

            $response = Http::timeout($timeout)
                ->attach(
                    'file',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )
                ->asMultipart()
                ->post($this->apiBaseUrl . '/analyze', $params);

            if ($response->failed()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to analyze file: ' . $e->getMessage());
        }
    }
}
