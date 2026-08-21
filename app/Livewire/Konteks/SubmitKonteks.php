<?php

namespace App\Livewire\Konteks;

use App\Models\MrKonteks;
use Livewire\Component;

class SubmitKonteks extends Component
{
    public MrKonteks $konteks;
    public bool $showModal = false;
    public array $validationErrors = [];

    public function mount(MrKonteks $konteks): void
    {
        $this->konteks = $konteks;
    }

    public function openModal(): void
    {
        $this->validateCompleteness();
        $this->showModal = true;
    }

    public function validateCompleteness(): void
    {
        $this->validationErrors = [];

        // Cek Nama Instansi & UPR
        if (! $this->konteks->nama_instansi || ! $this->konteks->nama_upr) {
            $this->validationErrors[] = 'Identitas Penetapan Konteks (Nama Instansi & UPR) belum lengkap.';
        }

        // Cek Sasaran UPR
        if ($this->konteks->sasaran()->count() === 0) {
            $this->validationErrors[] = 'Belum ada Sasaran UPR yang diinput (Formulir 2).';
        }

        // Cek Risiko
        $risikos = $this->konteks->risiko;
        if ($risikos->count() === 0) {
            $this->validationErrors[] = 'Belum ada risiko yang didaftarkan.';
        } else {
            foreach ($risikos as $r) {
                if (! $r->level_kemungkinan || ! $r->level_dampak) {
                    $this->validationErrors[] = "Risiko {$r->kode_risiko} belum memiliki analisis kemungkinan dan dampak.";
                }
            }
        }
    }

    public function submitToAdmin(): void
    {
        $this->validateCompleteness();

        if (! empty($this->validationErrors)) {
            return;
        }

        // Update Konteks
        $this->konteks->update(['status' => 'submitted']);

        // Update semua risiko berstatus draft / rejected -> submitted
        $this->konteks->risiko()
            ->whereIn('status', ['draft', 'rejected'])
            ->update([
                'status' => 'submitted',
                'catatan_penolakan' => null, // reset catatan reject saat re-submit
            ]);

        $this->showModal = false;
        session()->flash('success', 'Dokumen manajemen risiko berhasil diserahkan ke Admin untuk direview.');
        $this->redirect(route('konteks.index'), navigate: true);
    }

    public function render()
    {
        $canSubmit = in_array($this->konteks->status, ['draft', 'rejected']) && (auth()->user()->isOperator() || auth()->user()->isAdmin());

        return view('livewire.konteks.submit-konteks', [
            'canSubmit' => $canSubmit,
        ]);
    }
}
