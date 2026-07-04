@extends('layouts.app')

@section('title', 'Upload Data - Dashboard Monitoring Petugas')
@section('header_title', 'Upload Data Assignment')
@section('header_subtitle', 'Upload file Excel/CSV harian. Data tanggal lama tetap tersimpan untuk perbandingan.')

@section('content')

    {{-- ================= UPLOAD DATA PROGRESS HARIAN ================= --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 md:p-6">
        <h2 class="font-semibold text-slate-700 mb-4">Upload File Baru (Progress Harian)</h2>

       <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data" class="w-full">
    @csrf

    {{-- Grid Utama Form --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

        {{-- TANGGAL --}}
        <div class="flex flex-col w-full">
            <label class="text-xs text-slate-500 mb-1">Tanggal Data</label>
            <div class="h-[42px] w-full border border-slate-300 rounded-lg bg-white flex items-center px-3 focus-within:border-blue-500">
                <input type="date"
                       name="upload_date"
                       value="{{ old('upload_date', now()->format('Y-m-d')) }}"
                       class="w-full text-sm bg-transparent outline-none border-none p-0 m-0">
            </div>
        </div>

       {{-- FILE --}}
        <div class="flex flex-col w-full">
            <label class="text-xs text-slate-500 mb-1">File Excel / CSV</label>
            <div class="h-[42px] w-full border border-slate-300 rounded-lg bg-white flex items-center px-3">
                <input type="file"
                       name="file"
                       class="w-full text-sm outline-none border-none p-0 m-0 cursor-pointer">
            </div>
        </div>

        {{-- BUTTON --}}
        <div class="w-full">
            <button type="submit"
                    class="h-[42px] w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg flex items-center justify-center shadow-sm transition-colors">
                Upload Sekarang
            </button>
        </div>

    </div>

    {{-- Teks Bantuan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-1">
        <div class="md:col-start-2">
            <p class="text-[11px] text-slate-400">
                Format: .xlsx, .xls, atau .csv (maks 20MB)
            </p>
        </div>
    </div>
</form>

        <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-700 text-xs rounded-lg px-4 py-3">
            <strong>Catatan:</strong> Jika tanggal yang dipilih sudah pernah diupload sebelumnya, data lama untuk tanggal
            tersebut akan <strong>digantikan</strong> dengan file baru ini (bukan ditambah dobel). Data tanggal-tanggal lain
            tidak akan terpengaruh dan tetap tersimpan.
        </div>
    </div>

    {{-- ================= UPLOAD DATA REFERENSI PETUGAS (NAMA & KECAMATAN) ================= --}}
<div class="bg-white rounded-xl border border-slate-200 p-5 md:p-6">
    <h2 class="font-semibold text-slate-700 mb-1">Upload Data Referensi Petugas</h2>
    <p class="text-xs text-slate-400 mb-4">
        File berisi <strong>petugas_username</strong>, <strong>Nama Petugas</strong>, <strong>Kode Kec</strong>,
        dan <strong>Kecamatan</strong>. Data ini bersifat master/referensi (bukan harian) — setiap upload akan
        mengantikan seluruh data referensi sebelumnya. Data ini dipakai untuk menampilkan kolom "Nama" dan
        "Kecamatan" di Dashboard, dan <strong>cukup diupload sekali saja</strong> (upload ulang hanya kalau ada
        petugas baru / pindah kecamatan). Saat ini tersimpan <strong>{{ number_format($referenceCount) }} petugas</strong> di referensi.
    </p>

    <form action="{{ route('references.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-4 items-end">
        @csrf

        <div class="flex flex-col w-full md:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">File Referensi (Excel/CSV)</label>
            <div class="h-[42px] w-full border border-slate-300 rounded-lg bg-white flex items-center px-3 focus-within:border-blue-500">
                <input type="file" name="reference_file" required accept=".xlsx,.xls,.csv,.txt"
                       class="w-full text-sm outline-none border-none p-0 m-0 cursor-pointer">
            </div>
        </div>

        <div class="w-full">
            <button type="submit"
                    class="h-[42px] w-full bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg flex items-center justify-center gap-2 shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M12 12v9m0-9l-3 3m3-3l3 3"/>
                </svg>
                Update Referensi
            </button>
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