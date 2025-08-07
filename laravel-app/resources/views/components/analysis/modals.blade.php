<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-2 sm:p-4">
    <div class="relative max-w-4xl max-h-full w-full h-full flex flex-col">
        <!-- Modal Header -->
        <div class="flex justify-between items-center bg-white rounded-t-xl p-3 sm:p-4 shadow-lg">
            <h3 id="modalTitle" class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span id="modalTitleText">عرض الصورة</span>
            </h3>
            <div class="flex items-center space-x-2 sm:space-x-3 space-x-reverse">
                <!-- Zoom Controls -->
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button id="zoomOut" class="p-1 sm:p-2 hover:bg-gray-200 rounded-md transition-colors duration-200 text-gray-600 hover:text-gray-800">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <span id="zoomLevel" class="px-2 sm:px-3 text-xs sm:text-sm font-medium text-gray-700">100%</span>
                    <button id="zoomIn" class="p-1 sm:p-2 hover:bg-gray-200 rounded-md transition-colors duration-200 text-gray-600 hover:text-gray-800">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                    <button id="resetZoom" class="p-1 sm:p-2 hover:bg-gray-200 rounded-md transition-colors duration-200 text-gray-600 hover:text-gray-800 mr-1 sm:mr-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
                </div>
                <!-- Close Button -->
                <button onclick="closeModal()" class="p-1 sm:p-2 hover:bg-red-100 rounded-lg transition-colors duration-200 text-gray-600 hover:text-red-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Modal Content -->
        <div class="flex-1 bg-white rounded-b-xl overflow-hidden shadow-lg">
            <div class="w-full h-full flex items-center justify-center p-2 sm:p-4 bg-gray-50">
                <div class="relative w-full h-full flex items-center justify-center overflow-auto">
                    <img id="modalImage" src="" alt="Modal Image" class="min-w-[600px] min-h-[400px] sm:min-w-[800px] sm:min-h-[600px] w-auto h-auto object-contain rounded-lg shadow-xl transition-transform duration-300">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentZoom = 1;
    const zoomStep = 0.2;
    const maxZoom = 3;
    const minZoom = 0.3;

    function openModal(imageSrc, title) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitleText = document.getElementById('modalTitleText');
        
        modalImage.src = imageSrc;
        modalTitleText.textContent = title;
        
        currentZoom = 1;
        updateZoom();
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            modal.style.transition = 'all 0.3s ease-out';
            modal.style.opacity = '1';
            modal.style.transform = 'scale(1)';
        }, 10);
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        
        modal.style.transition = 'all 0.3s ease-in';
        modal.style.opacity = '0';
        modal.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('modal-open');
        }, 300);
    }

    function updateZoom() {
        const modalImage = document.getElementById('modalImage');
        const zoomLevel = document.getElementById('zoomLevel');
        
        modalImage.style.transform = `scale(${currentZoom})`;
        zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
    }

    function zoomIn() {
        if (currentZoom < maxZoom) {
            currentZoom += zoomStep;
            updateZoom();
        }
    }

    function zoomOut() {
        if (currentZoom > minZoom) {
            currentZoom -= zoomStep;
            updateZoom();
        }
    }

    function resetZoom() {
        currentZoom = 1;
        updateZoom();
    }

    // Event listeners
    document.getElementById('zoomIn').addEventListener('click', zoomIn);
    document.getElementById('zoomOut').addEventListener('click', zoomOut);
    document.getElementById('resetZoom').addEventListener('click', resetZoom);

    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('imageModal');
        if (!modal.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeModal();
            } else if (e.key === '+' || e.key === '=') {
                e.preventDefault();
                zoomIn();
            } else if (e.key === '-') {
                e.preventDefault();
                zoomOut();
            } else if (e.key === '0') {
                e.preventDefault();
                resetZoom();
            }
        }
    });

    // SweetAlert Functions
    function showFeedbackModal() {
        console.log('showFeedbackModal called');
        
        try {
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert is not loaded');
                alert('خطأ في تحميل النوافذ المنبثقة - SweetAlert غير محمل');
                return;
            }
            
            console.log('SweetAlert is loaded successfully');
            
            Swal.fire({
                title: 'تقييم النتيجة',
                html: `<div style='font-size:1.1em;'>يرجى تقييم دقة نتيجة التحليل.<br><span style='color:#6366F1;font-weight:bold;'>ملاحظتك تساعدنا في تحسين الخدمة.</span></div><div style='margin-top:1.5em;font-size:0.95em;color:#888;background:#f3f4f6;padding:8px 12px;border-radius:8px;'>تنبيه خصوصية: عند التقييم، فإنك توافق على إتاحة هذا التقرير لمسؤولي الموقع مع جميع تفاصيله حفاظًا على جودة الخدمة وخصوصية المستخدمين.</div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'النتيجة صحيحة',
                cancelButtonText: 'النتيجة خاطئة',
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#EF4444',
                reverseButtons: true,
                allowOutsideClick: true,
                allowEscapeKey: true
            }).then((result) => {
                console.log('SweetAlert result:', result);
                if (result.isConfirmed) {
                    submitFeedback('CORRECT');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    submitFeedback('INCORRECT');
                }
            }).catch((error) => {
                console.error('SweetAlert error:', error);
                alert('خطأ في عرض النافذة المنبثقة');
            });
            
        } catch (error) {
            console.error('Error in showFeedbackModal:', error);
            alert('خطأ في عرض النافذة المنبثقة: ' + error.message);
        }
    }

    function submitFeedback(feedback) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("analysis.feedback", $analysis->id) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const feedbackInput = document.createElement('input');
        feedbackInput.type = 'hidden';
        feedbackInput.name = 'feedback';
        feedbackInput.value = feedback;
        
        form.appendChild(csrfToken);
        form.appendChild(feedbackInput);
        document.body.appendChild(form);
        
        Swal.fire({
            title: 'جاري إرسال التقييم...',
            text: 'يرجى الانتظار',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        form.submit();
    }

    function showReportModal() {
        console.log('showReportModal called');
        
        try {
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert is not loaded');
                alert('خطأ في تحميل النوافذ المنبثقة - SweetAlert غير محمل');
                return;
            }
            
            console.log('SweetAlert is loaded successfully for report modal');
            
            Swal.fire({
                title: 'إبلاغ عن محتوى مزيف',
                html: `<div style='font-size:1.1em;'>هل أنت متأكد أنك تريد الإبلاغ عن هذا التحليل كمحتوى مزيف؟<br><span style='color:#EF4444;font-weight:bold;'>يرجى التأكد قبل الإبلاغ.</span></div><div style='margin-top:1.5em;font-size:0.95em;color:#888;background:#f3f4f6;padding:8px 12px;border-radius:8px;'>تنبيه خصوصية: عند الإبلاغ، فإنك توافق على إتاحة هذا التقرير لمسؤولي الموقع مع جميع تفاصيله حفاظًا على جودة الخدمة وخصوصية المستخدمين.</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، أبلغ',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                reverseButtons: true,
                allowOutsideClick: true,
                allowEscapeKey: true
            }).then((result) => {
                console.log('Report modal result:', result);
                if (result.isConfirmed) {
                    submitReport();
                }
            }).catch((error) => {
                console.error('Report modal error:', error);
                alert('خطأ في عرض نافذة الإبلاغ');
            });
            
        } catch (error) {
            console.error('Error in showReportModal:', error);
            alert('خطأ في عرض نافذة الإبلاغ: ' + error.message);
        }
    }

    function submitReport() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("analysis.report", $analysis->id) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        
        Swal.fire({
            title: 'جاري الإبلاغ...',
            text: 'يرجى الانتظار',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        form.submit();
    }

    function showSuccessMessage(message) {
        if (typeof Swal === 'undefined') {
            alert(message);
            return;
        }
        
        Swal.fire({
            title: 'تم بنجاح!',
            text: message,
            icon: 'success',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#10B981'
        });
    }

    function showErrorMessage(message) {
        if (typeof Swal === 'undefined') {
            alert(message);
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

    // Scroll-triggered animations
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.style.transition = 'all 0.6s ease-out';
                }
            });
        }, observerOptions);

        document.querySelectorAll('[data-aos]').forEach(el => {
            observer.observe(el);
        });
    }

    // Initialize animations when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        
        // اختبار SweetAlert عند تحميل الصفحة
        if (typeof Swal !== 'undefined') {
            console.log('SweetAlert is available on page load');
        } else {
            console.error('SweetAlert is NOT available on page load');
        }
        
        initScrollAnimations();
        
        @if(session('status'))
            showSuccessMessage('{{ session("status") }}');
        @endif
        
        @if(session('error'))
            showErrorMessage('{{ session("error") }}');
        @endif
    });

    // Re-initialize animations on window resize
    window.addEventListener('resize', function() {
        initScrollAnimations();
    });

    function showAdminCyberReport() {
        Swal.fire({
            title: 'تم إرسال البلاغ!',
            html: '<div style="font-size:1.1em;">تم إرسال البلاغ إلى قسم الجرائم الإلكترونية بنجاح.<br>سيتم متابعة الحالة من قبل الإدارة.</div>',
            icon: 'info',
            confirmButtonText: 'حسناً',
            confirmButtonColor: '#6366F1',
            customClass: {
                popup: 'swal2-border-radius'
            }
        });
    }
