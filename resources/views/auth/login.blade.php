<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a45c0">
    <link rel="icon" href="/icons/favicon-64.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="BerlianPay">
    <script src="/js/pwa.js" defer></script>
    <title>Login - BerlianPay</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { brand: {
            50: '#eef4ff', 600: '#1a45c0', 700: '#153a9e',
        } } } } }
    </script>
    <style>
        html, body { overflow-x: hidden; max-width: 100vw; }
        .dark body { background-color: #020617; }
        .dark .bg-slate-50 { background-color: #0f172a; }
        .dark .bg-white { background-color: #1e293b; }
        .dark .border-slate-200 { border-color: #334155; }
        .dark .text-slate-900 { color: #f8fafc; }
        .dark .text-slate-600 { color: #94a3b8; }
        .dark .text-slate-500 { color: #94a3b8; }
        .dark .text-slate-400 { color: #64748b; }
        .dark .bg-red-50 { background-color: rgba(239,68,68,0.15); }
        .dark .text-red-700 { color: #f87171; }
        .dark .bg-amber-50 { background-color: rgba(245,158,11,0.15); }
        .dark .text-amber-700 { color: #fbbf24; }
        .dark .border-amber-200 { border-color: rgba(245,158,11,0.3); }
        .dark input {
            background-color: #1e293b;
            border-color: #334155 !important;
            color: #e2e8f0;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    <div class="flex flex-col items-center mb-6">
        <img src="/icons/logo-mark.png" alt="BerlianPay" class="w-14 h-14 rounded-xl object-contain mb-3">
        <h1 class="text-lg font-medium text-slate-900">BerlianPay</h1>
        <p class="text-sm text-slate-500">Sistem pencatatan IPL perumahan</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        @if (session('idle_timeout'))
            <div class="mb-4 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg px-4 py-3 text-sm">
                <i class="ti ti-clock"></i> {{ session('idle_timeout') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Username</label>
                <input type="text" name="login" value="{{ old('login') }}" required autofocus
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-500">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Ingat saya
            </label>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white rounded-lg py-2.5 text-sm font-medium transition">
                Masuk
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">&copy; {{ date('Y') }} BerlianPay</p>

    <p class="text-center text-sm text-slate-500 mt-3">
        Warga baru? <a href="{{ route('register') }}" class="text-brand-600 font-medium">Daftar akun di sini</a>
    </p>
</div>

</body>
</html>
