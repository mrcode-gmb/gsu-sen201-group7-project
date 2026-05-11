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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    <style>
        .sidebar-link {
            @apply flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200;
        }

        .sidebar-link.active {
            @apply bg-blue-100 text-blue-700 border-r-4 border-blue-700;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="menuBtn"
            class="hidden max-md:flex fixed w-10 h-10 bg-white rounded-lg shadow-md right-3 top-2 text-white"
            onclick="toggleMobileMenu()">
            <!-- Hamburger Icon -->
            <svg id="hamburgerIcon" class="w-6 h-6 text-gray-700 m-auto" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Close Icon -->
            <svg id="closeIcon" class="w-6 h-6 text-gray-700 m-auto hidden" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <div class="w-64 max-md:hidden max-md:fixed max-md:h-screen bg-white overflow-auto shadow-lg">
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-blue-600 text-white">
                <div class="text-center leading-tight">
                    <h1 class="text-lg font-bold">Inventory & Warehouse</h1>
                    <p class="text-xs text-blue-100">{{ auth()->user()->business->name ?? 'Academic Project' }}</p>
                </div>
            </div>

            <!-- User Info -->
            <div class="p-4 border-b">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4">
                @if (auth()->user()->isAdmin())
                    <!-- Admin Navigation -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('inventory.index') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('inventory.index') || request()->routeIs('inventory.create') || request()->routeIs('inventory.show') || request()->routeIs('inventory.edit') || request()->routeIs('inventory.adjust-stock') || request()->routeIs('inventory.adjustStockDelete') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Inventory
                    </a>

                    <a href="{{ route('inventory.categories.index') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('inventory.categories.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.53 0 1.039.211 1.414.586l7 7a2 2 0 010 2.828l-6.586 6.586a2 2 0 01-2.828 0l-7-7A2 2 0 013 11V6a3 3 0 013-3z"></path>
                        </svg>
                        Categories
                    </a>

                    <a href="{{ route('purchases.index') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Goods Receipt
                    </a>

                    <a href="{{ route('sales.index') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.index') || request()->routeIs('sales.show') || request()->routeIs('sales.edit') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        Goods Dispatch
                    </a>

                    <a href="{{ route('sales.create') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Record Goods Dispatch
                    </a>

                    <a href="{{ route('expenses.index') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l2-2m0 0l2-2m-2 2v6m8-6a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        Expenses
                    </a>
                    <a href="{{ route('admin.myStaff') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        Staffs
                    </a>

                    <a href="{{ route('reports.index') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Reports
                    </a>

                    <a href="{{ route('admin.activity-log') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Activity Log
                    </a>
                @else
                    <!-- Salesperson Navigation -->
                    <a href="{{ route('sales.dashboard') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        <p>Dashboard</p>
                    </a>

                    <a href="{{ route('sales.create') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Record Goods Dispatch
                    </a>

                    <a href="{{ route('sales.my-sales') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.my-sales') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Dispatch Records
                    </a>

                    <a href="{{ route('sales.inventory') }}"
                        class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.inventory') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        View Inventory
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}"
                    class="sidebar-link flex py-3 px-5 {{ request()->routeIs('sales.inventory') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Profile
                </a>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button type="submit"
                        class="sidebar-link flex py-3 px-5 w-full text-left text-red-600 hover:bg-red-50">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-full mx-auto py-4 px-6">
                    @if (isset($header))
                        {{ $header }}
                    @endif
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                        role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                        role="alert">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Menu Toggle Script -->
    <script>
        function toggleMobileMenu() {
            const sidebar = document.querySelector('.w-64');
            const hamburgerIcon = document.getElementById("hamburgerIcon");
            const closeIcon = document.getElementById("closeIcon");

            if (sidebar.classList.contains("max-md:hidden")) {
                sidebar.classList.remove('max-md:hidden');
                sidebar.classList.add('max-md:block');

                // Switch icons
                hamburgerIcon.classList.add("hidden");
                closeIcon.classList.remove("hidden");
            } else {
                sidebar.classList.add('max-md:hidden');
                sidebar.classList.remove('max-md:block');

                // Switch icons back
                closeIcon.classList.add("hidden");
                hamburgerIcon.classList.remove("hidden");
            }
        }
    </script>
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
