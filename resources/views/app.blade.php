<!D        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- PWA Meta Tags -->
        <meta name="description" content="GoMoto - Book It, Ride It, Love It! Your premier vehicle rental service">
        <meta name="theme-color" content="#4F46E5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="GoMoto">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="GoMoto">
        <meta name="format-detection" content="telephone=no">
        <meta name="msapplication-TileColor" content="#4F46E5">
        <meta name="msapplication-tap-highlight" content="no">`html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- PWA Meta Tags -->
        <meta name="description" content="GoMoto - Book It, Ride It, Love It! Your premier vehicle rental service">
        <meta name="theme-color" content="#4F46E5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="GoMoto">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="application-name" content="GoMoto">
        
        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        
        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" href="/icons/icon-152x152.svg">
        <link rel="apple-touch-icon" sizes="72x72" href="/icons/icon-72x72.svg">
        <link rel="apple-touch-icon" sizes="96x96" href="/icons/icon-96x96.svg">
        <link rel="apple-touch-icon" sizes="128x128" href="/icons/icon-128x128.svg">
        <link rel="apple-touch-icon" sizes="144x144" href="/icons/icon-144x144.svg">
        <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.svg">
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.svg">
        <link rel="apple-touch-icon" sizes="384x384" href="/icons/icon-384x384.svg">
        <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.svg">
        
        <!-- Standard Favicon -->
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/svg+xml" sizes="32x32" href="/icons/icon-32x32.svg">
        <link rel="icon" type="image/svg+xml" sizes="16x16" href="/icons/icon-16x16.svg">
        <link rel="shortcut icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
