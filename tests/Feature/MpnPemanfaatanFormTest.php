<?php

namespace Tests\Feature;

use App\Livewire\Mpn\PemanfaatanForm;
use App\Models\Dinas;
use App\Models\MpnAlihPengetahuan;
use App\Models\MpnKonteks;
use App\Models\MpnLayanan;
use App\Models\MpnPemanfaatan;
use App\Models\MpnPengetahuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MpnPemanfaatanFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_cannot_access_form_3_if_pengetahuan_belum_terdokumentasi()
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
            'nama_pengetahuan' => 'SOP Layanan',
            'jenis_pengetahuan' => 'Eksplisit',
            'pemilik_pengetahuan' => 'Dukcapil',
            'sudah_terdokumentasi' => false,
            'status_dokumentasi' => 'belum',
            'created_by' => $operator->id,
        ]);

        // Form 3 will redirect if not 'sudah'
        Livewire::test(PemanfaatanForm::class, ['konteks' => $konteks, 'pengetahuan' => $pengetahuan])
            ->assertRedirect(route('mpn.pengetahuan.index', $konteks->id));
    }

    public function test_can_submit_pemanfaatan_log()
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
            'nama_pengetahuan' => 'SOP Layanan',
            'jenis_pengetahuan' => 'Eksplisit',
            'pemilik_pengetahuan' => 'Dukcapil',
            'sudah_terdokumentasi' => true,
            'status_dokumentasi' => 'sudah',
            'created_by' => $operator->id,
        ]);

        Livewire::test(PemanfaatanForm::class, ['konteks' => $konteks, 'pengetahuan' => $pengetahuan])
            ->set('activeTab', 'pemanfaatan')
            ->set('pemanfaatan_tanggal', '2026-02-01')
            ->set('pemanfaatan_tipe_pengguna', 'publik')
            ->set('pemanfaatan_unit_pengguna', 'Warga Desa Sukamaju')
            ->set('pemanfaatan_tujuan', 'Mengurus KTP')
            ->set('pemanfaatan_rating', 5)
            ->call('savePemanfaatan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mpn_pemanfaatan', [
            'mpn_pengetahuan_id' => $pengetahuan->id,
            'tipe_pengguna' => 'publik',
            'unit_pengguna' => 'Warga Desa Sukamaju',
        ]);
    }

    public function test_can_submit_alih_pengetahuan_log()
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
            'nama_pengetahuan' => 'SOP Layanan',
            'jenis_pengetahuan' => 'Eksplisit',
            'pemilik_pengetahuan' => 'Dukcapil',
            'sudah_terdokumentasi' => true,
            'status_dokumentasi' => 'sudah',
            'created_by' => $operator->id,
        ]);

        Livewire::test(PemanfaatanForm::class, ['konteks' => $konteks, 'pengetahuan' => $pengetahuan])
            ->set('activeTab', 'alih_pengetahuan')
            ->set('alih_tanggal_mulai', '2026-03-01')
            ->set('alih_metode_sosialisasi', true)
            ->set('alih_metode_sharing', true)
            ->set('alih_penerima', 'Seluruh Staf Front Office')
            ->set('alih_evaluasi', 'Staf lebih paham prosedur')
            ->call('saveAlihPengetahuan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mpn_alih_pengetahuan', [
            'mpn_pengetahuan_id' => $pengetahuan->id,
            'metode_sosialisasi' => 1,
            'metode_sharing' => 1,
            'metode_pelatihan' => 0,
            'penerima_pengetahuan' => 'Seluruh Staf Front Office',
        ]);
    }
}