</script>

<!-- AOS CSS -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- AOS JavaScript -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 100,
        delay: 0
    });
</script>

<style>
    /* Basic RTL Support for SweetAlert */
    .swal2-popup {
        direction: rtl;
        text-align: right;
        font-family: 'Amiri', serif;
    }
    
    .swal2-title {
        font-family: 'Amiri', serif;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1F2937;
    }
    
    .swal2-content {
        font-family: 'Amiri', serif;
        font-size: 1rem;
        color: #6B7280;
        line-height: 1.6;
    }
    
    .swal2-confirm,
    .swal2-cancel {
        font-family: 'Amiri', serif;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }
    
    .swal2-confirm:hover,
    .swal2-cancel:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    /* Ensure proper z-index */
    .swal2-container {
        z-index: 9999 !important;
    }
    
    .swal2-popup {
        z-index: 10000 !important;
    }

    #imageModal {
        z-index: 11000 !important;
    }
    body.modal-open .header-fixed.header-container {
        opacity: 0.7 !important;
        backdrop-filter: blur(6px) !important;
        z-index: 1000 !important;
        pointer-events: none !important;
    }
</style>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // اختبار تحميل SweetAlert
    if (typeof Swal !== 'undefined') {
        console.log('SweetAlert loaded successfully in modals component');
    } else {
        console.error('SweetAlert failed to load in modals component');
    }
</script> 