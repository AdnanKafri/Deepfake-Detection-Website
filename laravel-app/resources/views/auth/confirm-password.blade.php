@extends('layouts.app')

@section('title', 'تأكيد كلمة المرور')

@section('content')
<div class="min-h-[90vh] bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" dir="rtl">
    <div class="max-w-md w-full space-y-8">
        <!-- Header Section -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center mb-6 shadow-lg">
                <i class="fas fa-user-shield text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">تأكيد كلمة المرور</h2>
            <p class="text-gray-600 text-lg">هذه منطقة آمنة، يرجى تأكيد كلمة المرور للمتابعة</p>
        </div>

        <!-- Confirm Password Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-indigo-100 p-8">
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-amber-600 mt-1 ml-2"></i>
                    <div class="text-sm text-amber-800">
                        <p>هذه منطقة آمنة من التطبيق. يرجى تأكيد كلمة المرور قبل المتابعة.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
                @csrf

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        <i class="fas fa-lock text-amber-600 ml-2"></i>
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
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

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                    <i class="fas fa-check ml-2"></i>
                    تأكيد كلمة المرور
                </button>

                <!-- Back to Dashboard -->
                <div class="text-center pt-4 border-t border-gray-200">
                    <p class="text-gray-600 text-sm">
                        <a href="{{ route('dashboard') }}" class="text-amber-600 hover:text-amber-800 font-semibold transition-colors">
                            <i class="fas fa-arrow-right ml-1"></i>
                            العودة للوحة التحكم
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-gray-500 text-sm">
                <i class="fas fa-shield-alt text-amber-600 ml-1"></i>
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
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

/* Button hover effects */
button:hover {
    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
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