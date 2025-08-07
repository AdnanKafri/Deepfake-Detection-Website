@extends('layouts.dashboard')

@section('title', 'الملف الشخصي')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col" dir="rtl">
    <!-- Hero Section -->
    <section class="w-full max-w-7xl mx-auto text-center mb-8 mt-6">
        <div class="bg-white/90 rounded-3xl shadow-xl p-6 md:p-10 border border-indigo-100">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-800 mb-2 flex items-center justify-center gap-3">
                <i class="fas fa-user-circle text-indigo-600 text-2xl"></i>
                الملف الشخصي
            </h2>
            <p class="text-gray-600 text-base md:text-lg mb-4">إدارة معلومات حسابك الشخصي</p>
        </div>
    </section>

    <!-- Profile Content -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
                    <!-- Profile Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white text-center">
                        <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-user text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">{{ $user->name }}</h3>
                        <p class="text-indigo-100">{{ $user->email }}</p>
                        <div class="mt-3">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold {{ $user->role == 'admin' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role == 'admin' ? 'مدير' : 'مستخدم' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Profile Stats -->
                    <div class="p-6">
                        <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-indigo-600"></i>
                            إحصائيات الحساب
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-indigo-50 rounded-lg">
                                <span class="text-gray-700">عدد التحليلات</span>
                                <span class="font-bold text-indigo-600">{{ $user->analyses->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-gray-700">التحليلات الأصيلة</span>
                                <span class="font-bold text-green-600">{{ $user->analyses->where('prediction', 'REAL')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <span class="text-gray-700">التحليلات المزيفة</span>
                                <span class="font-bold text-red-600">{{ $user->analyses->where('prediction', 'FAKE')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-gray-700">التحليلات المبلغ عنها</span>
                                <span class="font-bold text-blue-600">{{ $user->analyses->where('report_flag', true)->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-gray-700">التحليلات المقيّمة</span>
                                <span class="font-bold text-purple-600">{{ $user->analyses->whereNotNull('user_feedback')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-teal-50 rounded-lg">
                                <span class="text-gray-700">تاريخ التسجيل</span>
                                <span class="font-bold text-teal-600">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Forms -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Update Profile Information -->
                <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-200">
                        <h3 class="text-xl font-bold text-indigo-800 flex items-center gap-2">
                            <i class="fas fa-user-edit text-indigo-600"></i>
                            معلومات الملف الشخصي
                        </h3>
                        <p class="text-gray-600 mt-1">تحديث معلومات حسابك الشخصي وعنوان البريد الإلكتروني</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                           class="w-full px-4 py-3 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" 
                                           required autocomplete="name">
                                    @error('name')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           class="w-full px-4 py-3 border border-indigo-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" 
                                           required autocomplete="username">
                                    @error('email')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                        <span class="font-medium text-yellow-800">البريد الإلكتروني غير مؤكد</span>
                                    </div>
                                    <p class="text-yellow-700 text-sm mb-3">
                                        بريدك الإلكتروني غير مؤكد. يرجى التحقق من بريدك الإلكتروني أو إعادة إرسال رابط التحقق.
                                    </p>
                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                                            <i class="fas fa-paper-plane"></i>
                                            إعادة إرسال رابط التحقق
                                        </button>
                                    </form>
                                    
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="text-green-600 text-sm mt-2">
                                            <i class="fas fa-check-circle"></i>
                                            تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني.
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-save"></i>
                                    حفظ التغييرات
                                </button>

                                @if (session('status') === 'profile-updated')
                                    <div class="flex items-center gap-2 text-green-600">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="font-medium">تم الحفظ بنجاح!</span>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 border-b border-green-200">
                        <h3 class="text-xl font-bold text-green-800 flex items-center gap-2">
                            <i class="fas fa-lock text-green-600"></i>
                            تغيير كلمة المرور
                        </h3>
                        <p class="text-gray-600 mt-1">تحديث كلمة المرور لحسابك</p>
                    </div>
                    
                    <div class="p-6">
                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('put')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الحالية</label>
                                    <input type="password" id="current_password" name="current_password" 
                                           class="w-full px-4 py-3 border border-green-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                           required autocomplete="current-password">
                                    @error('current_password')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة</label>
                                    <input type="password" id="password" name="password" 
                                           class="w-full px-4 py-3 border border-green-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                           required autocomplete="new-password">
                                    @error('password')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور الجديدة</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="w-full px-4 py-3 border border-green-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" 
                                       required autocomplete="new-password">
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-key"></i>
                                    تحديث كلمة المرور
                                </button>

                                @if (session('status') === 'password-updated')
                                    <div class="flex items-center gap-2 text-green-600">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="font-medium">تم تحديث كلمة المرور بنجاح!</span>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-3xl shadow-xl border border-red-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 p-6 border-b border-red-200">
                        <h3 class="text-xl font-bold text-red-800 flex items-center gap-2">
                            <i class="fas fa-trash-alt text-red-600"></i>
                            حذف الحساب
                        </h3>
                        <p class="text-gray-600 mt-1">حذف حسابك نهائياً (لا يمكن التراجع عن هذا الإجراء)</p>
                    </div>
                    
                    <div class="p-6">
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                <span class="font-medium text-red-800">تحذير مهم!</span>
                            </div>
                            <p class="text-red-700 text-sm">
                                بمجرد حذف حسابك، سيتم حذف جميع بياناتك نهائياً. لا يمكن استرداد هذه البيانات لاحقاً.
                            </p>
                        </div>

                        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
                            @csrf
                            @method('delete')

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور للتأكيد</label>
                                <input type="password" id="password" name="password" 
                                       class="w-full px-4 py-3 border border-red-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" 
                                       required>
                                @error('password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-trash"></i>
                                حذف الحساب نهائياً
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="w-full max-w-7xl mx-auto px-4 mb-8">
        <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-200">
                <h3 class="text-xl font-bold text-indigo-800 flex items-center gap-2">
                    <i class="fas fa-bolt text-indigo-600"></i>
                    إجراءات سريعة
                </h3>
                <p class="text-gray-600 mt-1">الوصول السريع للوظائف المهمة</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $user->isAdmin() ? '4' : '3' }} gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-all duration-300 group">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-home text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-indigo-800">لوحة التحكم</p>
                            <p class="text-sm text-indigo-600">عرض تحليلاتك</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('deepfake.index') }}" class="flex items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-all duration-300 group">
                        <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-green-800">تحليل جديد</p>
                            <p class="text-sm text-green-600">إضافة تحليل جديد</p>
                        </div>
                    </a>
                    
                    @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 p-4 bg-yellow-50 hover:bg-yellow-100 rounded-xl transition-all duration-300 group">
                        <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-crown text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-yellow-800">لوحة المدير</p>
                            <p class="text-sm text-yellow-600">إدارة الموقع</p>
                        </div>
                    </a>
                    @endif
                    
                    <a href="{{ url('/') }}" class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-all duration-300 group">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-arrow-right text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-blue-800">الصفحة الرئيسية</p>
                            <p class="text-sm text-blue-600">العودة للرئيسية</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete account confirmation
    const deleteForm = document.querySelector('form[action*="profile.destroy"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'تأكيد حذف الحساب',
                text: 'هل أنت متأكد من حذف حسابك نهائياً؟ لا يمكن التراجع عن هذا الإجراء!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف الحساب',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    }

    // Show success messages
    @if(session('status') === 'profile-updated')
        Swal.fire({
            title: 'تم بنجاح!',
            text: 'تم تحديث معلومات الملف الشخصي بنجاح',
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#10B981'
        });
    @endif

    @if(session('status') === 'password-updated')
        Swal.fire({
            title: 'تم بنجاح!',
            text: 'تم تحديث كلمة المرور بنجاح',
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#10B981'
        });
    @endif
});
</script>
@endsection
