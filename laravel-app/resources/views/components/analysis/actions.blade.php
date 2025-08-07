@props(['analysis'])

<div class="mb-10 p-8 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300 opacity-0 transform translate-y-8" data-aos="fade-up" data-aos-duration="800" data-aos-delay="700">
    <h3 class="text-2xl font-semibold text-gray-800 mb-8 border-b border-gray-300 pb-4 flex items-center">
        <svg class="w-8 h-8 mr-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
        </svg>
        الإجراءات المتاحة
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('analysis.pdf', $analysis->id) }}" target="_blank"
            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="800">
            <svg class="w-12 h-12 mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="font-semibold text-gray-800 mb-2">تحميل PDF</span>
            <span class="text-sm text-gray-600 text-center">تحميل التقرير بصيغة PDF</span>
        </a>
        
        
        @can('ownerActions', $analysis)
            {{-- Actions for analysis owner --}}
            @if (is_null($analysis->user_feedback))
                <button onclick="showFeedbackModal()"
                    class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="900">
                    <svg class="w-12 h-12 mb-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <span class="font-semibold text-gray-800 mb-2">تقييم النتيجة</span>
                    <span class="text-sm text-gray-600 text-center">ساعدنا في تحسين النموذج</span>
                </button>
            @else
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="900">
                    <svg class="w-12 h-12 mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-gray-800 mb-2">تم التقييم</span>
                    <span class="text-sm text-gray-600 text-center">
                        {{ $analysis->user_feedback == 'CORRECT' ? 'صحيحة' : 'خاطئة' }}
                    </span>
                </div>
            @endif
            
            @if(!$analysis->report_flag)
                <button onclick="showReportModal()" 
                        class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="950">
                    <svg class="w-12 h-12 mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <span class="font-semibold text-gray-800 mb-2">إبلاغ مزيف</span>
                    <span class="text-sm text-gray-600 text-center">الإبلاغ عن محتوى مزيف</span>
                </button>
            @else
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="950">
                    <svg class="w-12 h-12 mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-gray-800 mb-2">تم الإبلاغ</span>
                    <span class="text-sm text-gray-600 text-center">شكراً لك على الإبلاغ</span>
                </div>
            @endif
        @endcan

        @if (Auth::user()->isAdmin() && Auth::id() != $analysis->user_id)
            {{-- Actions for Admin (who is not the owner) --}}
            <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="900">
                @if(is_null($analysis->user_feedback))
                    <svg class="w-12 h-12 mb-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4c0-1.19.49-2.26 1.28-3.03"></path><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"></path>
                    </svg>
                    <span class="font-semibold text-gray-800 mb-2">تقييم المستخدم</span>
                    <span class="text-sm text-gray-600 text-center">لم يقم المستخدم بالتقييم بعد</span>
                @else
                    <svg class="w-12 h-12 mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-gray-800 mb-2">تقييم المستخدم</span>
                    <span class="text-sm text-gray-600 text-center">
                        النتيجة: {{ $analysis->user_feedback == 'CORRECT' ? 'صحيحة' : 'خاطئة' }}
                    </span>
                @endif
            </div>

            <button type="button" onclick="showAdminCyberReport()"
                    class="flex flex-col items-center p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="950">
                <svg class="w-12 h-12 mb-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="font-semibold text-gray-800 mb-2">إبلاغ جهة مختصة</span>
                <span class="text-sm text-gray-600 text-center">إبلاغ قسم الجرائم الإلكترونية</span>
            </button>
        @endif
    </div>
</div> 