<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSnapshot;
use App\Models\PetugasReference;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $availableDates = Upload::orderBy('upload_date')->pluck('upload_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->values();

        $latestDate = $availableDates->last();
        $selectedDate = $request->input('tanggal', $latestDate);

        if ($selectedDate && ! $availableDates->contains($selectedDate)) {
            $selectedDate = $latestDate;
        }

        $filters = [
            'petugas_username' => $request->input('petugas_username'),
            'petugas_role' => $request->input('petugas_role'),
            'sls_code' => $request->input('sls_code'),
            'nama_kecamatan' => $request->input('nama_kecamatan'),
        ];

        $referenceMap = PetugasReference::query()
            ->get(['petugas_username', 'nama_petugas', 'kode_kecamatan', 'nama_kecamatan'])
            ->keyBy('petugas_username');

        $kecamatanOptions = PetugasReference::query()
            ->whereNotNull('nama_kecamatan')
            ->distinct()
            ->orderBy('nama_kecamatan')
            ->pluck('nama_kecamatan');

        if ($filters['nama_kecamatan']) {
            $filters['_usernames_in_kecamatan'] = PetugasReference::query()
                ->where('nama_kecamatan', $filters['nama_kecamatan'])
                ->pluck('petugas_username')
                ->all();
        }

        $petugasOptions = AssignmentSnapshot::query()
            ->select('petugas_username', 'petugas_email')
            ->whereNotNull('petugas_username')
            ->distinct()
            ->get()
            ->map(function ($p) use ($referenceMap) {
                $ref = $referenceMap->get($p->petugas_username);
                $p->nama_petugas = $ref->nama_petugas ?? null;

                return $p;
            })
            ->sortBy(fn ($p) => $p->nama_petugas ?: $p->petugas_username)
            ->values();

        $roleOptions = AssignmentSnapshot::query()
            ->whereNotNull('petugas_role')
            ->distinct()
            ->orderBy('petugas_role')
            ->pluck('petugas_role');

        $slsOptions = AssignmentSnapshot::query()
            ->whereNotNull('sls_code')
            ->distinct()
            ->orderBy('sls_code')
            ->limit(500)
            ->pluck('sls_code');

        // ----- Data untuk tanggal yang dipilih -----
        $baseQuery = AssignmentSnapshot::query()->when($selectedDate, fn ($q) => $q->where('upload_date', $selectedDate));
        $this->applyFilters($baseQuery, $filters);

        $summaryRow = (clone $baseQuery)->selectRaw('
                COALESCE(SUM(status_open + status_draft + status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as total,
                COALESCE(SUM(status_open),0) as open,
                COALESCE(SUM(status_draft),0) as draft,
                COALESCE(SUM(status_submitted_pencacah),0) as submitted,
                COALESCE(SUM(status_approved_pengawas),0) as approved,
                COALESCE(SUM(status_rejected_pengawas),0) as rejected
            ')->first();

        $summary = $this->aggregateFromRow($summaryRow);

        // ----- Tabel ringkasan per petugas -----
        $perPetugas = (clone $baseQuery)
            ->selectRaw('
                petugas_username,
                MAX(petugas_email) as petugas_email,
                SUM(status_open + status_draft + status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas) as total_assignment,
                SUM(status_open) as status_open,
                SUM(status_draft) as status_draft,
                SUM(status_submitted_pencacah) as status_submitted_pencacah,
                SUM(status_approved_pengawas) as status_approved_pengawas,
                SUM(status_rejected_pengawas) as status_rejected_pengawas
            ')
            ->groupBy('petugas_username')
            ->orderByDesc('total_assignment')
            ->get()
            ->map(function ($row) use ($referenceMap) {
                $row->progress = $row->total_assignment > 0
                    ? round(($row->status_approved_pengawas / $row->total_assignment) * 100, 1)
                    : 0;

                $row->status_non_open = $row->total_assignment - $row->status_open;
                $row->pct_non_open = $row->total_assignment > 0
                    ? round(($row->status_non_open / $row->total_assignment) * 100)
                    : 0;

                $row->status_submit_plus = $row->status_submitted_pencacah
                    + $row->status_approved_pengawas
                    + $row->status_rejected_pengawas;
                $row->pct_submitted = $row->total_assignment > 0
                    ? round(($row->status_submit_plus / $row->total_assignment) * 100)
                    : 0;

                $ref = $referenceMap->get($row->petugas_username);
                $row->nama_petugas = $ref->nama_petugas ?? null;
                $row->kode_kecamatan = $ref->kode_kecamatan ?? null;
                $row->nama_kecamatan = $ref->nama_kecamatan ?? null;

                return $row;
            });

        // ----- Kelompokkan per Kecamatan -----
        $perPetugasGrouped = $perPetugas
            ->groupBy(fn ($row) => $row->kode_kecamatan ?? 'ZZZ_TANPA_KECAMATAN')
            ->map(function ($group) {
                $first = $group->first();
                $kodeSingkat = $first->kode_kecamatan ? substr($first->kode_kecamatan, -3) : '???';
                $namaKecamatan = $first->nama_kecamatan ?? 'Tanpa Kecamatan';

                $sorted = $group->sortBy(fn ($r) => $r->nama_petugas ?: $r->petugas_username)->values();

                $subtotal = [
                    'total_assignment' => $sorted->sum('total_assignment'),
                    'status_open' => $sorted->sum('status_open'),
                    'status_draft' => $sorted->sum('status_draft'),
                    'status_submitted_pencacah' => $sorted->sum('status_submitted_pencacah'),
                    'status_approved_pengawas' => $sorted->sum('status_approved_pengawas'),
                    'status_rejected_pengawas' => $sorted->sum('status_rejected_pengawas'),
                    'status_non_open' => $sorted->sum('status_non_open'),
                    'status_submit_plus' => $sorted->sum('status_submit_plus'),
                ];
                $subtotal['progress'] = $subtotal['total_assignment'] > 0
                    ? round(($subtotal['status_approved_pengawas'] / $subtotal['total_assignment']) * 100, 1)
                    : 0;
                $subtotal['pct_non_open'] = $subtotal['total_assignment'] > 0
                    ? round(($subtotal['status_non_open'] / $subtotal['total_assignment']) * 100)
                    : 0;
                $subtotal['pct_submitted'] = $subtotal['total_assignment'] > 0
                    ? round(($subtotal['status_submit_plus'] / $subtotal['total_assignment']) * 100)
                    : 0;

                return [
                    'label' => '['.$kodeSingkat.'] '.mb_strtoupper($namaKecamatan),
                    'kode' => $first->kode_kecamatan ?? 'ZZZ',
                    'petugas' => $sorted,
                    'subtotal' => $subtotal,
                ];
            })
            ->sortBy('kode')
            ->values();

        // ----- Data tren historis (dibatasi 7 hari terakhir) -----
        $availableDatesForTrend = $availableDates->slice(-7)->values();

        $trendQuery = AssignmentSnapshot::query()
            ->selectRaw('
                upload_date,
                COALESCE(SUM(status_open + status_draft + status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as total,
                COALESCE(SUM(status_open),0) as open,
                COALESCE(SUM(status_draft),0) as draft,
                COALESCE(SUM(status_submitted_pencacah),0) as submitted,
                COALESCE(SUM(status_approved_pengawas),0) as approved,
                COALESCE(SUM(status_rejected_pengawas),0) as rejected,
                COALESCE(SUM(status_draft + status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as non_open,
                COALESCE(SUM(status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as non_open_draft
            ')
            ->groupBy('upload_date')
            ->orderBy('upload_date');
        $this->applyFilters($trendQuery, $filters);
        $trendRows = $trendQuery->get()->keyBy(fn ($r) => Carbon::parse($r->upload_date)->format('Y-m-d'));

        // ----- Perbandingan dengan upload sebelumnya -----
        $previousDate = $availableDates->filter(fn ($d) => $selectedDate && $d < $selectedDate)->last();

        // Kalau tanggal sebelumnya tidak ada di trendRows (di luar 7 hari),
        // ambil langsung dari database supaya perbandingan tetap akurat.
        if ($previousDate && ! $trendRows->has($previousDate)) {
           $prevQueryDirect = AssignmentSnapshot::query()
            ->where('upload_date', $previousDate)
            ->selectRaw('
            COALESCE(SUM(status_open + status_draft + status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as total,
            COALESCE(SUM(status_open),0) as open,
            COALESCE(SUM(status_draft),0) as draft,
            COALESCE(SUM(status_submitted_pencacah),0) as submitted,
            COALESCE(SUM(status_approved_pengawas),0) as approved,
            COALESCE(SUM(status_rejected_pengawas),0) as rejected,
            COALESCE(SUM(status_draft + status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as non_open,
            COALESCE(SUM(status_submitted_pencacah + status_approved_pengawas + status_rejected_pengawas),0) as non_open_draft
    ');
            $this->applyFilters($prevQueryDirect, $filters);
            $prevRowDirect = $prevQueryDirect->first();
            if ($prevRowDirect) {
                $trendRows->put($previousDate, $prevRowDirect);
            }
        }

        $trend = [
            'labels' => [],
            'total' => [],
            'open' => [],
            'draft' => [],
            'submitted' => [],
            'approved' => [],
            'rejected' => [],
            'non_open' => [],
            'non_open_draft' => [],
        ];

        foreach ($availableDatesForTrend as $date) {
            $row = $trendRows->get($date);
            $trend['labels'][] = Carbon::parse($date)->translatedFormat('d M');
            $trend['total'][] = $row ? (int) $row->total : 0;
            $trend['open'][] = $row ? (int) $row->open : 0;
            $trend['draft'][] = $row ? (int) $row->draft : 0;
            $trend['submitted'][] = $row ? (int) $row->submitted : 0;
            $trend['approved'][] = $row ? (int) $row->approved : 0;
            $trend['rejected'][] = $row ? (int) $row->rejected : 0;
            $trend['non_open'][] = $row ? (int) $row->non_open : 0;
            $trend['non_open_draft'][] = $row ? (int) $row->non_open_draft : 0;
        }

        $comparison = null;

        if ($previousDate) {
            $prevRow = $trendRows->get($previousDate);
            $prevAgg = $this->aggregateFromRow($prevRow ?: (object) ['total' => 0, 'open' => 0, 'draft' => 0, 'submitted' => 0, 'approved' => 0, 'rejected' => 0]);

            $comparison = [
                'date' => Carbon::parse($previousDate)->translatedFormat('d F Y'),
                'total' => $summary['total'] - $prevAgg['total'],
                'open' => $summary['open'] - $prevAgg['open'],
                'draft' => $summary['draft'] - $prevAgg['draft'],
                'submitted' => $summary['submitted'] - $prevAgg['submitted'],
                'approved' => $summary['approved'] - $prevAgg['approved'],
                'rejected' => $summary['rejected'] - $prevAgg['rejected'],
            ];
        }

        // Mapping kecamatan -> daftar username petugas (untuk filter dinamis di JS)
        $kecamatanPetugasMap = PetugasReference::query()
            ->whereNotNull('nama_kecamatan')
            ->get(['petugas_username', 'nama_petugas', 'nama_kecamatan'])
            ->groupBy('nama_kecamatan')
            ->map(fn ($group) => $group->map(fn ($p) => [
                'username' => $p->petugas_username,
                'nama' => $p->nama_petugas ?? $p->petugas_username,
            ])->values())
            ->toArray();

        // Mapping kecamatan -> daftar SLS code
        $kecamatanSlsMap = \App\Models\AssignmentSnapshot::query()
            ->whereNotNull('sls_code')
            ->whereNotNull('petugas_username')
            ->when($selectedDate, fn ($q) => $q->where('upload_date', $selectedDate))
            ->select('petugas_username', 'sls_code')
            ->distinct()
            ->get()
            ->groupBy(fn ($row) => $referenceMap->get($row->petugas_username)?->nama_kecamatan ?? '')
            ->map(fn ($group) => $group->pluck('sls_code')->unique()->sort()->values())
            ->toArray();

        return view('dashboard.index', [
            'availableDates' => $availableDates,
            'selectedDate' => $selectedDate,
            'filters' => $filters,
            'petugasOptions' => $petugasOptions,
            'roleOptions' => $roleOptions,
            'slsOptions' => $slsOptions,
            'kecamatanOptions' => $kecamatanOptions,
            'summary' => $summary,
            'perPetugas' => $perPetugas,
            'perPetugasGrouped' => $perPetugasGrouped,
            'trend' => $trend,
            'comparison' => $comparison,
            'kecamatanPetugasMap' => $kecamatanPetugasMap,
            'kecamatanSlsMap' => $kecamatanSlsMap,
        ]);
    }

    private function applyFilters($query, array $filters): void
    {
        $query
            ->when($filters['petugas_username'], fn ($q, $v) => $q->where('petugas_username', $v))
            ->when($filters['petugas_role'], fn ($q, $v) => $q->where('petugas_role', $v))
            ->when($filters['sls_code'], fn ($q, $v) => $q->where('sls_code', 'like', '%'.$v.'%'))
            ->when($filters['nama_kecamatan'] ?? null, function ($q) use ($filters) {
                $q->whereIn('petugas_username', $filters['_usernames_in_kecamatan'] ?? []);
            });
    }

    private function aggregateFromRow($row): array
    {
        $total = (int) ($row->total ?? 0);
        $open = (int) ($row->open ?? 0);
        $draft = (int) ($row->draft ?? 0);
        $submitted = (int) ($row->submitted ?? 0);
        $approved = (int) ($row->approved ?? 0);
        $rejected = (int) ($row->rejected ?? 0);
        $nonOpen = $total - $open;
        $submitPlus = $submitted + $approved + $rejected;

        return [
            'total' => $total,
            'open' => $open,
            'draft' => $draft,
            'submitted' => $submitted,
            'approved' => $approved,
            'rejected' => $rejected,
            'non_open' => $nonOpen,
            'submit_plus' => $submitPlus,
            'pct_open' => $total > 0 ? round($open / $total * 100, 1) : 0,
            'pct_draft' => $total > 0 ? round($draft / $total * 100, 1) : 0,
            'pct_submitted' => $total > 0 ? round($submitPlus / $total * 100) : 0,
            'pct_submitted_pencacah' => $total > 0 ? round($submitted / $total * 100, 1) : 0,
            'pct_approved' => $total > 0 ? round($approved / $total * 100, 1) : 0,
            'pct_rejected' => $total > 0 ? round($rejected / $total * 100, 1) : 0,
            'pct_non_open' => $total > 0 ? round($nonOpen / $total * 100) : 0,
        ];
    }
}