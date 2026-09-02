<?php

namespace App\Livewire\Admin;

use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Monitoring Detail Admin — sebelumnya "Review & Approval Detail"
 *
 * Halaman ini sekarang bersifat read-only. Tidak ada tombol Approve/Reject.
 * Admin hanya bisa melihat data risiko yang telah disubmit operator.
 */
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

        return view('livewire.admin.review-detail', [
            'risikos'   => $risikos,
            'breadcrumb' => [
                'Admin'      => null,
                'Monitoring' => route('admin.review.index'),
                ($this->konteks->desa->nama_desa ?? 'Desa') . ' — ' . ($this->konteks->layanan->nama_layanan ?? 'Layanan') => null,
            ],
        ])->layout('components.layouts.app', [
            'konteks' => $this->konteks,
        ]);
    }
}
