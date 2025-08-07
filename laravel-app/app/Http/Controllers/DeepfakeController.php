<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DeepfakeDetectionService;
use Illuminate\Support\Facades\Log;
use App\Models\Analysis;
use App\Models\AnalysisDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeepfakeController extends Controller
{
    protected $deepfakeService;

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

            // زيادة timeout للتحليل الصوتي
            if ($request->file_type === 'audio') {
                set_time_limit(300); // 5 دقائق للصوت
            } else {
                set_time_limit(120); // دقيقتان للباقي
            }

            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            // تحديد نوع الملف
            $fileType = in_array($extension, ['jpg', 'jpeg', 'png']) ? 'image' : (in_array($extension, ['mp4', 'avi', 'mov']) ? 'video' : 'audio');

            // التحليل عبر الخدمة
            if ($fileType === 'image') {
                $result = $this->deepfakeService->analyzeImage($file);
            } elseif ($fileType === 'video') {
                // تحليل سريع للفيديو مع حفظ الصور للتقارير فقط
                $result = $this->deepfakeService->analyzeVideo($file, 10, true);
            } else {
                $result = $this->deepfakeService->analyzeAudio($file);
            }

            // تأكد أن الرد يحوي النتيجة المطلوبة
            $main = $result['result'] ?? $result;

            // إذا كان المستخدم غير مسجل دخول، أرجع النتيجة فقط بدون حفظ
            if (!Auth::check()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $result
                ]);
            }

            // حفظ في قاعدة البيانات فقط للمستخدم المسجل
            $analysis = Analysis::create([
                'user_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $main['type'] ?? $fileType,
                'prediction' => $main['prediction'] ?? 'UNKNOWN',
                'confidence' => $main['confidence'] ?? 0,
                'result_json' => json_encode($result),
            ]);

            // حفظ التفاصيل (إن وجدت)
            $details = $main['details'] ?? [];
            if ($fileType === 'video' && isset($details['frame_images'])) {
                foreach ($details['frame_images'] as $i => $frame) {
                    $originalPath = $frame['original_path'] ?? null;
                    $croppedPath = $frame['cropped_face_path'] ?? null;

                    $analysis->details()->create([
                        'segment_index' => $i,
                        'prediction' => $frame['prediction'] ?? 'UNKNOWN',
                        'confidence' => $frame['confidence'] ?? 0,
                        'original_image_path' => $originalPath,
                        'cropped_face_path' => $croppedPath,
                        'extra_json' => json_encode($frame),
                    ]);
                }
            } elseif ($fileType === 'image' && isset($details['face_details'])) {
                // حفظ تفاصيل الوجوه للصور
                foreach ($details['face_details'] as $i => $face) {
                    $originalPath = $face['original_path'] ?? null;
                    $croppedPath = $face['cropped_face_path'] ?? null;

                    $analysis->details()->create([
                        'segment_index' => $i,
                        'prediction' => $face['prediction'] ?? 'UNKNOWN',
                        'confidence' => $face['confidence'] ?? 0,
                        'original_image_path' => $originalPath,
                        'cropped_face_path' => $croppedPath,
                        'extra_json' => json_encode($face),
                    ]);
                }
            } elseif ($fileType === 'audio' && isset($details['segments'])) {
                foreach ($details['segments'] as $i => $segment) {
                    $analysis->details()->create([
                        'segment_index' => $i,
                        'prediction' => $segment['prediction'] ?? 'UNKNOWN',
                        'confidence' => $segment['confidence'] ?? 0,
                        'extra_json' => json_encode($segment),
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $result,
                'analysis_id' => $analysis->id
            ]);
        } catch (\Exception $e) {
            Log::error('Deepfake analysis error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to analyze file: ' . $e->getMessage()
            ], 500);
        }
    }
}
