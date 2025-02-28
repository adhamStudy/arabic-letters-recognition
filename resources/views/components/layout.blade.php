@props(['title'])
<!DOCTYPE html>
<html lang="en">

<head>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta charset="UTF-8">
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'The Workshop' }}</title>
</head>

<body class="bg-gray-200">


    <main class="container mx-auto px-4">
        {{ $slot }}
    </main>

</body>

</html>
