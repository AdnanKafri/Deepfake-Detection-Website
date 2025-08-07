<header class="w-full bg-white/80 backdrop-blur border-b border-indigo-100 shadow-sm fixed top-0 z-30">
    <div class="container mx-auto flex items-center justify-between py-4 px-6">
        <div class="flex items-center gap-3">
            <span class="text-3xl font-extrabold text-indigo-700">DeepGuard</span>
            <span class="text-xs bg-indigo-100 text-indigo-700 rounded-full px-3 py-1 font-bold">Beta</span>
        </div>
        <nav class="hidden md:flex gap-8 text-indigo-700 font-semibold text-lg">
            <a href="/" class="hover:text-purple-600 transition">الرئيسية</a>
            <a href="/dashboard" class="hover:text-purple-600 transition">لوحة التحكم</a>
            <a href="#features" class="hover:text-purple-600 transition">المزايا</a>
            <a href="#how" class="hover:text-purple-600 transition">كيف يعمل؟</a>
            <a href="#contact" class="hover:text-purple-600 transition">تواصل معنا</a>
        </nav>
        <div class="relative" x-data="{ open: false }">
            @auth
            <button @click="open = !open" class="flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-xl font-bold shadow hover:scale-105 transition">
                <i class="fas fa-user-circle text-xl"></i>
                <span>{{ Auth::user()->name }}</span>
                <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-indigo-100 z-50 py-2 text-right">
                <a href="/dashboard" class="block px-4 py-2 text-indigo-700 hover:bg-indigo-50">لوحة التحكم</a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-right px-4 py-2 text-red-600 hover:bg-red-50">تسجيل الخروج</button>
                </form>
            </div>
            @else
            <a href="{{ route('login') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2 rounded-xl font-bold shadow hover:scale-105 transition">تسجيل الدخول</a>
            <a href="{{ route('register') }}" class="ml-2 bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-5 py-2 rounded-xl font-bold shadow hover:scale-105 transition">إنشاء حساب</a>
            @endauth
        </div>
    </div>
</header> 