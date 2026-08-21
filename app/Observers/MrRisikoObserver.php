<?php

namespace App\Observers;

use App\Models\MrRisiko;
use App\Services\RiskMatrixCalculator;

/**
 * MrRisikoObserver
 *
 * Tugas otomatis yang dijalankan setiap kali ada perubahan pada mr_risiko:
 * 1. Hitung ulang `besaran_risiko` via RiskMatrixCalculator
 * 2. Rank ulang `prioritas_risiko` seluruh baris dalam 1 mr_konteks_id
 */
class MrRisikoObserver
{
    public function __construct(
        private readonly RiskMatrixCalculator $calculator
    ) {}

    public function saving(MrRisiko $risiko): void
    {
        // Hitung besaran_risiko setiap kali level_kemungkinan atau level_dampak berubah
        if (
            $risiko->isDirty(['level_kemungkinan', 'level_dampak']) &&
            $risiko->level_kemungkinan !== null &&
            $risiko->level_dampak !== null
        ) {
            $risiko->besaran_risiko = $this->calculator->hitung(
                $risiko->level_kemungkinan,
                $risiko->level_dampak
            );
        }
    }

    public function saved(MrRisiko $risiko): void
    {
        // Re-rank prioritas_risiko untuk semua risiko dalam konteks yang sama
        if ($risiko->isDirty(['besaran_risiko', 'mr_konteks_id']) || $risiko->wasRecentlyCreated) {
            $this->rankPrioritas($risiko->mr_konteks_id);
        }
    }

    public function deleted(MrRisiko $risiko): void
    {
        // Re-rank setelah soft delete
        $this->rankPrioritas($risiko->mr_konteks_id);
    }

    /**
     * Rank ulang prioritas_risiko dalam 1 konteks.
     *
     * Urutan: besaran_risiko DESC (tertinggi = prioritas 1).
     * Baris dengan besaran_risiko null ditempatkan di akhir.
     */
    private function rankPrioritas(int $konteksId): void
    {
        $risikos = MrRisiko::where('mr_konteks_id', $konteksId)
            ->whereNull('deleted_at')
            ->orderByRaw('CASE WHEN besaran_risiko IS NULL THEN 1 ELSE 0 END, besaran_risiko DESC')
            ->get(['id', 'besaran_risiko']);

        foreach ($risikos as $index => $item) {
            $item->updateQuietly(['prioritas_risiko' => $index + 1]);
        }
    }
}
