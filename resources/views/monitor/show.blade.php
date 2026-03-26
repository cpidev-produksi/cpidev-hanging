<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Monitor {{ $location }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-900 text-white min-h-screen">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-slate-300">Live Monitor</div>
                <div class="text-3xl font-bold">{{ $location }}</div>
            </div>
            <div class="text-sm text-slate-300 font-mono" id="report">-</div>
        </div>

        <div class="mt-6 bg-slate-800/60 border border-slate-700 rounded-2xl p-6">
            <div id="emptyState" class="text-slate-300">
                Tidak ada proses running.
            </div>

            <div id="row" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-center">
                    <div class="bg-slate-900/40 rounded-xl p-4 border border-slate-700">
                        <div class="text-xs text-slate-400">Nomor Truk</div>
                        <div class="text-3xl font-bold" id="truckNo">-</div>
                    </div>

                    <div class="bg-slate-900/40 rounded-xl p-4 border border-slate-700">
                        <div class="text-xs text-slate-400">Ekspedisi - Sopir</div>
                        <div class="text-xl font-semibold" id="expDriver">-</div>
                    </div>

                    <div class="bg-slate-900/40 rounded-xl p-4 border border-slate-700">
                        <div class="text-xs text-slate-400">Size - Farm</div>
                        <div class="text-xl font-semibold" id="sizeFarm">-</div>
                    </div>

                    <div class="bg-slate-900/40 rounded-xl p-4 border border-slate-700">
                        <div class="text-xs text-slate-400">Total Ayam</div>
                        <div class="text-3xl font-bold" id="totalAyam">0</div>
                    </div>

                    <div class="bg-slate-900/40 rounded-xl p-4 border border-slate-700">
                        <div class="text-xs text-slate-400">Nominal Ekoran</div>
                        <div class="text-3xl font-bold" id="nominal">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-xs text-slate-400">
            Auto refresh setiap 2 detik.
        </div>
    </div>

<script>
async function refresh() {
  const res = await fetch(`{{ route('monitor.data', $location) }}`, { headers: { 'Accept': 'application/json' }});
  const j = await res.json();

  const emptyState = document.getElementById('emptyState');
  const row = document.getElementById('row');

  if (!j.active) {
    row.classList.add('hidden');
    emptyState.classList.remove('hidden');
    document.getElementById('report').textContent = '-';
    return;
  }

  emptyState.classList.add('hidden');
  row.classList.remove('hidden');

  document.getElementById('report').textContent = j.report_code;
  document.getElementById('truckNo').textContent = j.truck_no;
  document.getElementById('expDriver').textContent = `${j.expedition_name} - ${j.driver_name}`;
  document.getElementById('sizeFarm').textContent = `${j.size} - ${j.farm_name}`;
  document.getElementById('totalAyam').textContent = j.total_ayam;
  document.getElementById('nominal').textContent = new Intl.NumberFormat('id-ID').format(j.farm_fee_amount);
}

refresh();
setInterval(refresh, 2000);
</script>
</body>
</html>