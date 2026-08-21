<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReviewIndex extends Component
{
    public ?int $filterDesa = null;
    public string $filterStatus = 'submitted'; // Default to submitted

    public function render()
    {
        $query = MrKonteks::with(['desa', 'risiko'])->withCount('risiko');

        if ($this->filterDesa) {
            $query->where('desa_id', $this->filterDesa);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $konteksList = $query->orderByDesc('updated_at')->get();

        return view('livewire.admin.review', [
            'konteksList' => $konteksList,
            'desaList'    => Desa::orderBy('nama_desa')->get(),
            'breadcrumb'  => [
                'Admin' => null,
                'Review & Approval' => null,
            ],
        ]);
    }
}
