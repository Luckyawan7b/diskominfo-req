<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Form 3: Pemanfaatan & Alih Pengetahuan</h1>
            <p class="text-sm text-slate-400 mt-1">ID Pengetahuan: <span class="font-mono text-indigo-400">{{ $pengetahuan->kode_pengetahuan }}</span></p>
        </div>
        <a href="{{ route('mpn.pengetahuan.index', $konteks->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Kembali
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-start">
        {{-- Info Sidebar (Left) --}}
        <div class="w-full lg:w-1/3 space-y-4 shrink-0">
            <div class="p-5 rounded-xl border border-slate-700/50 bg-slate-800/40">
                <h3 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2 border-b border-slate-700 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informasi Pengetahuan
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 uppercase tracking-wider mb-1">Nama Pengetahuan</label>
                        <div class="text-sm text-white font-medium">
                            {{ $pengetahuan->nama_pengetahuan }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-500 uppercase tracking-wider mb-1">Layanan SPBE</label>
                        <div class="text-sm text-slate-300">
                            {{ $pengetahuan->layanan->nama_layanan }}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-slate-500 uppercase tracking-wider mb-1">Jenis</label>
                            <div class="text-sm text-slate-300">{{ $pengetahuan->jenis_pengetahuan }}</div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-slate-500 uppercase tracking-wider mb-1">Pemilik</label>
                            <div class="text-sm text-slate-300">{{ $pengetahuan->pemilik_pengetahuan }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-5 rounded-xl border border-slate-700/50 bg-slate-800/40">
                <h3 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2 border-b border-slate-700 pb-3">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Data Pengumpulan
                </h3>
                @if($pengetahuan->pengumpulan)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Rating (1-5):</span>
                            <span class="text-white font-mono bg-indigo-500/20 px-2 py-0.5 rounded text-xs">{{ $pengetahuan->pengumpulan->rating_pengetahuan }} / 5</span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-t border-slate-700/50 pt-3">
                            <span class="text-slate-500">Terakhir Update:</span>
                            <span class="text-slate-300">{{ $pengetahuan->pengumpulan->tanggal_terakhir_update?->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm border-t border-slate-700/50 pt-3">
                            <span class="text-slate-500">Unit:</span>
                            <span class="text-slate-300">{{ $pengetahuan->pengumpulan->unit_pengumpulan }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-sm text-slate-400 italic">Data pengumpulan tidak ditemukan.</div>
                @endif
            </div>
        </div>

        {{-- Main Content (Right) --}}
        <div class="w-full lg:w-2/3">
            
            {{-- Tabs --}}
            <div class="flex space-x-1 bg-slate-900/50 p-1 rounded-xl border border-slate-700/50 mb-6">
                <button wire:click="switchTab('pemanfaatan')" type="button" class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ $activeTab === 'pemanfaatan' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Pemanfaatan Pengetahuan
                </button>
                <button wire:click="switchTab('alih_pengetahuan')" type="button" class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ $activeTab === 'alih_pengetahuan' ? 'bg-slate-700 text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Alih Pengetahuan
                </button>
            </div>

            {{-- Tab 1: Pemanfaatan --}}
            @if($activeTab === 'pemanfaatan')
                <div class="space-y-6">
                    @if (session('success_pemanfaatan'))
                        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success_pemanfaatan') }}
                        </div>
                    @endif

                    <div class="p-6 rounded-xl border border-slate-700/50 bg-slate-800/40">
                        <h3 class="text-base font-semibold text-white border-b border-slate-700/50 pb-3 mb-5">Tambah Log Pemanfaatan</h3>
                        <form wire:submit="savePemanfaatan" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Tanggal Pemanfaatan <span class="text-red-400">*</span></label>
                                    <input type="date" wire:model="pemanfaatan_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error('pemanfaatan_tanggal') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Tipe Pengguna <span class="text-red-400">*</span></label>
                                    <select wire:model="pemanfaatan_tipe_pengguna" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="internal">Internal (Pegawai)</option>
                                        <option value="publik">Publik (Masyarakat)</option>
                                    </select>
                                    @error('pemanfaatan_tipe_pengguna') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Unit / Nama Pengguna <span class="text-red-400">*</span></label>
                                    <input type="text" wire:model="pemanfaatan_unit_pengguna" placeholder="Contoh: Bidang E-Gov / Warga Desa..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error('pemanfaatan_unit_pengguna') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Tujuan Pemanfaatan <span class="text-red-400">*</span></label>
                                    <textarea wire:model="pemanfaatan_tujuan" rows="2" placeholder="Digunakan untuk..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                                    @error('pemanfaatan_tujuan') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Rating / Nilai Manfaat (1-5) <span class="text-red-400">*</span></label>
                                    <select wire:model="pemanfaatan_rating" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        <option value="">-- Pilih Rating --</option>
                                        @for($i=1; $i<=5; $i++)
                                            <option value="{{ $i }}">{{ $i }} - {{ ['Sangat Rendah', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi'][$i-1] }}</option>
                                        @endfor
                                    </select>
                                    @error('pemanfaatan_rating') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end pt-2">
                                <button type="submit" wire:loading.attr="disabled" wire:target="savePemanfaatan" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 animate-spin hidden" wire:loading.class.remove="hidden" wire:target="savePemanfaatan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Simpan Log Pemanfaatan
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-xl border border-slate-700/50 bg-slate-800/40 overflow-hidden">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-900/50 border-b border-slate-700/50 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Tanggal</th>
                                    <th class="px-4 py-3 font-medium">Pengguna</th>
                                    <th class="px-4 py-3 font-medium">Tujuan</th>
                                    <th class="px-4 py-3 font-medium text-center">Rating</th>
                                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @forelse($pemanfaatans as $p)
                                    <tr class="hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $p->tanggal_pemanfaatan->format('d M Y') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-200">{{ $p->unit_pengguna }}</div>
                                            <div class="text-[10px] uppercase tracking-wider text-slate-500">{{ $p->tipe_pengguna }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-xs leading-relaxed max-w-[200px] truncate" title="{{ $p->tujuan_pemanfaatan }}">
                                            {{ Str::limit($p->tujuan_pemanfaatan, 40) }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-500/20 text-indigo-400 font-mono text-xs border border-indigo-500/20">
                                                {{ $p->rating_pengetahuan }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" wire:click="deletePemanfaatan({{ $p->id }})" wire:confirm="Yakin ingin menghapus log pemanfaatan ini?" class="text-red-400 hover:text-red-300 transition-colors p-1.5 rounded-lg hover:bg-red-400/10" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-500 text-sm italic">Belum ada log pemanfaatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Tab 2: Alih Pengetahuan --}}
            @if($activeTab === 'alih_pengetahuan')
                <div class="space-y-6">
                    @if (session('success_alih'))
                        <div class="px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success_alih') }}
                        </div>
                    @endif

                    <div class="p-6 rounded-xl border border-slate-700/50 bg-slate-800/40">
                        <h3 class="text-base font-semibold text-white border-b border-slate-700/50 pb-3 mb-5">Tambah Log Alih Pengetahuan</h3>
                        <form wire:submit="saveAlihPengetahuan" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Tanggal Mulai <span class="text-red-400">*</span></label>
                                    <input type="date" wire:model="alih_tanggal_mulai" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error('alih_tanggal_mulai') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Tanggal Selesai (Opsional)</label>
                                    <input type="date" wire:model="alih_tanggal_selesai" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error('alih_tanggal_selesai') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-2">Metode Alih Pengetahuan <span class="text-red-400">*</span></label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="alih_metode_pelatihan" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                                            <span class="text-sm text-slate-300">Pelatihan</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="alih_metode_workshop" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                                            <span class="text-sm text-slate-300">Workshop</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="alih_metode_sosialisasi" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                                            <span class="text-sm text-slate-300">Sosialisasi</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="alih_metode_mentoring" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                                            <span class="text-sm text-slate-300">Mentoring</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model="alih_metode_sharing" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                                            <span class="text-sm text-slate-300">Sharing Session</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" wire:model.live="alih_metode_lainnya" class="rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-800">
                                            <span class="text-sm text-slate-300">Lainnya</span>
                                        </label>
                                    </div>
                                    @error('alih_metode') <span class="text-xs text-red-400 mt-2 block">{{ $message }}</span> @enderror
                                </div>
                                
                                @if($alih_metode_lainnya)
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Sebutkan Metode Lainnya <span class="text-red-400">*</span></label>
                                    <input type="text" wire:model="alih_keterangan_lainnya" placeholder="Sebutkan..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error('alih_keterangan_lainnya') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                @endif

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Penerima Pengetahuan <span class="text-red-400">*</span></label>
                                    <input type="text" wire:model="alih_penerima" placeholder="Contoh: Seluruh staf bidang, Masyarakat umum..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    @error('alih_penerima') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Hasil Evaluasi (Singkat) <span class="text-red-400">*</span></label>
                                    <textarea wire:model="alih_evaluasi" rows="2" placeholder="Bagaimana hasil penerimaan pengetahuan ini..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                                    @error('alih_evaluasi') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end pt-2">
                                <button type="submit" wire:loading.attr="disabled" wire:target="saveAlihPengetahuan" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 animate-spin hidden" wire:loading.class.remove="hidden" wire:target="saveAlihPengetahuan" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Simpan Log Alih Pengetahuan
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-xl border border-slate-700/50 bg-slate-800/40 overflow-hidden">
                        <table class="w-full text-left text-sm text-slate-300">
                            <thead class="bg-slate-900/50 border-b border-slate-700/50 text-xs uppercase text-slate-400">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Tanggal</th>
                                    <th class="px-4 py-3 font-medium">Penerima & Metode</th>
                                    <th class="px-4 py-3 font-medium">Evaluasi</th>
                                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @forelse($alihPengetahuans as $a)
                                    <tr class="hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-xs">
                                            {{ $a->tanggal_mulai->format('d M Y') }}
                                            @if($a->tanggal_selesai)
                                                <br><span class="text-slate-500">s/d</span> {{ $a->tanggal_selesai->format('d M Y') }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-200">{{ $a->penerima_pengetahuan }}</div>
                                            <div class="text-[10px] text-slate-400 mt-1 flex flex-wrap gap-1">
                                                @if($a->metode_pelatihan) <span class="bg-slate-700/50 px-1.5 py-0.5 rounded">Pelatihan</span> @endif
                                                @if($a->metode_workshop) <span class="bg-slate-700/50 px-1.5 py-0.5 rounded">Workshop</span> @endif
                                                @if($a->metode_sosialisasi) <span class="bg-slate-700/50 px-1.5 py-0.5 rounded">Sosialisasi</span> @endif
                                                @if($a->metode_mentoring) <span class="bg-slate-700/50 px-1.5 py-0.5 rounded">Mentoring</span> @endif
                                                @if($a->metode_sharing) <span class="bg-slate-700/50 px-1.5 py-0.5 rounded">Sharing</span> @endif
                                                @if($a->metode_lainnya) <span class="bg-slate-700/50 px-1.5 py-0.5 rounded">{{ $a->keterangan_lainnya }}</span> @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-xs leading-relaxed max-w-[200px] truncate" title="{{ $a->hasil_evaluasi }}">
                                            {{ Str::limit($a->hasil_evaluasi, 40) }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" wire:click="deleteAlihPengetahuan({{ $a->id }})" wire:confirm="Yakin ingin menghapus log alih pengetahuan ini?" class="text-red-400 hover:text-red-300 transition-colors p-1.5 rounded-lg hover:bg-red-400/10" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-500 text-sm italic">Belum ada log alih pengetahuan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
