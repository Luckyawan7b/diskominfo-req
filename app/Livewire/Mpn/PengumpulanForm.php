<?php

namespace App\Livewire\Mpn;

use App\Models\MpnKonteks;
use App\Models\MpnPengetahuan;
use App\Models\MpnPengumpulan;
use App\Models\RefMetodePengolahan;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PengumpulanForm extends Component
{
    public MpnKonteks $konteks;
    public MpnPengetahuan $pengetahuan;
    
    public $tanggal_pengumpulan;
    public $unit_pengumpulan;
    public $lokasi_penyimpanan;
    public $keterangan_lokasi_lainnya;
    public $tanggal_terakhir_update;
    public $rating_pengetahuan;
    
    public $status_publikasi_simpan;
    public $ref_metode_pengolahan_id;
    public $deskripsi_pengolahan;
    public $nama_pengetahuan_baru;

    public $isBelumDokumentasi;

    public function mount(MpnKonteks $konteks, MpnPengetahuan $pengetahuan)
    {
        $this->konteks = $konteks;
        $this->pengetahuan = $pengetahuan;
        
        $user = auth()->user();
        if ($user->isOperator() && $user->dinas_id !== $this->konteks->dinas_id) {
            abort(403);
        }

        $this->isBelumDokumentasi = $pengetahuan->status_dokumentasi === 'belum';

        $pengumpulan = $pengetahuan->pengumpulan;
        if ($pengumpulan) {
            $this->tanggal_pengumpulan = $pengumpulan->tanggal_pengumpulan?->format('Y-m-d');
            $this->unit_pengumpulan = $pengumpulan->unit_pengumpulan;
            $this->lokasi_penyimpanan = $pengumpulan->lokasi_penyimpanan;
            $this->keterangan_lokasi_lainnya = $pengumpulan->keterangan_lokasi_lainnya;
            $this->tanggal_terakhir_update = $pengumpulan->tanggal_terakhir_update?->format('Y-m-d');
            $this->rating_pengetahuan = $pengumpulan->rating_pengetahuan;
            $this->status_publikasi_simpan = $pengumpulan->status_publikasi_simpan;
            $this->ref_metode_pengolahan_id = $pengumpulan->ref_metode_pengolahan_id;
            $this->deskripsi_pengolahan = $pengumpulan->deskripsi_pengolahan;
            $this->nama_pengetahuan_baru = $pengumpulan->nama_pengetahuan_baru;
            
            // If it has pengumpulan, it means the logic for -REV was already executed.
            // Wait, what if they edit it? The form shouldn't change its visual structure 
            // from "Belum" to "Sudah" if they are editing the same record. 
            // So if `kode_pengetahuan_baru` is present, it means this record was created under 'belum' condition.
            if ($pengumpulan->kode_pengetahuan_baru) {
                $this->isBelumDokumentasi = true;
            } else {
                $this->isBelumDokumentasi = false;
            }
        }
    }

    public function rules()
    {
        $rules = [
            'tanggal_pengumpulan' => 'required|date',
            'unit_pengumpulan' => 'required|string|max:255',
            'lokasi_penyimpanan' => 'required|string|max:255',
            'tanggal_terakhir_update' => 'required|date',
            'rating_pengetahuan' => 'required|integer|min:1|max:5',
        ];

        if ($this->lokasi_penyimpanan === 'Lainnya') {
            $rules['keterangan_lokasi_lainnya'] = 'required|string|max:255';
        }

        if ($this->isBelumDokumentasi) {
            $rules['status_publikasi_simpan'] = ['nullable', Rule::in(['belum_dipublikasikan', 'ditolak', 'dipublikasikan', 'arsip'])];
            $rules['ref_metode_pengolahan_id'] = 'nullable|exists:ref_metode_pengolahan,id';
            $rules['deskripsi_pengolahan'] = 'nullable|string';
            $rules['nama_pengetahuan_baru'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        $pengumpulan = $this->pengetahuan->pengumpulan ?? new MpnPengumpulan();
        
        $pengumpulan->mpn_pengetahuan_id = $this->pengetahuan->id;
        $pengumpulan->tanggal_pengumpulan = $this->tanggal_pengumpulan;
        $pengumpulan->unit_pengumpulan = $this->unit_pengumpulan;
        $pengumpulan->lokasi_penyimpanan = $this->lokasi_penyimpanan;
        $pengumpulan->keterangan_lokasi_lainnya = $this->lokasi_penyimpanan === 'Lainnya' ? $this->keterangan_lokasi_lainnya : null;
        $pengumpulan->tanggal_terakhir_update = $this->tanggal_terakhir_update;
        $pengumpulan->rating_pengetahuan = $this->rating_pengetahuan;
        
        if ($this->isBelumDokumentasi) {
            $pengumpulan->status_publikasi_simpan = $this->status_publikasi_simpan;
            $pengumpulan->ref_metode_pengolahan_id = $this->ref_metode_pengolahan_id ?: null;
            $pengumpulan->deskripsi_pengolahan = $this->deskripsi_pengolahan;
            $pengumpulan->nama_pengetahuan_baru = $this->nama_pengetahuan_baru;
            
            if (!$pengumpulan->kode_pengetahuan_baru) {
                $pengumpulan->kode_pengetahuan_baru = $this->pengetahuan->kode_pengetahuan . '-REV';
            }
        } else {
            $pengumpulan->status_publikasi_simpan = null;
            $pengumpulan->ref_metode_pengolahan_id = null;
            $pengumpulan->deskripsi_pengolahan = null;
            $pengumpulan->nama_pengetahuan_baru = null;
            $pengumpulan->kode_pengetahuan_baru = null;
        }

        if (!$pengumpulan->exists) {
            $pengumpulan->created_by = auth()->id();
        }
        
        $pengumpulan->save();

        // Update status dokumentasi to 'sudah' if it was 'belum'
        if ($this->pengetahuan->status_dokumentasi === 'belum') {
            $this->pengetahuan->update(['status_dokumentasi' => 'sudah']);
        }

        session()->flash('success', 'Data Pengumpulan & Pengelolaan Pengetahuan berhasil disimpan.');
        
        return redirect()->route('mpn.pengetahuan.index', $this->konteks->id);
    }

    public function render()
    {
        return view('livewire.mpn.pengumpulan-form', [
            'metodeOptions' => RefMetodePengolahan::all(),
            'lokasiOptions' => [
                'Manajemen Risiko',
                'Manajemen Pengetahuan',
                'Manajemen Perubahan',
                'Manajemen Keberlangsungan',
                'Manajemen Relasi',
                'Lainnya'
            ],
            'statusPublikasiOptions' => [
                'belum_dipublikasikan' => 'Belum Dipublikasikan',
                'ditolak' => 'Ditolak',
                'dipublikasikan' => 'Dipublikasikan',
                'arsip' => 'Arsip'
            ],
            'breadcrumb' => [
                'Manajemen Pengetahuan' => route('mpn.index'),
                'Daftar Pengetahuan' => route('mpn.pengetahuan.index', $this->konteks->id),
                'Form 2 (Pengumpulan)' => null,
            ],
        ]);
    }
}
