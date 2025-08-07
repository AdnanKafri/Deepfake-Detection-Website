document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const dropZone = document.getElementById('dropZone');
    const previewContainer = document.getElementById('previewContainer');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const mediaPreview = document.getElementById('mediaPreview');
    const removeFile = document.getElementById('removeFile');
    const results = document.getElementById('results');
    const resultContent = document.getElementById('resultContent');
    const loading = document.getElementById('loading');
    const downloadReport = document.getElementById('downloadReport');
    const showDetailsBtn = document.getElementById('showDetailsBtn');

    // SweetAlert Functions
    function showError(message) {
        if (typeof Swal === 'undefined') {
            alert('خطأ: ' + message);
            return;
        }
        
        Swal.fire({
            title: 'خطأ!',
            text: message,
            icon: 'error',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#EF4444'
        });
    }

    function showWarning(message) {
        if (typeof Swal === 'undefined') {
            alert('تحذير: ' + message);
            return;
        }
        
        Swal.fire({
            title: 'تحذير!',
            text: message,
            icon: 'warning',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#F59E0B'
        });
    }

    function showInfo(message) {
        if (typeof Swal === 'undefined') {
            alert('معلومات: ' + message);
            return;
        }
        
        Swal.fire({
            title: 'معلومات',
            text: message,
            icon: 'info',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#3B82F6'
        });
    }

    // Handle drag and drop events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('bg-blue-50'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('bg-blue-50'), false);
    });

    // Handle file drop
    dropZone.addEventListener('drop', e => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    });

    // Handle file selection
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', function () {
        handleFiles(this.files);
    });

    // Handle file preview
    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            const validTypes = ['image/jpeg', 'image/png', 'video/mp4', 'video/avi', 'audio/mpeg', 'audio/wav'];

            if (!validTypes.includes(file.type)) {
                showError('نوع الملف غير مدعوم. يرجى اختيار صورة أو فيديو أو ملف صوتي.');
                return;
            }

            // Update file info
            if (fileName) fileName.textContent = file.name;
            if (fileSize) fileSize.textContent = formatFileSize(file.size);
            if (previewContainer) previewContainer.classList.remove('hidden');

            // إعادة إظهار زر التحليل عند رفع وسيط جديد
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'pointer-events-none');
                submitBtn.style.display = '';
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = function (e) {
                if (mediaPreview) {
                mediaPreview.innerHTML = '';
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'media-preview w-48 h-48 object-cover mx-auto rounded-lg';
                    mediaPreview.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = e.target.result;
                    video.controls = true;
                    video.className = 'media-preview w-48 h-48 object-cover mx-auto rounded-lg';
                    mediaPreview.appendChild(video);
                } else if (file.type.startsWith('audio/')) {
                    const audio = document.createElement('audio');
                    audio.src = e.target.result;
                    audio.controls = true;
                    audio.className = 'w-full';
                    mediaPreview.appendChild(audio);
                    }
                } else {
                    console.error('mediaPreview غير موجود في الصفحة!');
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove file
    removeFile.addEventListener('click', function () {
        fileInput.value = '';
        if (previewContainer) previewContainer.classList.add('hidden');
        if (mediaPreview) mediaPreview.innerHTML = '';
        // إعادة إظهار زر التحليل عند إزالة الملف
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'pointer-events-none');
            submitBtn.style.display = '';
        }
    });

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) {
            showWarning('الرجاء اختيار ملف للتحليل');
            return;
        }

        // إخفاء زر التحليل أثناء التحميل
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'pointer-events-none');
            submitBtn.style.display = 'none';
        }

        const formData = new FormData();
        formData.append('file', file);

        // Add the file type to the form data
        const fileType = file.type.split('/')[0];
        formData.append('file_type', fileType);

        // Show loader
        if (loading) loading.classList.remove('hidden');
        else console.error('عنصر #loading غير موجود في الصفحة!');
        if (results) results.classList.add('hidden');
        if (resultContent) resultContent.innerHTML = "";

        try {
            const response = await fetch(DEEPFAKE_ANALYZE_URL, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (!response.ok || data.status !== 'success') {
                throw new Error(data.message || 'فشل التحليل');
            }

            const result = data.data?.data || data.data;
            // تعيين معرف آخر تحليل من response مباشرة
            window.lastAnalysisId = data.analysis_id;
            displayResults(result);

        } catch (error) {
            console.error('Error:', error);
            // محاولة استخراج تفاصيل الخطأ من الاستجابة
            let errorMsg = error.message || 'حدث خطأ غير متوقع.';
            // إذا كانت الرسالة تشير لسبب معروف (عدم وجود وجه/صوت)
            if (errorMsg.includes('No faces detected') || errorMsg.includes('No valid faces detected')) {
                showError('لم يتم اكتشاف أي وجه في الصورة. يرجى رفع صورة تحتوي على وجه واضح.');
            } else if (errorMsg.includes('No voice activity detected')) {
                showError('لم يتم اكتشاف صوت واضح في الملف الصوتي. يرجى رفع مقطع صوتي واضح.');
            } else if (errorMsg.includes('نوع الملف غير مدعوم') || errorMsg.includes('Unsupported file type')) {
                showError('نوع الملف غير مدعوم. يرجى رفع صورة أو فيديو أو صوت بصيغة مدعومة.');
            } else if (errorMsg.includes('UNKNOWN')) {
                showError('لم يتمكن النظام من تحليل الملف. يرجى التأكد من وضوح المحتوى أو تجربة ملف آخر.');
            } else {
                showError('خطأ: ' + errorMsg);
            }
        } finally {
            if (loading) loading.classList.add('hidden');
            // إعادة إظهار زر التحليل بعد انتهاء التحليل
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'pointer-events-none');
                submitBtn.style.display = '';
            }
        }
    });

    function displayResults(data) {
        if (results) results.classList.remove('hidden');
        else console.error('عنصر #results غير موجود في الصفحة!');

        // Robust extraction of type, prediction, confidence, and details
        const type = data.type || (data.result && data.result.type) || 'unknown';
        const prediction = data.prediction || (data.result && data.result.prediction) || 'UNKNOWN';
        let confidence = data.confidence || (data.result && data.result.confidence);
        if (typeof confidence !== 'number' || isNaN(confidence)) confidence = 0;
        const confidencePercent = (confidence * 100).toFixed(2);
        const details = data.details || (data.result && data.result.details) || {};

        let color, icon, label, sublabel;
        if (prediction === 'FAKE') {
            color = 'red';
            icon = 'fa-xmark-circle';
            label = 'محتوى مزيف';
            sublabel = 'يرجى توخي الحذر عند التعامل مع هذا الملف.';
        } else if (prediction === 'REAL') {
            color = 'green';
            icon = 'fa-circle-check';
            label = 'محتوى أصيل';
            sublabel = 'لم يتم اكتشاف أي تلاعب واضح.';
        } else {
            color = 'gray';
            icon = 'fa-circle-question';
            label = 'غير معروف';
            sublabel = 'لم يتمكن النظام من تحديد النتيجة.';
        }

        // تفاصيل إضافية حسب النوع
        let extraGrid = '';
        if (type === 'image') {
            extraGrid = `
                <div class="flex flex-col items-center justify-center w-full" style="text-align: center;">
                    <div class="flex flex-col items-center justify-center bg-indigo-50 rounded-2xl shadow p-4 w-full max-w-xs mx-auto">
                        <span class="text-5xl mb-2 text-indigo-500"><i class="fa-regular fa-face-smile"></i></span>
                        <span class="font-extrabold text-3xl text-indigo-700 mb-1" style="letter-spacing:1px;">${details.faces_detected !== undefined ? details.faces_detected : 'غير متوفر'}</span>
                        <span class="text-base text-gray-600">عدد الوجوه المكتشفة</span>
                    </div>
                </div>
            `;
        } else if (type === 'video') {
            extraGrid = `
                <div class="flex flex-col items-center justify-center">
                    <span class="text-sm text-gray-500 mb-1">الإطارات المحللة</span>
                    <span class="font-bold text-lg">${details.frames_analyzed !== undefined ? details.frames_analyzed : 'غير متوفر'}</span>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <span class="text-sm text-gray-500 mb-1">إطارات بوجوه</span>
                    <span class="font-bold text-lg">${details.frames_with_faces !== undefined ? details.frames_with_faces : 'غير متوفر'}</span>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <span class="text-sm text-gray-500 mb-1">إطارات أصيلة</span>
                    <span class="font-bold text-lg text-green-600">${details.real_frames !== undefined ? details.real_frames : 'غير متوفر'}</span>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <span class="text-sm text-gray-500 mb-1">إطارات مزيفة</span>
                    <span class="font-bold text-lg text-red-600">${details.fake_frames !== undefined ? details.fake_frames : 'غير متوفر'}</span>
                </div>
            `;
        }

        let html = `
        <div class="result-card bg-gradient-to-br from-white via-${color}-50 to-${color}-100 rounded-3xl shadow-2xl border-2 border-${color}-200 p-8 mb-2 animate-fade-in-up" style="direction: rtl;">
            <div class="flex flex-col items-center mb-6">
                <div class="rounded-full bg-${color}-100 shadow-lg flex items-center justify-center mb-4 animate-bounce-slow" style="width: 90px; height: 90px;">
                    <i class="fa-solid ${icon} text-${color}-500 text-5xl"></i>
                </div>
                <h3 class="text-3xl font-extrabold text-${color}-700 mb-2">${label}</h3>
                <p class="text-base text-gray-600 mb-2">${sublabel}</p>
            </div>
            <div class="w-full flex flex-col items-center mb-6">
                <span class="text-sm text-gray-500 mb-1">نسبة الثقة</span>
                <div class="w-full bg-gray-200 rounded-full h-5 relative overflow-hidden mb-2" style="max-width: 350px;">
                    <div class="h-5 rounded-full bg-gradient-to-l from-${color}-400 to-${color}-600 animate-progress-bar" style="width: ${confidencePercent}%; transition: width 1s;"></div>
                </div>
                <span class="font-bold text-lg text-${color}-700">${confidencePercent}%</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                ${extraGrid}
            </div>
            <div class="text-center mt-2">
                <span class="inline-block bg-${color}-50 text-${color}-700 rounded-xl px-4 py-2 text-sm font-semibold shadow-sm">نوع الملف: <span class="font-bold">${type === 'image' ? 'صورة' : type === 'video' ? 'فيديو' : type === 'audio' ? 'صوت' : 'غير معروف'}</span></span>
            </div>
        </div>
        <style>
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-bounce-slow { animation: bounce-slow 2s infinite; }
        @keyframes progress-bar {
            0% { width: 0; }
            100% { width: var(--progress-width, 100%); }
        }
        .animate-progress-bar { animation: progress-bar 1.2s cubic-bezier(.4,2,.6,1) forwards; }
        </style>
        `;

        if (resultContent) resultContent.innerHTML = html;
        else console.error('عنصر #resultContent غير موجود في الصفحة!');
    }

    // إصلاح أزرار عرض التفاصيل/تحميل التقرير
    if (showDetailsBtn) {
        showDetailsBtn.addEventListener('click', function () {
            if (window.lastAnalysisId) {
                window.location.href = '/analysis/' + window.lastAnalysisId;
            } else {
                alert('لم يتم العثور على تحليل لعرض التفاصيل.');
            }
        });
    }
    if (downloadReport) {
        downloadReport.addEventListener('click', function (e) {
            e.preventDefault();
            if (window.lastAnalysisId) {
                window.open('/analysis/' + window.lastAnalysisId + '/pdf', '_blank');
            } else {
                showWarning('لم يتم العثور على تقرير للتحميل.');
            }
        });
    }

    // Function to cleanup hanging modals
    function cleanupModals() {
        // Remove any hanging modal containers
        const containers = document.querySelectorAll('.swal2-container');
        containers.forEach(container => {
            if (!container.classList.contains('swal2-shown')) {
                container.remove();
            }
        });
        
        // Remove any hanging backdrops
        const backdrops = document.querySelectorAll('.swal2-backdrop');
        backdrops.forEach(backdrop => {
            if (!backdrop.classList.contains('swal2-backdrop-show')) {
                backdrop.remove();
            }
        });
    }

    // Function to force close all modals
    function forceCloseAllModals() {
        // Close any open SweetAlert
        if (Swal.isVisible()) {
            Swal.close();
        }
        
        // Remove all modal containers
        const containers = document.querySelectorAll('.swal2-container');
        containers.forEach(container => {
            container.remove();
        });
        
        // Remove all backdrops
        const backdrops = document.querySelectorAll('.swal2-backdrop');
        backdrops.forEach(backdrop => {
            backdrop.remove();
        });
        
        // Remove any modal-related styles from body
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    // Global event listener for modal cleanup
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('swal2-backdrop')) {
            Swal.close();
        }
    });

    // Keyboard event listener for ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (Swal.isVisible()) {
                Swal.close();
            }
        }
    });

    // Ensure modals are cleaned up on page unload
    window.addEventListener('beforeunload', function() {
        forceCloseAllModals();
    });

    // Clean up modals on page load
    document.addEventListener('DOMContentLoaded', function() {
        cleanupModals();
    });
});

