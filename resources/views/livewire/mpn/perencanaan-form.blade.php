<div>
    {{-- ─── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Form 1 Perencanaan MPN — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Perencanaan Pengumpulan Pengetahuan Pemerintah Digital</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Status badge --}}
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $konteks->status === 'final' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                {{ $konteks->status === 'final' ? '✓ Final' : '✎ Draft' }}
            </span>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Dashboard
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── Panduan Singkat ─────────────────────────────────────────────────── --}}
    <div class="mb-6 p-4 rounded-xl border border-blue-500/20 bg-blue-500/5 flex gap-3">
        <svg class="w-5 h-5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-xs text-blue-300 leading-relaxed">
            <p class="font-semibold text-blue-200 mb-1">Cara pengisian form ini:</p>
            <ol class="list-decimal list-inside space-y-0.5 text-blue-300/80">
                <li>Isi <strong class="text-blue-200">Indikator Capaian</strong> (kondisi saat ini & target ke depan) di bagian atas.</li>
                <li>Klik <strong class="text-blue-200">"+ Tambah Layanan"</strong> untuk setiap layanan digital yang ingin didokumentasikan pengetahuannya.</li>
                <li>Untuk setiap layanan, tambahkan satu atau lebih <strong class="text-blue-200">pengetahuan</strong> beserta keterangan dokumentasinya.</li>
                <li>Klik <strong class="text-blue-200">"Simpan"</strong> pada setiap kartu layanan setelah selesai mengisi.</li>
            </ol>
        </div>
    </div>

    {{-- ─── SECTION 1: Indikator Capaian ─────────────────────────────────────── --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs font-bold shrink-0">1</span>
            <div>
                <h2 class="text-base font-bold text-white">Indikator Capaian Manajemen Pengetahuan</h2>
                <p class="text-xs text-slate-400">Isi kondisi saat ini (As-Is) dan target yang diharapkan (To-Be) untuk setiap indikator di bawah.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-700/50">
                        <th class="pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/2">Indikator</th>
                        <th class="pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/4 px-3">Kondisi As-Is (Saat Ini)</th>
                        <th class="pb-2 text-xs font-semibold text-slate-400 uppercase tracking-wider w-1/4 px-3">Target To-Be (Ke Depan)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/30">
                    @foreach($capaian as $cIndex => $cData)
                        @php
                            $isPercentage = stripos($cData['nama_indikator'], 'persentase') !== false;
                            $suffix = $isPercentage ? '%' : 'Poin';
                            $step = $isPercentage ? '1' : '0.1';
                        @endphp
                        <tr wire:key="capaian-{{ $cIndex }}" class="align-top">
                            <td class="py-3 pr-3">
                                <div class="flex items-start gap-2">
                                    <span class="mt-2 w-2 h-2 rounded-full bg-indigo-400 shrink-0"></span>
                                    <span class="text-slate-200 leading-relaxed">{{ $cData['nama_indikator'] }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="relative">
                                    <input type="number" step="{{ $step }}" min="0"
                                        wire:model.defer="capaian.{{ $cIndex }}.kondisi_as_is"
                                        class="w-full rounded-lg border border-slate-700 bg-slate-900 pl-3 pr-10 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 focus:outline-none transition-colors"
                                        placeholder="{{ $isPercentage ? 'Contoh: 70' : 'Contoh: 4.2' }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-slate-400 font-medium text-xs">{{ $suffix }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="relative">
                                    <input type="number" step="{{ $step }}" min="0"
                                        wire:model.defer="capaian.{{ $cIndex }}.target_to_be"
                                        class="w-full rounded-lg border border-slate-700 bg-slate-900 pl-3 pr-10 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 focus:outline-none transition-colors"
                                        placeholder="{{ $isPercentage ? 'Contoh: 80' : 'Contoh: 4.4' }}">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-slate-400 font-medium text-xs">{{ $suffix }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── SECTION 2: Layanan & Pengetahuan ──────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-4">
        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 text-xs font-bold shrink-0">2</span>
        <div>
            <h2 class="text-base font-bold text-white">Daftar Layanan & Pengetahuan Kritis</h2>
            <p class="text-xs text-slate-400">Tambahkan setiap layanan digital beserta pengetahuan yang perlu didokumentasikan.</p>
        </div>
    </div>

    <div class="space-y-5">
        @forelse($layanans as $i => $layanan)
            <div class="rounded-xl border border-slate-700/40 bg-slate-800/40 overflow-hidden" wire:key="layanan-{{ $i }}">

                {{-- Layanan Header --}}
                <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-700/40 bg-slate-700/20">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500/25 text-blue-400 text-xs font-bold shrink-0">{{ $i + 1 }}</span>
                        <div class="min-w-0">
                            <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">Layanan ke-{{ $i + 1 }}</p>
                            <p class="text-sm font-semibold text-white truncate">
                                {{ !empty($layanan['nama_layanan']) ? $layanan['nama_layanan'] : '(nama belum diisi)' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="removeLayanan({{ $i }})" wire:confirm="Hapus seluruh layanan ini beserta pengetahuannya?" class="inline-flex items-center gap-1 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 px-2.5 py-1.5 rounded-lg transition-colors cursor-pointer shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </div>

                <div class="p-5 space-y-5">
                    {{-- Nama Layanan + Prioritas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Nama Layanan Digital <span class="text-red-400">*</span>
                            </label>
                            <p class="text-[11px] text-slate-500 mb-2">Contoh: Layanan Perekaman e-KTP, Layanan Surat Pengantar Online</p>
                            <input type="text"
                                wire:model.defer="layanans.{{ $i }}.nama_layanan"
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 focus:outline-none transition-colors"
                                placeholder="Masukkan nama layanan digital...">
                            @error('layanans.'.$i.'.nama_layanan')
                                <span class="mt-1 flex items-center gap-1 text-xs text-red-400">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Layanan Prioritas?
                            </label>
                            <p class="text-[11px] text-slate-500 mb-2">Apakah ini layanan prioritas daerah?</p>
                            <div class="flex gap-4 mt-1">
                                <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer group">
                                    <input type="radio" wire:model.defer="layanans.{{ $i }}.termasuk_layanan_prioritas" value="1" class="text-blue-500 bg-slate-800 border-slate-600 focus:ring-blue-500/30">
                                    <span class="group-hover:text-white transition-colors">Ya</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-slate-300 cursor-pointer group">
                                    <input type="radio" wire:model.defer="layanans.{{ $i }}.termasuk_layanan_prioritas" value="0" class="text-blue-500 bg-slate-800 border-slate-600 focus:ring-blue-500/30">
                                    <span class="group-hover:text-white transition-colors">Tidak</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Pengetahuan List ─── --}}
                    <div class="border-t border-slate-700/40 pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Pengetahuan pada Layanan Ini
                                <span class="ml-1 px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-300 font-mono normal-case">{{ count($layanan['pengetahuan']) }}</span>
                            </p>
                        </div>

                        <div class="space-y-3">
                            @foreach($layanan['pengetahuan'] as $j => $pengetahuan)
                                <div class="rounded-lg border border-slate-700/40 bg-slate-900/60 p-4"
                                    wire:key="pengetahuan-{{ $i }}-{{ $j }}"
                                    x-data="{ sudah: @entangle('layanans.'.$i.'.pengetahuan.'.$j.'.sudah_terdokumentasi') }">

                                    {{-- Pengetahuan header --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 flex items-center justify-center rounded bg-slate-700 text-slate-400 text-[10px] font-bold">{{ $j + 1 }}</span>
                                            <span class="text-xs font-semibold text-slate-300">Pengetahuan #{{ $j + 1 }}</span>
                                            @if(!empty($pengetahuan['kode_pengetahuan']))
                                                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-[10px] font-mono text-slate-400">
                                                    {{ $pengetahuan['kode_pengetahuan'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <button type="button" wire:click="removePengetahuan({{ $i }}, {{ $j }})" wire:confirm="Hapus pengetahuan ini?" class="text-[11px] text-red-400 hover:text-red-300 hover:underline cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>

                                    <div class="space-y-4">
                                        {{-- Nama Pengetahuan --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-300 mb-1">
                                                Nama / Deskripsi Pengetahuan <span class="text-red-400">*</span>
                                            </label>
                                            <p class="text-[11px] text-slate-500 mb-1.5">Contoh: Prosedur percepatan perekaman e-KTP bagi masyarakat berkebutuhan khusus</p>
                                            <textarea
                                                wire:model.defer="layanans.{{ $i }}.pengetahuan.{{ $j }}.nama_pengetahuan"
                                                rows="2"
                                                class="w-full rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 focus:outline-none transition-colors resize-none"
                                                placeholder="Tuliskan nama atau deskripsi pengetahuan..."></textarea>
                                            @error('layanans.'.$i.'.pengetahuan.'.$j.'.nama_pengetahuan')
                                                <span class="mt-1 text-xs text-red-400 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        {{-- Aspek + Indikator PemDi --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-300 mb-1">
                                                    Aspek Pemerintah Digital <span class="text-red-400">*</span>
                                                </label>
                                                <p class="text-[11px] text-slate-500 mb-1.5">Pilih aspek yang paling relevan dengan pengetahuan ini.</p>
                                                <select
                                                    wire:model.live="layanans.{{ $i }}.pengetahuan.{{ $j }}.ref_aspek_pemdi_id"
                                                    class="w-full rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 focus:outline-none transition-colors">
                                                    <option value="">— Pilih Aspek —</option>
                                                    @foreach($aspeks as $aspek)
                                                        <option value="{{ $aspek['id'] }}">{{ $aspek['nama_aspek'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('layanans.'.$i.'.pengetahuan.'.$j.'.ref_aspek_pemdi_id')
                                                    <span class="mt-1 text-xs text-red-400">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-300 mb-1">
                                                    Indikator Pemerintah Digital <span class="text-red-400">*</span>
                                                </label>
                                                <p class="text-[11px] text-slate-500 mb-1.5">Terisi otomatis setelah memilih Aspek di atas.</p>
                                                @php
                                                    $selectedAspekId = $layanans[$i]['pengetahuan'][$j]['ref_aspek_pemdi_id'] ?? null;
                                                    $availableIndikators = [];
                                                    if ($selectedAspekId) {
                                                        $found = collect($aspeks)->firstWhere('id', $selectedAspekId);
                                                        if ($found) $availableIndikators = $found['indikators'];
                                                    }
                                                @endphp
                                                <select
                                                    wire:model.defer="layanans.{{ $i }}.pengetahuan.{{ $j }}.ref_indikator_pemdi_id"
                                                    class="w-full rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2.5 text-sm {{ $selectedAspekId ? 'text-slate-200' : 'text-slate-500' }} focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 focus:outline-none transition-colors"
                                                    {{ !$selectedAspekId ? 'disabled' : '' }}>
                                                    <option value="">{{ $selectedAspekId ? '— Pilih Indikator —' : '— Pilih Aspek dahulu —' }}</option>
                                                    @foreach($availableIndikators as $ind)
                                                        <option value="{{ $ind['id'] }}">{{ $ind['nama_indikator'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('layanans.'.$i.'.pengetahuan.'.$j.'.ref_indikator_pemdi_id')
                                                    <span class="mt-1 text-xs text-red-400">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Sudah Terdokumentasi? --}}
                                        <div class="border-t border-slate-700/30 pt-3">
                                            <p class="text-xs font-semibold text-slate-300 mb-2">
                                                Apakah pengetahuan ini sudah terdokumentasi? <span class="text-red-400">*</span>
                                            </p>
                                            <div class="flex gap-3">
                                                <label class="flex items-center gap-2.5 px-4 py-2 rounded-lg border cursor-pointer transition-all
                                                    {{ ($layanans[$i]['pengetahuan'][$j]['sudah_terdokumentasi'] ?? false) == 1 ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-300' : 'border-slate-700 text-slate-400 hover:border-slate-500' }}">
                                                    <input type="radio" wire:model.live="layanans.{{ $i }}.pengetahuan.{{ $j }}.sudah_terdokumentasi" value="1" class="sr-only">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-sm font-medium">Ya, sudah</span>
                                                </label>
                                                <label class="flex items-center gap-2.5 px-4 py-2 rounded-lg border cursor-pointer transition-all
                                                    {{ ($layanans[$i]['pengetahuan'][$j]['sudah_terdokumentasi'] ?? false) == 0 && isset($layanans[$i]['pengetahuan'][$j]['sudah_terdokumentasi']) ? 'bg-amber-500/10 border-amber-500/50 text-amber-300' : 'border-slate-700 text-slate-400 hover:border-slate-500' }}">
                                                    <input type="radio" wire:model.live="layanans.{{ $i }}.pengetahuan.{{ $j }}.sudah_terdokumentasi" value="0" class="sr-only">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="text-sm font-medium">Belum</span>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Kondisional: Sudah Terdokumentasi = Ya --}}
                                        <div x-show="sudah == 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;">
                                            <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 p-4 space-y-4">
                                                <p class="text-xs font-semibold text-emerald-300 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Rencana Dokumentasi (sudah terdokumentasi)
                                                </p>

                                                {{-- Tipe Dokumentasi --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-300 mb-2">Tipe Dokumentasi yang Ada</label>
                                                    <div class="flex flex-wrap gap-3">
                                                        @foreach([['field' => 'tipe_dok_teks', 'label' => 'Teks', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'], ['field' => 'tipe_dok_gambar', 'label' => 'Gambar/Foto', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'], ['field' => 'tipe_dok_audio', 'label' => 'Audio', 'icon' => 'M15.536 8.464a5 5 0 010 7.072M12 9a3 3 0 000 6v-6zM6.343 6.343a8 8 0 000 11.314'], ['field' => 'tipe_dok_video', 'label' => 'Video', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z']] as $tipe)
                                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                                <input type="checkbox"
                                                                    wire:model.defer="layanans.{{ $i }}.pengetahuan.{{ $j }}.{{ $tipe['field'] }}"
                                                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-emerald-500 focus:ring-emerald-500/30">
                                                                <span class="flex items-center gap-1.5 text-sm text-slate-300 group-hover:text-white transition-colors">
                                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $tipe['icon'] }}"/></svg>
                                                                    {{ $tipe['label'] }}
                                                                </span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-300 mb-1">
                                                            Penanggung Jawab Dokumentasi <span class="text-red-400">*</span>
                                                        </label>
                                                        <p class="text-[11px] text-slate-500 mb-1.5">Contoh: Bidang Pelayanan Dukcapil / Nama Petugas</p>
                                                        <input type="text"
                                                            wire:model.defer="layanans.{{ $i }}.pengetahuan.{{ $j }}.penanggung_jawab_dokumentasi"
                                                            class="w-full rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 focus:outline-none transition-colors"
                                                            placeholder="Nama unit/petugas...">
                                                        @error('layanans.'.$i.'.pengetahuan.'.$j.'.penanggung_jawab_dokumentasi')
                                                            <span class="mt-1 text-xs text-red-400">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-300 mb-1">
                                                            Target Waktu Dokumentasi <span class="text-red-400">*</span>
                                                        </label>
                                                        <p class="text-[11px] text-slate-500 mb-1.5">Contoh: Q3 2026 / Agustus 2026</p>
                                                        <input type="text"
                                                            wire:model.defer="layanans.{{ $i }}.pengetahuan.{{ $j }}.target_waktu_dokumentasi"
                                                            class="w-full rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 focus:outline-none transition-colors"
                                                            placeholder="Contoh: Q3 2026...">
                                                        @error('layanans.'.$i.'.pengetahuan.'.$j.'.target_waktu_dokumentasi')
                                                            <span class="mt-1 text-xs text-red-400">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Kondisional: Sudah Terdokumentasi = Tidak --}}
                                        <div x-show="sudah == 0" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;">
                                            <div class="rounded-lg border border-amber-500/20 bg-amber-500/5 p-4">
                                                <p class="text-xs font-semibold text-amber-300 flex items-center gap-1.5 mb-3">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    Pengetahuan belum terdokumentasi — isi pemilik pengetahuan
                                                </p>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                                                        Pemilik Pengetahuan (Unit Kerja / Instansi Terkait) <span class="text-red-400">*</span>
                                                    </label>
                                                    <p class="text-[11px] text-slate-500 mb-1.5">Siapa atau unit mana yang menjadi sumber/pemilik pengetahuan ini?</p>
                                                    <input type="text"
                                                        wire:model.defer="layanans.{{ $i }}.pengetahuan.{{ $j }}.pemilik_pengetahuan"
                                                        class="w-full rounded-lg border border-slate-700 bg-slate-800/60 px-3 py-2 text-sm text-slate-200 placeholder-slate-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 focus:outline-none transition-colors"
                                                        placeholder="Contoh: Bidang Kependudukan Disdukcapil...">
                                                    @error('layanans.'.$i.'.pengetahuan.'.$j.'.pemilik_pengetahuan')
                                                        <span class="mt-1 text-xs text-red-400">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tambah Pengetahuan --}}
                        <div class="mt-3">
                            <button type="button" wire:click="addPengetahuan({{ $i }})"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-dashed border-blue-500/40 bg-blue-500/5 text-xs text-blue-400 hover:bg-blue-500/15 hover:border-blue-400 transition-all cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Pengetahuan pada Layanan Ini
                            </button>
                        </div>
                    </div>

                    {{-- Save button --}}
                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-700/40">
                        <p class="text-[11px] text-slate-500">
                            <span class="text-red-400">*</span> Wajib diisi sebelum menyimpan
                        </p>
                        <button type="button" wire:click="saveLayanan({{ $i }})" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition-all cursor-pointer disabled:opacity-60">
                            <svg class="w-4 h-4" wire:loading.remove wire:target="saveLayanan({{ $i }})" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg class="w-4 h-4 animate-spin" wire:loading wire:target="saveLayanan({{ $i }})" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Simpan Layanan &amp; Pengetahuan
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-700/40 px-4 py-14 text-center bg-slate-800/20">
                <div class="w-12 h-12 rounded-xl bg-slate-700/50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <p class="font-semibold text-slate-300 mb-1">Belum ada layanan yang ditambahkan</p>
                <p class="text-xs text-slate-500">Klik tombol di bawah untuk menambah layanan digital pertama.</p>
            </div>
        @endforelse
    </div>

    {{-- ─── Tambah Layanan Button ──────────────────────────────────────────────── --}}
    <div class="mt-5 flex items-center gap-4">
        <button wire:click="addLayanan"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-dashed border-slate-600 text-sm text-slate-400 hover:text-white hover:border-blue-500/60 hover:bg-blue-500/5 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            + Tambah Layanan Baru
        </button>
        <span class="text-xs text-slate-500">Setiap layanan bisa memiliki lebih dari satu pengetahuan</span>
    </div>
</div>
