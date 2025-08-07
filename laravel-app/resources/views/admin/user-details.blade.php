@extends('layouts.dashboard')

@section('title', 'تفاصيل المستخدم')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Hero Section -->
    <section class="w-full max-w-7xl mx-auto text-center mb-8 mt-6">
        <div class="bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-user-circle text-indigo-600 text-2xl"></i>
                تفاصيل المستخدم
            </h2>
            <p class="text-gray-600 text-base md:text-lg mb-4">معلومات شاملة عن المستخدم وتحليلاته</p>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-right"></i>
                العودة للوحة المدير
            </a>
        </div>
    </section>

    <!-- User Info Card -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-6">
        <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold">{{ $user->name }}</h3>
                        <p class="text-indigo-100">{{ $user->email }}</p>
                        <p class="text-indigo-100 text-sm">عضو منذ {{ $user->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ $user->analyses->count() }}</div>
                        <div class="text-indigo-100 text-sm">تحليل</div>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- إحصائيات سريعة -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-700">{{ $user->analyses->where('prediction', 'REAL')->count() }}</div>
                                <div class="text-sm text-green-600">تحليل أصيل</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-2xl p-4 border border-red-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-red-700">{{ $user->analyses->where('prediction', 'FAKE')->count() }}</div>
                                <div class="text-sm text-red-600">تحليل مزيف</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-4 border border-blue-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-flag text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-blue-700">{{ $user->analyses->where('report_flag', true)->count() }}</div>
                                <div class="text-sm text-blue-600">تحليل مبلغ عنه</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-4 border border-purple-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-comments text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-700">{{ $user->analyses->whereNotNull('user_feedback')->count() }}</div>
                                <div class="text-sm text-purple-600">تحليل مقيّم</div>
                            </div>
                        </div>
                    </div>



                    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-4 border border-teal-200">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-teal-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-teal-700">{{ $user->analyses->where('created_at', '>=', now()->subDays(7))->count() }} تحليل </div>
                                <div class="text-sm text-teal-600">آخر 7 أيام</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Analyses -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-200">
                <h3 class="text-xl font-bold text-indigo-800 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                    تحليلات المستخدم ({{ $user->analyses->count() }})
                </h3>
            </div>
            
            @if($user->analyses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التحليل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النتيجة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الثقة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم/الإبلاغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->analyses as $analysis)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-white"></i>
                                    </div>
                                    <div class="mr-4 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $analysis->file_name ?? 'بدون اسم' }}">
                                            {{ Str::limit($analysis->file_name ?? 'بدون اسم', 20) }}
                                        </div>
                                        <div class="text-sm text-gray-500 truncate" title="{{ $analysis->file_path ?? '' }}">
                                            {{ Str::limit(basename($analysis->file_path ?? ''), 15) }}
                                        </div>
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
                                    <a href="{{ route('admin.analysis.details', $analysis->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-bar text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-600 mb-2">لا توجد تحليلات</h4>
                <p class="text-gray-500">هذا المستخدم لم يقم بأي تحليل بعد.</p>
            </div>
            @endif
        </div>
    </section>

    <!-- Action Buttons -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 p-6">
            <h3 class="text-xl font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-cogs text-indigo-600"></i>
                إجراءات المستخدم
            </h3>
            
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة لقائمة المستخدمين
                </a>
                
                @if($user->id !== auth()->id())
                    <!-- Role Update Form -->
                    <form method="POST" action="{{ route('admin.user.update-role', $user->id) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <select name="role" onchange="this.form.submit()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition border-0 cursor-pointer {{ $user->role == 'admin' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>تغيير إلى مستخدم</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>تغيير إلى مدير</option>
                        </select>
                    </form>
                    
                    <!-- Delete User Button -->
                    <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition">
                        <i class="fas fa-trash"></i>
                        حذف المستخدم
                    </button>
                @else
                    <span class="inline-flex items-center gap-2 bg-gray-400 text-white px-6 py-3 rounded-lg cursor-not-allowed">
                        <i class="fas fa-user-shield"></i>
                        لا يمكن حذف حسابك
                    </span>
                @endif
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteUser(id, userName) {
    Swal.fire({
        title: 'تأكيد حذف المستخدم',
        html: `هل أنت متأكد من حذف المستخدم <strong>${userName}</strong>؟<br><br><span class="text-red-600 font-bold">تحذير:</span> سيتم حذف جميع تحليلاته أيضاً ولا يمكن التراجع عن هذا الإجراء!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'نعم، احذف المستخدم',
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
            fetch(`/admin/user/${id}`, {
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
                        // العودة لقائمة المستخدمين
                        window.location.href = '{{ route("admin.users") }}';
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