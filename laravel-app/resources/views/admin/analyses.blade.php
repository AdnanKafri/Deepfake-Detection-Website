@extends('layouts.dashboard')

@section('title', 'إدارة التحليلات')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Hero Section -->
    <section class="w-full max-w-7xl mx-auto text-center mb-8 mt-6">
        <div class="bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-chart-bar text-indigo-600 text-2xl"></i>
                إدارة التحليلات
            </h2>
            <p class="text-gray-600 text-base md:text-lg mb-2">عدد التحليلات الاجمالية</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right"></i>
                العودة للوحة المدير
            </a>
        </div>
    </section>

    <!-- Advanced Search Bar -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-6">
        <div class="bg-white rounded-3xl shadow-lg border border-indigo-100 p-6">
            <h3 class="text-lg font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-search text-indigo-600"></i>
                البحث المتقدم
            </h3>
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-indigo-700 mb-2">بحث في التحليلات</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="search" name="search" 
                                   placeholder="رقم التحليل، اسم الملف، البريد الإلكتروني..." 
                                   value="{{ request('search') }}"
                                   class="w-full pr-10 pl-4 py-3 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white/50 backdrop-blur-sm" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <label for="file_type" class="block text-sm font-medium text-indigo-700 mb-2">نوع الملف</label>
                        <select id="file_type" name="file_type" 
                                class="w-full px-4 py-3 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                            <option value="">جميع الأنواع</option>
                            <option value="image" {{ request('file_type') == 'image' ? 'selected' : '' }}>صور</option>
                            <option value="video" {{ request('file_type') == 'video' ? 'selected' : '' }}>فيديو</option>
                            <option value="audio" {{ request('file_type') == 'audio' ? 'selected' : '' }}>صوت</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="prediction" class="block text-sm font-medium text-indigo-700 mb-2">النتيجة</label>
                        <select id="prediction" name="prediction" 
                                class="w-full px-4 py-3 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white/50 backdrop-blur-sm">
                            <option value="">جميع النتائج</option>
                            <option value="REAL" {{ request('prediction') == 'REAL' ? 'selected' : '' }}>أصيل</option>
                            <option value="FAKE" {{ request('prediction') == 'FAKE' ? 'selected' : '' }}>مزيف</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 justify-center">
                    <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                    <a href="{{ route('admin.analyses') }}" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-times"></i>
                        مسح الفلاتر
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Filter Buttons -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-6">
        <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 p-6">
            <h3 class="text-lg font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-indigo-600"></i>
                فلترة التحليلات
            </h3>
            
            <div class="flex flex-wrap gap-3">
                <!-- All Analyses -->
                <a href="{{ route('admin.analyses') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ !request('filter') ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-list"></i>
                    <span>جميع التحليلات</span>
                    <span class="{{ !request('filter') ? 'bg-white/20' : 'bg-gray-200' }} text-xs px-2 py-1 rounded-full">{{ $filterCounts['total'] }}</span>
                </a>
                
                <!-- Reported Analyses -->
                <a href="{{ route('admin.analyses', ['filter' => 'reported']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('filter') == 'reported' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-flag"></i>
                    <span>التحليلات المبلغ عنها</span>
                    <span class="{{ request('filter') == 'reported' ? 'bg-white/20' : 'bg-gray-200' }} text-xs px-2 py-1 rounded-full">{{ $filterCounts['reported'] }}</span>
                </a>
                
                <!-- All Feedback -->
                <a href="{{ route('admin.analyses', ['filter' => 'feedback']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('filter') == 'feedback' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-comments"></i>
                    <span>التحليلات المقيّمة</span>
                    <span class="{{ request('filter') == 'feedback' ? 'bg-white/20' : 'bg-gray-200' }} text-xs px-2 py-1 rounded-full">{{ $filterCounts['feedback'] }}</span>
                </a>
                
                <!-- Correct Feedback -->
                <a href="{{ route('admin.analyses', ['filter' => 'correct_feedback']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('filter') == 'correct_feedback' ? 'bg-green-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-check-circle"></i>
                    <span>التقييمات الصحيحة</span>
                    <span class="{{ request('filter') == 'correct_feedback' ? 'bg-white/20' : 'bg-gray-200' }} text-xs px-2 py-1 rounded-full">{{ $filterCounts['correct_feedback'] }}</span>
                </a>
                
                <!-- Incorrect Feedback -->
                <a href="{{ route('admin.analyses', ['filter' => 'incorrect_feedback']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('filter') == 'incorrect_feedback' ? 'bg-yellow-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-times-circle"></i>
                    <span>التقييمات الخاطئة</span>
                    <span class="{{ request('filter') == 'incorrect_feedback' ? 'bg-white/20' : 'bg-gray-200' }} text-xs px-2 py-1 rounded-full">{{ $filterCounts['incorrect_feedback'] }}</span>
                </a>
            </div>
            
            @if(request('filter'))
            <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                <div class="flex items-center gap-2 text-indigo-700">
                    <i class="fas fa-info-circle"></i>
                    <span class="text-sm">
                        @switch(request('filter'))
                            @case('reported')
                                عرض التحليلات المبلغ عنها فقط
                                @break
                            @case('feedback')
                                عرض التحليلات المقيّمة فقط
                                @break
                            @case('correct_feedback')
                                عرض التحليلات ذات التقييم الصحيح فقط
                                @break
                            @case('incorrect_feedback')
                                عرض التحليلات ذات التقييم الخاطئ فقط
                                @break
                        @endswitch
                    </span>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Analyses Table -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-indigo-800">كل التحليلات ({{ $filterCounts['total'] }})</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التحليل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النتيجة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الثقة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم/الإبلاغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($analyses as $analysis)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-white"></i>
                                    </div>
                                    <div class="mr-4 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $analysis->file_name ?? 'بدون اسم' }}">
                                            {{ Str::limit($analysis->file_name ?? 'بدون اسم', 15) }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate" title="{{ $analysis->file_path ?? '' }}">
                                            ID: {{ $analysis->id }} | {{ Str::limit(basename($analysis->file_path ?? ''), 12) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-sm"></i>
                                    </div>
                                    <div class="mr-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($analysis->user)
                                                <a href="{{ route('admin.user.details', $analysis->user->id) }}" class="hover:text-indigo-600 transition">
                                                    {{ $analysis->user->name ?? 'مستخدم غير معروف' }}
                                                </a>
                                            @else
                                                مستخدم غير معروف
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $analysis->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $analysis->file_type == 'image' ? 'صورة' : ($analysis->file_type == 'video' ? 'فيديو' : 'صوت') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $analysis->prediction == 'REAL' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <i class="fas fa-{{ $analysis->prediction == 'REAL' ? 'check-circle' : 'exclamation-triangle' }} mr-1"></i>
                                    {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full" style="width: {{ $analysis->confidence * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ round($analysis->confidence * 100, 1) }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    @if($analysis->report_flag)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-flag"></i>
                                        تم الإبلاغ
                                    </span>
                                    @endif
                                    @if($analysis->user_feedback)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full {{ $analysis->user_feedback == 'CORRECT' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        <i class="fas fa-{{ $analysis->user_feedback == 'CORRECT' ? 'check' : 'times' }}"></i>
                                        {{ $analysis->user_feedback == 'CORRECT' ? 'تقييم صحيح' : 'تقييم خاطئ' }}
                                    </span>
                                    @endif
                                    @if(!$analysis->report_flag && !$analysis->user_feedback)
                                    <span class="text-xs text-gray-500">لا يوجد</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $analysis->created_at->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">{{ $analysis->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- زر التفاصيل الجزئية: متاح دومًا -->
                                    <a href="{{ route('admin.analysis.details', $analysis->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- زر التفاصيل الكاملة: فقط إذا كان عليه بلاغ أو تقييم -->
                                    @if($analysis->report_flag || $analysis->user_feedback || (auth()->check() && auth()->id() == $analysis->user_id))
                                        <a href="{{ route('analysis.show', $analysis->id) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-300 cursor-not-allowed" title="لا يمكنك الدخول للتفاصيل الكاملة إلا إذا كان عليه بلاغ أو تقييم"><i class="fas fa-external-link-alt"></i></span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.analysis.delete', $analysis->id) }}" class="inline delete-analysis-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 delete-analysis-btn"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                                @if($manualSearch && !$analysis->report_flag && !$analysis->user_feedback)
                                <div class="mt-2">
                                    <span class="block bg-yellow-50 text-yellow-800 text-xs rounded px-2 py-1 text-center border border-yellow-200 w-full">
                                        معلومات عامة فقط <br>(لا يمكن عرض التفاصيل الكاملة<br> إلا إذا كان عليه بلاغ أو تقييم)
                                    </span>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                لا توجد تحليلات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Custom Pagination -->
            @if($analyses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex justify-center">
                    <nav class="flex items-center gap-2 bg-white/80 rounded-full shadow-md p-2 border border-indigo-100">
                        {{-- Previous Page Link --}}
                        @if($analyses->onFirstPage())
                            <span class="px-4 py-2 rounded-full text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @else
                            <a href="{{ $analyses->previousPageUrl() }}" class="px-4 py-2 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach(range(1, $analyses->lastPage()) as $i)
                            @if($i == $analyses->currentPage())
                                <span class="px-4 py-2 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow">{{ $i }}</span>
                            @else
                                <a href="{{ $analyses->url($i) }}" class="px-4 py-2 rounded-full text-indigo-600 hover:bg-indigo-50 transition">{{ $i }}</a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if($analyses->hasMorePages())
                            <a href="{{ $analyses->nextPageUrl() }}" class="px-4 py-2 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 rounded-full text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('status'))
        Swal.fire({
            title: 'تم بنجاح!',
            text: '{{ session('status') }}',
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#10B981'
        });
    @endif
    @if(session('error'))
        Swal.fire({
            title: 'خطأ!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#EF4444'
        });
    @endif

    // استبدال حذف التحليل بـ SweetAlert
    document.querySelectorAll('.delete-analysis-form').forEach(function(form) {
        console.log('Found delete form:', form); // للتأكد من عمل الكود
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submit prevented'); // للتأكد من عمل الكود
            
            // الحصول على معلومات التحليل من الصف
            const analysisRow = this.closest('tr');
            const fileName = analysisRow.querySelector('td:first-child .text-sm.font-medium').textContent.trim();
            const idText = analysisRow.querySelector('td:first-child .text-sm.text-gray-500').textContent.trim();
            const analysisId = idText.split('|')[0].replace('ID:', '').trim();
            const fileType = analysisRow.querySelector('td:nth-child(3) span').textContent.trim();
            const prediction = analysisRow.querySelector('td:nth-child(4) span').textContent.trim();
            
            console.log('Analysis info:', { analysisId, fileName, fileType, prediction }); // للتأكد من عمل الكود
            
            Swal.fire({
                title: 'تأكيد حذف التحليل',
                html: `هل أنت متأكد من حذف التحليل؟<br><br><strong>رقم التحليل:</strong> ${analysisId}<br><strong>اسم الملف:</strong> ${fileName}<br><strong>نوع الملف:</strong> ${fileType}<br><strong>النتيجة:</strong> ${prediction}<br><br><span class="text-red-600 font-bold">تحذير:</span> لا يمكن التراجع عن هذا الإجراء!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف التحليل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('User confirmed deletion'); // للتأكد من عمل الكود
                    
                    // إظهار loading
                    Swal.fire({
                        title: 'جاري الحذف...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // إرسال النموذج
                    form.submit();
                }
            });
        });
    });

    // بديل إضافي: event listener للزر مباشرة
    document.querySelectorAll('.delete-analysis-btn').forEach(function(button) {
        console.log('Found delete button:', button); // للتأكد من عمل الكود
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Button click prevented'); // للتأكد من عمل الكود
            
            const form = this.closest('form');
            const analysisRow = this.closest('tr');
            const fileName = analysisRow.querySelector('td:first-child .text-sm.font-medium').textContent.trim();
            const idText = analysisRow.querySelector('td:first-child .text-sm.text-gray-500').textContent.trim();
            const analysisId = idText.split('|')[0].replace('ID:', '').trim();
            const fileType = analysisRow.querySelector('td:nth-child(3) span').textContent.trim();
            const prediction = analysisRow.querySelector('td:nth-child(4) span').textContent.trim();
            
            console.log('Analysis info from button:', { analysisId, fileName, fileType, prediction }); // للتأكد من عمل الكود
            
            Swal.fire({
                title: 'تأكيد حذف التحليل',
                html: `هل أنت متأكد من حذف التحليل؟<br><br><strong>رقم التحليل:</strong> ${analysisId}<br><strong>اسم الملف:</strong> ${fileName}<br><strong>نوع الملف:</strong> ${fileType}<br><strong>النتيجة:</strong> ${prediction}<br><br><span class="text-red-600 font-bold">تحذير:</span> لا يمكن التراجع عن هذا الإجراء!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف التحليل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('User confirmed deletion from button'); // للتأكد من عمل الكود
                    
                    // إظهار loading
                    Swal.fire({
                        title: 'جاري الحذف...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // إرسال النموذج
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection 