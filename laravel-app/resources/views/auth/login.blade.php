@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-md w-full space-y-8">
        <!-- Header Section -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mb-6 shadow-lg">
                <i class="fas fa-shield-alt text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">مرحباً بك مجدداً</h2>
            <p class="text-gray-600 text-lg">سجل دخولك للوصول إلى لوحة التحكم</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-indigo-100 p-8">
            @if(session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 ml-2"></i>
                        <span class="text-green-800 font-medium">{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-envelope text-indigo-600 ml-2"></i>
                        البريد الإلكتروني
                    </label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
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
                        <i class="fas fa-lock text-indigo-600 ml-2"></i>
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                            placeholder="أدخل كلمة المرور">
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

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 ml-2">
                        <span class="text-sm text-gray-600">تذكرني</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                            نسيت كلمة المرور؟
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-sign-in-alt ml-2"></i>
                    تسجيل الدخول
                </button>

                <!-- Register Link -->
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-gray-600 text-sm">
                        ليس لديك حساب؟ 
                        <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                            سجل الآن
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-gray-500 text-sm">
                <i class="fas fa-shield-alt text-indigo-600 ml-1"></i>
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
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Button hover effects */
button:hover {
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
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
