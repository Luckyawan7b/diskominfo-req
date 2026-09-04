<?php

namespace App\Livewire\Layanan;

use App\Models\Layanan;
use App\Models\MpnKonteks;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LayananIndex extends Component
{
    public string $search = '';
    public string $filterStatus = '';

    public function render()
    {
        $user  = Auth::user();
        $query = Layanan::with(['mrKonteks'])->withCount([]);

        if ($user && $user->isOperator()) {
            $query->where('dinas_id', $user->dinas_id);
        }

        if ($this->search) {
            $query->where('nama_layanan', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus) {
            $query->where('status_layanan', $this->filterStatus);
        }

        $allLayanans = $query->orderBy('created_at', 'desc')->get();

        // Hitung progress modul per layanan
        $allLayanans = $allLayanans->map(function (Layanan $layanan) {
            $layanan->progress = $this->calculateProgress($layanan);
            return $layanan;
        });

        $prioritas = $allLayanans->where('is_prioritas', true)->values();
        $biasa     = $allLayanans->where('is_prioritas', false)->values();

        return view('livewire.layanan.layanan-index', [
            'layananPrioritas' => $prioritas,
            'layananBiasa'     => $biasa,
            'totalLayanan'     => $allLayanans->count(),
            'breadcrumb'       => ['Daftar Layanan Digital' => null],
        ]);
    }

    /**
     * Hitung berapa modul yang sudah diisi dari 2 modul aktif (MR + MPN).
     * Return array: ['selesai' => int, 'total' => int]
     */
    private function calculateProgress(Layanan $layanan): array
    {
        $total   = 2; // MR dan MPN aktif
        $selesai = 0;

        // Cek MR
        $konteks = $layanan->mrKonteks;
        if ($konteks) {
            $risikoCount  = $konteks->risiko()->count();
            $sasaranCount = $konteks->sasaranUpr()->count();
            $hasStruktur  = $konteks->strukturPelaksana()->exists();
            if ($risikoCount > 0 && $sasaranCount > 0 && $hasStruktur && $konteks->nama_upr) {
                $selesai++;
            }
        }

        // Cek MPN
        try {
            if (MpnKonteks::where('dinas_id', $layanan->dinas_id)->exists()) {
                $selesai++;
            }
        } catch (\Exception $e) {
            // Tabel MPN belum ada
        }

        return ['selesai' => $selesai, 'total' => $total];
    }
}

