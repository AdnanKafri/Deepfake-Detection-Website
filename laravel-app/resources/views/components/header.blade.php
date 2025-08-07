<style>
body.modal-open .header-fixed.header-container {
    opacity: 0.7 !important;
    backdrop-filter: blur(6px) !important;
    z-index: 1000 !important;
}

/* Status Bar Custom Styles */
#statusBar {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

#statusBar .animate-pulse {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .5;
    }
}

/* Hover effects for status bar buttons */
#statusBar a:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Responsive improvements */
@media (max-width: 640px) {
    #statusBar .flex-col {
        gap: 0.5rem;
    }
    
    #statusBar .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }
}

/* Smooth transitions */
#statusBar * {
    transition: all 0.3s ease;
}

/* Glass morphism effect */
#statusBar {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 50%, rgba(14, 165, 233, 0.1) 100%);
    border: 1px solid rgba(99, 102, 241, 0.2);
}

/* Floating animation for background elements */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

#statusBar .absolute {
    animation: float 6s ease-in-out infinite;
}

#statusBar .absolute:nth-child(2) {
    animation-delay: 2s;
}

#statusBar .absolute:nth-child(3) {
    animation-delay: 4s;
}

/* Enhanced button styles */
#statusBar a {
    position: relative;
    overflow: hidden;
}

#statusBar a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

#statusBar a:hover::before {
    left: 100%;
}

/* Icon animation */
#statusBar .fa-rocket {
    animation: rocket-bounce 2s ease-in-out infinite;
}

@keyframes rocket-bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-3px);
    }
}

/* Close button enhancement */
#statusBar button:hover {
    background: rgba(99, 102, 241, 0.1) !important;
    transform: rotate(90deg) scale(1.1);
}
</style>

