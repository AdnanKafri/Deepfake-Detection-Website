@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-md w-full space-y-8">
        <!-- Header Section -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-full flex items-center justify-center mb-6 shadow-lg">
                <i class="fas fa-lock-open text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">إعادة تعيين كلمة المرور</h2>
            <p class="text-gray-600 text-lg">أدخل كلمة المرور الجديدة</p>
        </div>

        <!-- Reset Password Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-indigo-100 p-8">
            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-envelope text-teal-600 ml-2"></i>
                        البريد الإلكتروني
                    </label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                            placeholder="أدخل بريدك الإلكتروني">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                    </div>
                    @error('email')
                        <div class="flex items-center text-red-600 text-sm mt-1">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-lock text-teal-600 ml-2"></i>
                        كلمة المرور الجديدة
                    </label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                            placeholder="أدخل كلمة المرور الجديدة">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                    </div>
                    @error('password')
                        <div class="flex items-center text-red-600 text-sm mt-1">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-shield-alt text-teal-600 ml-2"></i>
                        تأكيد كلمة المرور
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                            placeholder="أعد إدخال كلمة المرور الجديدة">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-shield-alt text-gray-400"></i>
                        </div>
                    </div>
                    @error('password_confirmation')
                        <div class="flex items-center text-red-600 text-sm mt-1">
                            <i class="fas fa-exclamation-circle ml-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Requirements -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 ml-2"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">متطلبات كلمة المرور:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>8 أحرف على الأقل</li>
                                <li>يجب أن تحتوي على حروف وأرقام</li>
                                <li>يُفضل استخدام رموز خاصة</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    <i class="fas fa-key ml-2"></i>
                    إعادة تعيين كلمة المرور
                </button>

                <!-- Back to Login -->
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-gray-600 text-sm">
                        <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-800 font-semibold transition-colors">
                            <i class="fas fa-arrow-right ml-1"></i>
                            العودة لتسجيل الدخول
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-gray-500 text-sm">
                <i class="fas fa-shield-alt text-teal-600 ml-1"></i>
                منصة كشف التزييف - حماية المحتوى الرقمي
            </p>
        </div>
    </div>
</div>

<style>
/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white\/90 {
    animation: fadeInUp 0.6s ease-out;
}

/* Input focus effects */
input:focus {
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
}

/* Button hover effects */
button:hover {
    box-shadow: 0 10px 25px rgba(20, 184, 166, 0.3);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .max-w-md {
        max-width: 100%;
        margin: 0 1rem;
    }
    
    .bg-white\/90 {
        padding: 1.5rem;
    }
}
</style>
@endsection
