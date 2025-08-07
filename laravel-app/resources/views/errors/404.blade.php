@extends('layouts.app')

@section('title', 'الصفحة غير موجودة - خطأ 404')

@section('content')
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - الصفحة غير موجودة</title>
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
        
        .search-animation {
            animation: search 2s ease-in-out infinite;
        }
        
        @keyframes search {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex items-center justify-center p-4">
    <div class="max-w-4xl mx-auto text-center">
        <!-- Main Error Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl p-8 md:p-12 border border-indigo-100 mb-8">
            <!-- Error Icon -->
            <div class="mb-8">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                    <i class="fas fa-search text-6xl text-blue-500 search-animation"></i>
                </div>
            </div>
            
            <!-- Error Title -->
            <h1 class="text-4xl md:text-6xl font-extrabold text-blue-600 mb-4 shake-animation">
                404
            </h1>
            
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
                🔍 الصفحة غير موجودة 🔍
            </h2>
            
            <!-- Funny Message -->
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 mb-8 border border-blue-200">
                <div class="flex items-center justify-center gap-3 mb-4">
                    <i class="fas fa-map-marker-alt text-2xl text-blue-600"></i>
                    <h3 class="text-xl font-bold text-blue-800">أين أنت ذاهب؟</h3>
                    <i class="fas fa-map-marker-alt text-2xl text-blue-600"></i>
                </div>
                <p class="text-lg text-gray-700 mb-4">
                    يبدو أنك تائه في عالم الإنترنت! 🗺️
                </p>
                <div class="bg-white rounded-xl p-4 border border-blue-300">
                    <p class="text-gray-600 font-medium">
                        <i class="fas fa-compass text-blue-500 mr-2"></i>
                        نصيحة: تحقق من الرابط أو استخدم البوصلة للعودة للمسار الصحيح!
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
                    "ليس كل ما يلمع ذهباً، وليس كل رابط يؤدي لوجهة! 🌟"
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-home"></i>
                    العودة للوحة التحكم
                </a>
                
                <a href="{{ route('deepfake.index') }}" class="inline-flex items-center gap-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-plus"></i>
                    تحليل جديد
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
                "حتى أنا أحياناً أتوه في الإنترنت! لكن لا تقلق، سأساعدك في العودة للمسار الصحيح! 🤖🧭"
            </p>
        </div>
        
        <!-- Decorative Elements -->
        <div class="fixed top-10 left-10 text-4xl opacity-20 floating">
            <i class="fas fa-search"></i>
        </div>
        <div class="fixed top-20 right-20 text-3xl opacity-20 floating" style="animation-delay: 1s;">
            <i class="fas fa-map"></i>
        </div>
        <div class="fixed bottom-20 left-20 text-2xl opacity-20 floating" style="animation-delay: 2s;">
            <i class="fas fa-compass"></i>
        </div>
        <div class="fixed bottom-10 right-10 text-3xl opacity-20 floating" style="animation-delay: 0.5s;">
            <i class="fas fa-question-circle"></i>
        </div>
    </div>
    
    <script>
        // Add some interactive fun
        document.addEventListener('DOMContentLoaded', function() {
            // Add click effect to buttons
            const buttons = document.querySelectorAll('a');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Add random emoji to the page
            const emojis = ['🔍', '🗺️', '🧭', '📍', '🎯', '🎪', '🎭', '🌟'];
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
            
            // Add search effect
            const searchIcon = document.querySelector('.search-animation');
            if (searchIcon) {
                setInterval(() => {
                    searchIcon.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        searchIcon.style.transform = 'scale(1)';
                    }, 200);
                }, 3000);
            }
        });
    </script>
</body>
</html>
@endsection 