<?php

namespace App\Http\Controllers;

use App\Models\PetugasReference;
use App\Support\SpreadsheetReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasReferenceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'reference_file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:20480'],
        ], [], [
            'reference_file' => 'File referensi petugas',
        ]);

        $file = $request->file('reference_file');
        $extension = $file->getClientOriginalExtension();
        [$headers, $rows] = SpreadsheetReader::read($file->getRealPath(), $extension);

        if (empty($rows)) {
            return back()->withErrors(['reference_file' => 'File referensi tidak terbaca atau kosong.']);
        }

        $map = $this->buildColumnMap($headers);

        if (! isset($map['petugas_username'])) {
            return back()->withErrors([
                'reference_file' => 'Kolom petugas (petugas_username / username / email) tidak ditemukan di file. '
                    .'Kolom yang terbaca dari file Anda: '.implode(', ', $headers),
            ]);
        }

        $count = 0;

        DB::transaction(function () use ($rows, $map, &$count) {
            
            PetugasReference::query()->delete();

            $now = now();
            $batch = [];

            foreach ($rows as $row) {
                $email = $this->val($row, $map, 'petugas_email');
                if (! $email) {
                    continue;
                }

                $batch[] = [
                    'petugas_username' => $email,
                    'Nama Petugas' => $this->val($row, $map, 'nama_petugas'),
                    'Kode Kec' => $this->val($row, $map, 'kode_kec'),
                    'Kecamatan' => $this->val($row, $map, 'nama_kecamatan'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $count++;
            }

            foreach (array_chunk($batch, 500) as $chunk) {
                PetugasReference::insert($chunk);
            }
        });

        return redirect()->route('uploads.index')
            ->with('success', "Data referensi petugas berhasil diupdate ({$count} petugas).");
    }

    private function buildColumnMap(array $headers): array
    {
        $targets = [
            'petugas_email' => ['petugas_email', 'email'],
            'nama_petugas' => ['nama_petugas', 'nama'],
            'kode_kec' => ['kode_kec', 'kode_kecamatan'],
            'nama_kecamatan' => ['nama_kecamatan', 'kecamatan'],
        ];

        $map = [];
        foreach ($targets as $target => $aliases) {
            foreach ($headers as $header) {
                foreach ($aliases as $alias) {
                    if ($header === $alias || str_contains($header, $alias)) {
                        $map[$target] = $header;
                        continue 3;
                    }
                }
            }
        }

        return $map;
    }

    private function val(array $row, array $map, string $target): ?string
    {
        $key = $map[$target] ?? null;
        if ($key === null || ! array_key_exists($key, $row)) {
            return null;
        }

        $value = trim((string) $row[$key]);

        return $value === '' ? null : $value;
    }
}