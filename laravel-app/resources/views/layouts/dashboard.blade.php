<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%234F46E5;stop-opacity:1' /><stop offset='100%' style='stop-color:%237C3AED;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='45' fill='url(%23grad)' stroke='%23ffffff' stroke-width='3'/><path d='M30 25 L70 25 L70 75 L30 75 Z' fill='%23ffffff' opacity='0.9'/><path d='M35 35 L65 35 L65 65 L35 65 Z' fill='%234F46E5' opacity='0.8'/><path d='M40 40 L60 40 L60 60 L40 60 Z' fill='%23ffffff' opacity='0.7'/></svg>">
    <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%234F46E5;stop-opacity:1' /><stop offset='100%' style='stop-color:%237C3AED;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='45' fill='url(%23grad)' stroke='%23ffffff' stroke-width='3'/><path d='M30 25 L70 25 L70 75 L30 75 Z' fill='%23ffffff' opacity='0.9'/><path d='M35 35 L65 35 L65 65 L35 65 Z' fill='%234F46E5' opacity='0.8'/><path d='M40 40 L60 40 L60 60 L40 60 Z' fill='%23ffffff' opacity='0.7'/></svg>">
    
    <!-- Page Title -->
    <title>@yield('title', 'كشف التزييف - لوحة التحكم') | DeepFake Detection</title>
    
    <!-- Meta Tags -->
    <meta name="description" content="منصة متقدمة لكشف المحتوى المزيف باستخدام الذكاء الاصطناعي">
    <meta name="keywords" content="كشف التزييف, deepfake, ذكاء اصطناعي, تحليل محتوى">
    <meta name="author" content="DeepFake Detection Team">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Force header to be fixed and white in all pages */
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

        /* Add padding to body to account for fixed header */
        body {
            padding-top: 80px !important;
        }

        /* Header container styles */
        .header-fixed {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            background: white !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .header-container {
            background: white !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1) !important;
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
<body class="bg-gray-100 text-gray-800 font-sans flex flex-col min-h-screen">

    <!-- Header -->
    <x-header />

    <!-- ✅ Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8 flex-1">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <x-footer />
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')
    
    <!-- Global SweetAlert Messages -->
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
