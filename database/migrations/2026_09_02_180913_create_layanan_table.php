<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            
            // Relasi dan Meta
            $table->foreignId('dinas_id')->constrained('dinas')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Identitas Layanan
            $table->string('nama_layanan');
            $table->text('deskripsi_layanan');
            $table->string('bidang_bagian')->nullable();
            $table->enum('status_layanan', ['berjalan', 'direncanakan', 'dihentikan']);
            $table->enum('target_pengguna', ['Publik/Masyarakat', 'Internal Pemerintahan']);
            $table->boolean('is_prioritas')->default(false);
            
            // Data & Integrasi
            $table->string('kl_terkait')->nullable();
            $table->string('supplier_data')->nullable();
            $table->text('nama_data_input')->nullable();
            $table->text('nama_data_output')->nullable();
            $table->string('sifat_data')->nullable(); // terbuka, terbatas, tertutup
            $table->string('jenis_data')->nullable();
            $table->string('validitas_data')->nullable();
            $table->boolean('interoperabilitas')->default(false);
            $table->text('tujuan_integrasi')->nullable();
            $table->string('metode_integrasi')->nullable();
            $table->string('link_dokumen_integrasi')->nullable();
            
            // Aplikasi & Infrastruktur
            $table->string('nama_aplikasi')->nullable();
            $table->string('tipe_aplikasi')->nullable();
            $table->string('link_aplikasi')->nullable();
            $table->text('keluaran_aplikasi')->nullable();
            $table->string('letak_server')->nullable();
            $table->integer('tahun_pembuatan')->nullable();
            
            // Dokumen Pendukung
            $table->string('link_dpa')->nullable();
            $table->string('link_sla')->nullable();
            $table->string('link_sop')->nullable();
            $table->string('helpdesk')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
