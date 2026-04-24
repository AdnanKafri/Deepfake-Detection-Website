<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMediaJob;
use App\Models\Analysis;
use App\Services\DeepfakeDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeepfakeController extends Controller
{
    protected DeepfakeDetectionService $deepfakeService;

    public function __construct(DeepfakeDetectionService $deepfakeService)
    {
        $this->deepfakeService = $deepfakeService;
    }

    public function index()
    {
        return view('deepfake.index');
    }

    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpg,jpeg,png,mp4,avi,mov,mp3,wav|max:102400',
            ]);

            $file = $request->file('file');
            $fileType = $this->detectFileType($file->getClientOriginalExtension());
            $storedName = Str::uuid()->toString() . '.' . strtolower($file->getClientOriginalExtension());
            $filePath = $file->storeAs('uploads/deepfake', $storedName);

            $analysis = Analysis::create([
                'user_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $fileType,
                'status' => Analysis::STATUS_QUEUED,
                'prediction' => null,
                'confidence' => null,
                'result_json' => null,
                'queued_at' => Carbon::now(),
            ]);

            ProcessMediaJob::dispatch($analysis->id);

            return response()->json([
                'status' => 'success',
                'analysis_id' => $analysis->id,
                'processing_status' => $analysis->status,
                'message' => 'Analysis is being processed.',
                'data' => [
                    'type' => $fileType,
                    'prediction' => 'UNKNOWN',
                    'confidence' => 0,
                    'details' => [
                        'status' => $analysis->status,
                        'message' => 'Analysis is being processed in the background.',
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Deepfake analysis error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to analyze file: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function status(Analysis $analysis)
    {
        if ($analysis->user_id !== null && (!Auth::check() || Auth::id() !== $analysis->user_id)) {
            abort(403);
        }

        $resultData = $analysis->result_json ? json_decode($analysis->result_json, true) : null;
        $mainResult = $resultData['result'] ?? $resultData;

        return response()->json([
            'status' => 'success',
            'analysis_id' => $analysis->id,
            'processing_status' => $analysis->status,
            'data' => $mainResult,
            'error_message' => $analysis->error_message,
        ]);
    }

    protected function detectFileType(string $extension): string
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return 'image';
        }

        if (in_array($extension, ['mp4', 'avi', 'mov'])) {
            return 'video';
        }

        return 'audio';
    }

    /**
     * Legacy synchronous flow kept in place for the next migration phase.
     */
    protected function analyzeSynchronously(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            throw new \InvalidArgumentException('Missing upload file.');
        }

        $fileType = $this->detectFileType($file->getClientOriginalExtension());

        if ($fileType === 'image') {
            return $this->deepfakeService->analyzeImage($file);
        }

        if ($fileType === 'video') {
            return $this->deepfakeService->analyzeVideo($file, 10, true);
        }

        return $this->deepfakeService->analyzeAudio($file);
    }
}
