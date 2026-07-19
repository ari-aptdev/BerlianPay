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
    <title>Daftar Akun Warga - BerlianPay</title>
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
        <div class="w-12 h-12 rounded-xl bg-brand-600 flex items-center justify-center text-white mb-3">
            <i class="ti ti-diamond text-2xl"></i>
        </div>
        <h1 class="text-lg font-medium text-slate-900">Daftar Akun Warga</h1>
        <p class="text-sm text-slate-500 text-center">Isi data di bawah, akun kamu perlu diaktifkan admin dulu sebelum bisa login.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Nama lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">No. HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-slate-600 mb-1.5">Blok</label>
                    <input type="text" name="block" value="{{ old('block') }}" placeholder="Mis. B" maxlength="5" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1.5">No. Rumah</label>
                    <input type="number" name="house_number" value="{{ old('house_number') }}" placeholder="Mis. 17" min="1" max="99" required
                        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
                </div>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">NIK (16 digit)</label>
                <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600">
                <p class="text-xs text-slate-400 mt-1">Username & password login akan dibuatkan otomatis dan ditampilkan setelah kamu daftar.</p>
            </div>

            <label class="flex items-start gap-3 border border-slate-200 rounded-lg p-3 cursor-pointer">
                <input type="checkbox" name="wants_rukem" value="1" {{ old('wants_rukem') ? 'checked' : '' }} class="mt-0.5 rounded border-slate-300">
                <span>
                    <span class="block text-sm font-medium text-slate-700">Daftar sebagai anggota Rukem</span>
                    <span class="block text-xs text-slate-400">Iuran bulanan sedikit lebih besar, tapi ada biaya pendaftaran satu kali Rp {{ number_format($registrationFee, 0, ',', '.') }}.</span>
                </span>
            </label>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white rounded-lg py-2.5 text-sm font-medium transition">
                Daftar
            </button>
        </form>
    </div>

    <p class="text-center text-sm text-slate-500 mt-4">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-brand-600 font-medium">Login di sini</a>
    </p>
</div>

</body>
</html>
