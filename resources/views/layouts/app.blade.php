<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SlaughterHouse</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen flex">
    <aside class="w-64 bg-white border-r">
        <div class="p-5 font-bold text-lg">SlaughterHouse</div>

        <nav class="px-3 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Dashboard</a>

            <div class="group">
                <div class="px-3 py-2 rounded hover:bg-slate-100 cursor-pointer font-medium">
                    Master Data
                </div>
                <div class="hidden group-hover:block pl-3">
                    <a href="{{ route('master.users.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Data User</a>
                    <a href="{{ route('master.expeditions.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Data Ekspedisi</a>
                    <a href="{{ route('master.trucks.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Data Truk</a>
                    <a href="{{ route('master.farms.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Data Farm</a>
                </div>
            </div>

            <a href="{{ route('monitor-controls.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Kontrol Monitor</a>

            <div class="pt-4">
                <div class="px-3 text-xs text-slate-500">Monitor</div>
                <a href="{{ route('monitor.show','SH01') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Live Monitor SH01</a>
                <a href="{{ route('monitor.show','SH02') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Live Monitor SH02</a>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="pt-6 px-3">
                @csrf
                <button class="w-full px-3 py-2 rounded bg-slate-900 text-white hover:bg-slate-800">Logout</button>
            </form>
        </nav>
    </aside>

    <main class="flex-1 p-6">
        @if(session('status'))
            <div class="mb-4 p-3 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded bg-rose-50 text-rose-800 border border-rose-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>