<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%234F46E5;stop-opacity:1' /><stop offset='100%' style='stop-color:%237C3AED;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='45' fill='url(%23grad)' stroke='%23ffffff' stroke-width='3'/><path d='M30 25 L70 25 L70 75 L30 75 Z' fill='%23ffffff' opacity='0.9'/><path d='M35 35 L65 35 L65 65 L35 65 Z' fill='%234F46E5' opacity='0.8'/><path d='M40 40 L60 40 L60 60 L40 60 Z' fill='%23ffffff' opacity='0.7'/></svg>">
        <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%234F46E5;stop-opacity:1' /><stop offset='100%' style='stop-color:%237C3AED;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='45' fill='url(%23grad)' stroke='%23ffffff' stroke-width='3'/><path d='M30 25 L70 25 L70 75 L30 75 Z' fill='%23ffffff' opacity='0.9'/><path d='M35 35 L65 35 L65 65 L35 65 Z' fill='%234F46E5' opacity='0.8'/><path d='M40 40 L60 40 L60 60 L40 60 Z' fill='%23ffffff' opacity='0.7'/></svg>">
        
        <!-- Page Title -->
        <title>@yield('title', 'كشف التزييف') | DeepFake Detection</title>
        
        <!-- Meta Tags -->
        <meta name="description" content="منصة متقدمة لكشف المحتوى المزيف باستخدام الذكاء الاصطناعي">
        <meta name="keywords" content="كشف التزييف, deepfake, ذكاء اصطناعي, تحليل محتوى">
        <meta name="author" content="DeepFake Detection Team">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <!-- Alpine.js for dropdown functionality -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Global SweetAlert fixes */
            .swal2-container {
                z-index: 9999 !important;
            }
            
            .swal2-backdrop-show {
                background: rgba(0, 0, 0, 0.4) !important;
            }
            
            .swal2-container.swal2-shown {
                background: rgba(0, 0, 0, 0.4) !important;
            }
            
            /* Ensure proper cleanup */
            .swal2-container:not(.swal2-shown) {
                display: none !important;
            }
            
            /* RTL Support for all SweetAlert popups */
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
                text-transform: none;
                letter-spacing: 0;
            }
            
            .swal2-confirm:hover,
            .swal2-cancel:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
            
            /* Close button styling */
            .swal2-close {
                position: absolute;
                top: 0;
                right: 0;
                width: 2rem;
                height: 2rem;
                background: transparent;
                border: none;
                font-size: 1.5rem;
                color: #6B7280;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                border-radius: 50%;
            }
            
            .swal2-close:hover {
                background-color: #F3F4F6;
                color: #374151;
                transform: scale(1.1);
            }

            body {
                font-family: 'Cairo', Arial, sans-serif !important;
                background: linear-gradient(to bottom right, #f3f4f6, #e0e7ff, #f5f3ff);
                direction: rtl;
                padding-top: 80px !important;
            }

            /* Fix header issues for different pages */
            .header-fixed {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 9999 !important;
                background: white !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
                border-bottom: 1px solid #e5e7eb !important;
                transition: all 0.3s ease !important;
            }

            .header-fixed.scrolled {
                background: white !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
            }

            /* Ensure header is always white and not transparent */
            .header-container {
                background: white !important;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
            }

            /* Force header to be fixed and white */
            header {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                z-index: 9999 !important;
                background: white !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            /* Mobile menu fixes */
            @media (max-width: 1023px) {
                .mobile-menu {
                    background: white !important;
                    border-left: 1px solid #e5e7eb !important;
                }
            }

            /* Fix header and footer width on mobile */
            @media (max-width: 768px) {
                header {
                    width: 100vw !important;
                    min-width: 100vw !important;
                    max-width: 100vw !important;
                    left: 0 !important;
                    right: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                
                .header-fixed {
                    width: 100vw !important;
                    min-width: 100vw !important;
                    max-width: 100vw !important;
                    left: 0 !important;
                    right: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                
                .header-container {
                    width: 100vw !important;
                    min-width: 100vw !important;
                    max-width: 100vw !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                
                footer {
                    width: 100vw !important;
                    min-width: 100vw !important;
                    max-width: 100vw !important;
                    left: 0 !important;
                    right: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                
                /* Prevent horizontal scroll */
                html, body {
                    overflow-x: hidden !important;
                    position: relative !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-right rtl">
        <div class="min-h-screen bg-gray-100 flex flex-col">
            <!-- Header -->
            <x-header />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <x-footer />
        </div>
        @stack('scripts')
        
        <!-- Custom Scripts -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global success messages
            @if(session('status'))
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: '{{ session('status') }}',
                    icon: 'success',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#10B981',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
            
            @if(session('success'))
                Swal.fire({
                    title: 'تم بنجاح!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#10B981',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
            
            @if(session('error'))
                Swal.fire({
                    title: 'خطأ!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#EF4444'
                });
            @endif
            
            @if($errors->any())
                Swal.fire({
                    title: 'خطأ في البيانات!',
                    html: '<ul class="text-right">' + 
                          '@foreach($errors->all() as $error)' +
                          '<li>{{ $error }}</li>' +
                          '@endforeach' +
                          '</ul>',
                    icon: 'error',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#EF4444'
                });
            @endif
        });
        </script>
    </body>
</html>
