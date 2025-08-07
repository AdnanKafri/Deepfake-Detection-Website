@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المدير')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Hero Section -->
    <section class="w-full max-w-7xl mx-auto text-center mb-8 mt-6 animate-fade-in">
        <div class="bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-crown text-yellow-500 text-2xl"></i>
                لوحة تحكم المدير
            </h2>
            <p class="text-gray-600 text-base md:text-lg mb-6">مرحباً بك في لوحة التحكم الإدارية - إدارة شاملة للموقع</p>
        </div>
    </section>

    <!-- Statistics Cards -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-indigo-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي المستخدمين</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_users'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600">
                        <i class="fas fa-arrow-up"></i>
                        +{{ $stats['today_users'] }} اليوم
                    </span>
                </div>
            </div>

            <!-- Total Analyses -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي التحليلات</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_analyses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-green-600">
                        <i class="fas fa-arrow-up"></i>
                        +{{ $stats['today_analyses'] }} اليوم
                    </span>
                </div>
            </div>


            <!-- This Month -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-sky-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">هذا الشهر</p>
                        <p class="text-3xl font-bold text-sky-600">{{ $stats['month_analyses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-sky-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-600">
                        {{ $stats['month_users'] }} مستخدم جديد
                    </span>
                </div>
            </div>

            <!-- Reports & Feedback -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-orange-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">التقارير والتقييمات</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-2xl font-bold text-red-600">{{ $stats['reported_analyses'] }}</span>
                            <span class="text-gray-400">/</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $stats['total_feedback'] }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-flag text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-xs text-red-600 font-medium">إبلاغات</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-xs text-blue-600 font-medium">تقييمات</span>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-sm text-gray-600">
                        صحيح: {{ $stats['correct_feedback'] }} | خطأ: {{ $stats['incorrect_feedback'] }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Section -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Analyses by Type -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-indigo-100">
                <h3 class="text-xl font-bold text-indigo-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-indigo-600"></i>
                    التحليلات حسب النوع
                </h3>
                <div class="space-y-3">
                    @foreach($analysesByType as $type)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-{{ $type->file_type == 'image' ? 'image' : ($type->file_type == 'video' ? 'video' : 'music') }} text-indigo-600"></i>
                            <span class="font-medium">{{ $type->file_type == 'image' ? 'صور' : ($type->file_type == 'video' ? 'فيديو' : 'صوت') }}</span>
                        </div>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold">{{ $type->count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Analyses by Status -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-purple-100">
                <h3 class="text-xl font-bold text-purple-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                    التحليلات حسب النتيجة
                </h3>
                <div class="space-y-3">
                    @foreach($analysesByStatus as $status)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-{{ $status->prediction == 'REAL' ? 'check-circle' : 'exclamation-triangle' }} text-{{ $status->prediction == 'REAL' ? 'green' : 'red' }}-600"></i>
                            <span class="font-medium">{{ $status->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}</span>
                        </div>
                        <span class="bg-{{ $status->prediction == 'REAL' ? 'green' : 'red' }}-100 text-{{ $status->prediction == 'REAL' ? 'green' : 'red' }}-700 px-3 py-1 rounded-full font-bold">{{ $status->count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Activities -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Analyses -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-indigo-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-indigo-800 flex items-center gap-2">
                        <i class="fas fa-clock text-indigo-600"></i>
                        آخر التحليلات
                    </h3>
                    <a href="{{ route('admin.analyses') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">عرض الكل</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentAnalyses as $analysis)
                    @if($analysis->report_flag || $analysis->user_feedback)
                    <a href="{{ route('analysis.show', $analysis->id) }}" class="block">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ Str::limit($analysis->file_name ?? 'بدون اسم', 20) }}</p>
                                    <p class="text-xs text-gray-500">{{ $analysis->user->name ?? 'مستخدم غير معروف' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-bold {{ $analysis->prediction == 'REAL' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $analysis->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg opacity-60 cursor-not-allowed" title="لا تفاصيل متاحة">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-indigo-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ Str::limit($analysis->file_name ?? 'بدون اسم', 20) }}</p>
                                <p class="text-xs text-gray-500">{{ $analysis->user->name ?? 'مستخدم غير معروف' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-bold {{ $analysis->prediction == 'REAL' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $analysis->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endif
                    @empty
                    <p class="text-gray-500 text-center py-4">لا توجد تحليلات حديثة</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-purple-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-purple-800 flex items-center gap-2">
                        <i class="fas fa-user-plus text-purple-600"></i>
                        آخر المستخدمين
                    </h3>
                    <a href="{{ route('admin.users') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">عرض الكل</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex justify-center">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-bold {{ $user->role == 'admin' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->role == 'admin' ? 'مدير' : 'مستخدم' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">لا يوجد مستخدمين جدد</p>
                    @endforelse
                </div>
            </div>

            <!-- Reported Analyses -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-orange-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-orange-800 flex items-center gap-2">
                        <i class="fas fa-flag text-orange-600"></i>
                        التحليلات المبلغ عنها
                    </h3>
                    <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-bold">{{ $reportedAnalyses->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($reportedAnalyses as $analysis)
                    <a href="{{ route('analysis.show', $analysis->id) }}" class="block">
                        <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 transition-colors cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-orange-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-sm">{{ Str::limit($analysis->file_name ?? 'بدون اسم', 20) }}</p>
                                    <p class="text-xs text-gray-500">{{ $analysis->user->name ?? 'مستخدم غير معروف' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                                    <i class="fas fa-flag mr-1"></i>مبلغ عنه
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $analysis->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="text-gray-500 text-center py-4">لا توجد تحليلات مبلغ عنها</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Feedback Section -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-blue-800 flex items-center gap-2">
                    <i class="fas fa-star text-blue-600"></i>
                    التقييمات
                </h3>
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-bold">{{ $feedbackAnalyses->count() }}</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($feedbackAnalyses as $analysis)
                <a href="{{ route('analysis.show', $analysis->id) }}" class="block">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-{{ $analysis->file_type == 'image' ? 'image' : ($analysis->file_type == 'video' ? 'video' : 'music') }} text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-sm">{{ Str::limit($analysis->file_name ?? 'بدون اسم', 20) }}</p>
                                <p class="text-xs text-gray-500">{{ $analysis->user->name ?? 'مستخدم غير معروف' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-bold {{ $analysis->prediction == 'REAL' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $analysis->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full text-center text-gray-500 py-8">
                    لا توجد تقييمات
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-indigo-100">
            <h3 class="text-xl font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-indigo-600"></i>
                إجراءات سريعة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-indigo-800">إدارة المستخدمين</p>
                        <p class="text-sm text-indigo-600">عرض وتعديل حسابات المستخدمين</p>
                    </div>
                </a>
                
                <a href="{{ route('admin.analyses') }}" class="flex items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-purple-800">إدارة التحليلات</p>
                        <p class="text-sm text-purple-600">عرض وحذف التحليلات</p>
                    </div>
                </a>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-all duration-300 group">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-home text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-green-800">العودة للوحة التحكم</p>
                        <p class="text-sm text-green-600">العودة للوحة التحكم العادية</p>
                    </div>
                </a>
            </div>
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
});
</script>
@endsection 