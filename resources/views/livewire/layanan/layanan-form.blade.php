<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">
                {{ $isEdit ? 'Edit Layanan' : 'Tambah Layanan Baru' }}
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                {{ $isEdit ? 'Perbarui informasi layanan digital' : 'Isi detail layanan digital sebelum mengisi modul manajemen' }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Auto-save indicator --}}
            <div wire:loading wire:target="nextStep,prevStep,saveAndFinish" class="flex items-center gap-1.5 text-xs text-slate-400">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Menyimpan...
            </div>
            <a href="{{ route('layanan.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- ── STEPPER HEADER ────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center">
            @foreach($stepLabels as $step => $info)
                @php
                    $isActive    = $step === $currentStep;
                    $isDone      = $step < $currentStep;
                    $isClickable = $isEdit && $step !== $currentStep;
                @endphp

                {{-- Step item --}}
                <div class="flex items-center {{ $step < $totalSteps ? 'flex-1' : '' }}">
                    <button
                        @if($isClickable) wire:click="goToStep({{ $step }})" @endif
                        class="flex items-center gap-2.5 group {{ $isClickable ? 'cursor-pointer' : 'cursor-default' }}"
                        @if(!$isClickable) type="button" disabled @endif>

                        {{-- Circle --}}
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all duration-200
                            @if($isDone)   border-emerald-500 bg-emerald-500 text-white
                            @elseif($isActive) border-blue-500 bg-blue-500 text-white shadow-lg shadow-blue-500/30
                            @else          border-slate-600 bg-slate-800 text-slate-500 group-hover:border-slate-400
                            @endif">
                            @if($isDone)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @else
                                {{ $step }}
                            @endif
                        </div>

                        {{-- Label (hidden on mobile) --}}
                        <div class="hidden sm:block text-left">
                            <p class="text-xs font-semibold {{ $isActive ? 'text-white' : ($isDone ? 'text-emerald-400' : 'text-slate-500') }} leading-tight">
                                {{ $info['label'] }}
                            </p>
                            <p class="text-[10px] {{ $isActive ? 'text-slate-400' : 'text-slate-600' }} leading-tight mt-0.5">
                                {{ $info['sub'] }}
                            </p>
                        </div>
                    </button>

                    {{-- Connector line --}}
                    @if($step < $totalSteps)
                        <div class="flex-1 mx-3 h-px {{ $isDone ? 'bg-emerald-500/60' : 'bg-slate-700' }} transition-colors duration-300"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── FORM ──────────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-700/50 bg-slate-800/50 overflow-hidden">

        {{-- Step header band --}}
        <div class="px-6 py-4 border-b border-slate-700/50 bg-slate-800/80 flex items-center gap-3">
            @php
                $stepColors = [1 => 'bg-blue-500/20 text-blue-400', 2 => 'bg-indigo-500/20 text-indigo-400', 3 => 'bg-emerald-500/20 text-emerald-400', 4 => 'bg-amber-500/20 text-amber-400'];
            @endphp
            <span class="flex items-center justify-center w-7 h-7 rounded-lg {{ $stepColors[$currentStep] ?? 'bg-slate-700 text-slate-400' }} text-sm font-bold">
                {{ $currentStep }}
            </span>
            <div>
                <h2 class="text-sm font-bold text-white">{{ $stepLabels[$currentStep]['label'] }}</h2>
                <p class="text-xs text-slate-400">{{ $stepLabels[$currentStep]['sub'] }}</p>
            </div>
            @if($currentStep > 1)
                <span class="ml-auto text-xs text-slate-500 bg-slate-700/50 px-2 py-1 rounded-md">Opsional — dapat dikosongkan</span>
            @endif
        </div>

        <div class="p-6">

            {{-- ─── STEP 1: Identitas Layanan ─────────────────────────────── --}}
            @if($currentStep === 1)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">
                            Nama Layanan Digital <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model.live.debounce.500ms="nama_layanan"
                               class="w-full rounded-lg border @error('nama_layanan') border-red-500 bg-red-500/5 @else border-slate-700 bg-slate-900 @enderror px-3 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition"
                               placeholder="Contoh: Sistem Informasi Kependudukan Online">
                        @error('nama_layanan')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">
                            Deskripsi Layanan <span class="text-red-400">*</span>
                        </label>
                        <textarea wire:model.live.debounce.500ms="deskripsi_layanan" rows="3"
                                  class="w-full rounded-lg border @error('deskripsi_layanan') border-red-500 bg-red-500/5 @else border-slate-700 bg-slate-900 @enderror px-3 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition resize-none"
                                  placeholder="Jelaskan fungsi dan tujuan layanan ini secara singkat..."></textarea>
                        @error('deskripsi_layanan')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">
                            Status Layanan <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="status_layanan"
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition">
                            <option value="berjalan">Berjalan</option>
                            <option value="direncanakan">Direncanakan</option>
                            <option value="dihentikan">Dihentikan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">
                            Target Pengguna <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="target_pengguna"
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition">
                            <option value="Publik/Masyarakat">Publik / Masyarakat</option>
                            <option value="Internal Pemerintahan">Internal Pemerintahan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Bidang / Bagian</label>
                        <input type="text" wire:model.defer="bidang_bagian"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 outline-none transition"
                               placeholder="Contoh: Bidang Pelayanan Publik">
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" wire:model="is_prioritas" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-700 peer-focus:ring-2 peer-focus:ring-blue-500/30 rounded-full peer peer-checked:bg-amber-500 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-slate-300">Layanan Prioritas ⭐</span>
                                <p class="text-xs text-slate-500 mt-0.5">Tampilkan di bagian teratas halaman</p>
                            </div>
                        </label>
                    </div>

                </div>
            @endif

            {{-- ─── STEP 2: Data & Integrasi ──────────────────────────────── --}}
            @if($currentStep === 2)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">K/L/D Terkait</label>
                        <input type="text" wire:model.defer="kl_terkait"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition"
                               placeholder="Kementerian / Lembaga / Dinas terkait">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Supplier Data</label>
                        <input type="text" wire:model.defer="supplier_data"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Nama Data Input</label>
                        <textarea wire:model.defer="nama_data_input" rows="2"
                                  class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Nama Data Output</label>
                        <textarea wire:model.defer="nama_data_output" rows="2"
                                  class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Sifat Data</label>
                        <select wire:model.defer="sifat_data"
                                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition">
                            <option value="">— Pilih —</option>
                            <option value="terbuka">Terbuka</option>
                            <option value="terbatas">Terbatas</option>
                            <option value="tertutup">Tertutup</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Jenis Data</label>
                        <input type="text" wire:model.defer="jenis_data"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Validitas Data (Frekuensi Pembaruan)</label>
                        <input type="text" wire:model.defer="validitas_data"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition"
                               placeholder="Contoh: Realtime / Harian / Bulanan">
                    </div>

                    {{-- Interoperabilitas toggle --}}
                    <div class="md:col-span-2 border-t border-slate-700/50 pt-5">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" wire:model.live="interoperabilitas" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-700 peer-focus:ring-2 peer-focus:ring-indigo-500/30 rounded-full peer peer-checked:bg-indigo-500 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-slate-300">Memiliki Interoperabilitas (Integrasi dengan sistem lain)</span>
                                <p class="text-xs text-slate-500 mt-0.5">Aktifkan jika layanan ini terhubung ke sistem lain melalui API atau Web Service</p>
                            </div>
                        </label>
                    </div>

                    @if($interoperabilitas)
                        <div class="md:col-span-2 bg-slate-900/40 rounded-xl p-4 grid grid-cols-1 md:grid-cols-2 gap-5 border border-slate-700/50">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tujuan Integrasi</label>
                                <textarea wire:model.defer="tujuan_integrasi" rows="2"
                                          class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Metode Integrasi</label>
                                <input type="text" wire:model.defer="metode_integrasi"
                                       class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition"
                                       placeholder="API / Web Service / Batch">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-300 mb-1.5">Link Dokumen Integrasi</label>
                                <input type="url" wire:model.defer="link_dokumen_integrasi"
                                       class="w-full rounded-lg border @error('link_dokumen_integrasi') border-red-500 @else border-slate-700 @enderror bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 outline-none transition"
                                       placeholder="https://...">
                                @error('link_dokumen_integrasi') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ─── STEP 3: Aplikasi & Infrastruktur ──────────────────────── --}}
            @if($currentStep === 3)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Nama Aplikasi</label>
                        <input type="text" wire:model.defer="nama_aplikasi"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tipe Aplikasi</label>
                        <input type="text" wire:model.defer="tipe_aplikasi"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition"
                               placeholder="Web / Mobile / Desktop / Hybrid">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Link / URL Aplikasi</label>
                        <input type="url" wire:model.defer="link_aplikasi"
                               class="w-full rounded-lg border @error('link_aplikasi') border-red-500 @else border-slate-700 @enderror bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition"
                               placeholder="https://...">
                        @error('link_aplikasi') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tahun Pembuatan</label>
                        <input type="number" wire:model.defer="tahun_pembuatan"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition"
                               placeholder="2024" min="2000" max="2099">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Keluaran Aplikasi</label>
                        <textarea wire:model.defer="keluaran_aplikasi" rows="2"
                                  class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition resize-none"
                                  placeholder="Jelaskan output / keluaran dari layanan ini..."></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Letak Server</label>
                        <input type="text" wire:model.defer="letak_server"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 outline-none transition"
                               placeholder="Pusat Data Nasional / Cloud / On-Premise / Hybrid">
                    </div>
                </div>
            @endif

            {{-- ─── STEP 4: Dokumen Pendukung ─────────────────────────────── --}}
            @if($currentStep === 4)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Link DPA</label>
                        <input type="url" wire:model.defer="link_dpa"
                               class="w-full rounded-lg border @error('link_dpa') border-red-500 @else border-slate-700 @enderror bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 outline-none transition"
                               placeholder="https://...">
                        @error('link_dpa') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Link SLA <span class="text-slate-500 font-normal">(Service Level Agreement)</span></label>
                        <input type="url" wire:model.defer="link_sla"
                               class="w-full rounded-lg border @error('link_sla') border-red-500 @else border-slate-700 @enderror bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 outline-none transition"
                               placeholder="https://...">
                        @error('link_sla') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Link SOP</label>
                        <input type="url" wire:model.defer="link_sop"
                               class="w-full rounded-lg border @error('link_sop') border-red-500 @else border-slate-700 @enderror bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 outline-none transition"
                               placeholder="https://...">
                        @error('link_sop') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Kontak Helpdesk</label>
                        <input type="text" wire:model.defer="helpdesk"
                               class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 outline-none transition"
                               placeholder="No. HP / Alamat Email helpdesk">
                    </div>

                    {{-- Finish call-to-action --}}
                    <div class="md:col-span-2 mt-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-emerald-300">Hampir selesai!</p>
                            <p class="text-xs text-emerald-400/80 mt-0.5">Klik "Selesai & Mulai Mengisi Modul" untuk menyimpan dan langsung menuju Dashboard Modul.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── NAVIGATION BUTTONS ─────────────────────────────────────────── --}}
        <div class="px-6 py-4 border-t border-slate-700/50 bg-slate-800/40 flex items-center justify-between gap-3">

            {{-- Kembali --}}
            <div>
                @if($currentStep > 1)
                    <button wire:click="prevStep" type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm font-medium text-slate-300 hover:bg-slate-700 transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        Kembali
                    </button>
                @endif
            </div>

            {{-- Step counter --}}
            <span class="text-xs text-slate-500">Langkah {{ $currentStep }} dari {{ $totalSteps }}</span>

            {{-- Lanjut / Selesai --}}
            <div>
                @if($currentStep < $totalSteps)
                    <button wire:click="nextStep" type="button"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-semibold text-white transition-colors shadow-lg shadow-blue-500/20 cursor-pointer">
                        Simpan & Lanjut
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                @else
                    <button wire:click="saveAndFinish" type="button"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-bold text-white transition-colors shadow-lg shadow-emerald-500/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Selesai & Mulai Mengisi Modul
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>
