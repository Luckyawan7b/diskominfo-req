<?php

namespace Tests\Feature;

use App\Livewire\Admin\Desa\DesaIndex;
use App\Livewire\Admin\ReviewIndex;
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Konteks\KonteksForm;
use App\Livewire\Konteks\KonteksIndex;
use App\Livewire\Layanan\LayananIndex;
use App\Livewire\Pemantauan\PemantauanForm;
use App\Livewire\Risiko\PetaRisiko;
use App\Livewire\Risiko\RisikoForm;
use App\Livewire\Risiko\RisikoIndex;
use App\Livewire\Sasaran\SasaranForm;
use App\Livewire\StrukturPelaksana\StrukturPelaksanaForm;
use App\Models\Desa;
use App\Models\Layanan;
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

    // ─── Helper: buat layanan untuk operator ─────────────────────────────────

    private function createLayananForOperator(User $operator): Layanan
    {
        return Layanan::create([
            'desa_id'         => $operator->desa_id,
            'nama_layanan'    => 'Layanan Test Otomatis',
            'status_layanan'  => 'berjalan',
            'created_by'      => $operator->id,
        ]);
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_flow(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'operator.skm@diskominfo.test')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('layanan.index'));

        $this->assertAuthenticated();
    }

    public function test_operator_without_layanan_is_redirected_to_create_layanan(): void
    {
        // Buat desa baru agar tidak bentrok dengan unique constraint desa_id dari seeder
        $desa = Desa::create([
            'kode_desa' => '999',
            'nama_desa' => 'Desa Test Baru',
            'kecamatan' => 'Kec Test',
            'kabupaten' => 'Kab Test'
        ]);

        $role  = Role::where('name', 'operator')->first();

        $operator = User::create([
            'name'     => 'Operator Baru',
            'email'    => 'operator.baru@test.com',
            'password' => bcrypt('password'),
            'role_id'  => $role->id,
            'desa_id'  => $desa->id,
        ]);

        $this->actingAs($operator);

        // Akses halaman layanan.index harus tetap OK (tidak kena guard)
        $response = $this->get('/');
        $response->assertStatus(200);

        // Akses halaman dalam has.layanan group harus redirect ke layanan.create
        $response = $this->get('/manajemen-risiko');
        $response->assertRedirect(route('layanan.create'));
    }

    public function test_layanan_index_renders_for_operator_with_layanan(): void
    {
        $operator = User::where('email', 'operator.skm@diskominfo.test')->first();
        $this->actingAs($operator);

        $layanan = $this->createLayananForOperator($operator);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Layanan Test Otomatis');
    }

    public function test_dashboard_5_modul_renders_for_layanan(): void
    {
        $operator = User::where('email', 'operator.skm@diskominfo.test')->first();
        $this->actingAs($operator);

        $layanan = $this->createLayananForOperator($operator);

        $response = $this->get(route('layanan.dashboard', $layanan));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Risiko');
        $response->assertSee('Manajemen Pengetahuan');
        $response->assertSee('Manajemen Perubahan');
        $response->assertSee('Manajemen Keberlangsungan');
        $response->assertSee('Manajemen Relasi');
    }

    public function test_operator_can_create_konteks_via_layanan_and_fill_all_forms(): void
    {
        $operator = User::where('email', 'operator.skm@diskominfo.test')->first();
        $this->actingAs($operator);

        // 1. Buat Layanan
        $layanan = $this->createLayananForOperator($operator);

        // 2. Auto-create Konteks MR via KonteksIndex::ensureKonteksForLayanan
        $konteks = KonteksIndex::ensureKonteksForLayanan($layanan, $operator);
        $this->assertNotNull($konteks);
        $this->assertEquals($layanan->id, $konteks->layanan_id);
        $this->assertEquals($operator->desa_id, $konteks->desa_id);

        // 3. Fill F1 & F4 — Konteks
        Livewire::test(KonteksForm::class, ['konteks' => $konteks])
            ->set('nama_instansi', 'Pemerintah Desa Sukamaju')
            ->set('nama_upr', 'UPR Desa Sukamaju')
            ->set('tugas_upr', 'Melaksanakan administrasi SPBE desa')
            ->set('fungsi_upr', 'Pengelolaan layanan digital desa')
            ->set('selera_risiko', 12)
            ->call('save')
            ->assertHasNoErrors();

        $konteks->refresh();
        $this->assertEquals('UPR Desa Sukamaju', $konteks->nama_upr);
        $this->assertEquals(12, $konteks->selera_risiko);

        // 4. Fill Sasaran F2
        Livewire::test(SasaranForm::class, ['konteks' => $konteks])
            ->call('addBlock')
            ->set('blocks.0.sasaran_nasional', 'Sasaran Nasional Test')
            ->set('blocks.0.sasaran_upr', 'Meningkatkan kualitas data desa online')
            ->set('blocks.0.indikator.0.indikator_kinerja', 'Persentase update data per bulan')
            ->set('blocks.0.indikator.0.target_kinerja', '100%')
            ->call('saveBlock', 0)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mr_sasaran_upr', [
            'mr_konteks_id' => $konteks->id,
            'sasaran_upr'   => 'Meningkatkan kualitas data desa online',
        ]);

        // 5. Fill Struktur F3
        Livewire::test(StrukturPelaksanaForm::class, ['konteks' => $konteks])
            ->set('pemilik_risiko', 'Kepala Desa Sukamaju')
            ->set('koordinator_risiko', 'Sekretaris Desa')
            ->set('pengelola_risiko', 'Kaur Keuangan & Kaur Umum')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mr_struktur_pelaksana', [
            'mr_konteks_id'  => $konteks->id,
            'pemilik_risiko' => 'Kepala Desa Sukamaju',
        ]);

        // 6. Fill Risiko F5-F7
        Livewire::test(RisikoForm::class, ['konteks' => $konteks, 'risiko' => 'new'])
            ->set('kode_risiko', 'SKM-R-01')
            ->set('peristiwa_risiko', 'Server website desa mengalami downtime saat jam kerja')
            ->set('penyebab', 'Koneksi internet desa terputus dan tidak ada backup')
            ->set('dampak', 'Pelayanan surat menyurat warga tertunda')
            ->set('level_kemungkinan', 4)
            ->set('level_dampak', 3)
            ->set('keputusan_perlakuan', 'Mengurangi risiko')
            ->set('deskripsi_detail_perlakuan', 'Menyediakan koneksi internet cadangan GSM modem')
            ->set('waktu_rencana_perlakuan', 'Triwulan I 2026')
            ->set('penanggung_jawab', 'Kaur Umum')
            ->set('level_kemungkinan_residual', 2)
            ->set('level_dampak_residual', 2)
            ->set('layanan_pendukung', 'Sistem Informasi Desa Sukamaju')
            ->set('layanan_prioritas', 'Prioritas')
            ->call('save')
            ->assertHasNoErrors();

        $risiko = MrRisiko::where('kode_risiko', 'SKM-R-01')->first();
        $this->assertNotNull($risiko);
        $this->assertEquals(12, $risiko->besaran_risiko); // 4 x 3 = 12
        $this->assertEquals(1, $risiko->prioritas_risiko);
    }

    public function test_admin_review_monitoring_shows_submitted_konteks(): void
    {
        $admin    = User::where('email', 'admin@diskominfo.test')->first();
        $operator = User::where('email', 'operator.skm@diskominfo.test')->first();

        // Buat layanan + konteks dalam status submitted
        $layanan = $this->createLayananForOperator($operator);

        $konteks = MrKonteks::create([
            'desa_id'         => $operator->desa_id,
            'layanan_id'      => $layanan->id,
            'nama_instansi'   => 'Desa Sukamaju',
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

        $this->actingAs($admin);

        // Admin melihat halaman review (sekarang berbasis monitoring)
        Livewire::test(ReviewIndex::class)
            ->assertSee('Desa Sukamaju')
            ->assertSee('2026');
    }

    public function test_admin_desa_and_user_crud(): void
    {
        $admin = User::where('email', 'admin@diskominfo.test')->first();
        $this->actingAs($admin);

        // Create new Desa
        Livewire::test(DesaIndex::class)
            ->set('kode_desa', 'BDG')
            ->set('nama_desa', 'Desa Bojonggede')
            ->set('kecamatan', 'Kecamatan Bojonggede')
            ->set('kabupaten', 'Kabupaten Bogor')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('desa', ['kode_desa' => 'BDG']);

        $desa        = Desa::where('kode_desa', 'BDG')->first();
        $operatorRole = Role::where('name', 'operator')->first();

        // Create new Operator User
        Livewire::test(UserIndex::class)
            ->set('name', 'Operator Bojonggede')
            ->set('email', 'operator.bdg@diskominfo.test')
            ->set('password', 'secret123')
            ->set('role_id', $operatorRole->id)
            ->set('desa_id', $desa->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'operator.bdg@diskominfo.test']);
    }
}
