<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center sm:pt-0 bg-gray-100">
        
            <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md max-md:h-screen overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        <script>
            document.addEventListener('submit', function(event) {
                const form = event.target;

                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') {
                    return;
                }

                if (form.dataset.noLoader === 'true') {
                    return;
                }

                if (form.dataset.submitting === '1') {
                    event.preventDefault();
                    return;
                }

                form.dataset.submitting = '1';

                const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                const submitter = event.submitter || submitButtons[0];

                submitButtons.forEach(function(button) {
                    button.disabled = true;
                    button.classList.add('opacity-60', 'cursor-not-allowed');
                });

                if (!submitter) {
                    return;
                }

                const loadingText = submitter.dataset.loadingText || 'Processing...';
                const spinner = '<svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

                if (submitter.tagName === 'BUTTON') {
                    if (!submitter.dataset.originalHtml) {
                        submitter.dataset.originalHtml = submitter.innerHTML;
                    }
                    submitter.innerHTML = '<span class="inline-flex items-center gap-2">' + spinner + '<span>' + loadingText + '</span></span>';
                } else if (submitter.tagName === 'INPUT') {
                    if (!submitter.dataset.originalValue) {
                        submitter.dataset.originalValue = submitter.value;
                    }
                    submitter.value = loadingText;
                }
            });
        </script>
    </body>
</html>
