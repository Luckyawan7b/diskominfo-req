<?php

namespace App\Livewire\Mpn;

use App\Models\Dinas;
use App\Models\MpnCapaian;
use App\Models\MpnKonteks;
use App\Models\MpnLayanan;
use App\Models\MpnPengetahuan;
use App\Models\RefAspekPemdi;
use Livewire\Component;

class PerencanaanForm extends Component
{
    public MpnKonteks $konteks;
    public $aspeks = [];

    // State for Capaian (As-Is & To-Be)
    public $capaian = [];

    // State for Layanan & Pengetahuan
    public $layanans = [];

    public function mount(MpnKonteks $konteks)
    {
        $this->konteks = $konteks;
        $this->aspeks = RefAspekPemdi::with('indikators')->orderBy('urutan')->get()->toArray();
        $this->loadData();
    }

    public function loadData()
    {
        // Load Capaian
        $capaians = $this->konteks->capaian()->orderBy('urutan')->get();
        if ($capaians->isEmpty()) {
            $this->capaian = [
                ['id' => null, 'nama_indikator' => 'Persentase layanan yang sudah punya pengetahuan terdokumentasi', 'kondisi_as_is' => '', 'target_to_be' => ''],
                ['id' => null, 'nama_indikator' => 'Kesesuaian pengetahuan dengan kebutuhan pengguna', 'kondisi_as_is' => '', 'target_to_be' => '']
            ];
        } else {
            $this->capaian = $capaians->toArray();
        }

        // Load Layanans
        $layanans = $this->konteks->layanan()->with('pengetahuan')->orderBy('urutan')->get();
        if ($layanans->isEmpty()) {
            $this->addLayanan();
        } else {
            $this->layanans = $layanans->toArray();
        }
    }

    // --- Layanan ---

    public function addLayanan()
    {
        $this->layanans[] = [
            'id' => null,
            'nama_layanan' => '',
            'termasuk_layanan_prioritas' => false,
            'pengetahuan' => [
                $this->getEmptyPengetahuan()
            ]
        ];
    }

    public function removeLayanan($index)
    {
        $layananId = $this->layanans[$index]['id'] ?? null;
        if ($layananId) {
            MpnLayanan::find($layananId)?->delete();
        }
        unset($this->layanans[$index]);
        $this->layanans = array_values($this->layanans);
    }

    // --- Pengetahuan ---

    protected function getEmptyPengetahuan()
    {
        return [
            'id' => null,
            'kode_pengetahuan' => '',
            'nama_pengetahuan' => '',
            'ref_aspek_pemdi_id' => '',
            'ref_indikator_pemdi_id' => '',
            'sudah_terdokumentasi' => false,
            'tipe_dok_teks' => false,
            'tipe_dok_gambar' => false,
            'tipe_dok_audio' => false,
            'tipe_dok_video' => false,
            'penanggung_jawab_dokumentasi' => '',
            'target_waktu_dokumentasi' => '',
            'pemilik_pengetahuan' => ''
        ];
    }

    public function addPengetahuan($layananIndex)
    {
        $this->layanans[$layananIndex]['pengetahuan'][] = $this->getEmptyPengetahuan();
    }

    public function removePengetahuan($layananIndex, $pengetahuanIndex)
    {
        $pengetahuanId = $this->layanans[$layananIndex]['pengetahuan'][$pengetahuanIndex]['id'] ?? null;
        if ($pengetahuanId) {
            MpnPengetahuan::find($pengetahuanId)?->delete();
        }
        unset($this->layanans[$layananIndex]['pengetahuan'][$pengetahuanIndex]);
        $this->layanans[$layananIndex]['pengetahuan'] = array_values($this->layanans[$layananIndex]['pengetahuan']);
    }

    // --- Save Logic ---

