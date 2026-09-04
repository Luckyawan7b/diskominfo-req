<?php

namespace App\Livewire\Admin\Dinas;

use App\Models\Dinas;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DinasIndex extends Component
{
    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $alias = '';
    public string $nama_dinas = '';

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'alias', 'nama_dinas']);
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $dinas = Dinas::findOrFail($id);
        $this->editingId = $dinas->id;
        $this->alias = $dinas->alias;
        $this->nama_dinas = $dinas->nama_dinas;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'alias'      => 'required|string|max:20|unique:dinas,alias,' . $this->editingId,
            'nama_dinas' => 'required|string|max:255',
        ]);

        Dinas::updateOrCreate(
            ['id' => $this->editingId],
            [
                'alias'      => strtoupper($this->alias),
                'nama_dinas' => $this->nama_dinas,
            ]
        );

        $this->showModal = false;
        session()->flash('success', 'Data Dinas berhasil disimpan.');
    }

    public function deleteDesa(int $id): void
    {
        $dinas = Dinas::findOrFail($id);
        $dinas->delete();
        session()->flash('success', 'Data Dinas berhasil dihapus.');
    }

    public function render()
    {
        $query = Dinas::withCount(['users', 'mrKonteks', 'mpnKonteks']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_dinas', 'like', "%{$this->search}%")
                  ->orWhere('alias', 'like', "%{$this->search}%");
            });
        }

        $dinasList = $query->orderBy('nama_dinas')->get();

        return view('livewire.admin.dinas.index', [
            'dinasList' => $dinasList,
            'breadcrumb' => [
                'Admin' => null,
                'Kelola Dinas' => null,
            ],
        ]);
    }
}
