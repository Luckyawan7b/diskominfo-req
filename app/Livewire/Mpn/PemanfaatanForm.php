<?php

namespace App\Livewire\Mpn;

use App\Models\MpnKonteks;
use App\Models\MpnPengetahuan;
use App\Models\MpnPemanfaatan;
use App\Models\MpnAlihPengetahuan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PemanfaatanForm extends Component
{
    public MpnKonteks $konteks;
    public MpnPengetahuan $pengetahuan;
    
    // Tab state: 'pemanfaatan' or 'alih_pengetahuan'
    public $activeTab = 'pemanfaatan';

    // --- State for Pemanfaatan ---
    public $pemanfaatan_tanggal;
    public $pemanfaatan_tipe_pengguna = '';
    public $pemanfaatan_unit_pengguna;
    public $pemanfaatan_tujuan;
    public $pemanfaatan_rating = '';

    // --- State for Alih Pengetahuan ---
    public $alih_tanggal_mulai;
    public $alih_tanggal_selesai;
    
    public $alih_metode_pelatihan = false;
    public $alih_metode_workshop = false;
    public $alih_metode_sosialisasi = false;
    public $alih_metode_mentoring = false;
    public $alih_metode_sharing = false;
    public $alih_metode_lainnya = false;
    
    public $alih_keterangan_lainnya;
    public $alih_penerima;
    public $alih_evaluasi;

    public function mount(MpnKonteks $konteks, MpnPengetahuan $pengetahuan)
    {
        $this->konteks = $konteks;
        $this->pengetahuan = $pengetahuan;
        
        $user = auth()->user();
        if ($user->isOperator() && $user->dinas_id !== $this->konteks->dinas_id) {
            abort(403);
        }

        // Form 3 is only available if status_dokumentasi is 'sudah'
        if ($this->pengetahuan->status_dokumentasi !== 'sudah') {
            session()->flash('error', 'Pengetahuan ini belum terdokumentasi (Form 2 belum selesai/tersimpan). Akses Form 3 ditolak.');
            return redirect()->route('mpn.pengetahuan.index', $this->konteks->id);
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function savePemanfaatan()
    {
        $this->validate([
            'pemanfaatan_tanggal' => 'required|date',
            'pemanfaatan_tipe_pengguna' => 'required|in:publik,internal',
            'pemanfaatan_unit_pengguna' => 'required|string|max:255',
            'pemanfaatan_tujuan' => 'required|string',
            'pemanfaatan_rating' => 'required|integer|min:1|max:5',
        ]);

        MpnPemanfaatan::create([
            'mpn_pengetahuan_id' => $this->pengetahuan->id,
            'tanggal_pemanfaatan' => $this->pemanfaatan_tanggal,
            'tipe_pengguna' => $this->pemanfaatan_tipe_pengguna,
            'unit_pengguna' => $this->pemanfaatan_unit_pengguna,
            'tujuan_pemanfaatan' => $this->pemanfaatan_tujuan,
            'rating_pengetahuan' => $this->pemanfaatan_rating,
            'created_by' => auth()->id(),
        ]);

        $this->reset([
            'pemanfaatan_tanggal', 'pemanfaatan_tipe_pengguna', 'pemanfaatan_unit_pengguna', 
            'pemanfaatan_tujuan', 'pemanfaatan_rating'
        ]);

        session()->flash('success_pemanfaatan', 'Log Pemanfaatan Pengetahuan berhasil ditambahkan.');
    }

    public function saveAlihPengetahuan()
    {
        $rules = [
            'alih_tanggal_mulai' => 'required|date',
            'alih_tanggal_selesai' => 'nullable|date|after_or_equal:alih_tanggal_mulai',
            'alih_penerima' => 'required|string',
            'alih_evaluasi' => 'required|string',
        ];

        if ($this->alih_metode_lainnya) {
            $rules['alih_keterangan_lainnya'] = 'required|string|max:255';
        }

        $this->validate($rules);
        
        // Ensure at least one method is selected
        if (!$this->alih_metode_pelatihan && !$this->alih_metode_workshop && !$this->alih_metode_sosialisasi && 
            !$this->alih_metode_mentoring && !$this->alih_metode_sharing && !$this->alih_metode_lainnya) {
            $this->addError('alih_metode', 'Minimal pilih satu metode alih pengetahuan.');
            return;
        }

        MpnAlihPengetahuan::create([
            'mpn_pengetahuan_id' => $this->pengetahuan->id,
            'tanggal_mulai' => $this->alih_tanggal_mulai,
            'tanggal_selesai' => $this->alih_tanggal_selesai ?: null,
            'metode_pelatihan' => $this->alih_metode_pelatihan,
            'metode_workshop' => $this->alih_metode_workshop,
            'metode_sosialisasi' => $this->alih_metode_sosialisasi,
            'metode_mentoring' => $this->alih_metode_mentoring,
            'metode_sharing' => $this->alih_metode_sharing,
            'metode_lainnya' => $this->alih_metode_lainnya,
            'keterangan_lainnya' => $this->alih_metode_lainnya ? $this->alih_keterangan_lainnya : null,
            'penerima_pengetahuan' => $this->alih_penerima,
            'hasil_evaluasi' => $this->alih_evaluasi,
            'created_by' => auth()->id(),
        ]);

        $this->reset([
            'alih_tanggal_mulai', 'alih_tanggal_selesai', 'alih_metode_pelatihan', 'alih_metode_workshop',
            'alih_metode_sosialisasi', 'alih_metode_mentoring', 'alih_metode_sharing', 'alih_metode_lainnya',
            'alih_keterangan_lainnya', 'alih_penerima', 'alih_evaluasi'
        ]);

        session()->flash('success_alih', 'Log Alih Pengetahuan berhasil ditambahkan.');
    }

    public function deletePemanfaatan($id)
    {
        $log = MpnPemanfaatan::findOrFail($id);
        if ($log->mpn_pengetahuan_id === $this->pengetahuan->id) {
            $log->delete();
            session()->flash('success_pemanfaatan', 'Log Pemanfaatan berhasil dihapus.');
        }
    }

    public function deleteAlihPengetahuan($id)
    {
        $log = MpnAlihPengetahuan::findOrFail($id);
        if ($log->mpn_pengetahuan_id === $this->pengetahuan->id) {
            $log->delete();
            session()->flash('success_alih', 'Log Alih Pengetahuan berhasil dihapus.');
        }
    }

    public function render()
    {
        $pemanfaatans = MpnPemanfaatan::where('mpn_pengetahuan_id', $this->pengetahuan->id)
            ->latest()
            ->get();
            
        $alihPengetahuans = MpnAlihPengetahuan::where('mpn_pengetahuan_id', $this->pengetahuan->id)
            ->latest()
            ->get();

        return view('livewire.mpn.pemanfaatan-form', [
            'pemanfaatans' => $pemanfaatans,
            'alihPengetahuans' => $alihPengetahuans,
            'breadcrumb' => [
                'Manajemen Pengetahuan' => route('mpn.index'),
                'Daftar Pengetahuan' => route('mpn.pengetahuan.index', $this->konteks->id),
                'Form 3 (Pemanfaatan)' => null,
            ],
        ]);
    }
}