    public function saveLayanan($layananIndex)
    {
        // Validation rules dynamic for this specific layanan block
        $rules = [
            "capaian.*.nama_indikator" => 'required',
            "capaian.*.kondisi_as_is" => 'nullable',
            "capaian.*.target_to_be" => 'nullable',
            "layanans.{$layananIndex}.nama_layanan" => 'required',
            "layanans.{$layananIndex}.pengetahuan.*.nama_pengetahuan" => 'required',
            "layanans.{$layananIndex}.pengetahuan.*.ref_aspek_pemdi_id" => 'required',
            "layanans.{$layananIndex}.pengetahuan.*.ref_indikator_pemdi_id" => 'required',
        ];

        // Conditional validation
        foreach ($this->layanans[$layananIndex]['pengetahuan'] as $pIndex => $p) {
            if ($p['sudah_terdokumentasi']) {
                $rules["layanans.{$layananIndex}.pengetahuan.{$pIndex}.penanggung_jawab_dokumentasi"] = 'required';
                $rules["layanans.{$layananIndex}.pengetahuan.{$pIndex}.target_waktu_dokumentasi"] = 'required';
            } else {
                $rules["layanans.{$layananIndex}.pengetahuan.{$pIndex}.pemilik_pengetahuan"] = 'required';
            }
        }

        $this->validate($rules, [
            'required' => 'Wajib diisi.'
        ]);

        // Save Capaian
        foreach ($this->capaian as $cIndex => $cData) {
            MpnCapaian::updateOrCreate(
                ['id' => $cData['id'], 'mpn_konteks_id' => $this->konteks->id],
                [
                    'mpn_konteks_id' => $this->konteks->id,
                    'nama_indikator' => $cData['nama_indikator'],
                    'kondisi_as_is' => $cData['kondisi_as_is'],
                    'target_to_be' => $cData['target_to_be'],
                    'urutan' => $cIndex + 1
                ]
            );
        }

        // Save Layanan
        $lData = $this->layanans[$layananIndex];
        $layanan = MpnLayanan::updateOrCreate(
            ['id' => $lData['id'], 'mpn_konteks_id' => $this->konteks->id],
            [
                'mpn_konteks_id' => $this->konteks->id,
                'nama_layanan' => $lData['nama_layanan'],
                'termasuk_layanan_prioritas' => $lData['termasuk_layanan_prioritas'],
                'urutan' => $layananIndex + 1
            ]
        );
        $this->layanans[$layananIndex]['id'] = $layanan->id;

        // Save Pengetahuan
        foreach ($lData['pengetahuan'] as $pIndex => $pData) {
            $isCreating = empty($pData['id']);
            $pengetahuan = MpnPengetahuan::updateOrCreate(
                ['id' => $pData['id'], 'mpn_layanan_id' => $layanan->id],
                [
                    'mpn_layanan_id'               => $layanan->id,
                    'nama_pengetahuan'              => $pData['nama_pengetahuan'],
                    'ref_aspek_pemdi_id'            => $pData['ref_aspek_pemdi_id'],
                    'ref_indikator_pemdi_id'        => $pData['ref_indikator_pemdi_id'],
                    'sudah_terdokumentasi'          => $pData['sudah_terdokumentasi'],
                    'tipe_dok_teks'                 => $pData['tipe_dok_teks'],
                    'tipe_dok_gambar'               => $pData['tipe_dok_gambar'],
                    'tipe_dok_audio'                => $pData['tipe_dok_audio'],
                    'tipe_dok_video'                => $pData['tipe_dok_video'],
                    'penanggung_jawab_dokumentasi'  => $pData['sudah_terdokumentasi'] ? $pData['penanggung_jawab_dokumentasi'] : null,
                    'target_waktu_dokumentasi'      => $pData['sudah_terdokumentasi'] ? $pData['target_waktu_dokumentasi'] : null,
                    'pemilik_pengetahuan'           => !$pData['sudah_terdokumentasi'] ? $pData['pemilik_pengetahuan'] : null,
                    // On update (not creating), sync status_dokumentasi only if still at initial state
                    'urutan'                        => $pIndex + 1,
                ]
            );

            // On existing record: keep status_dokumentasi unless already upgraded to 'sudah' via Form 2
            if (!$isCreating) {
                $pengetahuan->refresh();
                // Only update status_dokumentasi if it wasn't already set to 'sudah' by Form 2
                if ($pengetahuan->status_dokumentasi !== 'sudah') {
                    $pengetahuan->update([
                        'status_dokumentasi' => $pData['sudah_terdokumentasi'] ? 'sudah' : 'belum',
                    ]);
                }
            }

            $this->layanans[$layananIndex]['pengetahuan'][$pIndex]['id']               = $pengetahuan->id;
            $this->layanans[$layananIndex]['pengetahuan'][$pIndex]['kode_pengetahuan'] = $pengetahuan->kode_pengetahuan;
        }

        session()->flash('success', 'Data Layanan & Pengetahuan berhasil disimpan.');
        $this->loadData(); // reload fresh state
    }

    public function finalize(): void
    {
        // Pastikan ada minimal 1 layanan & 1 pengetahuan sebelum finalisasi
        $layananCount = $this->konteks->layanan()->count();
        if ($layananCount === 0) {
            session()->flash('error', 'Tidak bisa finalisasi: belum ada layanan & pengetahuan yang tersimpan.');
            return;
        }

        $this->konteks->update(['status' => 'final']);
        session()->flash('success', 'Form 1 MPN berhasil difinalisasi. Status sekarang: Final.');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.mpn.perencanaan-form')
            ->layout('layouts.app');
    }
}
