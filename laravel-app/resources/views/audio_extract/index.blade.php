@extends('layouts.app')

@section('content')
<div class="container py-5 max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl shadow-lg p-6 md:p-10 border border-emerald-100">
        <div class="flex items-center mb-6">
            <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mr-4">
                <i class="fas fa-volume-up text-emerald-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-emerald-700 mb-1">فصل الصوت عن الفيديو</h2>
                <p class="text-gray-500 text-sm">استخرج الصوت من أي فيديو بسهولة لتحليل الصوت منفصل عن الفيديو.</p>
            </div>
        </div>
        <form id="uploadForm" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="form-label font-semibold text-gray-700 block mb-2">اختر ملف فيديو</label>
                <div class="relative group">
                    <input type="file" id="videoInput" name="video" accept="video/*" class="hidden" required>
                    <label for="videoInput" class="flex items-center justify-center gap-3 cursor-pointer w-full py-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border-2 border-emerald-200 rounded-xl shadow hover:from-emerald-100 hover:to-emerald-200 transition-all duration-300 text-emerald-700 font-bold text-lg">
                        <i class="fas fa-upload text-2xl"></i>
                        <span id="videoFileName">اختر أو اسحب ملف فيديو هنا</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="form-label font-semibold text-gray-700 block mb-2">اختر صيغة الصوت</label>
                <div class="flex gap-6 justify-center">
                    <label class="flex items-center cursor-pointer gap-2 px-4 py-2 rounded-xl border-2 border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition-all duration-200">
                        <input type="radio" name="audio_format" value="mp3" class="accent-emerald-600 w-5 h-5" checked>
                        <span class="font-bold text-emerald-700">MP3</span>
                    </label>
                    <label class="flex items-center cursor-pointer gap-2 px-4 py-2 rounded-xl border-2 border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition-all duration-200">
                        <input type="radio" name="audio_format" value="wav" class="accent-emerald-600 w-5 h-5">
                        <span class="font-bold text-emerald-700">WAV</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-lg font-bold rounded-xl shadow-lg flex items-center justify-center gap-2 transition-all duration-300">
                <i class="fas fa-magic"></i>
                ابدأ الاستخراج
            </button>
        </form>
        <div id="statusBox" class="mt-6 text-center"></div>
        <div class="mt-8 bg-emerald-50 rounded-xl p-4 flex items-center gap-4">
            <div class="w-12 h-12 flex items-center justify-center bg-white rounded-full shadow">
                <i class="fas fa-lightbulb text-emerald-400 text-2xl"></i>
            </div>
            <div class="text-gray-700 text-sm text-right">
                <strong>مثال:</strong> يمكنك رفع فيديو MP4 أو MOV وسيتم استخراج الصوت منه وحفظه كملف MP3 أو WAV جاهز للتحميل.
            </div>
        </div>
    </div>
</div>
<script>
const form = document.getElementById('uploadForm');
const statusBox = document.getElementById('statusBox');
const videoInput = document.getElementById('videoInput');
const videoFileName = document.getElementById('videoFileName');

videoInput.addEventListener('change', function() {
    if (videoInput.files && videoInput.files.length > 0) {
        videoFileName.textContent = videoInput.files[0].name;
    } else {
        videoFileName.textContent = 'اختر أو اسحب ملف فيديو هنا';
    }
});

// دعم السحب والإفلات
const dropArea = videoFileName.parentElement;
dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.classList.add('ring-2', 'ring-emerald-400');
});
dropArea.addEventListener('dragleave', (e) => {
    dropArea.classList.remove('ring-2', 'ring-emerald-400');
});
dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.classList.remove('ring-2', 'ring-emerald-400');
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        videoInput.files = e.dataTransfer.files;
        videoFileName.textContent = videoInput.files[0].name;
    }
});

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    statusBox.innerHTML = `<div class='py-4 text-emerald-600 font-bold flex items-center justify-center gap-2'><i class='fas fa-spinner fa-spin'></i> جاري رفع الملف ومعالجته...</div>`;
    const formData = new FormData(form);
    try {
        const res = await fetch("{{ route('audio.extract.upload') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': form._token.value },
            body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
            statusBox.innerHTML = `
                <div class='py-4 text-emerald-700 font-bold flex flex-col items-center gap-2'>
                    <i class='fas fa-check-circle text-3xl'></i>
                    تم استخراج الصوت بنجاح!
                    <audio controls src="${data.audio_url}" class="w-full mt-4 rounded-lg shadow"></audio>
                    <a href="${data.audio_url}" download class="inline-flex items-center justify-center gap-2 mt-4 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-lg font-bold rounded-full shadow-lg transition-all duration-300 border-2 border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <i class='fas fa-download text-xl'></i>
                        تحميل الصوت (${data.format.toUpperCase()})
                    </a>
                </div>
            `;
        } else if (data.status === 'error') {
            statusBox.innerHTML = `<div class='py-4 text-red-600 font-bold flex items-center justify-center gap-2'><i class='fas fa-times-circle'></i> فشل الاستخراج: ${data.error || 'خطأ غير معروف'}</div>`;
        } else {
            statusBox.innerHTML = `<div class='py-4 text-red-600 font-bold flex items-center justify-center gap-2'><i class='fas fa-exclamation-triangle'></i> فشل إرسال الطلب.</div>`;
        }
    } catch (err) {
        statusBox.innerHTML = `<div class='py-4 text-red-600 font-bold flex items-center justify-center gap-2'><i class='fas fa-exclamation-triangle'></i> حدث خطأ أثناء الاتصال بالخادم.</div>`;
    }
});
</script>
@endsection
