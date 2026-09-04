<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Form 2: Pengumpulan & Pengelolaan</h1>
            <p class="text-sm text-slate-400 mt-1">ID Pengetahuan: <span class="font-mono text-indigo-400">{{ $pengetahuan->kode_pengetahuan }}</span></p>
        </div>
        <a href="{{ route('mpn.pengetahuan.index', $konteks->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Batal
        </a>
    </div>

    {{-- Info Pengetahuan (Read-only) --}}
    <div class="mb-6 p-5 rounded-xl border border-slate-700/50 bg-slate-800/40">
        <h3 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Informasi Pengetahuan (Dari Form 1)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Nama Layanan SPBE</label>
                <div class="px-3 py-2 bg-slate-900/50 rounded-lg border border-slate-700/50 text-sm text-slate-300">
                    {{ $pengetahuan->layanan->nama_layanan }}
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Nama Pengetahuan</label>
                <div class="px-3 py-2 bg-slate-900/50 rounded-lg border border-slate-700/50 text-sm text-slate-300">
                    {{ $pengetahuan->nama_pengetahuan }}
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Jenis Pengetahuan</label>
                <div class="px-3 py-2 bg-slate-900/50 rounded-lg border border-slate-700/50 text-sm text-slate-300">
                    {{ $pengetahuan->jenis_pengetahuan }}
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Pemilik Pengetahuan</label>
                <div class="px-3 py-2 bg-slate-900/50 rounded-lg border border-slate-700/50 text-sm text-slate-300">
                    {{ $pengetahuan->pemilik_pengetahuan }}
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        
        {{-- Section Wajib --}}
        <div class="p-6 rounded-xl border border-slate-700/50 bg-slate-800/40 space-y-6">
            <h3 class="text-base font-semibold text-white border-b border-slate-700/50 pb-3">Data Pengumpulan (Wajib)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Tanggal Pengumpulan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Pengumpulan <span class="text-red-400">*</span></label>
                    <input type="date" wire:model="tanggal_pengumpulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('tanggal_pengumpulan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                {{-- Unit Pengumpulan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Unit Pengumpulan <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="unit_pengumpulan" placeholder="Contoh: Bidang E-Government" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('unit_pengumpulan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Lokasi Penyimpanan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Lokasi Penyimpanan <span class="text-red-400">*</span></label>
                    <select wire:model.live="lokasi_penyimpanan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Pilih Lokasi Penyimpanan --</option>
                        @foreach($lokasiOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('lokasi_penyimpanan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Keterangan Lokasi Lainnya --}}
                @if($lokasi_penyimpanan === 'Lainnya')
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan Lokasi Lainnya <span class="text-red-400">*</span></label>
                        <input type="text" wire:model="keterangan_lokasi_lainnya" placeholder="Sebutkan lokasi penyimpanan..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('keterangan_lokasi_lainnya') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div class="hidden md:block"></div> {{-- Empty grid space --}}
                @endif

                {{-- Tanggal Terakhir Update --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal Terakhir Update <span class="text-red-400">*</span></label>
                    <input type="date" wire:model="tanggal_terakhir_update" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @error('tanggal_terakhir_update') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Rating Pengetahuan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Rating Pengetahuan (1-5) <span class="text-red-400">*</span></label>
                    <select wire:model="rating_pengetahuan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">-- Pilih Rating --</option>
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}">{{ $i }} - {{ ['Sangat Rendah', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'][$i-1] }}</option>
                        @endfor
                    </select>
                    @error('rating_pengetahuan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section Kondisional (Kuning) --}}
        @if($isBelumDokumentasi)
            <div class="p-6 rounded-xl border border-amber-500/30 bg-amber-500/5 space-y-6">
                <div class="flex items-start gap-3 border-b border-amber-500/20 pb-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-amber-300">Pengolahan Pengetahuan Tambahan</h3>
                        <p class="text-xs text-amber-400/70 mt-1">Bagian ini wajib diisi karena pada Form 1 pengetahuan ini ditandai sebagai "Belum Terdokumentasi".</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Status Publikasi --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Status Publikasi</label>
                        <select wire:model="status_publikasi_simpan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">-- Pilih Status --</option>
                            @foreach($statusPublikasiOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status_publikasi_simpan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Metode Pengolahan --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Metode Pengolahan</label>
                        <select wire:model="ref_metode_pengolahan_id" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">-- Pilih Metode --</option>
                            @foreach($metodeOptions as $metode)
                                <option value="{{ $metode->id }}">{{ $metode->nama_metode }}</option>
                            @endforeach
                        </select>
                        @error('ref_metode_pengolahan_id') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Deskripsi Pengolahan --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Deskripsi Pengolahan</label>
                        <textarea wire:model="deskripsi_pengolahan" rows="3" placeholder="Jelaskan proses pengolahan yang dilakukan..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></textarea>
                        @error('deskripsi_pengolahan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Nama Pengetahuan Baru --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Pengetahuan Baru <span class="text-xs text-slate-500 font-normal ml-2">(Opsional, jika ada perubahan nama setelah diolah)</span></label>
                        <input type="text" wire:model="nama_pengetahuan_baru" placeholder="Contoh: Dokumen Arsitektur SPBE v2.0" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        @error('nama_pengetahuan_baru') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Info ID Baru --}}
                    <div class="md:col-span-2 mt-2">
                        <div class="px-4 py-3 bg-slate-900/80 rounded-lg border border-slate-700/50 flex items-center justify-between">
                            <span class="text-sm text-slate-400">ID Pengetahuan Baru yang akan dihasilkan:</span>
                            <span class="font-mono text-amber-400 font-semibold">{{ $pengetahuan->pengumpulan?->kode_pengetahuan_baru ?: $pengetahuan->kode_pengetahuan . '-REV' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-700/50">
            <button type="submit" 
                wire:loading.attr="disabled"
                class="inline-flex justify-center items-center gap-2 px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-semibold text-white shadow-lg shadow-blue-500/20 transition-all disabled:opacity-60 cursor-pointer">
                <svg class="w-4 h-4" wire:loading.remove wire:target="save" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg class="w-4 h-4 animate-spin" wire:loading wire:target="save" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Simpan Form 2
            </button>
        </div>
    </form>
</div>
