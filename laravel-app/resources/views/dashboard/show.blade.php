@extends('layouts.dashboard')

@section('content')
    @if (session('status'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded text-center shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    @php
        // استخراج البيانات من result_json بشكل صحيح
        $json = json_decode($analysis->result_json, true);
        $mainResult = $json['result'] ?? $json;
        $rawJson = json_decode($analysis->result_json, true);
        
        // تحميل الصور من التخزين وتحويلها إلى base64
        foreach ($analysis->details as $detail) {
            // للصور الأصلية
            if ($detail->original_image_path) {
                $cleanPath = ltrim($detail->original_image_path, '/');
                if (str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, 8);
                }
                
                $storagePath = storage_path('app/public/' . $cleanPath);
                if (file_exists($storagePath)) {
                    try {
                        $detail->original_image_base64 = base64_encode(file_get_contents($storagePath));
                        $detail->original_image_type = pathinfo($storagePath, PATHINFO_EXTENSION);
                    } catch (\Exception $e) {
                        // Handle error silently
                    }
                } else {
                    $publicPath = public_path('storage/' . $cleanPath);
                    if (file_exists($publicPath)) {
                        try {
                            $detail->original_image_base64 = base64_encode(file_get_contents($publicPath));
                            $detail->original_image_type = pathinfo($publicPath, PATHINFO_EXTENSION);
                        } catch (\Exception $e) {
                            // Handle error silently
                        }
                    }
                }
            }

            // للوجوه المقطوعة
            if ($detail->cropped_face_path) {
                $cleanPath = ltrim($detail->cropped_face_path, '/');
                if (str_starts_with($cleanPath, 'storage/')) {
                    $cleanPath = substr($cleanPath, 8);
                }
                
                $storagePath = storage_path('app/public/' . $cleanPath);
                if (file_exists($storagePath)) {
                    try {
                        $detail->cropped_face_base64 = base64_encode(file_get_contents($storagePath));
                        $detail->cropped_face_type = pathinfo($storagePath, PATHINFO_EXTENSION);
                    } catch (\Exception $e) {
                        // Handle error silently
                    }
                } else {
                    $publicPath = public_path('storage/' . $cleanPath);
                    if (file_exists($publicPath)) {
                        try {
                            $detail->cropped_face_base64 = base64_encode(file_get_contents($publicPath));
                            $detail->cropped_face_type = pathinfo($publicPath, PATHINFO_EXTENSION);
                        } catch (\Exception $e) {
                            // Handle error silently
                        }
                    }
                }
            }
        }
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-4 sm:py-6 lg:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header with Back Button -->
            <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center w-full sm:w-auto">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 lg:px-6 py-2 sm:py-3 bg-white border border-gray-300 rounded-xl shadow-sm text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200 transform hover:-translate-y-1 text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        العودة للوحة التحكم
                    </a>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 w-full sm:w-auto">
                    <a href="{{ route('analysis.pdf', $analysis->id) }}" target="_blank" class="inline-flex items-center justify-center w-full sm:w-auto px-3 sm:px-4 lg:px-6 py-2 sm:py-3 bg-green-600 border border-transparent rounded-xl shadow-sm text-white font-medium hover:bg-green-700 transition-all duration-200 transform hover:-translate-y-1 text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        عرض PDF
                    </a>
                </div>
            </div>

            <!-- Page Title -->
            <div class="text-center mb-8 sm:mb-10">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 mb-3 sm:mb-4 flex items-center justify-center">
                    <span class="mr-3 sm:mr-4 text-3xl sm:text-4xl lg:text-5xl">🔍</span>
                    تقرير تحليل Deepfake
                </h1>
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed px-4">
                    تحليل مفصل للمحتوى باستخدام تقنيات الذكاء الاصطناعي المتقدمة
                </p>
            </div>

            <div class="max-w-7xl mx-auto bg-white p-4 sm:p-6 lg:p-8 rounded-2xl shadow-xl opacity-0 transform translate-y-8" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-700 mb-6 sm:mb-8 text-center bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">تفاصيل التحليل</h2>

                <!-- General Information Section -->
                <x-analysis.general-info :analysis="$analysis" />

                <!-- Technical Details Section -->
                <x-analysis.technical-details :analysis="$analysis" :json="$json" :mainResult="$mainResult" />

                <!-- Detailed Analysis Section -->
                <x-analysis.detailed-analysis :analysis="$analysis" :rawJson="$rawJson" />

                <!-- Special Sections (Audio Analysis, Classification Reasons, etc.) -->
                <x-analysis.special-sections :analysis="$analysis" :rawJson="$rawJson" />

                <!-- Action Buttons -->
                <x-analysis.actions :analysis="$analysis" />
            </div>
        </div>
    </div>

    <!-- Modals and Scripts -->
    <x-analysis.modals :analysis="$analysis" />
@endsection 