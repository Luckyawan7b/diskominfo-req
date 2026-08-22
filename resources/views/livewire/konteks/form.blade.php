<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Penetapan Konteks — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 1 & 4: Identitas instansi dan selera risiko</p>
        </div>
        <div class="flex gap-3">
            @if($isEditable)
                <button wire:click="save" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Draft
                </button>
            @endif
            <a href="{{ route('sasaran.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                Sasaran UPR
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>

    {{-- Wizard steps --}}
    <x-risk-wizard :konteks="$konteks" activeStep="Konteks" />

    {{-- Form --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 sm:p-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Nama Instansi --}}
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-1">Nama Instansi <span class="text-red-400">*</span></label>
                <p class="text-xs text-slate-400 mb-2">Diisi dengan nama instansi yang akan dinilai manajemen risikonya.</p>
                <input wire:model="nama_instansi" type="text" {{ !$isEditable ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    placeholder="Contoh: Pemerintah Desa Sukamaju / Diskominfo Kabupaten">
                @error('nama_instansi') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Nama UPR --}}
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-1">Nama UPR <span class="text-red-400">*</span></label>
                <p class="text-xs text-slate-400 mb-2">Diisi dengan nama Unit Pemilik Risiko yang akan dinilai risikonya.</p>
                <input wire:model="nama_upr" type="text" {{ !$isEditable ? 'disabled' : '' }}
                    class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    placeholder="Contoh: Pemerintah Desa Sukamaju">
                @error('nama_upr') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Tugas UPR --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1.5">
                <label class="block text-sm font-semibold text-slate-200">Tugas UPR</label>
                <span class="text-[11px] text-slate-400">Contoh: Melaksanakan pelayanan administrasi kependudukan dan penyelenggaraan sistem desa</span>
            </div>
            <p class="text-xs text-slate-400 mb-2">Diisi dengan tugas Unit Pemilik Risiko yang akan dinilai risikonya.</p>
            <x-textarea-auto wire:model="tugas_upr" rows="3" :disabled="!$isEditable"
                placeholder="Tulis tugas pokok Unit Pemilik Risiko..." />
        </div>

        {{-- Fungsi UPR --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1.5">
                <label class="block text-sm font-semibold text-slate-200">Fungsi UPR</label>
                <span class="text-[11px] text-slate-400">Contoh: Pengelolaan arsip desa, penerbitan surat pengantar, pengelolaan web/aplikasi desa</span>
            </div>
            <p class="text-xs text-slate-400 mb-2">Diisi dengan fungsi Unit Pemilik Risiko yang akan dinilai risikonya.</p>
            <x-textarea-auto wire:model="fungsi_upr" rows="4" :disabled="!$isEditable"
                placeholder="Tulis rincian fungsi yang dijalankan..." />
        </div>

        {{-- Selera Risiko --}}
        <div class="border-t border-slate-700/50 pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-white">
                        Besaran Selera Risiko (Formulir 4)
                    </label>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Diisi dengan Besaran Risiko minimal yang dapat diterima untuk dilakukan perlakuan Risiko. Di bawah angka selera risiko, risiko akan diterima.
                    </p>
                </div>
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="text-xs text-slate-400">Nilai Saat Ini:</span>
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold font-mono bg-slate-900 border border-slate-700 text-emerald-400">
                        Skor {{ $selera_risiko }} ({{ $riskLabel }})
                    </span>
                </div>
            </div>

            @php
                $levels = [
                    [
                        'label' => 'Rendah',
                        'score' => 4,
                        'badge' => 'Skor 1 - 4',
                        'desc' => 'Desa sangat berhati-hati. Segala potensi kegagalan sekecil apapun pada layanan publik harus segera dimitigasi.',
                        'bgActive' => 'bg-emerald-950/40 border-emerald-500/80 text-emerald-300 ring-1 ring-emerald-500/50',
                        'bgDefault' => 'bg-slate-700/30 border-slate-700 hover:border-slate-600 text-slate-300',
                        'dotColor' => 'bg-emerald-400',
                    ],
                    [
                        'label' => 'Sedang (Standar)',
                        'score' => 9,
                        'badge' => 'Skor 5 - 9 (Rekomendasi)',
                        'desc' => 'Dapat mentoleransi kendala teknis ringan/sementara yang tidak menghentikan pelayanan warga secara total.',
                        'bgActive' => 'bg-amber-950/40 border-amber-500/80 text-amber-300 ring-1 ring-amber-500/50',
                        'bgDefault' => 'bg-slate-700/30 border-slate-700 hover:border-slate-600 text-slate-300',
                        'dotColor' => 'bg-amber-400',
                    ],
                    [
                        'label' => 'Tinggi',
                        'score' => 16,
                        'badge' => 'Skor 10 - 16',
                        'desc' => 'Siap menghadapi risiko sedang-tinggi demi percepatan program, inovasi digital, atau pembangunan desa.',
                        'bgActive' => 'bg-orange-950/40 border-orange-500/80 text-orange-300 ring-1 ring-orange-500/50',
                        'bgDefault' => 'bg-slate-700/30 border-slate-700 hover:border-slate-600 text-slate-300',
                        'dotColor' => 'bg-orange-400',
                    ],
                    [
                        'label' => 'Sangat Tinggi',
                        'score' => 25,
                        'badge' => 'Skor 17 - 25',
                        'desc' => 'Toleransi risiko maksimal. Hanya digunakan jika ada instruksi khusus untuk proyek berisiko ekstrim.',
                        'bgActive' => 'bg-red-950/40 border-red-500/80 text-red-300 ring-1 ring-red-500/50',
                        'bgDefault' => 'bg-slate-700/30 border-slate-700 hover:border-slate-600 text-slate-300',
                        'dotColor' => 'bg-red-400',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($levels as $lvl)
                    @php
                        $isSelected = match($lvl['score']) {
                            4 => $selera_risiko <= 4,
                            9 => $selera_risiko > 4 && $selera_risiko <= 9,
                            16 => $selera_risiko > 9 && $selera_risiko <= 16,
                            25 => $selera_risiko > 16,
                            default => false,
                        };
                    @endphp
                    <button type="button" 
                        wire:click="$set('selera_risiko', {{ $lvl['score'] }})"
                        {{ !$isEditable ? 'disabled' : '' }}
                        class="text-left p-4 rounded-xl border transition-all cursor-pointer flex flex-col justify-between {{ $isSelected ? $lvl['bgActive'] : $lvl['bgDefault'] }} disabled:opacity-50 disabled:cursor-not-allowed">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $lvl['dotColor'] }}"></span>
                                    <span class="text-sm font-bold text-white">{{ $lvl['label'] }}</span>
                                </div>
                                @if($isSelected)
                                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                            </div>
                            <span class="inline-block px-2 py-0.5 text-[11px] font-semibold font-mono rounded bg-slate-800/80 text-slate-300 border border-slate-700 mb-2.5">
                                {{ $lvl['badge'] }}
                            </span>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                {{ $lvl['desc'] }}
                            </p>
                        </div>
                    </button>
                @endforeach
            </div>

            {{-- Custom numerical input toggle if user wants to match an exact excel number --}}
            <div x-data="{ openCustom: false }" class="mt-4 pt-3 border-t border-slate-700/30 flex items-center justify-between text-xs text-slate-400">
                <button type="button" @click="openCustom = !openCustom" class="text-slate-400 hover:text-slate-300 underline cursor-pointer">
                    <span x-show="!openCustom">Perlu input angka selera risiko spesifik (1-25)? Klik di sini</span>
                    <span x-show="openCustom">Sembunyikan input angka manual</span>
                </button>
                <div x-show="openCustom" x-cloak class="flex items-center gap-2">
                    <label class="text-slate-300">Nilai khusus (1–25):</label>
                    <input type="number" min="1" max="25" wire:model.live="selera_risiko" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-16 rounded-md border border-slate-600 bg-slate-700 px-2 py-1 text-white font-mono text-center focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>
        </div>
    </div>
</div>
