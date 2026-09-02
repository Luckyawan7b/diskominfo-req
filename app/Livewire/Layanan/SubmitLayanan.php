<?php

namespace App\Livewire\Layanan;

use App\Models\Layanan;
use App\Models\MrKonteks;
use Livewire\Component;

/**
 * Komponen submit di level Layanan.
 *
 * Sesuai kebijakan baru (auto-approve):
 * - Saat disubmit, status mr_konteks & semua risiko otomatis menjadi 'approved'
 * - Tidak ada alur review manual admin (halaman admin berubah jadi Monitoring)
 * - Validasi: modul MR wajib terisi. Modul 2-5 belum ada, jadi cukup tampilkan info.
 */
class SubmitLayanan extends Component
{
    public Layanan $layanan;
    public bool $showModal = false;
    public array $validationErrors = [];

    public function mount(Layanan $layanan): void
    {
        $this->layanan = $layanan;
    }

    public function openModal(): void
    {
        $this->checkCompleteness();
        $this->showModal = true;
    }

    private function checkCompleteness(): void
    {
        $this->validationErrors = [];

        $mrKonteks = MrKonteks::where('layanan_id', $this->layanan->id)->first();

        // Modul MR — satu-satunya yang wajib dicek saat ini
        if (! $mrKonteks) {
            $this->validationErrors[] = 'Modul Manajemen Risiko belum diisi.';
            return;
        }

        if (! $mrKonteks->nama_instansi || ! $mrKonteks->nama_upr) {
            $this->validationErrors[] = 'Penetapan Konteks (Nama Instansi & UPR) di modul Manajemen Risiko belum lengkap.';
        }

        if ($mrKonteks->sasaranUpr()->count() === 0) {
            $this->validationErrors[] = 'Sasaran UPR (Formulir 2) di modul Manajemen Risiko belum diisi.';
        }

        $risikos = $mrKonteks->risiko;
        if ($risikos->count() === 0) {
            $this->validationErrors[] = 'Belum ada risiko yang didaftarkan di modul Manajemen Risiko.';
        } else {
            foreach ($risikos as $r) {
                if (! $r->level_kemungkinan || ! $r->level_dampak) {
                    $this->validationErrors[] = "Risiko {$r->kode_risiko}: analisis kemungkinan & dampak belum diisi.";
                }
            }
        }
    }

    public function submitLayanan(): void
    {
        $this->checkCompleteness();

        if (! empty($this->validationErrors)) {
            return;
        }

        $mrKonteks = MrKonteks::where('layanan_id', $this->layanan->id)->first();

        if ($mrKonteks) {
            // Auto-approve: langsung set status approved (tidak perlu review admin)
            $mrKonteks->update(['status' => 'approved']);
            $mrKonteks->risiko()->whereIn('status', ['draft', 'submitted'])->update(['status' => 'approved']);
        }

        $this->showModal = false;
        session()->flash('success', 'Layanan berhasil dikunci. Data Manajemen Risiko telah disetujui secara otomatis.');
        $this->redirect(route('layanan.index'), navigate: true);
    }

    public function render()
    {
        $mrKonteks = MrKonteks::where('layanan_id', $this->layanan->id)->first();
        $isSubmitted = $mrKonteks && $mrKonteks->status === 'approved';

        return view('livewire.layanan.submit-layanan', [
            'isSubmitted' => $isSubmitted,
            'mrKonteks'   => $mrKonteks,
        ]);
    }
}
