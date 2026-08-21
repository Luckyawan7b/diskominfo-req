<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Manajemen Risiko SPBE Desa">
    <title>{{ $title ?? 'Manajemen Risiko' }} — SPBE Desa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-900 antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 z-30 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-800/95 backdrop-blur-sm border-r border-slate-700/50 transform transition-transform duration-300 ease-in-out lg:translate-x-0">
        {{-- Sidebar header --}}
        <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-700/50">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <span class="text-white font-semibold text-sm">Manajemen Risiko</span>
                <p class="text-slate-500 text-xs">SPBE Desa</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="p-3 space-y-1">
            {{-- Kembali ke Hub --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="border-t border-slate-700/50 my-2"></div>

            {{-- Konteks Risiko --}}
            <a href="{{ route('konteks.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('konteks.*') || request()->routeIs('sasaran.*') || request()->routeIs('struktur.*') || request()->routeIs('risiko.*') || request()->routeIs('pemantauan.*') ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Konteks Risiko
            </a>

            @if(auth()->user()->isAdmin())
                <div class="border-t border-slate-700/50 my-2"></div>
                <p class="px-3 py-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</p>

                {{-- Review & Approval --}}
                <a href="{{ route('admin.review.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.review.*') ? 'bg-amber-500/10 text-amber-400 font-medium' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Review & Approval
                </a>

                {{-- Kelola Desa --}}
                <a href="{{ route('admin.desa.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.desa.*') ? 'bg-violet-500/10 text-violet-400 font-medium' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Kelola Desa
                </a>

                {{-- Kelola User --}}
                <a href="{{ route('admin.user.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.user.*') ? 'bg-violet-500/10 text-violet-400 font-medium' : 'text-slate-300 hover:text-white hover:bg-slate-700/50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Kelola User
                </a>
            @endif
        </nav>
    </aside>

    {{-- Main content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">
        {{-- Topbar --}}
        <header class="h-16 bg-slate-800/50 backdrop-blur-sm border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-20">
            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-300 transition-colors">Dashboard</a>
                @if(isset($breadcrumb))
                    @foreach($breadcrumb as $label => $url)
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        @if($url)
                            <a href="{{ $url }}" class="text-slate-400 hover:text-slate-200 transition-colors">{{ $label }}</a>
                        @else
                            <span class="text-slate-200 font-medium">{{ $label }}</span>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- User menu --}}
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->role->label }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors cursor-pointer" title="Keluar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 p-4 text-emerald-400 text-sm">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4 text-red-400 text-sm">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
