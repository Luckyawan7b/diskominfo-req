<?php

namespace App\Livewire\Admin;

use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReviewDetail extends Component
{
    public MrKonteks $konteks;

    public function mount(MrKonteks $konteks): void
    {
        $this->konteks = $konteks;
    }

    public function render()
    {
        $risikos = $this->konteks->risiko()
            ->with(['kategoriRisiko', 'sasaran', 'perlakuan', 'residual', 'kolomTambahan', 'layananDigital'])
            ->orderBy('prioritas_risiko')
            ->get();

        // Hitung statistik ringkasan risiko
        $risikoStats = [
            'total'       => $risikos->count(),
            'sangat_tinggi' => $risikos->filter(fn($r) => $r->besaran_risiko > 16)->count(),
            'tinggi'      => $risikos->filter(fn($r) => $r->besaran_risiko > 9 && $r->besaran_risiko <= 16)->count(),
            'sedang'      => $risikos->filter(fn($r) => $r->besaran_risiko > 4 && $r->besaran_risiko <= 9)->count(),
            'rendah'      => $risikos->filter(fn($r) => $r->besaran_risiko !== null && $r->besaran_risiko <= 4)->count(),
        ];

        return view('livewire.admin.review-detail', [
            'risikos'      => $risikos,
            'risikoStats'  => $risikoStats,
            'breadcrumb'   => [
                'Admin'              => null,
                'Monitoring Laporan' => route('admin.review.index'),
                ($this->konteks->layanan->nama_layanan ?? 'Layanan') => null,
            ],
        ]);
    }
}
