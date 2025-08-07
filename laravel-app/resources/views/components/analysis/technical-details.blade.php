@props(['analysis', 'json', 'mainResult'])

<div class="mb-8 sm:mb-10 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-200 shadow-lg hover:shadow-xl transition-shadow duration-300 opacity-0 transform translate-y-8" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
    <h3 class="text-xl sm:text-2xl font-semibold text-indigo-800 mb-6 sm:mb-8 border-b border-indigo-300 pb-3 sm:pb-4 flex items-center">
        <span class="mr-3 sm:mr-4 text-2xl sm:text-3xl">⚙️</span>
        التفاصيل التقنية
    </h3>
    
    <div class="space-y-6 sm:space-y-8">
        <!-- Model Information -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-indigo-100 hover:shadow-md transition-all duration-300 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
            <h4 class="text-base sm:text-lg font-semibold text-indigo-700 mb-4 flex items-center">
                <span class="mr-2 sm:mr-3 text-xl sm:text-2xl">🤖</span>
                معلومات النموذج
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                    <span class="text-gray-700 font-medium text-sm">النموذج المستخدم:</span>
                    <span class="font-bold text-indigo-600 text-sm">
                        @if($analysis->file_type === 'image' || $analysis->file_type === 'video')
                            EfficientNet-B4
                        @elseif($analysis->file_type === 'audio')
                            BiLSTM
                        @else
                            غير محدد
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                    <span class="text-gray-700 font-medium text-sm">نوع التحليل:</span>
                    <span class="font-bold text-indigo-600 text-sm">
                        @if($analysis->file_type === 'image')
                            تحليل الوجوه
                        @elseif($analysis->file_type === 'video')
                            تحليل الإطارات
                        @elseif($analysis->file_type === 'audio')
                            تحليل MFCC
                        @else
                            غير محدد
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg">
                    <span class="text-gray-700 font-medium text-sm">الميزات المستخدمة:</span>
                    <span class="font-bold text-indigo-600 text-sm">
                        @if($analysis->file_type === 'image' || $analysis->file_type === 'video')
                            EfficientNet Features
                        @elseif($analysis->file_type === 'audio')
                            MFCC + Delta + Delta-Delta
                        @else
                            غير محدد
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Processing Statistics -->
        <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-indigo-100 hover:shadow-md transition-all duration-300 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
            <h4 class="text-base sm:text-lg font-semibold text-indigo-700 mb-4 flex items-center">
                <span class="mr-2 sm:mr-3 text-xl sm:text-2xl">📈</span>
                إحصائيات المعالجة
            </h4>
            <div class="space-y-4">
                @php
                    $processingTime = $json['processing_time'] ?? $mainResult['processing_time'] ?? $mainResult['details']['processing_time'] ?? null;
                    $fileSize = $json['file_size'] ?? $mainResult['file_size'] ?? $mainResult['details']['file_size'] ?? null;
                @endphp
                
                <!-- General Processing Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    @if($processingTime)
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <span class="text-gray-700 font-medium text-sm flex items-center">
                                <span class="mr-2 text-indigo-500">⏱️</span>
                                وقت المعالجة
                            </span>
                            <span class="font-bold text-indigo-600 text-sm">{{ $processingTime }}</span>
                        </div>
                    @endif
                    @if($fileSize)
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <span class="text-gray-700 font-medium text-sm flex items-center">
                                <span class="mr-2 text-indigo-500">📁</span>
                                حجم الملف
                            </span>
                            <span class="font-bold text-indigo-600 text-sm">{{ $fileSize }}</span>
                        </div>
                    @endif
                </div>

                <!-- File Type Specific Statistics -->
                @if ($analysis->file_type === 'image')
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                        <span class="text-gray-700 font-medium text-sm flex items-center">
                            <span class="mr-2 text-blue-500">👥</span>
                            الوجوه المكتشفة
                        </span>
                        <span class="font-bold text-blue-600 text-lg">{{ $json['faces_detected'] ?? $mainResult['faces_detected'] ?? $mainResult['details']['faces_detected'] ?? 'غير محدد' }}</span>
                    </div>
                @elseif ($analysis->file_type === 'video')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <span class="text-gray-700 font-medium text-sm flex items-center">
                                <span class="mr-2 text-purple-500">🎬</span>
                                إجمالي الإطارات
                            </span>
                            <span class="font-bold text-purple-600 text-sm">{{ $json['frames_analyzed'] ?? $mainResult['frames_analyzed'] ?? $mainResult['details']['frames_analyzed'] ?? 'غير محدد' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <span class="text-gray-700 font-medium text-sm flex items-center">
                                <span class="mr-2 text-purple-500">👤</span>
                                إطارات بوجوه
                            </span>
                            <span class="font-bold text-purple-600 text-sm">{{ $json['frames_with_faces'] ?? $mainResult['frames_with_faces'] ?? $mainResult['details']['frames_with_faces'] ?? 'غير محدد' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <span class="text-gray-700 font-medium text-sm flex items-center">
                                <span class="mr-2 text-green-500">✅</span>
                                إطارات أصيلة
                            </span>
                            <span class="font-bold text-green-600 text-sm">{{ $json['real_frames'] ?? $mainResult['real_frames'] ?? $mainResult['details']['real_frames'] ?? 'غير محدد' }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-red-50 to-pink-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                            <span class="text-gray-700 font-medium text-sm flex items-center">
                                <span class="mr-2 text-red-500">❌</span>
                                إطارات مزيفة
                            </span>
                            <span class="font-bold text-red-600 text-sm">{{ $json['fake_frames'] ?? $mainResult['fake_frames'] ?? $mainResult['details']['fake_frames'] ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                @elseif ($analysis->file_type === 'audio')
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                        <span class="text-gray-700 font-medium text-sm flex items-center">
                            <span class="mr-2 text-green-500">🎵</span>
                            المقاطع المحللة
                        </span>
                        <span class="font-bold text-green-600 text-lg">{{ $json['segments_analyzed'] ?? $mainResult['segments_analyzed'] ?? $mainResult['details']['segments_analyzed'] ?? (is_array($json['segments'] ?? null) ? count($json['segments']) : (is_array($mainResult['segments'] ?? null) ? count($mainResult['segments']) : 'غير محدد')) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 