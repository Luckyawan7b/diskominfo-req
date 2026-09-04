<?php

namespace App\Livewire\Mpn;

use App\Models\Dinas;
use App\Models\MpnKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class KonteksIndex extends Component
{
    public function createOrOpen(int $dinasId): void
    {
        $user = auth()->user();

        // Operator hanya bisa membuka dinas miliknya
        if ($user->isOperator() && $user->dinas_id !== $dinasId) {
            abort(403);
        }

        $tahun   = date('Y');
        $konteks = MpnKonteks::firstOrCreate(
            ['dinas_id' => $dinasId, 'tahun_penilaian' => $tahun],
            ['status' => 'draft', 'created_by' => $user->id]
        );

        $this->redirect(route('mpn.perencanaan', $konteks->id));
    }

    public function render()
    {
        $user  = auth()->user();
        $tahun = date('Y');

        if ($user->isAdmin()) {
            // Admin: tampilkan semua dinas + status MPN-nya
            $dinasList = Dinas::with([
                'mpnKonteks' => fn ($q) => $q->where('tahun_penilaian', $tahun),
            ])->orderBy('nama_dinas')->get();
        } else {
            // Operator: hanya dinas miliknya
            $dinasList = Dinas::with([
                'mpnKonteks' => fn ($q) => $q->where('tahun_penilaian', $tahun),
            ])->where('id', $user->dinas_id)->get();
        }

        return view('livewire.mpn.konteks-index', [
            'dinasList' => $dinasList,
            'tahun'     => $tahun,
            'breadcrumb' => [
                'Manajemen Pengetahuan' => null,
                'Daftar Konteks MPN'    => null,
            ],
        ]);
    }
}
