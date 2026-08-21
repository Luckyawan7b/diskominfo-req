<?php

namespace App\Livewire\Admin;

use App\Models\MrKonteks;
use App\Models\MrRisiko;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReviewDetail extends Component
{
    public MrKonteks $konteks;
    public ?int $selectedRisikoIdForReject = null;
    public string $catatan_penolakan = '';
    public bool $showRejectModal = false;

    public function mount(MrKonteks $konteks): void
    {
        $this->konteks = $konteks;
    }

    public function approveRisk(int $risikoId): void
    {
        $risiko = MrRisiko::where('mr_konteks_id', $this->konteks->id)->findOrFail($risikoId);
        $risiko->update([
            'status'            => 'approved',
            'catatan_penolakan' => null,
        ]);

        $this->checkAndUpdateKonteksStatus();
        session()->flash('success', "Risiko {$risiko->kode_risiko} berhasil disetujui (Approved).");
    }

    public function openRejectModal(int $risikoId): void
    {
        $this->selectedRisikoIdForReject = $risikoId;
        $this->catatan_penolakan = '';
        $this->showRejectModal = true;
    }

    public function submitRejectRisk(): void
    {
        $this->validate([
            'catatan_penolakan' => 'required|string|min:5',
        ]);

        $risiko = MrRisiko::where('mr_konteks_id', $this->konteks->id)->findOrFail($this->selectedRisikoIdForReject);
        $risiko->update([
            'status'            => 'rejected',
            'catatan_penolakan' => $this->catatan_penolakan,
        ]);

        // Jika ada 1 yang di-reject, konteks status jadi rejected
        $this->konteks->update(['status' => 'rejected']);

        $this->showRejectModal = false;
        $this->reset(['selectedRisikoIdForReject', 'catatan_penolakan']);
        session()->flash('success', "Risiko {$risiko->kode_risiko} ditolak dengan catatan.");
    }

    public function approveAll(): void
    {
        $this->konteks->risiko()->update([
            'status'            => 'approved',
            'catatan_penolakan' => null,
        ]);

        $this->konteks->update(['status' => 'approved']);
        session()->flash('success', 'Semua risiko dan dokumen konteks berhasil disetujui.');
    }

    private function checkAndUpdateKonteksStatus(): void
    {
        $total = $this->konteks->risiko()->count();
        $approved = $this->konteks->risiko()->where('status', 'approved')->count();
        $rejected = $this->konteks->risiko()->where('status', 'rejected')->count();

        if ($rejected > 0) {
            $this->konteks->update(['status' => 'rejected']);
        } elseif ($total > 0 && $approved === $total) {
            $this->konteks->update(['status' => 'approved']);
        }
    }

    public function render()
    {
        $risikos = $this->konteks->risiko()
            ->with(['kategoriRisiko', 'sasaran', 'perlakuan', 'residual', 'kolomTambahan', 'layananDigital'])
            ->orderBy('prioritas_risiko')
            ->get();

        return view('livewire.admin.review-detail', [
            'risikos' => $risikos,
            'breadcrumb' => [
                'Admin' => null,
                'Review & Approval' => route('admin.review.index'),
                ($this->konteks->desa->nama_desa ?? 'Desa') . ' (' . $this->konteks->tahun_penilaian . ')' => null,
            ],
        ]);
    }
}
