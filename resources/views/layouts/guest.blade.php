<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            <!-- Tailwind CSS CDN -->
            <script src="https://cdn.tailwindcss.com"></script>
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{ $slot }}
        
        <!-- JavaScript to prevent back button access to protected pages -->
        <script>
            // Prevent back button access to protected pages after logout
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache (back button), redirect to login
                    window.location.href = '{{ route("login") }}';
                }
            });
            
            // Clear any cached pages in browser history
            window.addEventListener('beforeunload', function() {
                // This helps prevent cached pages from being accessible
                if (window.history.length > 1) {
                    window.history.replaceState(null, null, window.location.href);
                }
            });
            
            // Additional protection: redirect if user tries to go back
            window.addEventListener('popstate', function(event) {
                // If user tries to go back, redirect to login
                window.location.href = '{{ route("login") }}';
            });
            
            // Push a new state to the history to prevent back navigation
            window.history.pushState(null, null, window.location.href);
            
            // Clear browser cache and localStorage on login page
            if (window.performance && window.performance.navigation.type === 1) {
                // Page was refreshed, clear any cached data
                localStorage.clear();
                sessionStorage.clear();
            }
            
            // Force reload without cache if coming from logout
            if (window.location.search.includes('logout') || window.location.hash.includes('logout')) {
                window.location.replace(window.location.origin + window.location.pathname);
            }
        </script>
    </body>
</html>
