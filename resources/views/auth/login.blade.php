<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SlaughterHouse</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
    <form method="POST" action="{{ route('login.post') }}" class="w-full max-w-md bg-white p-6 rounded-xl shadow">
        @csrf
        <h1 class="text-xl font-bold mb-1">Login</h1>
        <p class="text-sm text-slate-500 mb-6">Masuk menggunakan username.</p>

        <label class="block text-sm font-medium mb-1">Username</label>
        <input name="username" value="{{ old('username') }}" class="w-full border rounded px-3 py-2 mb-4" />

        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2 mb-6" />

        <button class="w-full bg-slate-900 text-white rounded px-4 py-2 hover:bg-slate-800">Login</button>

        <div class="mt-4 text-xs text-slate-500">
            Default seed: <span class="font-mono">superadmin / superadmin123</span>
        </div>
    </form>
</body>
</html>