<header class="header-fixed header-container" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; z-index: 9999 !important; background: white !important; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important; border-bottom: 1px solid #e5e7eb !important; width: 100vw !important; min-width: 100vw !important; max-width: 100vw !important; margin: 0 !important; padding: 0 !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('deepfake.index') }}" class="flex items-center space-x-3 space-x-reverse group">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-105">
                        <i class="fas fa-shield-alt text-white text-lg"></i>
                    </div>
                    <div class="block">
                        <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">DeepGuard</h1>
                        <p class="text-xs text-gray-500 -mt-1 hidden sm:block">كشف التزييف الذكي</p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-8 space-x-reverse">
                <a href="{{ route('deepfake.index') }}" class="text-gray-600 hover:text-indigo-600 transition-all duration-300 text-sm font-semibold flex items-center group">
                    <div class="w-8 h-8 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-3 transition-all duration-300">
                        <i class="fas fa-plus text-indigo-600 text-sm"></i>
                    </div>
                    &nbsp;تحليل جديد
                </a>
                <!-- زر فصل الصوت -->
                <a href="{{ route('audio.extract.index') }}" class="text-gray-600 hover:text-emerald-600 transition-all duration-300 text-sm font-semibold flex items-center group">
                    <div class="w-8 h-8 bg-emerald-50 group-hover:bg-emerald-100 rounded-lg flex items-center justify-center mr-3 transition-all duration-300">
                        <i class="fas fa-volume-up text-emerald-600 text-sm"></i>
                    </div>
                    &nbsp;فصل الصوت عن الفيديو
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600 transition-all duration-300 text-sm font-semibold flex items-center group">
                        <div class="w-8 h-8 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-3 transition-all duration-300">
                            <i class="fas fa-chart-bar text-indigo-600 text-sm"></i>
                        </div>
                        &nbsp;لوحة التحكم
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-yellow-600 hover:text-yellow-700 transition-all duration-300 text-sm font-semibold flex items-center group">
                            <div class="w-8 h-8 bg-yellow-50 group-hover:bg-yellow-100 rounded-lg flex items-center justify-center mr-3 transition-all duration-300">
                                <i class="fas fa-crown text-yellow-600 text-sm"></i>
                            </div>
                            لوحة المدير
                        </a>
                    @endif
                @endauth
            </nav>

            <!-- User Section - Hidden on Mobile -->
            <div class="hidden lg:flex items-center space-x-4 space-x-reverse">
                @auth
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ userDropdownOpen: false }">
                        <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center space-x-3 space-x-reverse p-2 rounded-xl hover:bg-gray-50 transition-all duration-300 group">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">مستخدم مسجل</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" :class="{ 'rotate-180': userDropdownOpen }"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="userDropdownOpen" @click.away="userDropdownOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50" style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-300">
                                <i class="fas fa-user-edit mr-3 text-gray-400"></i>
                                <span class="text-sm font-medium">الملف الشخصي</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-red-600 hover:bg-red-50 transition-all duration-300">
                                    <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                    <span class="text-sm font-medium">تسجيل الخروج</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest Links -->
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 transition-all duration-300 text-sm font-semibold flex items-center group">
                        <div class="w-8 h-8 bg-gray-50 group-hover:bg-indigo-50 rounded-lg flex items-center justify-center mr-3 transition-all duration-300">
                            <i class="fas fa-sign-in-alt text-gray-600 group-hover:text-indigo-600 text-sm"></i>
                        </div>
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-xl transition-all duration-300 text-sm font-semibold flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-user-plus mr-3"></i>
                        إنشاء حساب
                    </a>
                @endauth
            </div>

            <!-- Hamburger Button (Mobile) -->
            <button @click="mobileMenuOpen = true" class="lg:hidden flex items-center p-2 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-600 hover:text-indigo-600 transition-all duration-300">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 flex lg:hidden z-50" style="display: none;">
            <!-- خلفية متلاشية -->
            <div x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" @click="mobileMenuOpen = false" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            
            <!-- القائمة الجانبية -->
            <div x-cloak class="relative bg-white w-80 max-w-full h-full shadow-2xl flex flex-col z-50 transform transition-all duration-300"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full">
                
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-800 mr-3">DeepGuard</span>
                    </div>
                    <button class="w-10 h-10 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl flex items-center justify-center transition-all duration-300" @click="mobileMenuOpen = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- User Info (Mobile) -->
                @auth
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">مستخدم مسجل</p>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Navigation Links -->
                <nav class="flex-1 p-6">
                    <div class="space-y-2">
                        <a href="{{ route('deepfake.index') }}" class="flex items-center p-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-300 group">
                            <div class="w-10 h-10 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                <i class="fas fa-plus text-indigo-600"></i>
                            </div>
                            <span class="font-semibold">تحليل جديد</span>
                        </a>
                        <!-- زر فصل الصوت عن الفيديو -->
                        <a href="{{ route('audio.extract.index') }}" class="flex items-center p-4 text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-xl transition-all duration-300 group">
                            <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                <i class="fas fa-volume-up text-emerald-600"></i>
                            </div>
                            <span class="font-semibold">فصل الصوت عن الفيديو</span>
                        </a>
                        
                        @auth
                            <a href="{{ route('dashboard') }}" class="flex items-center p-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-300 group">
                                <div class="w-10 h-10 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                    <i class="fas fa-chart-bar text-indigo-600"></i>
                                </div>
                                <span class="font-semibold">لوحة التحكم</span>
                            </a>
                            
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-4 text-yellow-700 hover:bg-yellow-50 rounded-xl transition-all duration-300 group">
                                    <div class="w-10 h-10 bg-yellow-50 group-hover:bg-yellow-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                        <i class="fas fa-crown text-yellow-600"></i>
                                    </div>
                                    <span class="font-semibold">لوحة المدير</span>
                                </a>
                            @endif
                            
                            <a href="{{ route('profile.edit') }}" class="flex items-center p-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-300 group">
                                <div class="w-10 h-10 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                    <i class="fas fa-user-edit text-indigo-600"></i>
                                </div>
                                <span class="font-semibold">الملف الشخصي</span>
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center p-4 text-red-600 hover:bg-red-50 rounded-xl transition-all duration-300 group">
                                    <div class="w-10 h-10 bg-red-50 group-hover:bg-red-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                        <i class="fas fa-sign-out-alt text-red-600"></i>
                                    </div>
                                    <span class="font-semibold">تسجيل الخروج</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center p-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all duration-300 group">
                                <div class="w-10 h-10 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                    <i class="fas fa-sign-in-alt text-indigo-600"></i>
                                </div>
                                <span class="font-semibold">تسجيل الدخول</span>
                            </a>
                            
                            <a href="{{ route('register') }}" class="flex items-center p-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl transition-all duration-300 group">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-user-plus text-white"></i>
                                </div>
                                <span class="font-semibold">إنشاء حساب</span>
                            </a>
                        @endauth
                    </div>
                </nav>

                <!-- Footer القائمة -->
                <div class="p-6 border-t border-gray-100">
                    <div class="text-center text-sm text-gray-500">
                        <p>تقنيات الذكاء الاصطناعي المتقدمة</p>
                        <p class="mt-1">&copy; {{ date('Y') }} DeepGuard</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guest Status Bar -->
        @guest
        <div id="guestStatusBar" class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white py-3 px-4 rounded-lg shadow-lg mb-4 transition-all duration-500 ease-in-out transform translate-y-0 opacity-100 max-h-32">
            <!-- Animated Background Circles -->
            <div class="absolute top-0 left-0 w-32 h-32 bg-white/10 rounded-full blur-xl animate-pulse"></div>
            <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/10 rounded-full blur-lg animate-pulse delay-1000"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- Rocket Icon with Bounce Animation -->
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold mb-1">مرحباً بك في نظام كشف التزييف العميق!</h3>
                        <p class="text-xs opacity-90">سجل دخولك أو أنشئ حساب جديد للوصول إلى جميع الميزات</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                    <!-- Action Buttons -->
                    <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-white/20 hover:bg-white/30 rounded-md transition-all duration-200 hover:scale-105 backdrop-blur-sm">
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        تسجيل الدخول
                    </a>
                    
                    <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 rounded-md transition-all duration-200 hover:scale-105 shadow-md">
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        إنشاء حساب
                    </a>
                    
                    <!-- Close Button (X) -->
                    <button id="closeStatusBar" class="flex-shrink-0 p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-md transition-all duration-300 hover:scale-110 hover:rotate-90" title="إغلاق الشريط">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endguest
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    
    // Force header to be fixed and white
    function forceHeaderStyle() {
        if (header) {
            header.style.position = 'fixed';
            header.style.top = '0';
            header.style.left = '0';
            header.style.right = '0';
            header.style.zIndex = '9999';
            header.style.background = 'white';
            header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            header.style.borderBottom = '1px solid #e5e7eb';
            
            // Force full width on mobile
            if (window.innerWidth <= 768) {
                header.style.width = '100vw';
                header.style.minWidth = '100vw';
                header.style.maxWidth = '100vw';
                header.style.margin = '0';
                header.style.padding = '0';
            }
        }
    }
    
    // إضافة class للهيدر عند التمرير
    function handleScroll() {
        if (header) {
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
                header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
            } else {
                header.classList.remove('scrolled');
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            }
        }
    }
    
    // تشغيل مرة واحدة عند التحميل
    forceHeaderStyle();
    handleScroll();
    
    // استمع لحدث التمرير
    window.addEventListener('scroll', handleScroll);
    
    // إعادة تطبيق الأنماط عند تغيير حجم النافذة
    window.addEventListener('resize', forceHeaderStyle);
    
    // إعادة تطبيق الأنماط كل 100ms للتأكد من عدم تغييرها
    setInterval(forceHeaderStyle, 100);
});

document.addEventListener('DOMContentLoaded', function() {
    const statusBar = document.getElementById('guestStatusBar');
    const closeBtn = document.getElementById('closeStatusBar');
    
    function hideStatusBar() {
        // Slide up animation with fade out
        statusBar.style.transform = 'translateY(-100%)';
        statusBar.style.opacity = '0';
        statusBar.style.maxHeight = '0';
        statusBar.style.marginBottom = '0';
        
        // Hide status bar completely after animation
        setTimeout(() => {
            statusBar.style.display = 'none';
        }, 500);
    }
    
    // Always show by default - no localStorage check
    statusBar.style.transform = 'translateY(0)';
    statusBar.style.opacity = '1';
    statusBar.style.maxHeight = '8rem';
    statusBar.style.marginBottom = '1rem';
    
    // Close button click handler
    closeBtn.addEventListener('click', function() {
        hideStatusBar();
    });
});
</script> 