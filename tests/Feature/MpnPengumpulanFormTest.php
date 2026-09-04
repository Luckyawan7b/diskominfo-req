<?php

namespace Tests\Feature;

use App\Livewire\Mpn\PengumpulanForm;
use App\Models\Dinas;
use App\Models\MpnKonteks;
use App\Models\MpnLayanan;
use App\Models\MpnPengetahuan;
use App\Models\RefMetodePengolahan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MpnPengumpulanFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_submit_form_2_for_pengetahuan_sudah_terdokumentasi()
    {
        $dinas = Dinas::where('alias', 'DUKCAPIL')->first();
        $operator = User::where('email', 'operator.dukcapil@diskominfo.test')->first();
        $this->actingAs($operator);

        $konteks = MpnKonteks::create([
            'dinas_id' => $dinas->id,
            'tahun_penilaian' => 2026,
            'status' => 'final',
            'created_by' => $operator->id,
        ]);

        $layanan = MpnLayanan::create([
            'mpn_konteks_id' => $konteks->id,
            'nama_layanan' => 'Layanan Kependudukan',
            'deskripsi_layanan' => 'Deskripsi Kependudukan',
            'pengguna_layanan' => 'Masyarakat',
            'created_by' => $operator->id,
        ]);

        $pengetahuan = MpnPengetahuan::create([
            'mpn_layanan_id' => $layanan->id,
            'nama_pengetahuan' => 'SOP Layanan Kependudukan',
            'jenis_pengetahuan' => 'Eksplisit',
            'pemilik_pengetahuan' => 'Dukcapil',
            'sudah_terdokumentasi' => true,
            'status_dokumentasi' => 'sudah',
            'created_by' => $operator->id,
        ]);

        Livewire::test(PengumpulanForm::class, ['konteks' => $konteks, 'pengetahuan' => $pengetahuan])
            ->set('tanggal_pengumpulan', '2026-01-01')
            ->set('unit_pengumpulan', 'Bidang Pelayanan')
            ->set('lokasi_penyimpanan', 'Manajemen Pengetahuan')
            ->set('tanggal_terakhir_update', '2026-01-01')
            ->set('rating_pengetahuan', 5)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mpn_pengumpulan', [
            'mpn_pengetahuan_id' => $pengetahuan->id,
            'unit_pengumpulan' => 'Bidang Pelayanan',
            'lokasi_penyimpanan' => 'Manajemen Pengetahuan',
            'kode_pengetahuan_baru' => null, // Should be null because it was already 'sudah'
        ]);
        
        $pengetahuan->refresh();
        $this->assertEquals('sudah', $pengetahuan->status_dokumentasi);
    }

    public function test_can_submit_form_2_for_pengetahuan_belum_terdokumentasi_and_generates_rev_id()
    {
        $dinas = Dinas::where('alias', 'DUKCAPIL')->first();
        $operator = User::where('email', 'operator.dukcapil@diskominfo.test')->first();
        $this->actingAs($operator);

        $konteks = MpnKonteks::create([
            'dinas_id' => $dinas->id,
            'tahun_penilaian' => 2026,
            'status' => 'final',
            'created_by' => $operator->id,
        ]);

        $layanan = MpnLayanan::create([
            'mpn_konteks_id' => $konteks->id,
            'nama_layanan' => 'Layanan Kependudukan',
            'deskripsi_layanan' => 'Deskripsi Kependudukan',
            'pengguna_layanan' => 'Masyarakat',
            'created_by' => $operator->id,
        ]);

        $pengetahuan = MpnPengetahuan::create([
            'mpn_layanan_id' => $layanan->id,
            'nama_pengetahuan' => 'Catatan Rahasia',
            'jenis_pengetahuan' => 'Tasit',
            'pemilik_pengetahuan' => 'Staf Senior',
            'sudah_terdokumentasi' => false,
            'status_dokumentasi' => 'belum',
            'created_by' => $operator->id,
        ]);

        $metodeId = RefMetodePengolahan::first()->id;

        Livewire::test(PengumpulanForm::class, ['konteks' => $konteks, 'pengetahuan' => $pengetahuan])
            ->set('tanggal_pengumpulan', '2026-01-01')
            ->set('unit_pengumpulan', 'Bidang Pelayanan')
            ->set('lokasi_penyimpanan', 'Lainnya')
            ->set('keterangan_lokasi_lainnya', 'Google Drive Internal')
            ->set('tanggal_terakhir_update', '2026-01-01')
            ->set('rating_pengetahuan', 4)
            ->set('status_publikasi_simpan', 'belum_dipublikasikan')
            ->set('ref_metode_pengolahan_id', $metodeId)
            ->set('deskripsi_pengolahan', 'Digitalisasi manual')
            ->set('nama_pengetahuan_baru', 'Catatan Rahasia Terdigitalisasi')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mpn_pengumpulan', [
            'mpn_pengetahuan_id' => $pengetahuan->id,
            'keterangan_lokasi_lainnya' => 'Google Drive Internal',
            'status_publikasi_simpan' => 'belum_dipublikasikan',
            'kode_pengetahuan_baru' => $pengetahuan->kode_pengetahuan . '-REV',
        ]);

        $pengetahuan->refresh();
        $this->assertEquals('sudah', $pengetahuan->status_dokumentasi); // Must be updated to 'sudah'
    }
}
