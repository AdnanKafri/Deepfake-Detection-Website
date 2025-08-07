@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Hero Section -->
    <section class="w-full max-w-4xl mx-auto text-center mb-10 mt-10 animate-fade-in">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <div class="flex-1">
                <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                    <i class="fas fa-chart-bar text-indigo-500 text-2xl"></i>
                    لوحة تحكم التحليلات
                </h2>
                <p class="text-gray-600 text-base md:text-lg mb-3">تابع جميع تحليلاتك ونتائجها في مكان واحد.</p>
                <div class="flex flex-wrap justify-center gap-3 mt-4">
                    <span class="bg-gradient-to-r from-purple-400 to-indigo-400 text-white rounded-full px-4 py-2 font-bold text-sm shadow">
                        عدد التحليلات: <span class="font-extrabold">{{ $analyses->total() }}</span>
                    </span>
                    <a href="{{ route('deepfake.index') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-xl shadow transition text-sm flex items-center gap-3">
                        <i class="fas fa-plus"></i> إضافة تحليل جديد
                    </a>
                </div>
            </div>
            <img src="/images/undraw_artificial_intelligence_re_enpp.svg" alt="AI Dashboard" class="w-32 md:w-40 mx-auto animate-fade-in-up" />
        </div>
    </section>

    <!-- Advanced Search Bar -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-6">
        <div class="bg-white rounded-3xl shadow-lg border border-indigo-100 p-6">
            <h3 class="text-lg font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-search text-indigo-600"></i>
                البحث في التحليلات
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
                                   placeholder="رقم التحليل، اسم الملف..." 
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
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
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
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-list"></i>
                    <span>جميع التحليلات</span>
                </a>
                
                <!-- Reported Analyses -->
                <a href="{{ route('dashboard', ['status' => 'reported']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('status') == 'reported' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-flag"></i>
                    <span>التحليلات المبلغ عنها</span>
                </a>
                
                <!-- Feedback Analyses -->
                <a href="{{ route('dashboard', ['status' => 'feedback']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('status') == 'feedback' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-comments"></i>
                    <span>التحليلات المقيّمة</span>
                </a>
                
                <!-- No Feedback -->
                <a href="{{ route('dashboard', ['status' => 'no_feedback']) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all {{ request('status') == 'no_feedback' ? 'bg-yellow-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-clock"></i>
                    <span>بدون تقييم</span>
                </a>
            </div>
            
            @if(request('status'))
            <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                <div class="flex items-center gap-2 text-indigo-700">
                    <i class="fas fa-info-circle"></i>
                    <span class="text-sm">
                        @switch(request('status'))
                            @case('reported')
                                عرض التحليلات المبلغ عنها فقط
                                @break
                            @case('feedback')
                                عرض التحليلات المقيّمة فقط
                                @break
                            @case('no_feedback')
                                عرض التحليلات بدون تقييم فقط
                                @break
                        @endswitch
                    </span>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Cards Grid -->
    <div class="w-full px-4">
        <div id="analysis-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-7xl mx-auto">
            @forelse($analyses as $analysis)
            <div class="analysis-card">
                <div class="group bg-white rounded-3xl shadow-2xl p-0 relative border border-indigo-100 flex flex-col animate-fade-in-up transition-all duration-300 hover:-translate-y-2 hover:shadow-3xl min-h-[460px]">
                    <!-- نوع الوسيط -->
                    <div class="w-full flex justify-center pt-6">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center shadow-lg border-4 border-white">
                            <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : ($analysis->file_type == 'audio' ? 'music' : 'file')) }} text-4xl text-indigo-500"></i>
                        </div>
                    </div>
                    
                    <!-- اسم الملف -->
                    <div class="px-4 mt-4 mb-2 w-full">
                        <h3 class="font-bold text-lg text-indigo-900 text-center break-words line-clamp-2" title="{{ $analysis->file_name ?? 'بدون اسم' }}">
                            {{ $analysis->file_name ?? 'بدون اسم' }}
                        </h3>
                    </div>
                    
                    <!-- الحالة -->
                    <div class="mb-2 text-center">
                        <span class="inline-block px-3 py-1.5 rounded-full text-sm font-bold shadow-lg border-2
                            {{ $analysis->prediction == 'REAL' ? 'bg-green-100 text-green-700 border-green-300' : 'bg-red-100 text-red-700 border-red-300' }}">
                            <i class="fas fa-{{ $analysis->prediction == 'REAL' ? 'check-circle' : 'exclamation-triangle' }} mr-1"></i>
                            {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                        </span>
                    </div>
                    
                    <!-- التاريخ -->
                    <div class="text-xs text-gray-400 mb-2 flex items-center gap-1 justify-center">
                        <i class="fas fa-clock"></i>
                        {{ \Carbon\Carbon::parse($analysis->created_at)->translatedFormat('d M Y - H:i') }}
                    </div>
                    
                    <!-- الثقة -->
                    <div class="mb-4 w-full px-6">
                        <div class="flex justify-between text-xs mb-1 text-gray-500">
                            <span>نسبة الثقة</span>
                            <span>{{ round($analysis->confidence * 100, 2) }}%</span>
                        </div>
                        <div class="w-full h-3 bg-indigo-100 rounded-full overflow-hidden">
                            <div class="h-3 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-700" style="width: {{ $analysis->confidence * 100 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- أزرار -->
                    <div class="flex justify-center items-center w-full mt-auto gap-3 pb-6 px-4">
                        @if($analysis->report_flag || !is_null($analysis->user_feedback) || (auth()->check() && auth()->id() == $analysis->user_id))
                            <a href="{{ route('analysis.show', $analysis->id) }}" class="bg-blue-100 text-blue-700 font-bold py-2 px-4 rounded-lg shadow hover:bg-blue-200 transition flex items-center gap-2 text-sm flex-1 justify-center max-w-[120px]">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                        @else
                            <span class="bg-gray-100 text-gray-400 font-bold py-2 px-4 rounded-lg shadow flex items-center gap-2 text-sm flex-1 justify-center max-w-[120px] cursor-not-allowed" title="لا يمكنك عرض التفاصيل إلا إذا كان هناك بلاغ أو تقييم">
                                <i class="fas fa-eye-slash"></i> عرض التفاصيل
                            </span>
                        @endif
                        <button onclick="deleteAnalysis({{ $analysis->id }})" class="bg-red-100 text-red-700 font-bold py-2 px-4 rounded-lg shadow hover:bg-red-200 transition flex items-center gap-2 text-sm flex-1 justify-center max-w-[120px]">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center text-gray-400 flex flex-col items-center mt-16 animate-fade-in-up w-full">
                <img src="/images/undraw_artificial_intelligence_re_enpp.svg" alt="No Data" class="w-32 mb-4 opacity-60">
                <div class="text-lg font-bold mb-2">لا يوجد تحليلات بعد.</div>
                <div class="text-sm">ابدأ برفع ملفاتك لتحليلها واكتشاف حقيقتها!</div>
            </div>
            @endforelse
        </div>
    </div><br><br>

    <!-- Custom Pagination -->
    @if($analyses->hasPages())
    <div class="pagination-container w-full flex justify-center">
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
    @endif

</div>
@endsection

@push('styles')
<style>
    .analysis-card {
        min-width: 300px;
        max-width: 350px;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .analysis-card {
            min-width: 280px;
            max-width: 100%;
        }
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        padding-right: 2.5rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    /* حل مشكلة الفراغ في الترقيم */
    #analysis-container {
        min-height: auto;
    }
    
    /* جعل الترقيم يظهر مباشرة بعد البطاقات */
    .pagination-container {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteAnalysis(id) {
        // الحصول على معلومات التحليل من البطاقة
        const analysisCard = document.querySelector(`[onclick="deleteAnalysis(${id})"]`).closest('.analysis-card');
        const fileName = analysisCard.querySelector('h3').textContent.trim();
        
        Swal.fire({
            title: 'تأكيد حذف التحليل',
            html: `هل أنت متأكد من حذف التحليل؟<br><br><strong>اسم الملف:</strong> ${fileName}<br><br><span class="text-red-600 font-bold">تحذير:</span> لا يمكن التراجع عن هذا الإجراء!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'نعم، احذف التحليل',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار loading
                Swal.fire({
                    title: 'جاري الحذف...',
                    text: 'يرجى الانتظار',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // إرسال طلب الحذف
                fetch(`/analysis/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'تم الحذف بنجاح!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#10B981'
                        }).then(() => {
                            // إعادة تحميل الصفحة
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: data.message || 'حدث خطأ أثناء الحذف',
                            icon: 'error',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#EF4444'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء الحذف',
                        icon: 'error',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#EF4444'
                    });
                });
            }
        });
    }

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
    });
</script>
@endpush