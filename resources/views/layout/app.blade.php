<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'LMS')</title>
</head>
<body class="bg-gray-100">
    @include('components.sidebar') <!-- Sidebar tampil -->

    <main class="ml-64 p-4">
        @yield('content') <!-- Konten halaman -->
    </main>
</body>
</html>
