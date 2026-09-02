<?php

namespace App\Livewire\Admin;

use App\Models\Desa;
use App\Models\Layanan;
use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Monitoring Admin — sebelumnya "Review & Approval"
 *
 * Dengan alur auto-approve yang baru, admin tidak lagi perlu mengambil
 * tindakan. Halaman ini berubah fungsi menjadi monitoring read-only:
 * menampilkan daftar layanan beserta status modul MR masing-masing.
 */
#[Layout('components.layouts.app')]
class ReviewIndex extends Component
{
    public ?int $filterDesa   = null;
    public string $filterStatus = '';

    public function render()
    {
        $query = MrKonteks::with(['desa', 'risiko', 'layanan'])->withCount('risiko');

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
                'Admin'      => null,
                'Monitoring' => null,
            ],
        ]);
    }
}
