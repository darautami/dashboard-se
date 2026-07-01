@extends('layouts.app')

@section('title', 'Dashboard Monitoring Petugas')

@section('content')

    {{-- ================= FILTER BAR ================= --}}
    <form method="GET" action="{{ route('dashboard') }}"
          class="bg-white rounded-xl border border-slate-200 p-4 md:p-5 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal</label>
            <select name="tanggal" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @forelse ($availableDates as $date)
                    <option value="{{ $date }}" {{ $selectedDate === $date ? 'selected' : '' }}>
                        {{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('d F Y') }}
                    </option>
                @empty
                    <option value="">Belum ada data</option>
                @endforelse
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Petugas</label>
            <select name="petugas_username" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Petugas</option>
                @foreach ($petugasOptions as $p)
                    <option value="{{ $p->petugas_username }}" {{ $filters['petugas_username'] === $p->petugas_username ? 'selected' : '' }}>
                        {{ $p->nama_petugas ?: $p->petugas_username }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Kecamatan</label>
            <select name="nama_kecamatan" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kecamatan</option>
                @foreach ($kecamatanOptions as $kec)
                    <option value="{{ $kec }}" {{ $filters['nama_kecamatan'] === $kec ? 'selected' : '' }}>{{ $kec }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">SLS Code</label>
            <input type="text" name="sls_code" value="{{ $filters['sls_code'] }}" placeholder="Cari kode SLS..."
                   list="sls-suggestions"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <datalist id="sls-suggestions">
                @foreach ($slsOptions as $sls)
                    <option value="{{ $sls }}"></option>
                @endforeach
            </datalist>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            @if($filters['petugas_username'] || $filters['petugas_role'] || $filters['sls_code'] || $filters['nama_kecamatan'])
                <a href="{{ route('dashboard', ['tanggal' => $selectedDate]) }}"
                   class="shrink-0 border border-slate-300 text-slate-500 text-sm rounded-lg px-3 py-2.5 hover:bg-slate-50">Reset</a>
            @endif
        </div>
    </form>

    {{-- ================= EXPORT DATA ================= --}}
    <form method="GET" action="{{ route('export') }}"
          class="bg-white rounded-xl border border-slate-200 p-4 md:p-5 flex flex-col md:flex-row md:items-end gap-4">

        {{-- bawa serta filter yang sedang aktif --}}
        <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
        <input type="hidden" name="petugas_username" value="{{ $filters['petugas_username'] }}">
        <input type="hidden" name="petugas_role" value="{{ $filters['petugas_role'] }}">
        <input type="hidden" name="sls_code" value="{{ $filters['sls_code'] }}">
        <input type="hidden" name="nama_kecamatan" value="{{ $filters['nama_kecamatan'] }}">

        <div class="flex-1">
            <p class="text-xs font-bold text-slate-700 uppercase mb-3">Export Data</p>
            </p>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Cakupan Tanggal</label>
            <select name="scope" class="w-full md:w-56 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="current">Tanggal Terpilih ({{ $selectedDate ? \Illuminate\Support\Carbon::parse($selectedDate)->translatedFormat('d M Y') : '-' }})</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Format File</label>
            <select name="format" class="w-full md:w-36 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="xlsx">Excel (.xlsx)</option>
                <option value="csv">CSV (.csv)</option>
            </select>
        </div>

        <div>
            <button type="submit"
                    class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </button>
        </div>
    </form>

    @if (empty($availableDates) || $availableDates->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
            <p class="text-slate-500 mb-3">Belum ada data yang diupload.</p>
            <a href="{{ route('uploads.index') }}" class="text-blue-600 font-medium hover:underline">Upload file pertama Anda &rarr;</a>
        </div>
    @else

        {{-- ================= KPI CARDS ================= --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

            <div class="kpi-card">
                <div class="kpi-icon shrink-0 mb-2" style="background:#2563eb">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="text-xs font-semibold text-blue-600 uppercase mb-1">Total Assignment</div>
                <div class="text-xl font-bold text-slate-800 truncate">{{ number_format($summary['total']) }}</div>
                @include('dashboard.partials.delta', ['delta' => $comparison['total'] ?? null])
            </div>

            <div class="kpi-card">
                <div class="kpi-icon shrink-0 mb-2" style="background:#16a34a">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </div>
                <div class="text-xs font-semibold text-green-600 uppercase mb-1">Open</div>
                <div class="text-xl font-bold text-slate-800 truncate">{{ number_format($summary['open']) }}</div>
                <p class="text-xs text-slate-400">{{ $summary['pct_open'] }}% dari total</p>
                @include('dashboard.partials.delta', ['delta' => $comparison['open'] ?? null])
            </div>

            <div class="kpi-card">
                <div class="kpi-icon shrink-0 mb-2" style="background:#f59e0b">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-6 4h6m2 5H7a2 2 0 01-2-2V4a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V20a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="text-xs font-semibold text-amber-600 uppercase mb-1">Draft</div>
                <div class="text-xl font-bold text-slate-800 truncate">{{ number_format($summary['draft']) }}</div>
                <p class="text-xs text-slate-400">{{ $summary['pct_draft'] }}% dari total</p>
                @include('dashboard.partials.delta', ['delta' => $comparison['draft'] ?? null])
            </div>

            <div class="kpi-card">
                <div class="kpi-icon shrink-0 mb-2" style="background:#7c3aed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div class="text-xs font-semibold text-violet-600 uppercase mb-1">Submitted</div>
                <div class="text-xl font-bold text-slate-800 truncate">{{ number_format($summary['submitted']) }}</div>
                <p class="text-xs text-slate-400">{{ $summary['pct_submitted_pencacah'] }}% dari total</p>
                @include('dashboard.partials.delta', ['delta' => $comparison['submitted'] ?? null])
            </div>

            <div class="kpi-card">
                <div class="kpi-icon shrink-0 mb-2" style="background:#0d9488">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-xs font-semibold text-teal-600 uppercase mb-1">Approved</div>
                <div class="text-xl font-bold text-slate-800 truncate">{{ number_format($summary['approved']) }}</div>
                <p class="text-xs text-slate-400">{{ $summary['pct_approved'] }}% dari total</p>
                @include('dashboard.partials.delta', ['delta' => $comparison['approved'] ?? null])
            </div>

            <div class="kpi-card">
                <div class="kpi-icon shrink-0 mb-2" style="background:#e11d48">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="text-xs font-semibold text-rose-600 uppercase mb-1">Rejected</div>
                <div class="text-xl font-bold text-slate-800 truncate">{{ number_format($summary['rejected']) }}</div>
                <p class="text-xs text-slate-400">{{ $summary['pct_rejected'] }}% dari total</p>
                @include('dashboard.partials.delta', ['delta' => $comparison['rejected'] ?? null])
            </div>
        </div>

        @if ($comparison)
            <p class="text-xs text-slate-400 -mt-2">* Perbandingan dihitung terhadap data tanggal {{ $comparison['date'] }} (upload sebelumnya).</p>
        @endif

        {{-- ================= GRAFIK ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-4">Tren Status Assignment (Histori Semua Tanggal)</h3>
                <canvas id="trendChart" height="260"></canvas>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="text-xs font-bold text-slate-500 uppercase mb-4">Komposisi Status Hari Ini</h3>
                <canvas id="donutChart" height="260"></canvas>
            </div>
        </div>

        {{-- ================= TABEL PER PETUGAS (DIKELOMPOKKAN PER KECAMATAN) ================= --}}
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="p-5 border-b border-slate-200">
                <h3 class="text-xs font-bold text-slate-500 uppercase">
                    Ringkasan Status Assignment per Petugas
                    ({{ \Illuminate\Support\Carbon::parse($selectedDate)->translatedFormat('d F Y') }})
                </h3>
            </div>
            <div class="table-scroll">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="text-left px-5 py-3 font-medium">#</th>
                            <th class="text-left px-5 py-3 font-medium">Petugas / Kecamatan</th>
                            <th class="text-right px-5 py-3 font-medium">Total Assignment</th>
                            <th class="text-right px-5 py-3 font-medium text-green-600">Open</th>
                            <th class="text-right px-5 py-3 font-medium text-amber-600">Draft</th>
                            <th class="text-right px-5 py-3 font-medium text-violet-600">Submitted</th>
                            <th class="text-right px-5 py-3 font-medium text-teal-600">Approved</th>
                            <th class="text-right px-5 py-3 font-medium text-rose-600">Rejected</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-600">Non Open</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-600">Submit+</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-600">% Non Open</th>
                            <th class="text-right px-5 py-3 font-medium text-slate-600">% Submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($perPetugasGrouped as $group)
                            {{-- Baris subtotal kecamatan --}}
                            <tr class="bg-blue-50/60">
                                <td class="px-5 py-2.5"></td>
                                <td class="px-5 py-2.5 font-bold text-slate-700">{{ $group['label'] }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-slate-700">{{ number_format($group['subtotal']['total_assignment']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-green-700">{{ number_format($group['subtotal']['status_open']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-amber-700">{{ number_format($group['subtotal']['status_draft']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-violet-700">{{ number_format($group['subtotal']['status_submitted_pencacah']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-teal-700">{{ number_format($group['subtotal']['status_approved_pengawas']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-rose-700">{{ number_format($group['subtotal']['status_rejected_pengawas']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-slate-700">{{ number_format($group['subtotal']['status_non_open']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-slate-700">{{ number_format($group['subtotal']['status_submit_plus']) }}</td>
                                <td class="px-5 py-2.5 text-right font-bold text-slate-700">{{ $group['subtotal']['pct_non_open'] }}%</td>
                                <td class="px-5 py-2.5 text-right font-bold text-slate-700">{{ $group['subtotal']['pct_submitted'] }}%</td>
                            </tr>

                            {{-- Baris tiap petugas dalam kecamatan ini --}}
                            @foreach ($group['petugas'] as $i => $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-slate-700">{{ $row->nama_petugas ?: $row->petugas_username }}</div>
                                        <div class="text-xs text-slate-400">{{ $row->petugas_username }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-right font-medium text-slate-700">{{ number_format($row->total_assignment) }}</td>
                                    <td class="px-5 py-3 text-right text-green-600">{{ number_format($row->status_open) }}</td>
                                    <td class="px-5 py-3 text-right text-amber-600">{{ number_format($row->status_draft) }}</td>
                                    <td class="px-5 py-3 text-right text-violet-600">{{ number_format($row->status_submitted_pencacah) }}</td>
                                    <td class="px-5 py-3 text-right text-teal-600">{{ number_format($row->status_approved_pengawas) }}</td>
                                    <td class="px-5 py-3 text-right text-rose-600">{{ number_format($row->status_rejected_pengawas) }}</td>
                                    <td class="px-5 py-3 text-right text-slate-600">{{ number_format($row->status_non_open) }}</td>
                                    <td class="px-5 py-3 text-right text-slate-600">{{ number_format($row->status_submit_plus) }}</td>
                                    <td class="px-5 py-3 text-right text-slate-600">{{ $row->pct_non_open }}%</td>
                                    <td class="px-5 py-3 text-right text-slate-600">{{ $row->pct_submitted }}%</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="12" class="px-5 py-8 text-center text-slate-400">Tidak ada data untuk filter ini.</td></tr>
                        @endforelse

                        {{-- Baris grand total seluruh kabupaten/kota --}}
                        @if ($perPetugasGrouped->isNotEmpty())
                            <tr class="bg-slate-800">
                                <td class="px-5 py-3"></td>
                                <td class="px-5 py-3 font-bold text-white">GRAND TOTAL</td>
                                <td class="px-5 py-3 text-right font-bold text-white">{{ number_format($summary['total']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-green-300">{{ number_format($summary['open']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-amber-300">{{ number_format($summary['draft']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-violet-300">{{ number_format($summary['submitted']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-teal-300">{{ number_format($summary['approved']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-rose-300">{{ number_format($summary['rejected']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-slate-200">{{ number_format($summary['non_open']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-slate-200">{{ number_format($summary['submit_plus']) }}</td>
                                <td class="px-5 py-3 text-right font-bold text-slate-200">{{ $summary['pct_non_open'] }}%</td>
                                <td class="px-5 py-3 text-right font-bold text-white">{{ $summary['pct_submitted'] }}%</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 text-xs text-slate-400 border-t border-slate-100">
                * Non Open = Total &minus; Open. Submit+ = Submitted + Approved + Rejected. Baris biru muda = subtotal kecamatan, baris gelap = grand total seluruh kabupaten/kota.
            </div>
        </div>

    @endif

    <script id="dashboard-data" type="application/json">
        {!! json_encode([
            'trend' => $trend,
            'summary' => $summary,
        ]) !!}
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

@endsection