<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard Monitoring Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex">

    {{-- Sisi Kiri (biru) --}}
    <div class="hidden md:block md:w-1/2 bg-[#003d7a] relative overflow-hidden" style="min-height:100vh;">

        {{-- Dekorasi lingkaran background --}}
        <div style="position:absolute;width:400px;height:400px;border-radius:50%;border:1px solid rgba(255,255,255,0.05);top:-100px;right:-100px;"></div>
        <div style="position:absolute;width:300px;height:300px;border-radius:50%;border:1px solid rgba(255,255,255,0.05);bottom:-50px;left:-50px;"></div>

        <div style="position:absolute;top:50%;left:3rem;transform:translateY(-50%);z-index:10;">
            <p class="text-blue-300 text-sm font-medium mb-2">Halo, Admin! 👋</p>
            <h2 class="text-white text-3xl font-bold leading-snug mb-3">
                Selamat datang di<br>Monitor Progress<br>Assignment Petugas
            </h2>
        </div>

        <div style="position:absolute;top:0;bottom:0;left:0;right:0;opacity:0.45;z-index:5;">
            <canvas id="loginChart" style="width:100%;height:100%;"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        const ctx = document.getElementById('loginChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['25 Jun', '26 Jun', '27 Jun', '28 Jun', '29 Jun', '30 Jun', '01 Jul'],
                datasets: [
                    {
                        label: 'Non Open',
                        data: [22000, 30000, 38000, 45000, 48000, 50000, 52748],
                        backgroundColor: 'rgba(255,255,255,0.5)',
                        borderRadius: 4,
                        order: 2,
                    },
                    {
                        label: 'Total Assignment',
                        data: [158000, 162000, 165000, 168000, 170000, 172000, 174575],
                        backgroundColor: 'rgba(255,255,255,0.15)',
                        borderRadius: 4,
                        order: 3,
                    },
                    {
                        label: 'Tren',
                        data: [22000, 30000, 38000, 45000, 48000, 50000, 52748],
                        type: 'line',
                        borderColor: 'rgba(251,191,36,0.8)',
                        backgroundColor: 'transparent',
                        pointBackgroundColor: 'rgba(251,191,36,0.8)',
                        pointRadius: 4,
                        borderWidth: 2,
                        tension: 0.4,
                        order: 1,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: {
                        ticks: { display: false },
                        grid: { color: 'rgba(255,255,255,0.05)' },
                    },
                    y: {
                        ticks: { display: false },
                        grid: { color: 'rgba(255,255,255,0.05)' },
                    }
                }
            }
        });
        </script>
    </div>

    {{-- Sisi Kanan (putih) --}}
    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8" style="min-height:100vh;">
        <div class="w-full max-w-sm">

            <h1 class="text-2xl font-bold text-slate-800 mb-1">
                Masuk sebagai Admin
            </h1>

            <p class="text-sm text-slate-400 mb-8">
                Masukkan password untuk mengakses fitur upload data.
            </p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-medium text-slate-500 mb-1">
                        Password Admin
                    </label>

                    <input
                        type="password"
                        name="password"
                        required
                        autofocus
                        placeholder="Masukkan password..."
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#003d7a] hover:bg-blue-900 text-white font-semibold rounded-lg px-4 py-3 text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk
                </button>

            </form>

        </div>
    </div>

</body>
</html>