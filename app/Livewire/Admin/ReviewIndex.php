<?php

namespace App\Livewire\Admin;

use App\Models\Dinas;
use App\Models\Layanan;
use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReviewIndex extends Component
{
    public ?int $filterDinas = null;
    public string $filterStatus = '';
    public string $search = '';

    public function render()
    {
        // ─── Statistik Global ─────────────────────────────────────────────
        $totalLayanan  = Layanan::count();
        $totalSubmitted = MrKonteks::where('status', 'submitted')->count();
        $totalApproved  = MrKonteks::where('status', 'approved')->count();
        $totalDraft     = MrKonteks::where('status', 'draft')->orWhereNull('status')->count();

        // ─── Query List ───────────────────────────────────────────────────
        $query = MrKonteks::with(['dinas', 'layanan', 'risiko'])
            ->withCount('risiko');

        if ($this->filterDinas) {
            $query->where('dinas_id', $this->filterDinas);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->whereHas('layanan', function ($q) {
                $q->where('nama_layanan', 'like', '%' . $this->search . '%');
            })->orWhereHas('dinas', function ($q) {
                $q->where('nama_dinas', 'like', '%' . $this->search . '%');
            });
        }

        $konteksList = $query->orderByDesc('updated_at')->get();

        return view('livewire.admin.review', [
            'konteksList'   => $konteksList,
            'dinasList'     => Dinas::orderBy('nama_dinas')->get(),
            'stats'         => [
                'total_layanan'   => $totalLayanan,
                'total_submitted' => $totalSubmitted,
                'total_approved'  => $totalApproved,
                'total_draft'     => $totalDraft,
            ],
            'breadcrumb'    => [
                'Admin'             => null,
                'Monitoring Laporan' => null,
            ],
        ]);
    }
}
