<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - BerlianPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <script>
        tailwind.config = {
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
            <h1 class="font-medium text-slate-900">{{ $title ?? 'Dashboard' }}</h1>
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-sm font-medium">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <span class="text-sm text-slate-600 hidden sm:block">{{ auth()->user()->name }}</span>
            </div>
        </header>

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
