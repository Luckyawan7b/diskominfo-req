<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Struktur Pelaksana — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 3: Pemilik, koordinator, dan pengelola risiko</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('sasaran.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Sasaran
            </a>
            @if($isEditable)
                <button wire:click="save" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            @endif
            <a href="{{ route('risiko.index', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                Daftar Risiko
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>

    <x-risk-wizard :konteks="$konteks" activeStep="Struktur" />

    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 sm:p-8 space-y-6 max-w-3xl">
        {{-- <div class="p-4 rounded-lg bg-emerald-950/20 border border-emerald-500/20 text-xs text-slate-300 leading-relaxed">
            <p class="font-semibold text-emerald-400 mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Panduan Jabatan untuk Pemerintah Dinas:
            </p>
            <p>Struktur pelaksana manajemen risiko di tingkat dinas umumnya diisi oleh perangkat dinas yang bertugas dalam pengambilan keputusan, koordinasi, dan pelayanan operasional.</p>
        </div> --}}

        {{-- Pemilik Risiko --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-semibold text-white">1. Pemilik Risiko</label>
                {{-- <span class="text-xs px-2 py-0.5 rounded bg-slate-700 text-emerald-400 font-medium">Umumnya: Kepala Dinas / Lurah</span> --}}
            </div>
            <p class="text-xs text-slate-400 mb-2 leading-relaxed">
                Diisi dengan nama dan jabatan pimpinan unit kerja pemilik risiko.
            </p>
            <input wire:model="pemilik_risiko" type="text" {{ !$isEditable ? 'disabled' : '' }}
                placeholder="Contoh: Kepala Dinas Sukamaju (H. Ahmad Fauzi)"
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
        </div>

        {{-- Koordinator Risiko --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-semibold text-white">2. Koordinator Risiko</label>
                {{-- <span class="text-xs px-2 py-0.5 rounded bg-slate-700 text-amber-400 font-medium">Umumnya: Sekretaris Dinas</span> --}}
            </div>
            <p class="text-xs text-slate-400 mb-2 leading-relaxed">
                Diisi dengan nama dan jabatan pegawai yang mengoordinasikan proses manajemen risiko antar unit kerja. Koordinator bertugas menyusun jadwal, mengkonsolidasikan daftar risiko dari unit kerja, dan memastikan dokumen risiko terintegrasi dalam dokumen perencanaan (RPJMDes/RPJMD, Renstra, RKPDes/RKPD).
            </p>
            <input wire:model="koordinator_risiko" type="text" {{ !$isEditable ? 'disabled' : '' }}
                placeholder="Contoh: Sekretaris Dinas (Budi Santoso, S.AP)"
                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
        </div>

        {{-- Pengelola Risiko --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="block text-sm font-semibold text-white">3. Pengelola Risiko</label>
                {{-- <span class="text-xs px-2 py-0.5 rounded bg-slate-700 text-sky-400 font-medium">Umumnya: Kaur / Kasi / Tim Teknis</span> --}}
            </div>
            <p class="text-xs text-slate-400 mb-2 leading-relaxed">
                Diisi dengan nama dan jabatan tim teknis yang sehari‑hari melaksanakan identifikasi, analisis, pemantauan, dan pelaporan risiko. Pengelola menyiapkan <em>risk register</em>, memutakhirkan profil risiko, dan menyusun laporan pengendalian risiko untuk disampaikan kepada Pemilik Risiko dan Koordinator Risiko.
            </p>
            <x-textarea-auto wire:model="pengelola_risiko" rows="4" :disabled="!$isEditable"
                placeholder="Contoh:
1. Kasi Pelayanan (Siti Aminah) - Layanan Administrasi Kependudukan
2. Kaur Keuangan (Rudi Hartono) - Pengelolaan Keuangan & APBDes
3. Operator IT / SPBE Dinas (Danang) - Pengelolaan Web & Sistem Online" />
        </div>
    </div>
</div>
