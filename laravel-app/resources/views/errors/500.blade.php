@extends('layouts.app')

@section('title', 'خطأ في الخادم - خطأ 500')

@section('content')
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - خطأ في الخادم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Cairo', sans-serif; }
        
        .bounce-animation {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .shake-animation {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .sparkle {
            animation: sparkle 1.5s ease-in-out infinite;
        }
        
        @keyframes sparkle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex items-center justify-center p-4">
    <div class="max-w-4xl mx-auto text-center">
        <!-- Main Error Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl p-8 md:p-12 border border-indigo-100 mb-8">
            <!-- Error Icon -->
            <div class="mb-8">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-orange-100 to-red-100 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                    <i class="fas fa-exclamation-triangle text-6xl text-orange-500 bounce-animation"></i>
                </div>
            </div>
            
            <!-- Error Title -->
            <h1 class="text-4xl md:text-6xl font-extrabold text-orange-600 mb-4 shake-animation">
                500
            </h1>
            
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
                ⚡ خطأ في الخادم ⚡
            </h2>
            
            <!-- Funny Message -->
            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 mb-8 border border-orange-200">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <i class="fas fa-cog text-2xl text-orange-600"></i>
                    <h3 class="text-xl font-bold text-orange-800">عذراً، شيء ما حدث!</h3>
                    <i class="fas fa-cog text-2xl text-orange-600"></i>
                </div>
                <p class="text-lg text-gray-700 mb-4">
                    يبدو أن الخادم يحتاج إلى استراحة قصيرة! ☕
                </p>
                <div class="bg-white rounded-xl p-4 border border-orange-300">
                    <p class="text-gray-600 font-medium">
                        <i class="fas fa-tools text-orange-500 mr-2"></i>
                        نصيحة: جرب مرة أخرى بعد قليل، أو اتصل بفريق الدعم الفني!
                    </p>
                </div>
            </div>
            
            <!-- Funny Quote -->
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6 mb-8 border border-purple-200">
                <div class="flex items-center justify-center gap-2 mb-3">
                    <i class="fas fa-quote-right text-purple-600 text-xl"></i>
                    <span class="text-lg font-semibold text-purple-800">حكمة اليوم</span>
                    <i class="fas fa-quote-left text-purple-600 text-xl"></i>
                </div>
                <p class="text-gray-700 text-lg italic">
                    "حتى أجهزة الكمبيوتر تحتاج إلى استراحة أحياناً! 😴💻"
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="location.reload()" class="inline-flex items-center gap-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-redo"></i>
                    إعادة المحاولة
                </button>
                
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-home"></i>
                    العودة للوحة التحكم
                </a>
                
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right"></i>
                    الصفحة الرئيسية
                </a>
            </div>
        </div>
        
        <!-- Funny Footer -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-6 border border-indigo-100">
            <div class="flex items-center justify-center gap-4 mb-4">
                <i class="fas fa-robot text-2xl text-indigo-600 floating"></i>
                <span class="text-lg font-semibold text-gray-700">رسالة من الذكاء الاصطناعي</span>
                <i class="fas fa-robot text-2xl text-indigo-600 floating"></i>
            </div>
            <p class="text-gray-600">
                "أنا آسف، حتى أنا أحياناً أتعطل! لكن لا تقلق، سأعود قريباً! 🤖🔧"
            </p>
        </div>
        
        <!-- Decorative Elements -->
        <div class="fixed top-10 left-10 text-4xl opacity-20 floating">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="fixed top-20 right-20 text-3xl opacity-20 floating" style="animation-delay: 1s;">
            <i class="fas fa-cog"></i>
        </div>
        <div class="fixed bottom-20 left-20 text-2xl opacity-20 floating" style="animation-delay: 2s;">
            <i class="fas fa-tools"></i>
        </div>
        <div class="fixed bottom-10 right-10 text-3xl opacity-20 floating" style="animation-delay: 0.5s;">
            <i class="fas fa-wrench"></i>
        </div>
    </div>
    
    <script>
        // Add some interactive fun
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to buttons
            const buttons = document.querySelectorAll('a, button');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Add random emoji to the page
            const emojis = ['⚡', '🔧', '🛠️', '💻', '☕', '😴', '🎪', '🎭'];
            const randomEmoji = emojis[Math.floor(Math.random() * emojis.length)];
            
            // Create floating emoji
            const emoji = document.createElement('div');
            emoji.textContent = randomEmoji;
            emoji.style.position = 'fixed';
            emoji.style.top = Math.random() * window.innerHeight + 'px';
            emoji.style.left = Math.random() * window.innerWidth + 'px';
            emoji.style.fontSize = '2rem';
            emoji.style.opacity = '0.1';
            emoji.style.pointerEvents = 'none';
            emoji.style.animation = 'floating 4s ease-in-out infinite';
            emoji.style.animationDelay = Math.random() * 2 + 's';
            document.body.appendChild(emoji);
            
            // Add sparkle effect to error icon
            const errorIcon = document.querySelector('.bounce-animation');
            if (errorIcon) {
                setInterval(() => {
                    errorIcon.classList.add('sparkle');
                    setTimeout(() => {
                        errorIcon.classList.remove('sparkle');
                    }, 1500);
                }, 4000);
            }
        });
    </script>
</body>
</html>
@endsection 