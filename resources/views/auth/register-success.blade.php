<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - BerlianPay</title>
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
        .dark .bg-amber-50 { background-color: rgba(245,158,11,0.15); }
        .dark .text-amber-700, .dark .text-amber-800 { color: #fbbf24; }
        .dark .border-amber-200 { border-color: rgba(245,158,11,0.3); }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    <div class="flex flex-col items-center mb-6">
        <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center text-white mb-3">
            <i class="ti ti-check text-2xl"></i>
        </div>
        <h1 class="text-lg font-medium text-slate-900 text-center">Registrasi Berhasil!</h1>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-4">
        <p class="text-sm text-slate-600 mb-4">Catat baik-baik, informasi ini <strong>hanya ditampilkan sekali</strong>:</p>

        <div class="mb-3">
            <p class="text-xs text-slate-500 mb-1">Username login</p>
            <p class="font-mono text-base bg-slate-50 rounded-lg px-3 py-2 text-slate-900">{{ $username }}</p>
        </div>
        <div class="mb-1">
            <p class="text-xs text-slate-500 mb-1">Password</p>
            <p class="font-mono text-base bg-slate-50 rounded-lg px-3 py-2 text-slate-900">{{ $password }}</p>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
        <p class="text-sm text-amber-800">
            <i class="ti ti-clock"></i> Akun kamu <strong>belum bisa dipakai login</strong> sampai diaktifkan oleh admin/pengurus perumahan. Silakan hubungi admin untuk konfirmasi aktivasi.
        </p>
    </div>

    <a href="{{ route('login') }}" class="block w-full text-center bg-brand-600 hover:bg-brand-700 text-white rounded-lg py-2.5 text-sm font-medium transition">
        Kembali ke halaman Login
    </a>
</div>

</body>
</html>
