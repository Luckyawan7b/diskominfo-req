<?php

namespace App\Livewire\Sasaran;

use App\Models\MrKonteks;
use App\Models\MrSasaran;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SasaranForm extends Component
{
    public MrKonteks $konteks;
    public array $rows = [];

    public function mount(MrKonteks $konteks): void
    {
        $this->konteks = $konteks;
        $this->loadRows();
    }

    public function loadRows(): void
    {
        $this->rows = $this->konteks->sasaran()
            ->orderBy('urutan')
            ->get()
            ->map(fn ($s) => [
                'id'                            => $s->id,
                'sasaran_upr'                   => $s->sasaran_upr,
                'indikator_kinerja'             => $s->indikator_kinerja ?? '',
                'target_kinerja'                => $s->target_kinerja ?? '',
                'sasaran_pembangunan_nasional'  => $s->sasaran_pembangunan_nasional ?? '',
            ])
            ->toArray();
    }

    public function addRow(): void
    {
        $sasaran = MrSasaran::create([
            'mr_konteks_id' => $this->konteks->id,
            'sasaran_upr'   => '',
            'urutan'        => count($this->rows),
        ]);
        $this->loadRows();
    }

    public function saveRow(int $index): void
    {
        $row = $this->rows[$index] ?? null;
        if (! $row || ! $row['id']) return;

        MrSasaran::find($row['id'])?->update([
            'sasaran_upr'                  => $row['sasaran_upr'],
            'indikator_kinerja'            => $row['indikator_kinerja'],
            'target_kinerja'               => $row['target_kinerja'],
            'sasaran_pembangunan_nasional'  => $row['sasaran_pembangunan_nasional'],
        ]);

        session()->flash('success', 'Baris sasaran berhasil disimpan.');
    }

    public function deleteRow(int $index): void
    {
        $row = $this->rows[$index] ?? null;
        if (! $row || ! $row['id']) return;

        MrSasaran::find($row['id'])?->delete();
        $this->loadRows();
    }

    public function render()
    {
        return view('livewire.sasaran.form', [
            'isEditable' => $this->konteks->isEditableByOperator() || auth()->user()->isAdmin(),
            'breadcrumb' => [
                'Manajemen Risiko' => route('konteks.index'),
                'Konteks ' . $this->konteks->tahun_penilaian => route('konteks.form', $this->konteks),
                'Sasaran UPR' => null,
            ],
        ]);
    }
}
