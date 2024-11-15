@php use Illuminate\Support\Facades\Auth; @endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!-- Fonts -->
    {{--        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">--}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=blinker:100,300,700" rel="stylesheet"/>

</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-50">
    @include('layouts.nav',['slot' => $slot, 'user' => Auth::user()])

    <!-- Page Heading -->
    {{--            @if (isset($header))--}}
    {{--                <header class="bg-white shadow">--}}
    {{--                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">--}}
    {{--                        {{ $header }}--}}
    {{--                    </div>--}}
    {{--                </header>--}}
    {{--            @endif--}}

    <!-- Page Content -->
    {{--            <main>--}}
    {{--                {{ $slot }}--}}
    {{--            </main>--}}
</div>
@yield('page-js-script')
</body>
</html>
