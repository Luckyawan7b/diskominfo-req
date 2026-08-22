<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">
                {{ $isNew ? 'Tambah Risiko Baru' : 'Edit Risiko — ' . $kode_risiko }}
            </h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 5-7 & Kolom Tambahan SPBE</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('risiko.index', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Daftar Risiko
            </a>
            @if($isEditable)
                <button wire:click="save" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Risiko
                </button>
            @endif
        </div>
    </div>

    <x-risk-wizard :konteks="$konteks" activeStep="Risiko" />

    {{-- Tabs Header --}}
    <div class="flex border-b border-slate-700/50 space-x-2 overflow-x-auto mb-6">
        @php
            $tabs = [
                1 => ['label' => '1. Identifikasi (F5)', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                2 => ['label' => '2. Analisis & Evaluasi (F6)', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                3 => ['label' => '3. Rencana Perlakuan (F7)', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                4 => ['label' => '4. Risiko Residual', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                5 => ['label' => '5. Kolom Tambahan (E)', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
            ];
        @endphp

        @foreach($tabs as $key => $tab)
            <button wire:click="$set('activeTab', {{ $key }})"
                class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === $key ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>

    {{-- Tabs Content --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 sm:p-8">

        {{-- TAB 1: IDENTIFIKASI --}}
        <div class="{{ $activeTab === 1 ? 'block' : 'hidden' }} space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Kode Risiko <span class="text-red-400">*</span></label>
                    <input wire:model="kode_risiko" type="text" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white font-mono text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                    @error('kode_risiko') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Sasaran UPR Terkait</label>
                    <select wire:model="mr_sasaran_upr_id" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Sasaran UPR --</option>
                        @foreach($sasaranList as $s)
                            <option value="{{ $s->id }}">{{ Str::limit($s->sasaran_upr, 80) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Peristiwa Risiko <span class="text-red-400">*</span></label>
                <x-textarea-auto wire:model="peristiwa_risiko" rows="3" :disabled="!$isEditable"
                    placeholder="Deskripsi kejadian atau peristiwa risiko" />
                @error('peristiwa_risiko') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Penyebab / Akar Masalah</label>
                    <x-textarea-auto wire:model="penyebab" rows="3" :disabled="!$isEditable"
                        placeholder="Faktor penyebab timbulnya risiko" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Dampak Risiko</label>
                    <x-textarea-auto wire:model="dampak_risiko" rows="3" :disabled="!$isEditable"
                        placeholder="Akibat yang ditimbulkan jika risiko terjadi" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-700/50">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Kategori Risiko</label>
                    <select wire:model="ref_kategori_risiko_id" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Kategori Risiko --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->kode_kategori }} - {{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Area Dampak</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Finansial', 'Operasional', 'Reputasi', 'Hukum/Regulasi'] as $area)
                            <label class="flex items-center gap-2 p-2.5 rounded-lg border border-slate-700 bg-slate-700/30 text-sm text-slate-300 cursor-pointer hover:bg-slate-700/50">
                                <input type="radio" wire:model="area_dampak" value="{{ $area }}" {{ !$isEditable ? 'disabled' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                                <span>{{ $area }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="button" wire:click="$set('activeTab', 2)" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm text-white transition-colors cursor-pointer">
                    Lanjut ke Analisis (F6)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        {{-- TAB 2: ANALISIS & EVALUASI --}}
        <div class="{{ $activeTab === 2 ? 'block' : 'hidden' }} space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Level Kemungkinan (1-5)</label>
                    <select wire:model.live="level_kemungkinan" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Level Kemungkinan --</option>
                        <option value="1">1 - Hampir Tidak Terjadi</option>
                        <option value="2">2 - Jarang Terjadi</option>
                        <option value="3">3 - Kadang Terjadi</option>
                        <option value="4">4 - Sering Terjadi</option>
                        <option value="5">5 - Hampir Pasti Terjadi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Level Dampak (1-5)</label>
                    <select wire:model.live="level_dampak" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Level Dampak --</option>
                        <option value="1">1 - Tidak Signifikan</option>
                        <option value="2">2 - Kecil / Minor</option>
                        <option value="3">3 - Sedang / Moderat</option>
                        <option value="4">4 - Besar / Signifikan</option>
                        <option value="5">5 - Sangat Besar / Katastropik</option>
                    </select>
                </div>
            </div>

            {{-- Hasil Analisis Visual --}}
            <div class="p-6 rounded-xl border border-slate-700 bg-slate-900/50 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-slate-300 uppercase tracking-wider">Hasil Perhitungan Besaran Risiko</h3>
                    <p class="text-xs text-slate-500 mt-1">Dihitung otomatis berdasarkan Matriks SPBE Resmi</p>
                    <div class="mt-4 flex items-center gap-4">
                        <div>
                            <span class="text-xs text-slate-400">Besaran Risiko:</span>
                            <div class="text-3xl font-bold text-white">{{ $besaran ?? '-' }}</div>
                        </div>
                        @if($besaranLabel)
                            @php
                                $badgeCls = match($besaranLabel) {
                                    'Rendah'        => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                    'Sedang'        => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                    'Tinggi'        => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                                    'Sangat Tinggi' => 'bg-red-500/20 text-red-400 border-red-500/30',
                                    default         => 'bg-slate-700 text-slate-400 border-slate-600',
                                };
                            @endphp
                            <div class="px-3 py-1.5 rounded-full border text-sm font-semibold {{ $badgeCls }}">
                                Level: {{ $besaranLabel }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Mini 5x5 Matrix Preview --}}
                <div class="bg-slate-800 p-3 rounded-lg border border-slate-700">
                    <p class="text-xs text-slate-400 text-center mb-2 font-medium">Posisi Matriks 5×5</p>
                    <div class="grid grid-cols-5 gap-1 w-32 h-32">
                        @for($k = 5; $k >= 1; $k--)
                            @for($d = 1; $d <= 5; $d++)
                                @php
                                    $isSelected = ($level_kemungkinan == $k && $level_dampak == $d);
                                    $cellCalc = app(\App\Services\RiskMatrixCalculator::class)->calculate($k, $d);
                                    $cellColor = match(true) {
                                        $cellCalc <= 4  => 'bg-emerald-600/40',
                                        $cellCalc <= 9  => 'bg-amber-600/40',
                                        $cellCalc <= 16 => 'bg-orange-600/40',
                                        default         => 'bg-red-600/40',
                                    };
                                @endphp
                                <div class="rounded-xs {{ $cellColor }} {{ $isSelected ? 'ring-2 ring-white scale-110 z-10' : 'opacity-60' }} flex items-center justify-center text-[9px] text-white">
                                    {{ $isSelected ? '★' : '' }}
                                </div>
                            @endfor
                        @endfor
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-4">
                <button type="button" wire:click="$set('activeTab', 1)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer">← Kembali</button>
                <button type="button" wire:click="$set('activeTab', 3)" class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm text-white cursor-pointer">Lanjut ke Perlakuan →</button>
            </div>
        </div>

        {{-- TAB 3: RENCANA PERLAKUAN --}}
        <div class="{{ $activeTab === 3 ? 'block' : 'hidden' }} space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Keputusan Perlakuan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach(['Mengurangi risiko', 'Menghindari risiko', 'Membagi risiko', 'Menerima risiko'] as $opt)
                        <label class="flex items-center gap-2 p-3 rounded-lg border border-slate-700 bg-slate-700/30 text-sm text-slate-300 cursor-pointer hover:bg-slate-700/50">
                            <input type="radio" wire:model="keputusan_perlakuan" value="{{ $opt }}" {{ !$isEditable ? 'disabled' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                            <span>{{ $opt }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1.5">Deskripsi Rencana Tindak Pengendalian</label>
                <x-textarea-auto wire:model="deskripsi_detail_perlakuan" rows="3" :disabled="!$isEditable"
                    placeholder="Langkah-langkah mitigasi / pengendalian risiko" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Target Waktu Pelaksanaan</label>
                    <input wire:model="waktu_rencana_perlakuan" type="text" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50"
                        placeholder="Contoh: Triwulan II / Juni 2026">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Penanggung Jawab (PIC)</label>
                    <input wire:model="penanggung_jawab" type="text" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50"
                        placeholder="Nama seksi / unit / pejabat penanggung jawab">
                </div>
            </div>

            <div class="flex justify-between pt-4">
                <button type="button" wire:click="$set('activeTab', 2)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer">← Kembali</button>
                <button type="button" wire:click="$set('activeTab', 4)" class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm text-white cursor-pointer">Lanjut ke Residual →</button>
            </div>
        </div>

        {{-- TAB 4: RISIKO RESIDUAL --}}
        <div class="{{ $activeTab === 4 ? 'block' : 'hidden' }} space-y-6">
            <p class="text-sm text-slate-400">Estimasi tingkat risiko yang tersisa setelah rencana perlakuan diterapkan.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Level Kemungkinan Residual (1-5)</label>
                    <select wire:model.live="level_kemungkinan_residual" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Level --</option>
                        <option value="1">1 - Hampir Tidak Terjadi</option>
                        <option value="2">2 - Jarang Terjadi</option>
                        <option value="3">3 - Kadang Terjadi</option>
                        <option value="4">4 - Sering Terjadi</option>
                        <option value="5">5 - Hampir Pasti Terjadi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Level Dampak Residual (1-5)</label>
                    <select wire:model.live="level_dampak_residual" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Level --</option>
                        <option value="1">1 - Tidak Signifikan</option>
                        <option value="2">2 - Kecil / Minor</option>
                        <option value="3">3 - Sedang / Moderat</option>
                        <option value="4">4 - Besar / Signifikan</option>
                        <option value="5">5 - Sangat Besar / Katastropik</option>
                    </select>
                </div>
            </div>

            <div class="p-5 rounded-xl border border-slate-700 bg-slate-900/40 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400">Besaran Risiko Residual:</span>
                    <div class="text-2xl font-bold text-white">{{ $besaranResidual ?? '-' }}</div>
                </div>
                @if($besaranResidual)
                    @php
                        $resLabel = app(\App\Services\RiskMatrixCalculator::class)->label($besaranResidual);
                        $resBadgeCls = match($resLabel) {
                            'Rendah'        => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                            'Sedang'        => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                            'Tinggi'        => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                            'Sangat Tinggi' => 'bg-red-500/20 text-red-400 border-red-500/30',
                            default         => 'bg-slate-700 text-slate-400',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full border text-xs font-semibold {{ $resBadgeCls }}">
                        {{ $resLabel }}
                    </span>
                @endif
            </div>

            <div class="flex justify-between pt-4">
                <button type="button" wire:click="$set('activeTab', 3)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer">← Kembali</button>
                <button type="button" wire:click="$set('activeTab', 5)" class="px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm text-white cursor-pointer">Lanjut ke Kolom Tambahan →</button>
            </div>
        </div>

        {{-- TAB 5: KOLOM TAMBAHAN (BAGIAN E) --}}
        <div class="{{ $activeTab === 5 ? 'block' : 'hidden' }} space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Layanan Pendukung</label>
                    <input wire:model="layanan_pendukung" type="text" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50"
                        placeholder="Nama aplikasi / layanan SPBE">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Pemilik Layanan</label>
                    <select wire:model="pemilik_layanan" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="">-- Pilih Pemilik Layanan --</option>
                        <option value="Milik sendiri">Milik sendiri (Desa)</option>
                        <option value="Pusat">Pusat (Kementerian/Lembaga)</option>
                        <option value="Instansi lain">Instansi lain (Pemda/OPD Lain)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Prioritas Layanan</label>
                    <select wire:model.live="layanan_prioritas" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="Instansional">Instansional (Biasa)</option>
                        <option value="Tematik">Tematik</option>
                        <option value="Prioritas">Prioritas (Layanan Digital SPBE)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Jenis Layanan</label>
                    <select wire:model="strategis_atau_operasional" {{ !$isEditable ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50">
                        <option value="Operasional">Operasional</option>
                        <option value="Strategis">Strategis</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-700/50">
                <div class="space-y-4">
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-700 bg-slate-700/20 text-sm text-slate-300 cursor-pointer">
                        <input type="checkbox" wire:model="lintas_sektor" {{ !$isEditable ? 'disabled' : '' }} class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500">
                        <span>Layanan Lintas Sektor</span>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-700 bg-slate-700/20 text-sm text-slate-300 cursor-pointer">
                        <input type="checkbox" wire:model="membutuhkan_perubahan" {{ !$isEditable ? 'disabled' : '' }} class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500">
                        <span>Membutuhkan Manajemen Perubahan</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">IPPD Terkait (Instansi Pusat / Pemda)</label>
                    <x-textarea-auto wire:model="ippd_terkait" rows="3" :disabled="!$isEditable"
                        placeholder="Nama-nama IPPD terkait" />
                </div>
            </div>

            {{-- Layanan Digital conditional fields --}}
            @if($layanan_prioritas === 'Prioritas')
                <div class="p-5 rounded-xl border border-emerald-500/30 bg-emerald-950/20 space-y-4">
                    <h3 class="text-sm font-semibold text-emerald-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Layanan Digital Prioritas (Formulir Khusus Layanan Digital)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center">
                            <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                                <input type="checkbox" wire:model="perlu_mkb" {{ !$isEditable ? 'disabled' : '' }} class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500">
                                <span>Perlu Manajemen Keberlangsungan (MKB)</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">PIC Layanan Digital</label>
                            <input wire:model="pic" type="text" {{ !$isEditable ? 'disabled' : '' }}
                                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Target Waktu Penyusunan</label>
                            <input wire:model="target_waktu_penyusunan" type="text" {{ !$isEditable ? 'disabled' : '' }}
                                class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-between pt-4">
                <button type="button" wire:click="$set('activeTab', 4)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer">← Kembali</button>
                @if($isEditable)
                    <button type="button" wire:click="save" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Semua Data Risiko
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>
