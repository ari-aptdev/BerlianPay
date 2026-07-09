<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BerlianPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: {
            50: '#eef4ff', 600: '#1a45c0', 700: '#153a9e',
        } } } } }
    </script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    <div class="flex flex-col items-center mb-6">
        <div class="w-12 h-12 rounded-xl bg-brand-600 flex items-center justify-center text-white mb-3">
            <i class="ti ti-diamond text-2xl"></i>
        </div>
        <h1 class="text-lg font-medium text-slate-900">BerlianPay</h1>
        <p class="text-sm text-slate-500">Sistem pencatatan IPL perumahan</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Email (admin) / Username (warga)</label>
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
</div>

</body>
</html>
