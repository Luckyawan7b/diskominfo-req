<?php

namespace App\Livewire\Sasaran;

use App\Models\MrIndikatorKinerja;
use App\Models\MrKonteks;
use App\Models\MrSasaranUpr;
use App\Models\RefSasaranNasional;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class SasaranForm extends Component
{
    public MrKonteks $konteks;

    /**
     * Daftar blok Sasaran UPR. Tiap blok:
     * [
     *   'id', 'ref_sasaran_nasional_id', 'sasaran_nasional_baru', 'sasaran_upr',
     *   'indikator' => [ ['id','indikator_kinerja','target_kinerja'], ... ]
     * ]
     */
    public array $blocks = [];

    /** Opsi dropdown Sasaran Pembangunan Nasional yang sudah pernah dipakai (di seluruh instansi) */
    public array $sasaranNasionalOptions = [];

    public function mount(MrKonteks $konteks): void
    {
        $this->konteks = $konteks;
        $this->loadOptions();
        $this->loadBlocks();
    }

    public function loadOptions(): void
    {
        $this->sasaranNasionalOptions = RefSasaranNasional::orderBy('teks_sasaran')
            ->get(['id', 'teks_sasaran'])
            ->map(fn ($r) => ['id' => $r->id, 'teks' => $r->teks_sasaran])
            ->toArray();
    }

    public function loadBlocks(): void
    {
        $this->blocks = $this->konteks->sasaranUpr()
            ->with('indikator')
            ->orderBy('urutan')
            ->get()
            ->map(fn (MrSasaranUpr $upr) => [
                'id'                      => $upr->id,
                'ref_sasaran_nasional_id' => $upr->ref_sasaran_nasional_id,
                'sasaran_nasional_baru'   => '',
                'sasaran_upr'             => $upr->sasaran_upr,
                'indikator' => $upr->indikator->map(fn (MrIndikatorKinerja $ind) => [
                    'id'                => $ind->id,
                    'indikator_kinerja' => $ind->indikator_kinerja ?? '',
                    'target_kinerja'    => $ind->target_kinerja ?? '',
                ])->toArray(),
            ])
            ->toArray();
    }

    public function addBlock(): void
    {
        $upr = MrSasaranUpr::create([
            'mr_konteks_id' => $this->konteks->id,
            'sasaran_upr'   => '',
            'urutan'        => count($this->blocks),
        ]);

        $newInd = MrIndikatorKinerja::create([
            'mr_sasaran_upr_id' => $upr->id,
            'urutan'            => 0,
        ]);

        $this->blocks[] = [
            'id' => $upr->id,
            'ref_sasaran_nasional_id' => null,
            'sasaran_nasional_baru'   => '',
            'sasaran_upr'             => '',
            'indikator' => [
                [
                    'id'                => $newInd->id,
                    'indikator_kinerja' => '',
                    'target_kinerja'    => '',
                ]
            ],
        ];
    }

    public function removeBlock(int $index): void
    {
        $block = $this->blocks[$index] ?? null;
        if (! $block || ! $block['id']) {
            return;
        }

        MrSasaranUpr::find($block['id'])?->delete();
        
        unset($this->blocks[$index]);
        $this->blocks = array_values($this->blocks);

        session()->flash('success', 'Sasaran UPR berhasil dihapus.');
    }

    public function saveBlock(int $index): void
    {
        $block = $this->blocks[$index] ?? null;
        if (! $block || ! $block['id']) {
            return;
        }

        $refId = $block['ref_sasaran_nasional_id'] ?: null;

        // Jika operator tidak memilih dari dropdown tapi mengetik kalimat baru,
        // buatkan referensinya (atau pakai yang sudah ada kalau persis sama).
        if (! $refId && filled($block['sasaran_nasional_baru'] ?? null)) {
            $ref = RefSasaranNasional::firstOrCreate([
                'teks_sasaran' => mb_substr(trim($block['sasaran_nasional_baru']), 0, 500),
            ]);
            $refId = $ref->id;
            
            $this->blocks[$index]['ref_sasaran_nasional_id'] = $refId;
            $this->blocks[$index]['sasaran_nasional_baru'] = '';
            
            $this->loadOptions();
        }

        MrSasaranUpr::find($block['id'])?->update([
            'ref_sasaran_nasional_id' => $refId,
            'sasaran_upr'             => $block['sasaran_upr'],
        ]);

        foreach ($block['indikator'] as $ind) {
            if (! $ind['id']) {
                continue;
            }

            MrIndikatorKinerja::find($ind['id'])?->update([
                'indikator_kinerja' => $ind['indikator_kinerja'],
                'target_kinerja'    => $ind['target_kinerja'],
            ]);
        }

        session()->flash('success', 'Sasaran UPR berhasil disimpan.');
    }

    public function addIndikator(int $blockIndex): void
    {
        $block = $this->blocks[$blockIndex] ?? null;
        if (! $block || ! $block['id']) {
            return;
        }

        $newInd = MrIndikatorKinerja::create([
            'mr_sasaran_upr_id' => $block['id'],
            'urutan'            => count($block['indikator']),
        ]);

        $this->blocks[$blockIndex]['indikator'][] = [
            'id'                => $newInd->id,
            'indikator_kinerja' => '',
            'target_kinerja'    => '',
        ];
    }

    public function removeIndikator(int $blockIndex, int $indIndex): void
    {
        $ind = $this->blocks[$blockIndex]['indikator'][$indIndex] ?? null;
        if (! $ind || ! $ind['id']) {
            return;
        }

        MrIndikatorKinerja::find($ind['id'])?->delete();
        
        unset($this->blocks[$blockIndex]['indikator'][$indIndex]);
        $this->blocks[$blockIndex]['indikator'] = array_values($this->blocks[$blockIndex]['indikator']);
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
