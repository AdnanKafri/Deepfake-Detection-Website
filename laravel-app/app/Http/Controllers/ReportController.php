<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analysis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function generate($id)
    {
        $analysis = Analysis::with('details', 'user')->findOrFail($id);
        
        // استخراج البيانات من result_json بشكل صحيح
        $resultData = json_decode($analysis->result_json, true);
        
        // الحصول على البيانات الرئيسية
        $mainResult = $resultData['result'] ?? $resultData;
        $details = $mainResult['details'] ?? [];
        
        // تحميل الصور من التخزين وتحويلها إلى base64
        foreach ($analysis->details as $detail) {
            // للصور الأصلية
            if ($detail->original_image_path) {
                // إزالة 'storage/' من بداية المسار إذا كان موجوداً
                $cleanPath = ltrim($detail->original_image_path, '/');
                if (str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, 8); // إزالة 'storage/'
                }
                
                // محاولة الوصول من storage/app/public
                $storagePath = storage_path('app/public/' . $cleanPath);
                if (file_exists($storagePath)) {
                    try {
                        $detail->original_image_base64 = base64_encode(file_get_contents($storagePath));
                        $detail->original_image_type = pathinfo($storagePath, PATHINFO_EXTENSION);
                        Log::info("Original image loaded successfully: {$storagePath}");
                    } catch (\Exception $e) {
                        Log::error("Failed to load original image: {$storagePath}", ['error' => $e->getMessage()]);
                    }
                } else {
                    // محاولة ثانية من public/storage
                    $publicPath = public_path('storage/' . $cleanPath);
                    if (file_exists($publicPath)) {
                        try {
                            $detail->original_image_base64 = base64_encode(file_get_contents($publicPath));
                            $detail->original_image_type = pathinfo($publicPath, PATHINFO_EXTENSION);
                            Log::info("Original image loaded from public: {$publicPath}");
                        } catch (\Exception $e) {
                            Log::error("Failed to load original image from public: {$publicPath}", ['error' => $e->getMessage()]);
                        }
                    } else {
                        Log::warning("Original image not found: {$cleanPath}");
                    }
                }
            }

            // للوجوه المقطوعة
            if ($detail->cropped_face_path) {
                // إزالة 'storage/' من بداية المسار إذا كان موجوداً
                $cleanPath = ltrim($detail->cropped_face_path, '/');
                if (str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, 8); // إزالة 'storage/'
                }
                
                // محاولة الوصول من storage/app/public
                $storagePath = storage_path('app/public/' . $cleanPath);
                if (file_exists($storagePath)) {
                    try {
                        $detail->cropped_face_base64 = base64_encode(file_get_contents($storagePath));
                        $detail->cropped_face_type = pathinfo($storagePath, PATHINFO_EXTENSION);
                        Log::info("Cropped face loaded successfully: {$storagePath}");
                    } catch (\Exception $e) {
                        Log::error("Failed to load cropped face: {$storagePath}", ['error' => $e->getMessage()]);
                    }
                } else {
                    // محاولة ثانية من public/storage
                    $publicPath = public_path('storage/' . $cleanPath);
                    if (file_exists($publicPath)) {
                        try {
                            $detail->cropped_face_base64 = base64_encode(file_get_contents($publicPath));
                            $detail->cropped_face_type = pathinfo($publicPath, PATHINFO_EXTENSION);
                            Log::info("Cropped face loaded from public: {$publicPath}");
                        } catch (\Exception $e) {
                            Log::error("Failed to load cropped face from public: {$publicPath}", ['error' => $e->getMessage()]);
                        }
                    } else {
                        Log::warning("Cropped face not found: {$cleanPath}");
                    }
                }
            }
        }

        $pdf = Pdf::loadView('reports.analysis', [
            'analysis' => $analysis,
            'json' => $details, // استخدام details مباشرة
            'mainResult' => $mainResult, // إضافة النتيجة الرئيسية
        ]);

        return $pdf->download("deepfake_report_{$analysis->id}.pdf");
    }

    public function showImage($id, $segment_index)
    {
        $analysis = Analysis::findOrFail($id);
        $resultData = json_decode($analysis->result_json, true);
        $mainResult = $resultData['result'] ?? $resultData;
        $details = $mainResult['details'] ?? [];
        
        if (!isset($details['segments'][$segment_index]['mfcc_image_base64'])) {
            abort(404, 'Image not found');
        }
        
        return view('reports.image_viewer', [
            'analysis' => $analysis,
            'segment_index' => $segment_index,
            'image_base64' => $details['segments'][$segment_index]['mfcc_image_base64'],
            'segment_data' => $details['segments'][$segment_index]
        ]);
    }
}
