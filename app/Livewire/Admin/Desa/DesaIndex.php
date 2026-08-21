<?php

namespace App\Livewire\Admin\Desa;

use App\Models\Desa;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class DesaIndex extends Component
{
    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $kode_desa = '';
    public string $nama_desa = '';
    public string $kecamatan = '';
    public string $kabupaten = '';
    public string $provinsi = '';

    public function openCreateModal(): void
    {
        $this->reset(['editingId', 'kode_desa', 'nama_desa', 'kecamatan', 'kabupaten', 'provinsi']);
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $desa = Desa::findOrFail($id);
        $this->editingId = $desa->id;
        $this->kode_desa = $desa->kode_desa;
        $this->nama_desa = $desa->nama_desa;
        $this->kecamatan = $desa->kecamatan ?? '';
        $this->kabupaten = $desa->kabupaten ?? '';
        $this->provinsi  = $desa->provinsi ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'kode_desa' => 'required|string|max:20|unique:desa,kode_desa,' . $this->editingId,
            'nama_desa' => 'required|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'provinsi'  => 'nullable|string|max:255',
        ]);

        Desa::updateOrCreate(
            ['id' => $this->editingId],
            [
                'kode_desa' => strtoupper($this->kode_desa),
                'nama_desa' => $this->nama_desa,
                'kecamatan' => $this->kecamatan,
                'kabupaten' => $this->kabupaten,
                'provinsi'  => $this->provinsi,
            ]
        );

        $this->showModal = false;
        session()->flash('success', 'Data Desa berhasil disimpan.');
    }

    public function deleteDesa(int $id): void
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();
        session()->flash('success', 'Data Desa berhasil dihapus.');
    }

    public function render()
    {
        $query = Desa::withCount(['users', 'konteks']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_desa', 'like', "%{$this->search}%")
                  ->orWhere('kode_desa', 'like', "%{$this->search}%")
                  ->orWhere('kecamatan', 'like', "%{$this->search}%");
            });
        }

        $desas = $query->orderBy('nama_desa')->get();

        return view('livewire.admin.desa.index', [
            'desas' => $desas,
            'breadcrumb' => [
                'Admin' => null,
                'Kelola Desa' => null,
            ],
        ]);
    }
}
