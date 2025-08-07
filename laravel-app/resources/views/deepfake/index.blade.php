@extends('layouts.dashboard')

@section('title', 'تحليل التزييف')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-sky-100 flex flex-col justify-between" dir="rtl" style="scroll-behavior: smooth;">
    <main class="flex-1 flex flex-col items-center justify-center pt-32 pb-16 px-2">
        <!-- Hero Section -->
        <section class="w-full max-w-3xl text-center mb-12">
            <img src="/images/undraw_artificial_intelligence_re_enpp.svg" alt="AI Illustration" class="mx-auto w-40 mb-6 animate-fade-in" />
            <h1 class="text-4xl md:text-5xl font-extrabold text-indigo-800 mb-4 leading-tight">حلّل ملفاتك واكتشف الحقيقة خلال ثوانٍ!</h1>
            <p class="text-lg md:text-xl text-gray-700 mb-6">منصة احترافية لكشف الصور والفيديوهات والصوتيات المزيفة باستخدام الذكاء الاصطناعي.</p>
            <a href="#upload" id="scrollToUploadBtn" class="inline-block bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold py-3 px-10 rounded-xl shadow-lg text-lg hover:scale-105 transition">ابدأ التحليل الآن</a>
        </section>
        <!-- Upload Card -->
        <section id="upload" class="w-full max-w-xl bg-white/90 rounded-3xl shadow-2xl p-4 sm:p-8 md:p-10 mb-10 border border-indigo-100 flex flex-col items-center animate-fade-in-up">
            <div class="w-24 h-24 flex items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-6 shadow-lg">
                <i class="fas fa-cloud-upload-alt text-5xl text-indigo-400 animate-bounce"></i>
        </div>
            <form id="uploadForm" class="w-full space-y-6">
                <div class="drop-zone rounded-2xl border-2 border-dashed border-indigo-400 bg-indigo-50/50 hover:bg-indigo-100 transition p-4 sm:p-8 text-center cursor-pointer w-full" id="dropZone">
                    <input type="file" id="fileInput" class="hidden" accept=".jpg,.jpeg,.png,.mp4,.avi,.mov,.mp3,.wav">
                    <div class="space-y-3">
                        <p class="text-base sm:text-lg font-semibold text-indigo-700">اسحب الملف هنا أو انقر لاختياره</p>
                        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mt-2">
                            <span class="bg-white rounded-full px-3 py-1 text-indigo-600 font-bold text-xs border">JPG</span>
                            <span class="bg-white rounded-full px-3 py-1 text-indigo-600 font-bold text-xs border">PNG</span>
                            <span class="bg-white rounded-full px-3 py-1 text-purple-600 font-bold text-xs border">MP4</span>
                            <span class="bg-white rounded-full px-3 py-1 text-purple-600 font-bold text-xs border">AVI</span>
                            <span class="bg-white rounded-full px-3 py-1 text-sky-600 font-bold text-xs border">MP3</span>
                            <span class="bg-white rounded-full px-3 py-1 text-sky-600 font-bold text-xs border">WAV</span>
                        </div>
                    </div>
                </div>
                <div id="previewContainer" class="hidden w-full">
                    <div class="border rounded-xl p-4 bg-indigo-50">
                        <div class="flex flex-col sm:flex-row items-center justify-between mb-4 gap-3">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file text-2xl text-indigo-600"></i>
                                <div>
                                    <p class="font-semibold text-sm sm:text-base" id="fileName"></p>
                                    <p class="text-xs sm:text-sm text-gray-500" id="fileSize"></p>
                                </div>
                            </div>
                            <button type="button" id="removeFile" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="mediaPreview" class="mt-4"></div>
                    </div>
                </div>
                <div class="text-center w-full">
                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-8 sm:px-10 rounded-xl shadow-lg transition duration-300 transform hover:scale-105 text-base sm:text-lg">
                        <i class="fas fa-search ml-3"></i>
                        تحليل الوسيط
                    </button>
                </div>
            </form>
        </section>
        <!-- Loading Spinner -->
        <div id="loading" class="hidden flex flex-col items-center justify-center py-8">
            <svg class="animate-spin h-12 w-12 text-indigo-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <div class="text-indigo-700 font-bold text-lg">جاري التحليل...</div>
        </div>
        <!-- Result Card -->
        <section id="results" class="w-full max-w-xl bg-white rounded-3xl shadow-xl p-8 border border-indigo-100 hidden mt-8 animate-fade-in-up">
            <div class="flex flex-col items-center mb-6">
                <h2 class="text-2xl font-bold text-indigo-800 mb-2">نتيجة التحليل</h2>
                <div id="resultContent" class="w-full"></div>
            </div>
            @auth
            <div class="flex justify-center mt-6">
                <button id="showDetailsBtn" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg transition duration-300 transform hover:scale-105 text-lg">
                    <i class="fas fa-list-alt ml-3"></i>
                    عرض التفاصيل الكاملة
                </button>
            </div>
            @else
            <div class="flex flex-col items-center justify-center mt-6">
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 text-center shadow-md max-w-md">
                    <div class="text-2xl mb-2 text-indigo-700 font-bold">لرؤية تفاصيل التحليل الكاملة</div>
                    <div class="text-gray-700 mb-4">سجّل دخولك أو أنشئ حساب جديد لتتمكن من استعراض جميع تفاصيل التحليل بدقة!</div>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white font-bold py-2 px-6 rounded-lg shadow transition">إنشاء حساب</a>
                    </div>
                </div>
            </div>
            @endauth
        </section>
        <!-- Features Section -->
        <section id="features" class="w-full max-w-5xl mx-auto mt-20 mb-10">
            <h3 class="text-3xl font-extrabold text-indigo-800 text-center mb-10">لماذا DeepGuard؟</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center border-t-4 border-indigo-400">
                    <i class="fas fa-bolt text-4xl text-indigo-500 mb-4"></i>
                    <h4 class="font-bold text-lg mb-3">تحليل فوري</h4>
                    <p class="text-gray-600">نتائج سريعة خلال ثوانٍ بفضل تقنيات الذكاء الاصطناعي المتقدمة.</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center border-t-4 border-purple-400">
                    <i class="fas fa-shield-alt text-4xl text-purple-500 mb-4"></i>
                    <h4 class="font-bold text-lg mb-3">دقة عالية</h4>
                    <p class="text-gray-600">خوارزميات متطورة تضمن كشف المحتوى المزيف بدقة كبيرة.</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center border-t-4 border-sky-400">
                    <i class="fas fa-lock text-4xl text-sky-500 mb-4"></i>
                    <h4 class="font-bold text-lg mb-3">خصوصية تامة</h4>
                    <p class="text-gray-600">ملفاتك آمنة ولا يتم الاحتفاظ بها بعد التحليل.</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-6 flex flex-col items-center text-center border-t-4 border-green-400">
                    <i class="fas fa-layer-group text-4xl text-green-500 mb-4"></i>
                    <h4 class="font-bold text-lg mb-3">دعم شامل</h4>
                    <p class="text-gray-600">يدعم الصور والفيديو والصوت بمختلف الصيغ الشائعة.</p>
        </div>
    </div>
        </section>
    </main>
</div>
@endsection

@push('scripts')
<script>
    const DEEPFAKE_ANALYZE_URL = "{{ route('deepfake.analyze') }}";

    // Scroll smoothly to upload section
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('scrollToUploadBtn');
        const uploadSection = document.getElementById('upload');
        if (btn && uploadSection) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                uploadSection.scrollIntoView({ behavior: 'smooth' });
            });
        }
    });
</script>
<script src="/js/deepfake.js"></script>
@endpush
