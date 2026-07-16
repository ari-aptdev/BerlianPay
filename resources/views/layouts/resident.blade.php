<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BerlianPay</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { brand: {
            50: '#eef4ff', 100: '#d9e6ff', 500: '#1d4ed8', 600: '#1a45c0', 700: '#153a9e',
        } } } } }
    </script>
    <style>
        html, body { overflow-x: hidden; max-width: 100vw; }
        .dark body { background-color: #020617; }
        .dark .bg-slate-50 { background-color: #0f172a; }
        .dark .bg-white { background-color: #1e293b; }
        .dark .bg-slate-100 { background-color: #334155; }
        .dark .border-slate-100 { border-color: #334155; }
        .dark .border-slate-200 { border-color: #334155; }
        .dark .text-slate-900 { color: #f8fafc; }
        .dark .text-slate-800 { color: #e2e8f0; }
        .dark .text-slate-700 { color: #cbd5e1; }
        .dark .text-slate-600 { color: #94a3b8; }
        .dark .text-slate-500 { color: #94a3b8; }
        .dark .text-slate-400 { color: #64748b; }
        .dark .bg-brand-50 { background-color: rgba(26,69,192,0.18); }
        .dark .bg-brand-100 { background-color: rgba(26,69,192,0.28); }
        .dark .text-brand-700 { color: #93b4ff; }
        .dark .bg-green-50 { background-color: rgba(34,197,94,0.15); }
        .dark .text-green-700 { color: #4ade80; }
        .dark .border-green-100 { border-color: rgba(34,197,94,0.25); }
        .dark .bg-red-50 { background-color: rgba(239,68,68,0.15); }
        .dark .text-red-700 { color: #f87171; }
        .dark .text-red-600 { color: #f87171; }
        .dark .border-red-100 { border-color: rgba(239,68,68,0.25); }
        .dark .bg-amber-50 { background-color: rgba(245,158,11,0.15); }
        .dark .text-amber-700 { color: #fbbf24; }
        .dark .text-amber-800 { color: #fbbf24; }
        .dark .border-amber-200 { border-color: rgba(245,158,11,0.3); }
        .dark input, .dark select, .dark textarea {
            background-color: #1e293b;
            border-color: #334155 !important;
            color: #e2e8f0;
        }
        .dark input::placeholder { color: #64748b; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">

<header class="bg-white border-b border-slate-200 sticky top-0 z-10">
    <div class="max-w-md mx-auto px-3 sm:px-4 h-14 flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-md bg-brand-600 flex items-center justify-center text-white flex-shrink-0">
                <i class="ti ti-diamond text-sm"></i>
            </div>
            <span class="font-medium text-sm text-slate-900 truncate">BerlianPay</span>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            <button onclick="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';"
                    class="text-slate-400 p-1">
                <i class="ti ti-sun text-lg dark:hidden"></i>
                <i class="ti ti-moon text-lg hidden dark:inline"></i>
            </button>

            @php
                $unreadNotifs = auth()->user()->residentNotifications()->whereNull('read_at')->latest()->limit(10)->get();
                $unreadCount = $unreadNotifs->count();
            @endphp
            <div class="relative">
                <button onclick="document.getElementById('residentNotifDropdown').classList.toggle('hidden'); fetch('{{ route('resident.notifications.mark-read') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});"
                        class="text-slate-400 relative p-1">
                    <i class="ti ti-bell text-lg"></i>
                    @if ($unreadCount > 0)
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <div id="residentNotifDropdown" class="hidden absolute right-0 mt-2 w-72 max-w-[85vw] bg-white border border-slate-200 rounded-xl shadow-lg z-20 overflow-hidden">
                    <div class="px-4 py-2 border-b border-slate-100 text-sm font-medium text-slate-700">Notifikasi</div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse (auth()->user()->residentNotifications()->latest()->limit(10)->get() as $notif)
                            <a href="{{ $notif->url ?? '#' }}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 {{ $notif->read_at ? '' : 'bg-brand-50/40' }}">
                                <p class="text-sm font-medium text-slate-700">{{ $notif->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $notif->message }}</p>
                                <p class="text-[11px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                            </a>
                        @empty
                            <p class="px-4 py-4 text-sm text-slate-400">Belum ada notifikasi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <a href="{{ route('resident.profile.edit') }}" class="text-slate-400 p-1">
                <i class="ti ti-user text-lg"></i>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-slate-400 text-sm flex items-center gap-1.5 p-1">
                    <i class="ti ti-logout"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<main class="max-w-md mx-auto px-4 py-5 pb-24">
    @if (session('success'))
        <div class="mb-4 bg-green-50 text-green-700 border border-green-200 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

<nav class="fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 md:hidden">
    <div class="max-w-md mx-auto grid grid-cols-2">
        <a href="{{ route('resident.dashboard') }}" class="flex flex-col items-center py-2.5 text-xs {{ request()->routeIs('resident.dashboard') ? 'text-brand-600' : 'text-slate-400' }}">
            <i class="ti ti-layout-dashboard text-lg mb-0.5"></i> Status Bulan Ini
        </a>
        <a href="{{ route('resident.payments.index') }}" class="flex flex-col items-center py-2.5 text-xs {{ request()->routeIs('resident.payments.*') ? 'text-brand-600' : 'text-slate-400' }}">
            <i class="ti ti-receipt text-lg mb-0.5"></i> Riwayat
        </a>
    </div>
</nav>

</body>
</html>
