<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Page</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/app.css']) --}}
    <link rel="stylesheet" href="http://[::1]:5173/resources/css/app.css">
    <script type="module" src="http://[::1]:5173/resources/js/app.js"></script>
    <link rel="stylesheet" href="http://[::1]:5173/resources/sass/app.scss">
        {{-- <link rel="stylesheet" href="app.css"> --}}
</head>
<body>
    <div class="layout-wrapper" id="app">
        @include('includes.adminsidebar')

        <main class="py-4">
            @yield('admincontent')
        </main>
    </div>
    <script>
        document.getElementById('toggleSidebarBtn').addEventListener('click', function () {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('sidebar-collapsed');
    
            const texts = sidebar.querySelectorAll('.sidebar-text');
            texts.forEach(text => {
                text.style.display = sidebar.classList.contains('sidebar-collapsed') ? 'inline' : 'none';
            });
        });
    </script>
<script>
    function toggleSubmenu(button) {
        const submenu = button.nextElementSibling;
        const arrow = button.querySelector('.arrow');
    
        submenu.classList.toggle('show');
        arrow.classList.toggle('rotate');
    }
    
    </script>
    
    
    
</body>
</html>