@props(['analysis'])

<div class="mb-8 sm:mb-10 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300 opacity-0 transform translate-y-6" data-aos="fade-up" data-aos-duration="700" data-aos-delay="150">
    <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-6 sm:mb-8 border-b border-gray-300 pb-3 sm:pb-4 flex items-center">
        <span class="mr-3 sm:mr-4 text-2xl sm:text-3xl">📊</span>
        المعلومات العامة
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="flex items-center p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
            <span class="text-2xl sm:text-3xl mr-3 sm:mr-4 text-blue-500">🧾</span>
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm text-gray-600 mb-1 font-medium">اسم الملف</p>
                <p class="font-semibold text-gray-800 text-sm sm:text-lg truncate">{{ $analysis->file_name }}</p>
            </div>
        </div>
        <div class="flex items-center p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="250">
            <span class="text-2xl sm:text-3xl mr-3 sm:mr-4 text-orange-500">🎯</span>
            <div class="flex-1">
                <p class="text-xs sm:text-sm text-gray-600 mb-1 font-medium">مستوى الثقة</p>
                <p class="font-semibold text-gray-800 text-sm sm:text-lg">
                    {{ round($analysis->confidence * 100, 2) }}%
                </p>
            </div>
        </div>
        <div class="flex items-center p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="300">
            <span class="text-2xl sm:text-3xl mr-3 sm:mr-4 text-purple-500">📊</span>
            <div class="flex-1">
                <p class="text-xs sm:text-sm text-gray-600 mb-1 font-medium">النتيجة</p>
                <span class="font-bold text-lg sm:text-xl {{ $analysis->prediction == 'REAL' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $analysis->prediction == 'REAL' ? 'أصيل' : 'مزيف' }}
                </span>
            </div>
        </div>
        <div class="flex items-center p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="350">
            <span class="text-2xl sm:text-3xl mr-3 sm:mr-4 text-green-500">📁</span>
            <div class="flex-1">
                <p class="text-xs sm:text-sm text-gray-600 mb-1 font-medium">نوع الملف</p>
                <p class="font-semibold text-gray-800 text-sm sm:text-lg">{{ strtoupper($analysis->file_type) }}</p>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
        <div class="flex items-center p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
            <span class="text-2xl sm:text-3xl mr-3 sm:mr-4 text-indigo-500">📅</span>
            <div class="flex-1">
                <p class="text-xs sm:text-sm text-gray-600 mb-1 font-medium">تاريخ التحليل</p>
                <p class="font-semibold text-gray-800 text-sm sm:text-lg">{{ $analysis->created_at->translatedFormat('d M Y - H:i') }}</p>
            </div>
        </div>
        @if($analysis->user)
            <div class="flex items-center p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 opacity-0 transform translate-y-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="450">
                <span class="text-2xl sm:text-3xl mr-3 sm:mr-4 text-pink-500">👤</span>
                <div class="flex-1">
                    <p class="text-xs sm:text-sm text-gray-600 mb-1 font-medium">المستخدم</p>
                    <p class="font-semibold text-gray-800 text-sm sm:text-lg">{{ $analysis->user->name }}</p>
                </div>
            </div>
        @endif
    </div>
</div> 