<?php

namespace App\Livewire\Konteks;

use App\Models\Dinas;
use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class KonteksIndex extends Component
{
    public ?int $filterDesa = null;
    public string $filterStatus = '';

    public function render()
    {
        $user = auth()->user();
        $query = MrKonteks::with(['dinas', 'layanan', 'risiko'])->withCount('risiko');

        // Operator: hanya desanya sendiri
        if ($user->isOperator()) {
            $query->where('dinas_id', $user->dinas_id);
        } else {
            // Admin: bisa filter by dinas
            if ($this->filterDesa) {
                $query->where('dinas_id', $this->filterDesa);
            }
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $konteks = $query->orderByDesc('created_at')->get();

        return view('livewire.konteks.index', [
            'konteks' => $konteks,
            'dinasList' => $user->isAdmin() ? Dinas::orderBy('nama_dinas')->get() : collect(),
            'breadcrumb' => ['Manajemen Risiko' => route('konteks.index'), 'Daftar Konteks' => null],
        ]);
    }
}
