@props(['analysis', 'rawJson'])

@if($analysis->file_type === 'audio' && (isset($rawJson['override_reason']) || isset($rawJson['result']['override_reason'])))
    <div class="mb-10 p-8 bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl border border-red-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
        <h3 class="text-2xl font-semibold text-red-800 mb-8 border-b border-red-300 pb-4 flex items-center">
            <span class="mr-4 text-3xl">🎵</span>
            سبب التصنيف - تحليل الصوت
        </h3>
        <div class="bg-white p-6 rounded-xl border border-red-100 shadow-md">
            <div class="flex items-start">
                <div class="text-3xl mr-5 mt-1 text-red-500">🔍</div>
                <div class="flex-1">
                    <p class="text-red-700 font-medium leading-relaxed text-lg mb-4">
                        {{ $rawJson['override_reason'] ?? $rawJson['result']['override_reason'] ?? 'تم اكتشاف هذا المحتوى الصوتي كـ مزيف بناءً على تحليل متقدم للنموذج.' }}
                    </p>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            <strong class="text-red-600">ملاحظة:</strong> تم اكتشاف هذا المحتوى الصوتي كـ <strong class="text-red-600">مزيف</strong> بناءً على تحليل متقدم للنموذج. يرجى توخي الحذر عند التعامل مع هذا المحتوى.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Audio Segments Analysis -->
@if($analysis->file_type === 'audio' && (isset($rawJson['segments']) || isset($rawJson['result']['segments']) || isset($rawJson['result']['details']['segments'])) && is_array($rawJson['segments'] ?? $rawJson['result']['segments'] ?? $rawJson['result']['details']['segments'] ?? null))
    @php
        $segments = $rawJson['segments'] ?? $rawJson['result']['segments'] ?? $rawJson['result']['details']['segments'] ?? [];
        $most_suspicious_segment = null;
        foreach ($segments as $i => $seg) {
            if (isset($seg['is_most_suspicious']) && $seg['is_most_suspicious']) {
                $most_suspicious_segment = ['index' => $i, 'data' => $seg];
                break;
            }
        }
    @endphp
    
    @if($most_suspicious_segment && isset($most_suspicious_segment['data']['mfcc_image_base64']))
        <div class="mb-10 p-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
            <h3 class="text-2xl font-semibold text-blue-800 mb-8 border-b border-blue-300 pb-4 flex items-center">
                <span class="mr-4 text-3xl">📊</span>
                تحليل مفصل - المقطع الأكثر شكاً ({{ $most_suspicious_segment['index'] + 1 }})
            </h3>
            <div class="bg-white p-6 rounded-xl border border-blue-100 shadow-md">
                <div class="flex justify-center mb-6">
                    <div class="max-w-2xl">
                        <img src="data:image/png;base64,{{ $most_suspicious_segment['data']['mfcc_image_base64'] }}" 
                             alt="MFCC Most Suspicious Segment" 
                             class="w-full h-auto rounded-xl shadow-lg cursor-pointer hover:scale-105 transition-transform duration-300 border-4 border-blue-300"
                             onclick="openModal('data:image/png;base64,{{ $most_suspicious_segment['data']['mfcc_image_base64'] }}', 'تحليل MFCC - المقطع الأكثر شكاً')">
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            @if($analysis->prediction === 'FAKE')
                                <strong class="text-blue-600">تحليل MFCC:</strong> يظهر هذا التحليل الطيفي للمقطع الأكثر شكاً (أقل ثقة). اكتشف النموذج أنماطاً اصطناعية، أو انقطاعات في التردد، أو انتقالات غير طبيعية تشير إلى التلاعب أو التوليف الصوتي. ابحث عن أنماط التردد غير المنتظمة، أو القفزات المفاجئة، أو الميزات الطيفية غير المتسقة.
                            @else
                                <strong class="text-blue-600">تحليل MFCC:</strong> يظهر هذا التحليل الطيفي للمقطع الأكثر ثقة (أعلى ثقة). تحافظ أنماط التردد على خصائص الكلام الطبيعي مع انتقالات سلسة، وميزات طيفية متسقة، وتقدم ترددي منتظم نموذجي للكلام البشري الأصيل.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

<!-- Classification Reason for FAKE Media -->
@if($analysis->prediction === 'FAKE' && (isset($rawJson['classification_reason']) || isset($rawJson['result']['classification_reason']) || isset($rawJson['override_reason']) || isset($rawJson['result']['override_reason']) || isset($rawJson['reason']) || isset($rawJson['result']['reason']) || isset($rawJson['details']['classification_reason']) || isset($rawJson['details']['reason'])))
    <div class="mb-8 p-8 bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl border border-red-200 shadow-lg">
        <h3 class="text-2xl font-semibold text-red-800 mb-6 border-b border-red-300 pb-3 flex items-center">
            <span class="mr-4">⚠️</span>
            سبب التصنيف
        </h3>
        <div class="bg-white p-6 rounded-xl border border-red-100 shadow-md">
            <div class="flex items-start">
                <div class="text-3xl mr-5 mt-1">🔍</div>
                <div class="flex-1">
                    <p class="text-red-700 font-medium leading-relaxed text-lg mb-3">
                        {{ $rawJson['classification_reason'] ?? $rawJson['result']['classification_reason'] ?? $rawJson['override_reason'] ?? $rawJson['result']['override_reason'] ?? $rawJson['reason'] ?? $rawJson['result']['reason'] ?? $rawJson['details']['classification_reason'] ?? $rawJson['details']['reason'] ?? 'تم اكتشاف هذا المحتوى كـ مزيف بناءً على تحليل متقدم للنموذج.' }}
                    </p>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            <strong class="text-red-600">ملاحظة:</strong> تم اكتشاف هذا المحتوى كـ <strong class="text-red-600">مزيف</strong> بناءً على تحليل متقدم للنموذج. يرجى توخي الحذر عند التعامل مع هذا المحتوى.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif 