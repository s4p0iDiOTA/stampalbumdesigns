<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark" />
    <title>Stamp Albums for Sale</title>
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
        <small>© 2024 Stamp Albums. All rights reserved.</small>
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
