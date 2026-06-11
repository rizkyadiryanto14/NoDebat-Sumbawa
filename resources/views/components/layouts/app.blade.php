<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'NoDebat') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col">
        @auth
            <x-navbar />
        @endauth

        <main class="flex-1 pb-24 md:pb-8">
            <div class="mx-auto w-full max-w-6xl px-4 py-6 md:px-6 md:py-8">
                @if(isset($header))
                    <div class="mb-6">
                        {{ $header }}
                    </div>
                @endif

                <x-flash-message />

                {{ $slot }}
            </div>
        </main>

        @auth
            <x-bottom-nav />
        @endauth
    </div>
</body>
</html>
