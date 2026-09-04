<?php

namespace App\Livewire\Mpn;

use App\Models\MpnKonteks;
use App\Models\MpnPengetahuan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PengetahuanIndex extends Component
{
    public MpnKonteks $konteks;

    public function mount(MpnKonteks $konteks)
    {
        $this->konteks = $konteks;
        
        // Ensure user can access this konteks
        $user = auth()->user();
        if ($user->isOperator() && $user->dinas_id !== $this->konteks->dinas_id) {
            abort(403);
        }
    }

    public function render()
    {
        $pengetahuanList = MpnPengetahuan::whereHas('layanan', function ($q) {
            $q->where('mpn_konteks_id', $this->konteks->id);
        })
        ->with(['layanan', 'pengumpulan'])
        ->orderBy('kode_pengetahuan')
        ->get();

        return view('livewire.mpn.pengetahuan-index', [
            'pengetahuanList' => $pengetahuanList,
            'breadcrumb' => [
                'Manajemen Pengetahuan' => route('mpn.index'),
                'Daftar Pengetahuan' => null,
            ],
        ]);
    }
}
