<?php

namespace App\Livewire\Konteks;

use App\Models\Desa;
use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class KonteksIndex extends Component
{
    public ?int $filterDesa = null;
    public string $filterStatus = '';

    public bool $showCreateModal = false;
    public int $newTahun = 0;

    public function mount(): void
    {
        $this->newTahun = (int) date('Y');
    }

    public function render()
    {
        $user = auth()->user();
        $query = MrKonteks::with(['desa', 'risiko'])->withCount('risiko');

        // Operator: hanya desanya sendiri
        if ($user->isOperator()) {
            $query->where('desa_id', $user->desa_id);
        } else {
            // Admin: bisa filter by desa
            if ($this->filterDesa) {
                $query->where('desa_id', $this->filterDesa);
            }
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $konteks = $query->orderByDesc('tahun_penilaian')->get();

        return view('livewire.konteks.index', [
            'konteks' => $konteks,
            'desaList' => $user->isAdmin() ? Desa::orderBy('nama_desa')->get() : collect(),
            'breadcrumb' => ['Manajemen Risiko' => route('konteks.index'), 'Daftar Konteks' => null],
        ]);
    }

    public function createKonteks(): void
    {
        $user = auth()->user();

        // Operator harus punya desa_id
        $desaId = $user->desa_id;
        if (! $desaId) {
            $this->addError('newTahun', 'Akun Anda belum terhubung ke desa.');
            return;
        }

        // Cek duplikat
        $exists = MrKonteks::where('desa_id', $desaId)
            ->where('tahun_penilaian', $this->newTahun)
            ->exists();

        if ($exists) {
            $this->addError('newTahun', 'Konteks untuk tahun ini sudah ada.');
            return;
        }

        $konteks = MrKonteks::create([
            'desa_id'         => $desaId,
            'nama_instansi'   => $user->desa->nama_desa,
            'nama_upr'        => '',
            'tahun_penilaian' => $this->newTahun,
            'created_by'      => $user->id,
        ]);

        $this->showCreateModal = false;
        $this->redirect(route('konteks.form', $konteks), navigate: true);
    }
}
