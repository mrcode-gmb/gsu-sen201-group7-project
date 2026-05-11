<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="font-sans text-gray-900 antialiased">

    <!-- Navbar -->
    <header class="bg-blue-500 text-white sticky top-0 z-50">
        <nav class="container mx-auto flex justify-between items-center py-4 px-6">
            <a href="/" class="text-lg font-bold">{{ config('app.name', 'Palm Oil Shop') }}</a>
            <ul class="flex space-x-6">
                <li><a href="#about" class="hover:text-gray-200">About</a></li>
                <li><a href="#gallery" class="hover:text-gray-200">Gallery</a></li>
                <li><a href="#news" class="hover:text-gray-200">News</a></li>
                <li><a href="#faq" class="hover:text-gray-200">FAQ</a></li>
                <li><a href="#contact" class="hover:text-gray-200">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-blue-500 text-white text-center py-20">
        <h1 class="text-4xl font-bold mb-4">Welcome to Palm Oil Shop</h1>
        <p class="max-w-2xl mx-auto mb-6">Your trusted source for premium palm oil and related products.</p>
        <a href="#about" class="bg-white text-blue-500 px-6 py-3 rounded-md font-semibold hover:bg-blue-800 hover:text-white transition">Learn More</a>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-100">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">About Us</h2>
            <p class="max-w-3xl mx-auto text-gray-700">We specialize in quality palm oil sourcing and distribution, serving businesses and households nationwide.</p>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="py-16">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-8">Gallery</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <div class="bg-gray-200 h-48 rounded-lg"></div>
                <div class="bg-gray-200 h-48 rounded-lg"></div>
                <div class="bg-gray-200 h-48 rounded-lg"></div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="py-16 bg-gray-100">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">Latest News</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">News Item 1</div>
                <div class="bg-white p-6 rounded-lg shadow">News Item 2</div>
                <div class="bg-white p-6 rounded-lg shadow">News Item 3</div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-16">
        <div class="container mx-auto">
            <h2 class="text-3xl font-bold text-center mb-6">Frequently Asked Questions</h2>
            <div class="space-y-4 max-w-3xl mx-auto">
                <details class="bg-gray-100 p-4 rounded-lg">
                    <summary class="cursor-pointer font-semibold">What products do you offer?</summary>
                    <p class="mt-2 text-gray-700">We offer premium palm oil and related products for retail and wholesale customers.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-gray-100">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl font-bold mb-6">Contact Us</h2>
            <form class="max-w-lg mx-auto space-y-4">
                <input type="text" placeholder="Name" class="w-full border p-3 rounded">
                <input type="email" placeholder="Email" class="w-full border p-3 rounded">
                <textarea placeholder="Message" class="w-full border p-3 rounded"></textarea>
                <button type="submit" class="bg-blue-500 hover:bg-blue-800 text-white px-6 py-3 rounded-md font-semibold">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-500 text-white py-6 text-center">
        <p>&copy; {{ date('Y') }} Palm Oil Shop. All rights reserved.</p>
        <p class="mt-2 text-sm">Developed by <a href="https://aqtris.com" class="underline hover:text-gray-200">Aqtris Tech</a></p>
    </footer>

</body>
</html>
