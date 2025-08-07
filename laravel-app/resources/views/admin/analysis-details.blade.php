@extends('layouts.dashboard')

@section('title', 'تفاصيل التحليل')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Hero Section -->
    <section class="w-full max-w-7xl mx-auto text-center mb-8 mt-6">
        <div class="bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-eye text-indigo-600 text-2xl"></i>
                تفاصيل التحليل
            </h2>
            <p class="text-gray-600 text-base md:text-lg mb-4">معلومات شاملة عن التحليل ونتائجه</p>
            <a href="{{ route('admin.analyses') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right"></i>
                العودة لقائمة التحليلات
            </a>
        </div>
    </section>

    <!-- Analysis Details Card -->
    <section class="w-full max-w-4xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">تحليل #{{ $analysis->id }}</h3>
                            <p class="text-indigo-100">{{ $analysis->file_name ?? 'بدون اسم' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ round($analysis->confidence * 100, 1) }}%</div>
                        <div class="text-indigo-100 text-sm">نسبة الثقة</div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Result Status -->
                <div class="mb-6">
                    <div class="flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-24 h-24 rounded-full flex items-center justify-center mb-4 {{ $analysis->prediction == 'REAL' ? 'bg-green-100' : 'bg-red-100' }}">
                                <i class="fas fa-{{ $analysis->prediction == 'REAL' ? 'check-circle' : 'exclamation-triangle' }} text-4xl {{ $analysis->prediction == 'REAL' ? 'text-green-600' : 'text-red-600' }}"></i>
                            </div>
                            <h4 class="text-2xl font-bold {{ $analysis->prediction == 'REAL' ? 'text-green-700' : 'text-red-700' }}">
                                {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                            </h4>
                            <p class="text-gray-600">نتيجة التحليل</p>
                        </div>
                    </div>
                </div>

                <!-- Confidence Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">نسبة الثقة</span>
                        <span class="font-semibold text-gray-900">{{ round($analysis->confidence * 100, 1) }}%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-4 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 transition-all duration-1000" style="width: {{ $analysis->confidence * 100 }}%"></div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- File Information -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-4 border border-blue-200">
                        <h5 class="font-bold text-blue-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-600"></i>
                            معلومات الملف
                        </h5>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-blue-700">نوع الملف:</span>
                                <span class="font-semibold text-blue-900">{{ $analysis->file_type == 'image' ? 'صورة' : ($analysis->file_type == 'video' ? 'فيديو' : 'صوت') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">اسم الملف:</span>
                                <span class="font-semibold text-blue-900 truncate" title="{{ $analysis->file_name ?? 'بدون اسم' }}">{{ Str::limit($analysis->file_name ?? 'بدون اسم', 20) }}</span>
                            </div>

                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-200">
                        <h5 class="font-bold text-green-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-user text-green-600"></i>
                            معلومات المستخدم
                        </h5>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-green-700">الاسم:</span>
                                <span class="font-semibold text-green-900">
                                    @if($analysis->user)
                                        <a href="{{ route('admin.user.details', $analysis->user->id) }}" class="hover:text-green-700 underline">
                                            {{ $analysis->user->name ?? 'مستخدم غير معروف' }}
                                        </a>
                                    @else
                                        مستخدم غير معروف
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-green-700">البريد الإلكتروني:</span>
                                <span class="font-semibold text-green-900 truncate" title="{{ $analysis->user->email ?? '' }}">{{ Str::limit($analysis->user->email ?? '', 20) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-green-700">تاريخ التسجيل:</span>
                                <span class="font-semibold text-green-900">{{ $analysis->user->created_at->format('d/m/Y') ?? '' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Information -->
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-4 border border-purple-200">
                        <h5 class="font-bold text-purple-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            معلومات التحليل
                        </h5>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-purple-700">تاريخ التحليل:</span>
                                <span class="font-semibold text-purple-900">{{ $analysis->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-purple-700">وقت التحليل:</span>
                                <span class="font-semibold text-purple-900">{{ $analysis->created_at->format('H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-purple-700">آخر تحديث:</span>
                                <span class="font-semibold text-purple-900">{{ $analysis->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-4 border border-orange-200">
                        <h5 class="font-bold text-orange-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-orange-600"></i>
                            الحالة والتقييم
                        </h5>
                        <div class="space-y-3">
                            @if($analysis->report_flag)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-flag"></i>
                                    تم الإبلاغ
                                </span>
                            </div>
                            @endif
                            @if($analysis->user_feedback)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold rounded-full {{ $analysis->user_feedback == 'CORRECT' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    <i class="fas fa-{{ $analysis->user_feedback == 'CORRECT' ? 'check' : 'times' }}"></i>
                                    {{ $analysis->user_feedback == 'CORRECT' ? 'تقييم صحيح' : 'تقييم خاطئ' }}
                                </span>
                            </div>
                            @endif
                            @if(!$analysis->report_flag && !$analysis->user_feedback)
                            <div class="text-sm text-gray-500">لا يوجد بلاغ أو تقييم</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('admin.analyses') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-arrow-right"></i>
                        العودة للقائمة
                    </a>
                    @if($analysis->user)
                    <a href="{{ route('admin.user.details', $analysis->user->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-user"></i>
                        عرض تفاصيل المستخدم
                    </a>
                    @endif
                    <button onclick="deleteAnalysis({{ $analysis->id }})" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                        حذف التحليل
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteAnalysis(id) {
    // الحصول على معلومات التحليل من الصفحة
    const analysisId = {{ $analysis->id }};
    const fileName = '{{ $analysis->file_name ?? 'بدون اسم' }}';
    const fileType = '{{ $analysis->file_type == 'image' ? 'صورة' : ($analysis->file_type == 'video' ? 'فيديو' : 'صوت') }}';
    const prediction = '{{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}';
    
    Swal.fire({
        title: 'تأكيد حذف التحليل',
        html: `هل أنت متأكد من حذف التحليل؟<br><br><strong>رقم التحليل:</strong> ${analysisId}<br><strong>اسم الملف:</strong> ${fileName}<br><strong>نوع الملف:</strong> ${fileType}<br><strong>النتيجة:</strong> ${prediction}<br><br><span class="text-red-600 font-bold">تحذير:</span> لا يمكن التراجع عن هذا الإجراء!`,
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
            fetch(`/admin/analyses/${id}`, {
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
                        // العودة لقائمة التحليلات
                        window.location.href = '{{ route("admin.analyses") }}';
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
@endsection 