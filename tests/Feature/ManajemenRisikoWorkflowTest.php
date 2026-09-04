<?php

namespace Tests\Feature;

use App\Livewire\Admin\Dinas\DinasIndex;
use App\Livewire\Admin\ReviewDetail;
use App\Livewire\Admin\ReviewIndex;
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Konteks\KonteksForm;
use App\Livewire\Konteks\KonteksIndex;
use App\Livewire\Konteks\SubmitKonteks;
use App\Livewire\Pemantauan\PemantauanForm;
use App\Livewire\Risiko\PetaRisiko;
use App\Livewire\Risiko\RisikoForm;
use App\Livewire\Risiko\RisikoIndex;
use App\Livewire\Sasaran\SasaranForm;
use App\Livewire\StrukturPelaksana\StrukturPelaksanaForm;
use App\Models\Dinas;
use App\Models\MrKonteks;
use App\Models\MrRisiko;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManajemenRisikoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_flow(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'operator.dukcapil@diskominfo.test')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_dashboard_hub_renders_5_modules(): void
    {
        $operator = User::where('email', 'operator.dukcapil@diskominfo.test')->first();

        $this->actingAs($operator);
        
        $layanan = \App\Models\Layanan::create([
            'dinas_id' => $operator->dinas_id,
            'nama_layanan' => 'Layanan Tes',
            'deskripsi_layanan' => 'Deskripsi tes',
            'status_layanan' => 'berjalan',
            'target_pengguna' => 'Publik/Masyarakat',
            'created_by' => $operator->id,
        ]);

        $response = $this->get('/layanan/' . $layanan->id . '/manajemen');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Risiko');
        $response->assertSee('Manajemen Pengetahuan');
        $response->assertSee('Manajemen Perubahan');
        $response->assertSee('Manajemen Keberlangsungan');
        $response->assertSee('Manajemen Relasi');
    }

    public function test_operator_can_create_konteks_and_fill_all_forms(): void
    {
        $operator = User::where('email', 'operator.dukcapil@diskominfo.test')->first();
        $this->actingAs($operator);

        $layanan = \App\Models\Layanan::create([
            'dinas_id' => $operator->dinas_id,
            'nama_layanan' => 'Layanan Tes MR',
            'deskripsi_layanan' => 'Deskripsi',
            'status_layanan' => 'berjalan',
            'target_pengguna' => 'Publik/Masyarakat',
            'created_by' => $operator->id,
        ]);

        // 1. Create Konteks by clicking Modul MR in Dashboard
        Livewire::test(Dashboard::class, ['layanan' => $layanan])
            ->call('openModulMr')
            ->assertRedirect();

        $konteks = MrKonteks::where('layanan_id', $layanan->id)->first();
        $this->assertNotNull($konteks);

        // 2. Fill F1 & F4
        Livewire::test(KonteksForm::class, ['konteks' => $konteks])
            ->set('nama_instansi', 'Pemerintah Dinas Sukamaju')
            ->set('nama_upr', 'UPR Dinas Sukamaju')
            ->set('tugas_upr', 'Melaksanakan administrasi SPBE dinas')
            ->set('fungsi_upr', 'Pengelolaan layanan digital dinas')
            ->set('selera_risiko', 12)
            ->call('save')
            ->assertHasNoErrors();

        $konteks->refresh();
        $this->assertEquals('UPR Dinas Sukamaju', $konteks->nama_upr);
        $this->assertEquals(12, $konteks->selera_risiko);

        // 3. Fill Sasaran F2
        Livewire::test(SasaranForm::class, ['konteks' => $konteks])
            ->call('addBlock')
            ->set('blocks.0.sasaran_nasional', 'Sasaran Nasional Test')
            ->set('blocks.0.sasaran_upr', 'Meningkatkan kualitas data dinas online')
            ->set('blocks.0.indikator.0.indikator_kinerja', 'Persentase update data per bulan')
            ->set('blocks.0.indikator.0.target_kinerja', '100%')
            ->call('saveBlock', 0)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mr_sasaran_upr', [
            'mr_konteks_id' => $konteks->id,
            'sasaran_upr'   => 'Meningkatkan kualitas data dinas online',
        ]);

        // 4. Fill Struktur F3
        Livewire::test(StrukturPelaksanaForm::class, ['konteks' => $konteks])
            ->set('pemilik_risiko', 'Kepala Dinas Sukamaju')
            ->set('koordinator_risiko', 'Sekretaris Dinas')
            ->set('pengelola_risiko', 'Kaur Keuangan & Kaur Umum')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mr_struktur_pelaksana', [
            'mr_konteks_id'  => $konteks->id,
            'pemilik_risiko' => 'Kepala Dinas Sukamaju',
        ]);

        // 5. Fill Risiko F5-F7
        Livewire::test(RisikoForm::class, ['konteks' => $konteks, 'risiko' => 'new'])
            ->set('kode_risiko', 'SKM-R-01')
            ->set('peristiwa_risiko', 'Server website dinas mengalami downtime saat jam kerja')
            ->set('penyebab', 'Koneksi internet dinas terputus dan tidak ada backup')
            ->set('dampak', 'Pelayanan surat menyurat warga tertunda')
            ->set('level_kemungkinan', 4)
            ->set('level_dampak', 3)
            ->set('keputusan_perlakuan', 'Mengurangi risiko')
            ->set('deskripsi_detail_perlakuan', 'Menyediakan koneksi internet cadangan GSM modem')
            ->set('waktu_rencana_perlakuan', 'Triwulan I 2026')
            ->set('penanggung_jawab', 'Kaur Umum')
            ->set('level_kemungkinan_residual', 2)
            ->set('level_dampak_residual', 2)
            ->set('layanan_pendukung', 'Sistem Informasi Dinas Sukamaju')
            ->set('layanan_prioritas', 'Prioritas')
            ->call('save')
            ->assertHasNoErrors();

        $risiko = MrRisiko::where('kode_risiko', 'SKM-R-01')->first();
        $this->assertNotNull($risiko);
        $this->assertEquals(12, $risiko->besaran_risiko); // 4 x 3 = 12 di matriks SPBE
        $this->assertEquals(1, $risiko->prioritas_risiko);

        // 6. Test Submit flow on Dashboard
        Livewire::test(Dashboard::class, ['layanan' => $layanan])
            ->call('submitLayanan')
            ->assertHasNoErrors();

        $konteks->refresh();
        $risiko->refresh();
        $this->assertEquals('submitted', $konteks->status);
        $this->assertEquals('submitted', $risiko->status);
    }

    public function test_admin_review_approve_and_reject_flow(): void
    {
        $admin = User::where('email', 'admin@diskominfo.test')->first();
        $operator = User::where('email', 'operator.dukcapil@diskominfo.test')->first();

        $layanan = \App\Models\Layanan::create([
            'dinas_id'        => $operator->dinas_id,
            'nama_layanan'    => 'Layanan Tes MR',
            'deskripsi_layanan' => 'Deskripsi',
            'status_layanan'  => 'berjalan',
            'target_pengguna' => 'Publik/Masyarakat',
            'created_by'      => $operator->id,
        ]);

        // Create submitted context with 2 risks
        $konteks = MrKonteks::create([
            'dinas_id'        => $operator->dinas_id,
            'layanan_id'      => $layanan->id,
            'nama_instansi'   => 'Dinas Sukamaju',
            'nama_upr'        => 'UPR Sukamaju',
            'tahun_penilaian' => 2026,
            'status'          => 'submitted',
        ]);

        MrRisiko::create([
            'mr_konteks_id'     => $konteks->id,
            'kode_risiko'       => 'SKM-R-01',
            'peristiwa_risiko'  => 'Kebocoran data penduduk',
            'level_kemungkinan' => 3,
            'level_dampak'      => 4,
            'status'            => 'submitted',
        ]);

        MrRisiko::create([
            'mr_konteks_id'     => $konteks->id,
            'kode_risiko'       => 'SKM-R-02',
            'peristiwa_risiko'  => 'Gagal login aplikasi SPBE',
            'level_kemungkinan' => 2,
            'level_dampak'      => 2,
            'status'            => 'submitted',
        ]);

        $this->actingAs($admin);

        // Admin sees submitted data in monitoring index
        Livewire::test(ReviewIndex::class)
            ->assertSee('Monitoring Laporan')
            ->assertSee('Layanan Tes MR') // nama_layanan muncul di tabel
            ->assertSee('Laporan Terkirim') // badge status submitted
            ->assertHasNoErrors();

        // Admin can view detail (read-only)
        Livewire::test(ReviewDetail::class, ['konteks' => $konteks])
            ->assertSee('UPR Sukamaju')
            ->assertSee('SKM-R-01')
            ->assertSee('SKM-R-02')
            ->assertSee('Hanya untuk dipantau') // read-only notice
            ->assertHasNoErrors();

        // Konteks status stays submitted (no approval logic)
        $konteks->refresh();
        $this->assertEquals('submitted', $konteks->status);
    }


    public function test_admin_desa_and_user_crud(): void
    {
        $admin = User::where('email', 'admin@diskominfo.test')->first();
        $this->actingAs($admin);

        // Create new Dinas
        Livewire::test(DinasIndex::class)
            ->set('alias', 'BDG')
            ->set('nama_dinas', 'Dinas Bojonggede')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('dinas', ['alias' => 'BDG']);

        $dinas = Dinas::where('alias', 'BDG')->first();
        $operatorRole = Role::where('name', 'operator')->first();

        // Create new Operator User
        Livewire::test(UserIndex::class)
            ->set('name', 'Operator Bojonggede')
            ->set('email', 'operator.bdg@diskominfo.test')
            ->set('password', 'secret123')
            ->set('role_id', $operatorRole->id)
            ->set('dinas_id', $dinas->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'operator.bdg@diskominfo.test']);
    }
}
