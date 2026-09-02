<?php

namespace App\Livewire\Konteks;

use App\Models\Desa;
use App\Models\Layanan;
use App\Models\MrKonteks;
use App\Models\MrSasaranUpr;
use App\Models\MrStrukturPelaksana;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class KonteksIndex extends Component
{
    public ?int $filterDesa = null;
    public string $filterStatus = '';

    // ─── Dipakai admin saja ──────────────────────────────────────────────────
    public bool $showCreateModal = false;
    public int $newTahun = 0;
    public int $newTahunPelaksanaan = 0;
    public ?int $duplicateFromId = null;

    public function mount(): void
    {
        $this->newTahun           = (int) date('Y');
        $this->newTahunPelaksanaan = (int) date('Y');

        // Operator: langsung pastikan ada konteks MR untuk layanan aktif.
        // Jika belum ada, buat otomatis, lalu redirect ke form-nya.
        $user = auth()->user();
        if ($user->isOperator()) {
            // Kita hanya redirect otomatis jika datang dari layanan.dashboard
            // (URL mengandung /layanan/{id}/manajemen → referer atau session)
            $layananId = session('active_layanan_id');
            if ($layananId) {
                $layanan = Layanan::where('desa_id', $user->desa_id)->find($layananId);
                if ($layanan) {
                    $konteks = $this->ensureKonteksForLayanan($layanan, $user);
                    session()->forget('active_layanan_id');
                    $this->redirect(route('konteks.form', $konteks), navigate: true);
                    return;
                }
            }
        }
    }

    /**
     * Ambil atau buat MrKonteks untuk layanan yang diberikan.
     */
    public static function ensureKonteksForLayanan(Layanan $layanan, $user): MrKonteks
    {
        return MrKonteks::firstOrCreate(
            ['layanan_id' => $layanan->id],
            [
                'desa_id'           => $layanan->desa_id,
                'nama_instansi'     => $layanan->desa->nama_desa ?? '',
                'nama_upr'          => $layanan->nama_layanan,
                'tahun_penilaian'   => (int) date('Y'),
                'tahun_pelaksanaan' => (int) date('Y'),
                'status'            => 'draft',
                'created_by'        => $user->id,
            ]
        );
    }

    public function render()
    {
        $user  = auth()->user();
        $query = MrKonteks::with(['desa', 'risiko', 'layanan'])->withCount('risiko');

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

        $previousKonteksOptions = collect();
        if ($user->desa_id) {
            $previousKonteksOptions = MrKonteks::where('desa_id', $user->desa_id)
                ->orderByDesc('tahun_penilaian')
                ->get();
        }

        return view('livewire.konteks.index', [
            'konteks'                => $konteks,
            'desaList'               => $user->isAdmin() ? Desa::orderBy('nama_desa')->get() : collect(),
            'previousKonteksOptions' => $previousKonteksOptions,
            'breadcrumb'             => ['Manajemen Risiko' => route('konteks.index'), 'Daftar Konteks' => null],
        ]);
    }

    /**
     * Hanya dipakai Admin untuk membuat konteks tanpa layanan (mode lama).
     */
    public function createKonteks(): void
    {
        $user = auth()->user();

        if (! $user->isAdmin()) {
            $this->addError('newTahun', 'Operator harus membuat konteks melalui Layanan.');
            return;
        }

        $desaId = $this->filterDesa ?? $user->desa_id;
        if (! $desaId) {
            $this->addError('newTahun', 'Pilih Desa terlebih dahulu.');
            return;
        }

        $this->validate([
            'newTahunPelaksanaan' => 'required|integer|min:2020|max:2099',
            'newTahun'            => 'required|integer|min:2020|max:2099',
        ]);

        DB::beginTransaction();
        try {
            $desa    = Desa::findOrFail($desaId);
            $konteks = MrKonteks::create([
                'desa_id'           => $desaId,
                'nama_instansi'     => $desa->nama_desa,
                'nama_upr'          => '',
                'tahun_penilaian'   => $this->newTahun,
                'tahun_pelaksanaan' => $this->newTahunPelaksanaan,
                'created_by'        => $user->id,
            ]);

            if ($this->duplicateFromId) {
                $source = MrKonteks::find($this->duplicateFromId);
                if ($source && $source->desa_id === $desaId) {
                    $this->duplicateNonRiskData($source, $konteks);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('newTahun', 'Terjadi kesalahan: ' . $e->getMessage());
            return;
        }

        $this->showCreateModal = false;
        $this->redirect(route('konteks.form', $konteks), navigate: true);
    }

    private function duplicateNonRiskData(MrKonteks $source, MrKonteks $target): void
    {
        $target->update([
            'nama_instansi' => $source->nama_instansi,
            'nama_upr'      => $source->nama_upr,
            'tugas_upr'     => $source->tugas_upr,
            'fungsi_upr'    => $source->fungsi_upr,
            'selera_risiko' => $source->selera_risiko,
        ]);

        foreach ($source->sasaranUpr as $sasaran) {
            $newSasaran = $target->sasaranUpr()->create([
                'urutan'                  => $sasaran->urutan,
                'ref_sasaran_nasional_id' => $sasaran->ref_sasaran_nasional_id,
                'sasaran_upr'             => $sasaran->sasaran_upr,
            ]);
            foreach ($sasaran->indikator as $indikator) {
                $newSasaran->indikator()->create([
                    'urutan'            => $indikator->urutan,
                    'indikator_kinerja' => $indikator->indikator_kinerja,
                    'target_kinerja'    => $indikator->target_kinerja,
                ]);
            }
        }

        if ($source->strukturPelaksana) {
            $target->strukturPelaksana()->create([
                'pemilik_risiko'     => $source->strukturPelaksana->pemilik_risiko,
                'koordinator_risiko' => $source->strukturPelaksana->koordinator_risiko,
                'pengelola_risiko'   => $source->strukturPelaksana->pengelola_risiko,
            ]);
        }
    }
}
