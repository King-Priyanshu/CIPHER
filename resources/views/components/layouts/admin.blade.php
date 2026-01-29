<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard – CIPHER' }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-navy: #1A2F4B;
            --color-teal: #00BFA6;
            --color-teal-dark: #00A893;
            --color-teal-light: #E0F7F4;
            --color-slate: #64748B;
            --color-slate-dark: #334155;
            --color-bg: #F8FAFC;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--color-bg);
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #1A2F4B 0%, #0F1E30 100%);
            box-shadow: 4px 0 24px rgba(26, 47, 75, 0.12);
        }

        .sidebar-link {
            position: relative;
            padding: 0.875rem 1.25rem;
            border-radius: 0.875rem;
            color: #94a3b8;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 0.9375rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 191, 166, 0.15), rgba(0, 191, 166, 0.05));
            opacity: 0;
            transition: opacity 0.25s ease;
            border-radius: 0.875rem;
        }

        .sidebar-link:hover::before {
            opacity: 1;
        }

        .sidebar-link:hover {
            color: #ffffff;
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(0, 191, 166, 0.2), rgba(0, 191, 166, 0.1));
            color: #00BFA6;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 191, 166, 0.15);
        }

        .sidebar-link.active::after {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 65%;
            border-radius: 0 999px 999px 0;
            background: linear-gradient(180deg, #00BFA6, #00D4B8);
            box-shadow: 0 2px 8px rgba(0, 191, 166, 0.4);
        }

        .sidebar-link svg {
            width: 1.25rem;
            height: 1.25rem;
            stroke-width: 2;
            flex-shrink: 0;
        }

        /* Logo Styles */
        .logo-wrapper {
            background: linear-gradient(135deg, rgba(0, 191, 166, 0.15), rgba(0, 191, 166, 0.05));
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
        }

        .logo-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #00BFA6, #00D4B8);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0, 191, 166, 0.3);
            position: relative;
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #00BFA6, #00D4B8);
            opacity: 0.3;
            filter: blur(8px);
            z-index: -1;
        }

        .logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 0.02em;
            background: linear-gradient(135deg, #ffffff 0%, #e0f7f4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Topbar Styles */
        .topbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(100, 116, 139, 0.1);
            box-shadow: 0 2px 16px rgba(26, 47, 75, 0.04);
        }

        .topbar-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--color-navy);
            letter-spacing: -0.01em;
        }

        .topbar-link {
            color: var(--color-slate);
            font-weight: 500;
            transition: all 0.2s ease;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
        }

        .topbar-link:hover {
            color: var(--color-teal);
            background: var(--color-teal-light);
        }

        /* User Section */
        .user-section {
            background: linear-gradient(135deg, rgba(0, 191, 166, 0.08), rgba(0, 191, 166, 0.03));
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1rem 1rem 0 0;
            margin: 0 0.75rem;
            padding: 1.25rem;
        }

        .user-avatar {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #00BFA6, #00D4B8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.125rem;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 191, 166, 0.25);
        }

        .user-name {
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
        }

        .user-role {
            color: #94a3b8;
            font-size: 0.8125rem;
            font-weight: 500;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 8px 24px rgba(26, 47, 75, 0.06);
            border: 1px solid rgba(100, 116, 139, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 12px 32px rgba(26, 47, 75, 0.1);
            transform: translateY(-2px);
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            padding: 0.625rem;
            border-radius: 0.75rem;
            background: var(--color-teal-light);
            color: var(--color-teal);
            transition: all 0.2s ease;
        }

        .mobile-menu-btn:hover {
            background: var(--color-teal);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 191, 166, 0.2);
        }

        /* Scrollbar Styles */
        .sidebar nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar nav::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 999px;
        }

        .sidebar nav::-webkit-scrollbar-thumb {
            background: rgba(0, 191, 166, 0.3);
            border-radius: 999px;
        }

        .sidebar nav::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 191, 166, 0.5);
        }

        /* Animations */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sidebar-link {
            animation: slideInRight 0.3s ease backwards;
        }

        .sidebar-link:nth-child(1) { animation-delay: 0.05s; }
        .sidebar-link:nth-child(2) { animation-delay: 0.1s; }
        .sidebar-link:nth-child(3) { animation-delay: 0.15s; }
        .sidebar-link:nth-child(4) { animation-delay: 0.2s; }
        .sidebar-link:nth-child(5) { animation-delay: 0.25s; }
        .sidebar-link:nth-child(6) { animation-delay: 0.3s; }
        .sidebar-link:nth-child(7) { animation-delay: 0.35s; }
    </style>
</head>

<body class="h-full bg-slate-50 antialiased">
<div class="min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed lg:static inset-y-0 left-0 z-40 w-64 sidebar transform transition-transform duration-300 lg:translate-x-0">

        <div class="flex flex-col h-full">

            <!-- Logo -->
            <div class="h-16 flex items-center justify-center logo-wrapper">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5">
                    <div class="logo-icon">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="logo-text">CIPHER</span>
                </a>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-4 py-8 space-y-2 overflow-y-auto">
                @php
                    $links = [
                        ['route'=>'admin.dashboard','label'=>'Dashboard','icon'=>'home'],
                        ['route'=>'admin.users.index','label'=>'Users','icon'=>'users'],
                        ['route'=>'admin.projects.index','label'=>'Projects','icon'=>'folder'],
                        ['route'=>'admin.plans.index','label'=>'Subscription Plans','icon'=>'clipboard'],
                        ['route'=>'admin.pools.index','label'=>'Fund Pools','icon'=>'wallet'],
                        ['route'=>'admin.reward-pools.index','label'=>'Reward Pools','icon'=>'currency'],
                        ['route'=>'admin.pages.index','label'=>'Content Pages','icon'=>'document'],
                    ];

                    $icons = [
                        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'folder' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                        'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                        'currency' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
                    ];
                @endphp

                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="sidebar-link {{ request()->routeIs($link['route'].'*') ? 'active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$link['icon']] ?? 'M3 12h18M3 6h18M3 18h18' }}"/>
                        </svg>
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- User Section -->
            <div class="user-section">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="user-name truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="user-role">Super Admin</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full justify-start">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen=false"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden"
         style="display: none;"></div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Topbar -->
        <header class="h-16 topbar flex items-center justify-between px-6 lg:px-8 sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen=!sidebarOpen"
                        class="mobile-menu-btn lg:hidden">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <h1 class="topbar-title">
                    @yield('page_title', $title ?? 'Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="topbar-link hidden sm:flex items-center gap-2 text-sm">
                    <span>View Site</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                
                <div class="hidden sm:flex items-center gap-2 text-sm text-slate-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">{{ now()->format('D, M j, Y') }}</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            @yield('content')
        </main>

    </div>
</div>
</body>
</html>