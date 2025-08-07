<footer class="bg-white border-t border-indigo-100 mt-auto" style="width: 100vw !important; min-width: 100vw !important; max-width: 100vw !important; margin: 0 !important; padding: 0 !important;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- معلومات النظام -->
            <div class="lg:col-span-2">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg mr-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">DeepGuard</h3>
                        <p class="text-sm text-gray-500 font-medium">نظام كشف التزييف المتقدم</p>
                    </div>&nbsp;
                </div>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    نظام متقدم يستخدم تقنيات الذكاء الاصطناعي لتحليل المحتوى بكفاءة عالية وأمان تام. 
                    نحن نقدم أحدث التقنيات لحماية المستخدمين من المحتوى المزيف.
                </p>
                <div class="flex space-x-4 space-x-reverse">
                    <a href="#" class="w-10 h-10 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="w-10 h-10 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
            </div>

            <!-- روابط سريعة -->
            <div>
                <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-link text-indigo-600 text-sm"></i>
                    </div>
                    روابط سريعة
                </h4>
                <div class="space-y-3">
                    <a href="{{ route('deepfake.index') }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 group">
                        <div class="w-6 h-6 bg-gray-50 group-hover:bg-indigo-50 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                            <i class="fas fa-plus text-gray-500 group-hover:text-indigo-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium">تحليل جديد</span>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 group">
                            <div class="w-6 h-6 bg-gray-50 group-hover:bg-indigo-50 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                <i class="fas fa-chart-bar text-gray-500 group-hover:text-indigo-600 text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">لوحة التحكم</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 group">
                            <div class="w-6 h-6 bg-gray-50 group-hover:bg-indigo-50 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                <i class="fas fa-user text-gray-500 group-hover:text-indigo-600 text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">الملف الشخصي</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 group">
                            <div class="w-6 h-6 bg-gray-50 group-hover:bg-indigo-50 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                <i class="fas fa-sign-in-alt text-gray-500 group-hover:text-indigo-600 text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">تسجيل الدخول</span>
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-all duration-300 group">
                            <div class="w-6 h-6 bg-gray-50 group-hover:bg-indigo-50 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                                <i class="fas fa-user-plus text-gray-500 group-hover:text-indigo-600 text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">إنشاء حساب</span>
                        </a>
                    @endauth
                    <a href="{{ route('audio.extract.index') }}" class="flex items-center text-gray-600 hover:text-emerald-600 transition-all duration-300 group">
                        <div class="w-6 h-6 bg-emerald-50 group-hover:bg-emerald-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                            <i class="fas fa-volume-up text-emerald-600 text-xs"></i>
                        </div>
                        <span class="text-sm font-medium">فصل الصوت عن الفيديو</span>
                    </a>
                </div>
            </div>

            <!-- معلومات الاتصال -->
            <div>
                <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-envelope text-indigo-600 text-sm"></i>
                    </div>
                    &nbsp;تواصل معنا
                </h4>
                <div class="space-y-4">
                    <div class="flex items-center text-gray-600 group">
                        <div class="w-8 h-8 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                            <i class="fas fa-envelope text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">البريد الإلكتروني</p>
                            <p class="text-xs text-gray-500" dir="ltr">support@deepguard.com</p>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600 group">
                        <div class="w-8 h-8 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                            <i class="fas fa-phone text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">الهاتف</p>
                            <p class="text-xs text-gray-500" dir="ltr">+963 952 749 458</p>
                        </div>
                    </div>
                    <div class="flex items-center text-gray-600 group">
                        <div class="w-8 h-8 bg-indigo-50 group-hover:bg-indigo-100 rounded-lg flex items-center justify-center mr-4 transition-all duration-300">
                            <i class="fas fa-map-marker-alt text-indigo-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium">العنوان</p>
                            <p class="text-xs text-gray-500">حماه، الجمهورية العربية السورية</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- خط فاصل -->
        <div class="border-t border-gray-100 mt-12 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-shield-alt text-white text-sm"></i>
                    </div>
                    <p class="text-gray-600 text-sm font-medium">
                        &copy; {{ date('Y') }} DeepGuard. جميع الحقوق محفوظة.
                    </p>
                </div>
                <div class="flex items-center space-x-6 space-x-reverse">
                    <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-300 text-sm">سياسة الخصوصية</a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-300 text-sm">شروط الاستخدام</a>
                    <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-300 text-sm">سياسة ملفات تعريف الارتباط</a>
                </div>
            </div>
        </div>
    </div>
</footer> 