@extends('layouts.app')

@section('title', 'Upload Data - Dashboard Monitoring Petugas')
@section('header_title', 'Upload Data Assignment')
@section('header_subtitle', 'Upload file Excel/CSV harian. Data tanggal lama tetap tersimpan untuk perbandingan.')

@section('content')

    {{-- ================= UPLOAD DATA PROGRESS HARIAN ================= --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 md:p-6">
        <h2 class="font-semibold text-slate-700 mb-4">Upload File Baru (Progress Harian)</h2>

        @if ($errors->has('upload_password'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-xs rounded-lg px-4 py-3">
                ⚠️ {{ $errors->first('upload_password') }}
            </div>
        @endif

        <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-4 gap-4 items-start">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Data</label>
                <input type="date" name="upload_date" required
                       value="{{ old('upload_date', now()->format('Y-m-d')) }}"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">File Excel / CSV</label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv,.txt"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-slate-400 mt-1">Format: .xlsx, .xls, atau .csv (maks 20MB)</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Password Upload</label>
                <input type="password" name="upload_password" required
                       placeholder="Masukkan password..."
                       class="w-full border {{ $errors->has('upload_password') ? 'border-red-400' : 'border-slate-300' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-[11px] text-slate-400 mt-1">Hubungi admin jika lupa password.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-transparent mb-1">&nbsp;</label>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/></svg>
                    Upload Sekarang
                </button>
                <p class="text-[11px] text-transparent mt-1">&nbsp;</p>
            </div>
        </form>

        <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-lg px-4 py-3">
            <strong>Catatan:</strong> Jika tanggal yang dipilih sudah pernah diupload sebelumnya, data lama untuk tanggal
            tersebut akan <strong>digantikan</strong> dengan file baru ini (bukan ditambah dobel). Data tanggal-tanggal lain
            tidak akan terpengaruh dan tetap tersimpan untuk perbandingan.
        </div>
    </div>

    {{-- ================= UPLOAD DATA REFERENSI PETUGAS (NAMA & KECAMATAN) ================= --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 md:p-6">
        <h2 class="font-semibold text-slate-700 mb-1">Upload Data Referensi Petugas (Nama & Kecamatan)</h2>
        <p class="text-xs text-slate-400 mb-4">
            File berisi <strong>petugas_username</strong>, <strong>Nama Petugas</strong>, <strong>Kode Kec</strong>,
            dan <strong>Kecamatan</strong>. Data ini bersifat master/referensi (bukan harian) — setiap upload akan
            menggantikan seluruh data referensi sebelumnya. Data ini dipakai untuk menampilkan kolom "Nama" dan
            "Kecamatan" di Dashboard, dan <strong>cukup diupload sekali saja</strong> (upload ulang hanya kalau ada
            petugas baru / pindah kecamatan). Saat ini tersimpan <strong>{{ number_format($referenceCount) }} petugas</strong> di referensi.
        </p>

        <form action="{{ route('references.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-4 items-start">
            @csrf

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-500 mb-1">File Referensi (Excel/CSV)</label>
                <input type="file" name="reference_file" required accept=".xlsx,.xls,.csv,.txt"
                       class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-transparent mb-1">&nbsp;</label>
                <button type="submit"
                        class="w-full bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg px-4 py-2.5 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/></svg>
                    Update Referensi
                </button>
                <p class="text-[11px] text-transparent mt-1">&nbsp;</p>
            </div>
        </form>
    </div>

    {{-- ================= RIWAYAT UPLOAD ================= --}}
    <div class="bg-white rounded-xl border border-slate-200">
        <div class="p-5 md:p-6 border-b border-slate-200 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700">Riwayat Upload</h2>
            <span class="text-xs text-slate-400">{{ $uploads->count() }} file tersimpan</span>
        </div>

        <div class="table-scroll">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium">Tanggal Data</th>
                        <th class="text-left px-5 py-3 font-medium">Nama File</th>
                        <th class="text-left px-5 py-3 font-medium">Jumlah Baris</th>
                        <th class="text-left px-5 py-3 font-medium">Diupload Pada</th>
                        <th class="text-right px-5 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($uploads as $upload)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $upload->upload_date->translatedFormat('d F Y') }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $upload->original_filename }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ number_format($upload->total_rows) }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $upload->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('dashboard', ['tanggal' => $upload->upload_date->format('Y-m-d')]) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium mr-3">Lihat di Dashboard</a>
                                <form action="{{ route('uploads.destroy', $upload) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus data tanggal {{ $upload->upload_date->format('d-m-Y') }}? Tindakan ini tidak bisa dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada data yang diupload.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection