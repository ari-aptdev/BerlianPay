<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BerlianPay</title>
    <script>
        // Cek preferensi tema sebelum halaman render, biar gak "kedip" putih dulu
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef4ff', 100: '#d9e6ff', 500: '#1d4ed8',
                            600: '#1a45c0', 700: '#153a9e', 900: '#0f2a70',
                        },
                    },
                },
            },
        }
    </script>
    <style>
        /* Override universal dark mode - berlaku ke semua halaman tanpa perlu ubah tiap view */
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
        .dark .text-red-500 { color: #f87171; }
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
        .dark input::placeholder, .dark textarea::placeholder { color: #64748b; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
<div class="flex min-h-screen">
    <aside class="w-64 bg-white border-r border-slate-200 flex-shrink-0 hidden md:flex md:flex-col">
        <div class="h-16 flex items-center gap-2 px-5 border-b border-slate-100">
            <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white">
                <i class="ti ti-diamond text-lg"></i>
            </div>
            <span class="font-semibold text-slate-900">BerlianPay</span>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard', 'icon' => 'ti-layout-dashboard', 'label' => 'Dashboard'],
                    ['route' => 'admin.houses.index', 'icon' => 'ti-home', 'label' => 'Data warga'],
                    ['route' => 'admin.ipl-rates.index', 'icon' => 'ti-receipt', 'label' => 'Tarif IPL'],
                    ['route' => 'admin.payments.index', 'icon' => 'ti-cash', 'label' => 'Pembayaran'],
                    ['route' => 'admin.reports.index', 'icon' => 'ti-file-export', 'label' => 'Laporan'],
                    ['route' => 'admin.residents.index', 'icon' => 'ti-users', 'label' => 'Akun warga'],
                    ['route' => 'admin.settings.reminder', 'icon' => 'ti-bell', 'label' => 'Pengaturan reminder'],
                    ['route' => 'admin.reminder-logs.index', 'icon' => 'ti-history', 'label' => 'Log reminder'],
                ];
            @endphp
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs(explode('.index', $item['route'])[0].'*') ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="ti {{ $item['icon'] }} text-base"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="p-3 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-500 hover:bg-slate-50 w-full">
                    <i class="ti ti-logout text-base"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8">
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('mobileNav').classList.toggle('hidden')" class="md:hidden text-slate-500">
                    <i class="ti ti-menu-2 text-xl"></i>
                </button>
                <h1 class="font-medium text-slate-900">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.documentElement.classList.toggle('dark'); localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';"
                        class="w-9 h-9 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100">
                    <i class="ti ti-sun text-lg dark:hidden"></i>
                    <i class="ti ti-moon text-lg hidden dark:inline"></i>
                </button>
                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-medium">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button class="flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 ml-2">
                        <i class="ti ti-logout text-base"></i> Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Menu mobile: dropdown penuh berisi navigasi + logout, karena sidebar di atas hilang total di layar kecil -->
        <div id="mobileNav" class="hidden md:hidden bg-white border-b border-slate-200 px-3 py-3 space-y-1">
            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs(explode('.index', $item['route'])[0].'*') ? 'bg-brand-50 text-brand-700 font-medium' : 'text-slate-600' }}">
                    <i class="ti {{ $item['icon'] }} text-base"></i>
                    {{ $item['label'] }}
                </a>
            @endforeach
            <form method="POST" action="{{ route('logout') }}" class="pt-1 border-t border-slate-100 mt-1">
                @csrf
                <button class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-500 w-full">
                    <i class="ti ti-logout text-base"></i> Keluar
                </button>
            </form>
        </div>

        <main class="flex-1 p-4 md:p-8">
            @if (session('success'))
                <div class="mb-4 bg-green-50 text-green-700 border border-green-200 rounded-lg px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
