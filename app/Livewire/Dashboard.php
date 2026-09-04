<?php

namespace App\Livewire;

use App\Models\Layanan;
use App\Models\MrKonteks;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.hub')]
class Dashboard extends Component
{
    public Layanan $layanan;

    public array $validationErrors = [];
    public bool $showSubmitModal = false;

    public function mount(Layanan $layanan)
    {
        $this->layanan = $layanan;

        $user = auth()->user();
        if ($user->isOperator() && $this->layanan->dinas_id !== $user->dinas_id) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function render()
    {
        $moduleStatuses       = $this->getModuleStatuses();
        $allActiveModulesDone = $this->checkAllActiveModulesDone($moduleStatuses);
        $isSubmitted          = $this->layanan->mrKonteks?->status === 'submitted';

        return view('livewire.dashboard', [
            'modules'              => $this->getModules($moduleStatuses),
            'allActiveModulesDone' => $allActiveModulesDone,
            'isSubmitted'          => $isSubmitted,
        ]);
    }

    // ─── Module Status Computation ────────────────────────────────────────────

    private function getModuleStatuses(): array
    {
        $statuses = [];

        // Modul MR
        $konteks = $this->layanan->mrKonteks;
        if (!$konteks) {
            $statuses['mr'] = ['status' => 'empty', 'label' => 'Belum dimulai', 'summary' => null];
        } else {
            $risikoCount  = $konteks->risiko()->count();
            $sasaranCount = $konteks->sasaranUpr()->count();
            $hasStruktur  = $konteks->strukturPelaksana()->exists();

            $isDone = $sasaranCount > 0 && $risikoCount > 0 && $hasStruktur && $konteks->nama_upr;

            $parts = [];
            if ($risikoCount > 0) $parts[] = $risikoCount . ' risiko';
            if ($sasaranCount > 0) $parts[] = $sasaranCount . ' sasaran';

            $statuses['mr'] = [
                'status'  => $isDone ? 'done' : 'in_progress',
                'label'   => $isDone ? 'Selesai' : 'Sedang dikerjakan',
                'summary' => count($parts) > 0 ? implode(', ', $parts) : null,
            ];
        }

        // Modul MPN — aktif, cek data
        try {
            $mpnKonteks = \App\Models\MpnKonteks::where('dinas_id', $this->layanan->dinas_id)->first();
            $statuses['mpn'] = $mpnKonteks
                ? ['status' => 'in_progress', 'label' => 'Sedang dikerjakan', 'summary' => null]
                : ['status' => 'empty',       'label' => 'Belum dimulai',     'summary' => null];
        } catch (\Exception $e) {
            $statuses['mpn'] = ['status' => 'empty', 'label' => 'Belum dimulai', 'summary' => null];
        }

        // Modul belum aktif
        $statuses['mpr']    = null;
        $statuses['bcpdrp'] = null;
        $statuses['mrp']    = null;

        return $statuses;
    }

    private function checkAllActiveModulesDone(array $moduleStatuses): bool
    {
        foreach ($moduleStatuses as $status) {
            if ($status !== null && $status['status'] !== 'done') {
                return false;
            }
        }
        return true;
    }

    // ─── Module Actions ───────────────────────────────────────────────────────

    public function openModulMr()
    {
        $user    = auth()->user();
        $konteks = MrKonteks::where('layanan_id', $this->layanan->id)->first();

        if (!$konteks) {
            $konteks = MrKonteks::create([
                'dinas_id'        => $this->layanan->dinas_id,
                'layanan_id'      => $this->layanan->id,
                'nama_instansi'   => $this->layanan->dinas->nama_dinas ?? 'Pemerintah Daerah',
                'nama_upr'        => 'UPR ' . $this->layanan->nama_layanan,
                'tahun_penilaian' => date('Y'),
                'selera_risiko'   => 16,
                'status'          => 'draft',
                'created_by'      => $user->id,
            ]);
        }

        return redirect()->route('konteks.form', ['konteks' => $konteks->id]);
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    public function openSubmitModal()
    {
        $this->validateCompleteness();
        $this->showSubmitModal = true;
    }

    public function validateCompleteness()
    {
        $this->validationErrors = [];
        $konteks = $this->layanan->mrKonteks;

        if (!$konteks) {
            $this->validationErrors[] = 'Modul Manajemen Risiko belum dikerjakan.';
        } else {
            if ($konteks->sasaranUpr()->count() === 0) {
                $this->validationErrors[] = 'Belum ada Sasaran UPR yang diinput pada Modul MR.';
            }

            $risikos = $konteks->risiko;
            if ($risikos->count() === 0) {
                $this->validationErrors[] = 'Belum ada risiko yang didaftarkan pada Modul MR.';
            } else {
                foreach ($risikos as $r) {
                    if (! $r->level_kemungkinan || ! $r->level_dampak) {
                        $this->validationErrors[] = "Risiko {$r->kode_risiko} belum memiliki nilai kemungkinan dan dampak.";
                    }
                }
            }
        }
    }

    public function submitLayanan()
    {
        $this->validateCompleteness();

        if (!empty($this->validationErrors)) {
            return;
        }

        $konteks = $this->layanan->mrKonteks;
        if ($konteks) {
            $konteks->update(['status' => 'submitted']);
            $konteks->risiko()
                ->where('status', 'draft')
                ->update(['status' => 'submitted', 'catatan_penolakan' => null]);
        }

        $this->showSubmitModal = false;
        session()->flash('success', 'Laporan layanan berhasil dikirimkan.');
        return redirect()->route('layanan.dashboard', ['layanan' => $this->layanan->id]);
    }

    // ─── Module Definitions ───────────────────────────────────────────────────

    private function getModules(array $moduleStatuses): array
    {
        return [
            [
                'key'         => 'mr',
                'name'        => 'Manajemen Risiko',
                'description' => 'Identifikasi, analisis, dan penanganan risiko SPBE',
                'icon'        => 'shield-check',
                'action'      => 'openModulMr',
                'active'      => true,
                'gradient'    => 'from-emerald-500 to-teal-600',
                'shadow'      => 'shadow-emerald-500/25',
                'bg'          => 'bg-emerald-500/10',
                'text'        => 'text-emerald-400',
                'border'      => 'border-emerald-500/20',
                'status'      => $moduleStatuses['mr'],
            ],
            [
                'key'         => 'mpn',
                'name'        => 'Manajemen Pengetahuan',
                'description' => 'Pengelolaan dan berbagi pengetahuan organisasi',
                'icon'        => 'book-open',
                'route'       => route('mpn.index'),
                'active'      => true,
                'gradient'    => 'from-blue-500 to-indigo-600',
                'shadow'      => 'shadow-blue-500/25',
                'bg'          => 'bg-blue-500/10',
                'text'        => 'text-blue-400',
                'border'      => 'border-blue-500/20',
                'status'      => $moduleStatuses['mpn'],
            ],
            [
                'key'         => 'mpr',
                'name'        => 'Manajemen Perubahan',
                'description' => 'Perencanaan dan pelaksanaan perubahan organisasi',
                'icon'        => 'arrows-right-left',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-amber-500 to-orange-600',
                'shadow'      => 'shadow-amber-500/25',
                'bg'          => 'bg-amber-500/10',
                'text'        => 'text-amber-400',
                'border'      => 'border-amber-500/20',
                'status'      => null,
            ],
            [
                'key'         => 'bcpdrp',
                'name'        => 'Manajemen Keberlangsungan',
                'description' => 'Jaminan kelangsungan layanan dan operasional',
                'icon'        => 'arrow-path',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-violet-500 to-purple-600',
                'shadow'      => 'shadow-violet-500/25',
                'bg'          => 'bg-violet-500/10',
                'text'        => 'text-violet-400',
                'border'      => 'border-violet-500/20',
                'status'      => null,
            ],
            [
                'key'         => 'mrp',
                'name'        => 'Manajemen Relasi',
                'description' => 'Pengelolaan hubungan dengan pemangku kepentingan',
                'icon'        => 'users',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-rose-500 to-pink-600',
                'shadow'      => 'shadow-rose-500/25',
                'bg'          => 'bg-rose-500/10',
                'text'        => 'text-rose-400',
                'border'      => 'border-rose-500/20',
                'status'      => null,
            ],
        ];
    }
}

