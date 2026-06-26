<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HRM System') - HRM</title>
    @vite('resources/js/app.js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Smooth sidebar transition */
        #sidebar {
            transition: width 0.3s ease-in-out, min-width 0.3s ease-in-out;
            width: 16rem; /* w-64 = 16rem */
            min-width: 16rem;
        }
        
        #sidebar.collapsed {
            width: 4rem !important;
            min-width: 4rem !important;
        }
        
        #sidebar.collapsed .sidebar-text,
        #sidebar.collapsed .sidebar-section-title .sidebar-text {
            display: none;
        }
        
        #sidebar.collapsed .sidebar-section-title .sidebar-icon-only {
            display: block !important;
        }
        
        #sidebar.collapsed nav a {
            justify-content: center;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        #sidebar.collapsed nav a .mr-3 {
            margin-right: 0 !important;
        }
        
        #sidebar.collapsed > div {
            text-align: center;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        #sidebar.collapsed > div i {
            margin-right: 0 !important;
        }
        
        #sidebar.collapsed .sidebar-section-title {
            text-align: center;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        /* Ensure main content adjusts properly */
        .flex-1 {
            min-width: 0; /* Prevent flex item overflow issues */
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        @include('layouts.partials.sidebar')
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            @include('layouts.partials.header')
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
                @endif
                @yield('content')
            </main>
            @include('layouts.partials.footer')
        </div>
    </div>
    @include('layouts.partials.scripts')
    <script>
        $(document).ready(function() {
            var $sidebar = $('#sidebar');
            var $toggleBtn = $('#sidebarToggle');
            if (!$sidebar.length || !$toggleBtn.length) return;

            // Restore sidebar state from localStorage
            var collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (collapsed) {
                $sidebar.addClass('collapsed');
                updateToggleIcon(true);
            } else {
                updateToggleIcon(false);
            }

            $toggleBtn.on('click', function() {
                $sidebar.toggleClass('collapsed');
                var isCollapsed = $sidebar.hasClass('collapsed');
                updateToggleIcon(isCollapsed);
                localStorage.setItem('sidebar_collapsed', isCollapsed);
            });

            function updateToggleIcon(isCollapsed) {
                var $icon = $toggleBtn.find('i');
                if (!$icon.length) return;

                if (isCollapsed) {
                    $icon.removeClass('fa-bars').addClass('fa-chevron-right');
                    $toggleBtn.attr({ 'aria-label': 'Expand sidebar', 'title': 'Expand sidebar' });
                } else {
                    $icon.removeClass('fa-chevron-right').addClass('fa-bars');
                    $toggleBtn.attr({ 'aria-label': 'Collapse sidebar', 'title': 'Collapse sidebar' });
                }
            }
        });
    </script>
</body>
</html>