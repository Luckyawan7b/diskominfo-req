<?php

namespace App\Livewire\Layanan;

use App\Models\Layanan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.hub')]
class LayananIndex extends Component
{
    public function render()
    {
        $user = auth()->user();

        if ($user->isOperator()) {
            $prioritas = Layanan::where('desa_id', $user->desa_id)
                ->where('is_prioritas', true)
                ->withCount(['mrKonteks'])
                ->orderBy('nama_layanan')
                ->get();

            $reguler = Layanan::where('desa_id', $user->desa_id)
                ->where('is_prioritas', false)
                ->withCount(['mrKonteks'])
                ->orderBy('nama_layanan')
                ->get();
        } else {
            // Admin: tampilkan semua layanan dengan relasi desa
            $prioritas = Layanan::with('desa')
                ->where('is_prioritas', true)
                ->withCount(['mrKonteks'])
                ->orderBy('nama_layanan')
                ->get();

            $reguler = Layanan::with('desa')
                ->where('is_prioritas', false)
                ->withCount(['mrKonteks'])
                ->orderBy('nama_layanan')
                ->get();
        }

        return view('livewire.layanan.layanan-index', [
            'prioritas'   => $prioritas,
            'reguler'     => $reguler,
            'totalLayanan' => $prioritas->count() + $reguler->count(),
        ]);
    }
}
