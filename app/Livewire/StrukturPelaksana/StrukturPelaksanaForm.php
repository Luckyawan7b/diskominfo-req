<?php

namespace App\Livewire\StrukturPelaksana;

use App\Models\MrKonteks;
use App\Models\MrStrukturPelaksana as StrukturModel;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class StrukturPelaksanaForm extends Component
{
    public MrKonteks $konteks;
    public string $pemilik_risiko = '';
    public string $koordinator_risiko = '';
    public string $pengelola_risiko = '';

    public function mount(MrKonteks $konteks): void
    {
        $this->konteks = $konteks;
        $struktur = $konteks->strukturPelaksana;
        if ($struktur) {
            $this->pemilik_risiko     = $struktur->pemilik_risiko ?? '';
            $this->koordinator_risiko = $struktur->koordinator_risiko ?? '';
            $this->pengelola_risiko   = $struktur->pengelola_risiko ?? '';
        }
    }

    public function save(): void
    {
        StrukturModel::updateOrCreate(
            ['mr_konteks_id' => $this->konteks->id],
            [
                'pemilik_risiko'     => $this->pemilik_risiko,
                'koordinator_risiko' => $this->koordinator_risiko,
                'pengelola_risiko'   => $this->pengelola_risiko,
            ]
        );
        session()->flash('success', 'Struktur pelaksana berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.struktur-pelaksana.form', [
            'isEditable' => $this->konteks->isEditableByOperator() || auth()->user()->isAdmin(),
            'breadcrumb' => [
                'Manajemen Risiko' => route('konteks.index'),
                'Konteks ' . $this->konteks->tahun_penilaian => route('konteks.form', $this->konteks),
                'Struktur Pelaksana' => null,
            ],
        ]);
    }
}
