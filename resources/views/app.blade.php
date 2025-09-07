<!D <meta charset="utf-8">
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

        <!-- Icons -->
        <link rel="icon" type="image/png" href="icon2/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="icon2/favicon.svg" />
        <link rel="shortcut icon" href="icon2/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="icon2/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="GoMoto" />
        <link rel="manifest" href="icon2/site.webmanifest" />

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