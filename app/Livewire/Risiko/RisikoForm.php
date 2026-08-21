<?php
namespace App\Livewire\Risiko;

use App\Models\MrKonteks;
use App\Models\MrRisiko;
use App\Models\RefKategoriRisiko;
use App\Services\RiskMatrixCalculator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class RisikoForm extends Component
{
    public MrKonteks $konteks;
    public ?MrRisiko $risikoModel = null;
    public bool $isNew = false;
    public int $activeTab = 1;

    // Tab 1: Identifikasi
    public ?int $mr_sasaran_id = null;
    public string $kode_risiko = '';
    public string $peristiwa_risiko = '';
    public string $penyebab = '';
    public string $dampak_risiko = '';
    public ?int $ref_kategori_risiko_id = null;
    public string $area_dampak = '';

    // Tab 2: Analisis
    public ?int $level_kemungkinan = null;
    public ?int $level_dampak = null;

    // Tab 3: Perlakuan
    public string $keputusan_perlakuan = '';
    public string $deskripsi_detail_perlakuan = '';
    public string $waktu_rencana_perlakuan = '';
    public string $penanggung_jawab = '';

    // Tab 4: Residual
    public ?int $level_kemungkinan_residual = null;
    public ?int $level_dampak_residual = null;

    // Tab 5: Kolom Tambahan (Bagian E)
    public string $layanan_pendukung = '';
    public string $layanan_prioritas = 'Non-Prioritas';
    public string $pemilik_layanan = '';
    public string $strategis_atau_operasional = 'Operasional';
    public bool $lintas_sektor = false;
    public string $ippd_terkait = '';
    public bool $membutuhkan_perubahan = false;

    // Layanan Digital (jika Prioritas)
    public bool $perlu_mkb = false;
    public string $pic = '';
    public string $target_waktu_penyusunan = '';

    public function mount(MrKonteks $konteks, $risiko = null): void
    {
        $this->konteks = $konteks;

        if ($risiko instanceof MrRisiko) {
            $this->risikoModel = $risiko->loadMissing(['perlakuan', 'residual', 'kolomTambahan', 'layananDigital']);
            $this->fillFromRisiko();
        } elseif ($risiko && $risiko !== 'new') {
            $this->risikoModel = MrRisiko::with(['perlakuan', 'residual', 'kolomTambahan', 'layananDigital'])->findOrFail($risiko);
            $this->fillFromRisiko();
        } else {
            $this->isNew = true;
            $count = $konteks->risiko()->count();
            $desa = $konteks->desa;
            $this->kode_risiko = ($desa->kode_desa ?? 'DSA') . '-R-' . ($count + 1);
        }
    }

    private function fillFromRisiko(): void
    {
        $r = $this->risikoModel;
        $this->mr_sasaran_id          = $r->mr_sasaran_id;
        $this->kode_risiko            = $r->kode_risiko ?? '';
        $this->peristiwa_risiko       = $r->peristiwa_risiko ?? '';
        $this->penyebab               = $r->penyebab ?? '';
        $this->dampak_risiko          = $r->dampak_risiko ?? '';
        $this->ref_kategori_risiko_id = $r->ref_kategori_risiko_id;
        $this->area_dampak            = $r->area_dampak ?? '';
        $this->level_kemungkinan      = $r->level_kemungkinan;
        $this->level_dampak           = $r->level_dampak;
        $this->keputusan_perlakuan         = $r->perlakuan?->keputusan_perlakuan ?? '';
        $this->deskripsi_detail_perlakuan  = $r->perlakuan?->deskripsi_detail_perlakuan ?? '';
        $this->waktu_rencana_perlakuan     = $r->perlakuan?->waktu_rencana_perlakuan ?? '';
        $this->penanggung_jawab            = $r->perlakuan?->penanggung_jawab ?? '';
        $this->level_kemungkinan_residual  = $r->residual?->level_kemungkinan;
        $this->level_dampak_residual       = $r->residual?->level_dampak;

        // Kolom Tambahan
        if ($r->kolomTambahan) {
            $this->layanan_pendukung          = $r->kolomTambahan->layanan_pendukung ?? '';
            $this->layanan_prioritas          = $r->kolomTambahan->layanan_prioritas ?? 'Non-Prioritas';
            $this->pemilik_layanan            = $r->kolomTambahan->pemilik_layanan ?? '';
            $this->strategis_atau_operasional = $r->kolomTambahan->strategis_atau_operasional ?? 'Operasional';
            $this->lintas_sektor              = (bool) $r->kolomTambahan->lintas_sektor;
            $this->ippd_terkait               = $r->kolomTambahan->ippd_terkait ?? '';
            $this->membutuhkan_perubahan      = (bool) $r->kolomTambahan->membutuhkan_perubahan;
        }

        // Layanan Digital
        if ($r->layananDigital) {
            $this->perlu_mkb               = (bool) $r->layananDigital->perlu_mkb;
            $this->pic                     = $r->layananDigital->pic ?? '';
            $this->target_waktu_penyusunan = $r->layananDigital->target_waktu_penyusunan ?? '';
        }
    }

    public function save(): void
    {
        $this->validate([
            'kode_risiko'            => 'required|string|max:50',
            'peristiwa_risiko'       => 'required|string',
            'ref_kategori_risiko_id' => 'nullable|exists:ref_kategori_risiko,id',
        ]);

        $data = [
            'mr_konteks_id'          => $this->konteks->id,
            'mr_sasaran_id'          => $this->mr_sasaran_id,
            'kode_risiko'            => $this->kode_risiko,
            'peristiwa_risiko'       => $this->peristiwa_risiko,
            'penyebab'               => $this->penyebab,
            'dampak_risiko'          => $this->dampak_risiko,
            'ref_kategori_risiko_id' => $this->ref_kategori_risiko_id,
            'area_dampak'            => $this->area_dampak ?: null,
            'level_kemungkinan'      => $this->level_kemungkinan,
            'level_dampak'           => $this->level_dampak,
            'created_by'             => auth()->id(),
        ];

        if ($this->isNew) {
            $this->risikoModel = MrRisiko::create($data);
            $this->isNew = false;
        } else {
            $this->risikoModel->update($data);
        }

        // Perlakuan (Tab 3)
        if ($this->keputusan_perlakuan) {
            $this->risikoModel->perlakuan()->updateOrCreate([], [
                'keputusan_perlakuan'         => $this->keputusan_perlakuan,
                'deskripsi_detail_perlakuan'  => $this->deskripsi_detail_perlakuan,
                'waktu_rencana_perlakuan'     => $this->waktu_rencana_perlakuan,
                'penanggung_jawab'            => $this->penanggung_jawab,
            ]);
        }

        // Residual (Tab 4)
        if ($this->level_kemungkinan_residual && $this->level_dampak_residual) {
            $calc = app(RiskMatrixCalculator::class);
            $this->risikoModel->residual()->updateOrCreate([], [
                'level_kemungkinan'  => $this->level_kemungkinan_residual,
                'level_dampak'       => $this->level_dampak_residual,
                'besaran_risiko'     => $calc->calculate($this->level_kemungkinan_residual, $this->level_dampak_residual),
            ]);
        }

        // Kolom Tambahan (Tab 5)
        if ($this->layanan_pendukung || $this->pemilik_layanan || $this->layanan_prioritas) {
            $kolom = $this->risikoModel->kolomTambahan()->updateOrCreate([], [
                'layanan_pendukung'          => $this->layanan_pendukung ?: null,
                'layanan_prioritas'          => $this->layanan_prioritas ?: null,
                'pemilik_layanan'            => $this->pemilik_layanan ?: null,
                'strategis_atau_operasional' => $this->strategis_atau_operasional ?: null,
                'lintas_sektor'              => (bool) $this->lintas_sektor,
                'ippd_terkait'               => $this->ippd_terkait ?: null,
                'membutuhkan_perubahan'      => (bool) $this->membutuhkan_perubahan,
            ]);

            // Layanan Digital (jika prioritas)
            if ($this->layanan_prioritas === 'Prioritas') {
                $this->risikoModel->layananDigital()->updateOrCreate([], [
                    'perlu_mkb'               => (bool) $this->perlu_mkb,
                    'pic'                     => $this->pic ?: null,
                    'target_waktu_penyusunan' => $this->target_waktu_penyusunan ?: null,
                ]);
            }
        }

        session()->flash('success', 'Risiko berhasil disimpan.');
        $this->redirect(route('risiko.index', $this->konteks), navigate: true);
    }

    public function render()
    {
        $calc = app(RiskMatrixCalculator::class);
        $besaran = ($this->level_kemungkinan && $this->level_dampak) ? $calc->calculate($this->level_kemungkinan, $this->level_dampak) : null;
        $besaranResidual = ($this->level_kemungkinan_residual && $this->level_dampak_residual) ? $calc->calculate($this->level_kemungkinan_residual, $this->level_dampak_residual) : null;

        return view('livewire.risiko.form', [
            'kategoriList' => RefKategoriRisiko::orderBy('id')->get(),
            'sasaranList'  => $this->konteks->sasaran()->orderBy('urutan')->get(),
            'besaran'      => $besaran,
            'besaranLabel' => $besaran ? $calc->label($besaran) : null,
            'besaranResidual' => $besaranResidual,
            'isEditable'   => $this->konteks->isEditableByOperator() || auth()->user()->isAdmin(),
            'breadcrumb'   => [
                'Manajemen Risiko' => route('konteks.index'),
                'Risiko' => route('risiko.index', $this->konteks),
                ($this->isNew ? 'Baru' : $this->kode_risiko) => null,
            ],
        ]);
    }
}
