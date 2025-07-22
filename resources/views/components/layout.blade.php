<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark" />
    <title>Professional Stamp Album Pages | Stamp Album Designs</title>
    <meta name="description" content="Premium printed stamp album pages for over 300 countries. Professional quality heavyweight paper. Order entire countries or specific years.">
    <link rel="icon" type="image/png" href="{{ asset('logo-original.png') }}">
    <!-- Fluid viewport -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.slate.min.css">
    <link rel="stylesheet" href="{{ asset('/css/main.css')}}">

</head>

<body>

    <!-- Header -->
    <header class="container">
        <x-navigation />
    </header>

    <!-- Main Content -->
    <main class="container">
        <article>

            {{ $slot }}

    </main>

    <!-- Footer -->
    <footer>
        <small>© 2024 Stamp Album Designs. All rights reserved.</small>
        <nav>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </nav>
    </footer>
    </article>


</body>

</html>
