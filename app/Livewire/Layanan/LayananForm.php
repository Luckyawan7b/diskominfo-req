<?php

namespace App\Livewire\Layanan;

use App\Models\Layanan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LayananForm extends Component
{
    public ?Layanan $layanan = null;

    // ─── Stepper state ────────────────────────────────────────────────────────
    public int $currentStep = 1;
    public int $totalSteps  = 4;
    public bool $isSaving   = false;

    // ─── Step 1: Identitas Layanan ────────────────────────────────────────────
    public $nama_layanan      = '';
    public $deskripsi_layanan = '';
    public $bidang_bagian     = '';
    public $status_layanan    = 'berjalan';
    public $target_pengguna   = 'Publik/Masyarakat';
    public $is_prioritas      = false;

    // ─── Step 2: Data & Integrasi ─────────────────────────────────────────────
    public $kl_terkait;
    public $supplier_data;
    public $nama_data_input;
    public $nama_data_output;
    public $sifat_data;
    public $jenis_data;
    public $validitas_data;
    public $interoperabilitas  = false;
    public $tujuan_integrasi;
    public $metode_integrasi;
    public $link_dokumen_integrasi;

    // ─── Step 3: Aplikasi & Infrastruktur ────────────────────────────────────
    public $nama_aplikasi;
    public $tipe_aplikasi;
    public $link_aplikasi;
    public $keluaran_aplikasi;
    public $letak_server;
    public $tahun_pembuatan;

    // ─── Step 4: Dokumen Pendukung ────────────────────────────────────────────
    public $link_dpa;
    public $link_sla;
    public $link_sop;
    public $helpdesk;

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount(Layanan $layanan = null)
    {
        if ($layanan && $layanan->exists) {
            if (Auth::user()->isOperator() && $layanan->dinas_id !== Auth::user()->dinas_id) {
                abort(403, 'Unauthorized action.');
            }
            $this->layanan = $layanan;
            $this->fill($layanan->toArray());
        }
    }

    // ─── Validation rules per step ───────────────────────────────────────────

    protected function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => [
                'nama_layanan'      => 'required|string|max:255',
                'deskripsi_layanan' => 'required|string',
                'status_layanan'    => 'required|in:berjalan,direncanakan,dihentikan',
                'target_pengguna'   => 'required|in:Publik/Masyarakat,Internal Pemerintahan',
                'bidang_bagian'     => 'nullable|string|max:255',
                'is_prioritas'      => 'boolean',
            ],
            2 => [
                'kl_terkait'              => 'nullable|string|max:255',
                'supplier_data'           => 'nullable|string|max:255',
                'nama_data_input'         => 'nullable|string',
                'nama_data_output'        => 'nullable|string',
                'sifat_data'              => 'nullable|string|max:255',
                'jenis_data'              => 'nullable|string|max:255',
                'validitas_data'          => 'nullable|string|max:255',
                'interoperabilitas'       => 'boolean',
                'tujuan_integrasi'        => 'nullable|string',
                'metode_integrasi'        => 'nullable|string|max:255',
                'link_dokumen_integrasi'  => 'nullable|url',
            ],
            3 => [
                'nama_aplikasi'    => 'nullable|string|max:255',
                'tipe_aplikasi'    => 'nullable|string|max:255',
                'link_aplikasi'    => 'nullable|url',
                'keluaran_aplikasi'=> 'nullable|string',
                'letak_server'     => 'nullable|string|max:255',
                'tahun_pembuatan'  => 'nullable|integer',
            ],
            4 => [
                'link_dpa'  => 'nullable|url',
                'link_sla'  => 'nullable|url',
                'link_sop'  => 'nullable|url',
                'helpdesk'  => 'nullable|string|max:255',
            ],
            default => [],
        };
    }

    protected function rules()
    {
        return array_merge(
            $this->rulesForStep(1),
            $this->rulesForStep(2),
            $this->rulesForStep(3),
            $this->rulesForStep(4),
        );
    }

    // ─── Stepper navigation ──────────────────────────────────────────────────

    public function nextStep()
    {
        // Validasi hanya field step saat ini
        $this->validate($this->rulesForStep($this->currentStep));

        // Auto-save setiap pindah step
        $this->persistData();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        // Simpan data tanpa validasi saat mundur
        $this->persistData(validate: false);

        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step)
    {
        // Hanya bisa loncat ke step yang sudah pernah diisi (layanan sudah exist)
        if ($this->layanan && $this->layanan->exists && $step >= 1 && $step <= $this->totalSteps) {
            $this->persistData(validate: false);
            $this->currentStep = $step;
        }
    }

    // ─── Save ─────────────────────────────────────────────────────────────────

    /**
     * Simpan data ke DB. Dipanggil saat pindah step atau tombol Selesai.
     */
    private function persistData(bool $validate = true): void
    {
        if ($validate) {
            $this->validate($this->rulesForStep($this->currentStep));
        }

        $this->isSaving = true;

        $data = $this->only([
            'nama_layanan', 'deskripsi_layanan', 'bidang_bagian', 'status_layanan',
            'target_pengguna', 'is_prioritas', 'kl_terkait', 'supplier_data',
            'nama_data_input', 'nama_data_output', 'sifat_data', 'jenis_data',
            'validitas_data', 'interoperabilitas', 'tujuan_integrasi', 'metode_integrasi',
            'link_dokumen_integrasi', 'nama_aplikasi', 'tipe_aplikasi', 'link_aplikasi',
            'keluaran_aplikasi', 'letak_server', 'tahun_pembuatan', 'link_dpa',
            'link_sla', 'link_sop', 'helpdesk',
        ]);

        if ($this->layanan && $this->layanan->exists) {
            $this->layanan->update($data);
        } else {
            $data['dinas_id']   = Auth::user()->dinas_id;
            $data['created_by'] = Auth::id();
            $this->layanan      = Layanan::create($data);
        }

        $this->isSaving = false;
    }

    /**
     * Simpan & Selesai — dipanggil di step terakhir.
     */
    public function saveAndFinish()
    {
        $this->persistData();

        session()->flash('success', 'Layanan berhasil disimpan. Silakan mulai mengisi modul.');
        return redirect()->route('layanan.dashboard', ['layanan' => $this->layanan->id]);
    }

    /**
     * Simpan langsung (kompatibilitas dengan wire:submit lama).
     */
    public function save()
    {
        $this->validate();
        $this->persistData();
        session()->flash('success', 'Layanan berhasil disimpan.');
        return redirect()->route('layanan.index');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Apakah step tertentu sudah pernah diisi (untuk stepper navigation) */
    public function isStepComplete(int $step): bool
    {
        return match ($step) {
            1 => !empty($this->nama_layanan) && !empty($this->deskripsi_layanan),
            2 => true, // Opsional, selalu dianggap "bisa dilewati"
            3 => true,
            4 => true,
            default => false,
        };
    }

    /** Label tiap step */
    public function stepLabels(): array
    {
        return [
            1 => ['label' => 'Identitas',       'sub' => 'Nama, deskripsi, status'],
            2 => ['label' => 'Data & Integrasi', 'sub' => 'Sumber data, integrasi'],
            3 => ['label' => 'Aplikasi',         'sub' => 'Teknis & infrastruktur'],
            4 => ['label' => 'Dokumen',          'sub' => 'DPA, SLA, SOP'],
        ];
    }

    public function render()
    {
        return view('livewire.layanan.layanan-form', [
            'stepLabels' => $this->stepLabels(),
            'isEdit'     => $this->layanan && $this->layanan->exists,
        ]);
    }
}
