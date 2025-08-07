@props(['analysis', 'rawJson'])

<div class="mb-8 sm:mb-10 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 shadow-lg hover:shadow-xl transition-shadow duration-300 opacity-0 transform translate-y-8" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
    <h3 class="text-xl sm:text-2xl font-semibold text-amber-800 mb-6 sm:mb-8 border-b border-amber-300 pb-3 sm:pb-4 flex items-center">
        <span class="mr-3 sm:mr-4 text-2xl sm:text-3xl">
            @if($analysis->file_type === 'image')
                🖼️
            @elseif($analysis->file_type === 'video')
                🎬
            @elseif($analysis->file_type === 'audio')
                🎵
            @else
                🔍
            @endif
        </span>
        @if($analysis->file_type === 'image')
            تفاصيل تحليل الصورة
        @elseif($analysis->file_type === 'video')
            تفاصيل تحليل الفيديو
        @elseif($analysis->file_type === 'audio')
            تفاصيل تحليل الصوت
        @else
            التحليل المفصل
        @endif
    </h3>
    
    @if($analysis->file_type === 'video' && count($analysis->details))
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-amber-100 to-orange-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الإطار</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الصورة المصغرة</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الوجه بعد الاقتصاص</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">النتيجة</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الثقة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        @foreach($analysis->details as $index => $detail)
                            <tr class="hover:bg-amber-50 transition-colors duration-200">
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-gray-700 font-medium text-sm sm:text-lg text-center">{{ $detail->frame_number ?? ($index + 1) }}</td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5">
                                    @if(isset($detail->original_image_base64))
                                        <div class="flex justify-center">
                                            <img src="data:image/{{ $detail->original_image_type }};base64,{{ $detail->original_image_base64 }}" 
                                                 alt="Frame {{ $detail->frame_number ?? ($index + 1) }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('data:image/{{ $detail->original_image_type }};base64,{{ $detail->original_image_base64 }}', 'إطار {{ $detail->frame_number ?? ($index + 1) }}')">
                                            </div>
                                    @elseif($detail->image_path)
                                        <div class="flex justify-center">
                                            <img src="{{ asset('storage/' . $detail->image_path) }}" 
                                                 alt="Frame {{ $detail->frame_number ?? ($index + 1) }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('{{ asset('storage/' . $detail->image_path) }}', 'إطار {{ $detail->frame_number ?? ($index + 1) }}')">
                                            </div>
                                    @else
                                        <div class="flex justify-center">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">غير متوفر</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5">
                                    @if(isset($detail->cropped_face_base64))
                                        <div class="flex justify-center">
                                            <img src="data:image/{{ $detail->cropped_face_type }};base64,{{ $detail->cropped_face_base64 }}" 
                                                 alt="Face {{ $detail->frame_number ?? ($index + 1) }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('data:image/{{ $detail->cropped_face_type }};base64,{{ $detail->cropped_face_base64 }}', 'وجه الإطار {{ $detail->frame_number ?? ($index + 1) }}')">
                                            </div>
                                    @elseif($detail->cropped_face_path)
                                        <div class="flex justify-center">
                                            <img src="{{ asset('storage/' . $detail->cropped_face_path) }}" 
                                                 alt="Face {{ $detail->frame_number ?? ($index + 1) }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('{{ asset('storage/' . $detail->cropped_face_path) }}', 'وجه الإطار {{ $detail->frame_number ?? ($index + 1) }}')">
                                            </div>
                                    @else
                                        <div class="flex justify-center">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">غير متوفر</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                                    <span class="px-2 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold {{ $detail->prediction == 'REAL' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                                        {{ $detail->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-gray-700 font-semibold text-sm sm:text-lg text-center">{{ round($detail->confidence * 100, 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($analysis->file_type === 'image' && count($analysis->details))
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-amber-100 to-orange-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الوجه</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الصورة المصغرة</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الوجه بعد الاقتصاص</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">النتيجة</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الثقة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        @foreach($analysis->details as $index => $detail)
                            <tr class="hover:bg-amber-50 transition-colors duration-200">
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-gray-700 font-medium text-sm sm:text-lg text-center">{{ $detail->face_number ?? ($index + 1) }}</td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5">
                                    @if(isset($detail->original_image_base64))
                                        <div class="flex justify-center">
                                            <img src="data:image/{{ $detail->original_image_type }};base64,{{ $detail->original_image_base64 }}" 
                                                 alt="Original Image" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('data:image/{{ $detail->original_image_type }};base64,{{ $detail->original_image_base64 }}', 'الصورة الأصلية')">
                                            </div>
                                    @elseif($detail->image_path)
                                        <div class="flex justify-center">
                                            <img src="{{ asset('storage/' . $detail->image_path) }}" 
                                                 alt="Original Image" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('{{ asset('storage/' . $detail->image_path) }}', 'الصورة الأصلية')">
                                            </div>
                                    @else
                                        <div class="flex justify-center">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">غير متوفر</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5">
                                    @if(isset($detail->cropped_face_base64))
                                        <div class="flex justify-center">
                                            <img src="data:image/{{ $detail->cropped_face_type }};base64,{{ $detail->cropped_face_base64 }}" 
                                                 alt="Face {{ $detail->face_number ?? ($index + 1) }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('data:image/{{ $detail->cropped_face_type }};base64,{{ $detail->cropped_face_base64 }}', 'وجه {{ $detail->face_number ?? ($index + 1) }}')">
                                            </div>
                                    @elseif($detail->cropped_face_path)
                                        <div class="flex justify-center">
                                            <img src="{{ asset('storage/' . $detail->cropped_face_path) }}" 
                                                 alt="Face {{ $detail->face_number ?? ($index + 1) }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('{{ asset('storage/' . $detail->cropped_face_path) }}', 'وجه {{ $detail->face_number ?? ($index + 1) }}')">
                                            </div>
                                    @else
                                        <div class="flex justify-center">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">غير متوفر</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                                    <span class="px-2 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold {{ $detail->prediction == 'REAL' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                                        {{ $detail->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-gray-700 font-semibold text-sm sm:text-lg text-center">{{ round($detail->confidence * 100, 2) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($analysis->file_type === 'audio' && (isset($rawJson['segments']) || isset($rawJson['result']['segments']) || isset($rawJson['result']['details']['segments'])) && is_array($rawJson['segments'] ?? $rawJson['result']['segments'] ?? $rawJson['result']['details']['segments'] ?? null))
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-amber-100 to-orange-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">المقطع</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">تحليل MFCC</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">النتيجة</th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-amber-800 font-semibold text-sm sm:text-lg">الثقة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        @php
                            $segments = $rawJson['segments'] ?? $rawJson['result']['segments'] ?? $rawJson['result']['details']['segments'] ?? [];
                        @endphp
                        @foreach($segments as $index => $segment)
                            <tr class="hover:bg-amber-50 transition-colors duration-200">
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-gray-700 font-medium text-sm sm:text-lg text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="mr-2 sm:mr-3 text-xl sm:text-2xl">🎵</span>
                                        <span>المقطع {{ $index + 1 }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5">
                                    @if(isset($segment['mfcc_image_base64']))
                                        <div class="flex justify-center">
                                            <img src="data:image/png;base64,{{ $segment['mfcc_image_base64'] }}" 
                                                 alt="MFCC Segment {{ $index + 1 }}" 
                                                 class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform duration-200 border-2 border-gray-200"
                                                 onclick="openModal('data:image/png;base64,{{ $segment['mfcc_image_base64'] }}', 'تحليل MFCC - المقطع {{ $index + 1 }}')">
                                                </div>
                                    @else
                                        <div class="flex justify-center">
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">غير متوفر</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-center">
                                    @php
                                        $prediction = $segment['prediction'] ?? $segment['result'] ?? 'UNKNOWN';
                                        $confidence = $segment['confidence'] ?? $segment['score'] ?? 0;
                                    @endphp
                                    <span class="px-2 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold {{ $prediction == 'REAL' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }}">
                                        {{ $prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-4 sm:py-5 text-gray-700 font-semibold text-sm sm:text-lg text-center">
                                    {{ round($confidence * 100, 2) }}%
                                    <small class="text-gray-500 text-xs sm:text-sm block">({{ rtrim(rtrim(number_format($confidence, 8, '.', ''), '0'), '.') }})</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div> 