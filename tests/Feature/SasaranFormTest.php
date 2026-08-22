<?php

namespace Tests\Feature;

use App\Livewire\Sasaran\SasaranForm;
use App\Models\Desa;
use App\Models\MrIndikatorKinerja;
use App\Models\MrKonteks;
use App\Models\MrSasaranUpr;
use App\Models\RefSasaranNasional;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SasaranFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_add_save_and_manage_sasaran_upr_with_new_and_existing_national_goals(): void
    {
        $operator = User::where('email', 'operator.skm@diskominfo.test')->first();
        $this->actingAs($operator);

        $konteks = MrKonteks::create([
            'desa_id'         => $operator->desa_id,
            'nama_instansi'   => $operator->desa->nama_desa,
            'nama_upr'        => 'UPR Desa Sukamaju',
            'tahun_penilaian' => 2026,
            'status'          => 'draft',
            'created_by'      => $operator->id,
        ]);

        // 1. Operator adds a block and types a brand new Sasaran Pembangunan Nasional
        $testComponent = Livewire::test(SasaranForm::class, ['konteks' => $konteks])
            ->call('addBlock')
            ->set('blocks.0.sasaran_upr', 'Peningkatan Kualitas Pelayanan Publik Desa')
            ->set('blocks.0.sasaran_nasional', 'Terwujudnya Tata Kelola Pemerintahan Desa Digital')
            ->set('blocks.0.indikator.0.indikator_kinerja', 'Tingkat kepuasan warga')
            ->set('blocks.0.indikator.0.target_kinerja', '85%')
            ->call('saveBlock', 0)
            ->assertHasNoErrors();

        // Check database for national ref and UPR
        $this->assertDatabaseHas('ref_sasaran_nasional', [
            'teks_sasaran' => 'Terwujudnya Tata Kelola Pemerintahan Desa Digital',
        ]);

        $createdRef = RefSasaranNasional::where('teks_sasaran', 'Terwujudnya Tata Kelola Pemerintahan Desa Digital')->first();
        $this->assertNotNull($createdRef);

        $this->assertDatabaseHas('mr_sasaran_upr', [
            'mr_konteks_id' => $konteks->id,
            'sasaran_upr'   => 'Peningkatan Kualitas Pelayanan Publik Desa',
            'ref_sasaran_nasional_id' => $createdRef->id,
        ]);

        // 2. Add multiple indicators to the first block
        $testComponent->call('addIndikator', 0)
            ->set('blocks.0.indikator.1.indikator_kinerja', 'Waktu penyelesaian permohonan surat')
            ->set('blocks.0.indikator.1.target_kinerja', '< 1 jam')
            ->call('saveBlock', 0)
            ->assertHasNoErrors();

        $this->assertCount(2, MrIndikatorKinerja::all());

        // 3. Operator adds another block with the same or related Sasaran Nasional
        $testComponent->call('addBlock')
            ->set('blocks.1.sasaran_upr', 'Pengembangan Sistem Arsip Digital Desa')
            ->set('blocks.1.sasaran_nasional', 'Terwujudnya Tata Kelola Pemerintahan Desa Digital')
            ->set('blocks.1.indikator.0.indikator_kinerja', 'Persentase arsip digital')
            ->set('blocks.1.indikator.0.target_kinerja', '100%')
            ->call('saveBlock', 1)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mr_sasaran_upr', [
            'mr_konteks_id' => $konteks->id,
            'sasaran_upr'   => 'Pengembangan Sistem Arsip Digital Desa',
            'ref_sasaran_nasional_id' => $createdRef->id,
        ]);

        // 4. Remove an indicator from the first block
        $testComponent->call('removeIndikator', 0, 1)
            ->assertHasNoErrors();

        $firstBlockUpr = MrSasaranUpr::where('sasaran_upr', 'Peningkatan Kualitas Pelayanan Publik Desa')->first();
        $this->assertCount(1, $firstBlockUpr->indikator);

        // 5. Remove the second block
        $testComponent->call('removeBlock', 1)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('mr_sasaran_upr', [
            'sasaran_upr' => 'Pengembangan Sistem Arsip Digital Desa',
        ]);
    }
}
