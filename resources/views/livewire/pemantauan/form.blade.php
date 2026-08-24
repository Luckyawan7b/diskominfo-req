<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Pemantauan Risiko — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 9 (Modul 3.1): Catatan realisasi dan data dukung pelaksanaan mitigasi</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('risiko.index', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Daftar Risiko
            </a>
        </div>
    </div>

    {{-- Wizard Navigasi --}}
    <x-risk-wizard :konteks="$konteks" activeStep="Pemantauan" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left: Risk Selector --}}
        <div class="lg:col-span-4 rounded-xl border border-slate-700/50 bg-slate-800/50 p-5">
            <h3 class="text-sm font-semibold text-white mb-3">Pilih Risiko untuk Dipantau</h3>
            <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                @forelse($risikos as $r)
                    <button wire:click="$set('selectedRisikoId', {{ $r->id }})"
                        class="w-full text-left p-3 rounded-lg border transition-all cursor-pointer {{ $selectedRisikoId === $r->id ? 'border-emerald-500 bg-emerald-500/10 text-white' : 'border-slate-700 bg-slate-900/30 text-slate-300 hover:bg-slate-700/40' }}">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-mono font-bold text-emerald-400">{{ $r->kode_risiko }}</span>
                            <span class="text-[11px] text-slate-400">Besaran: {{ $r->besaran_risiko }}</span>
                        </div>
                        <p class="text-xs line-clamp-2 leading-relaxed">{{ $r->peristiwa_risiko }}</p>
                    </button>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">Belum ada risiko yang terdaftar.</p>
                @endforelse
            </div>
        </div>

        {{-- Right: Monitoring Form & History --}}
        <div class="lg:col-span-8 space-y-6">
            @if($selectedRisiko)
                {{-- Detail Risiko Mini Card --}}
                <div class="rounded-xl border border-slate-700 bg-slate-800/80 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 border-b border-slate-700/50 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">{{ $selectedRisiko->kode_risiko }}</span>
                            <span class="text-xs text-slate-300">Level Saat Ini: <strong class="text-white">{{ $selectedRisiko->besaran_risiko ?? '-' }}</strong></span>
                            @php
                                $proyeksi = $selectedRisiko->residual?->besaran_risiko;
                                $lblProyeksi = $proyeksi ? app(\App\Services\RiskMatrixCalculator::class)->label($proyeksi) : null;
                                $clsProyeksi = match($lblProyeksi) {
                                    'Rendah' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                    'Sedang' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                    'Tinggi' => 'bg-orange-500/10 text-orange-400 border-orange-500/30',
                                    'Sangat Tinggi' => 'bg-red-500/10 text-red-400 border-red-500/30',
                                    default => 'bg-slate-700 text-slate-400',
                                };
                            @endphp
                            <span class="text-xs text-slate-300 flex items-center gap-2 border-l border-slate-600 pl-3">
                                Proyeksi (Residual): 
                                @if($proyeksi)
                                    <span class="px-2 py-0.5 rounded border text-[10px] font-bold {{ $clsProyeksi }}">{{ $proyeksi }} - {{ $lblProyeksi }}</span>
                                @else
                                    <span class="text-[10px] text-amber-400 italic">Belum disetel di Formulir 1.0</span>
                                @endif
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-400">Keputusan Perlakuan: <strong class="text-slate-200">{{ $selectedRisiko->perlakuan?->keputusan_perlakuan ?? 'Belum ada' }}</strong></span>
                    </div>
                    <h4 class="text-sm font-semibold text-white mb-2">{{ $selectedRisiko->peristiwa_risiko }}</h4>
                    <div class="bg-slate-900/50 rounded-lg p-3 border border-slate-700/50">
                        <span class="block text-[10px] uppercase text-slate-500 font-semibold mb-1">Rencana Penanganan / Mitigasi</span>
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $selectedRisiko->perlakuan?->deskripsi_detail_perlakuan ?? 'Belum ada deskripsi rencana mitigasi.' }}</p>
                        @if($selectedRisiko->perlakuan?->penanggung_jawab)
                            <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-4">
                                <span><strong class="text-slate-300">PIC:</strong> {{ $selectedRisiko->perlakuan->penanggung_jawab }}</span>
                                <span><strong class="text-slate-300">Waktu:</strong> {{ $selectedRisiko->perlakuan->waktu_rencana_perlakuan ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Form Tambah Pemantauan --}}
                @if($isEditable)
                    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 space-y-4">
                        <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Input Catatan Pemantauan Baru
                        </h3>

                        <form wire:submit="savePemantauan" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1">Periode Pemantauan</label>
                                    <select wire:model="periode" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                        <option value="semester_1">Semester 1 (Jan - Jun)</option>
                                        <option value="semester_2">Semester 2 (Jul - Des)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1">Tahun</label>
                                    <input wire:model="tahun" type="number" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">Hasil Pelaksanaan / Realisasi Mitigasi <span class="text-red-400">*</span></label>
                                <textarea wire:model="hasil_pelaksanaan" rows="3" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Uraikan kemajuan pelaksanaan mitigasi risiko ini"></textarea>
                                @error('hasil_pelaksanaan') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">Catatan Data Dukung / Hambatan</label>
                                <textarea wire:model="data_dukung_catatan" rows="2" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Kendala atau catatan tambahan"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-300 mb-1">Upload File Bukti Dukung (PDF, JPG, PNG, DOCX max 10MB)</label>
                                <input wire:model="file_bukti" type="file" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-slate-200 hover:file:bg-slate-600 cursor-pointer">
                                <div wire:loading wire:target="file_bukti" class="text-xs text-emerald-400 mt-1">Mengunggah file...</div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-semibold text-white transition-colors cursor-pointer">
                                    Simpan Catatan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Riwayat Pemantauan --}}
                <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Riwayat Pemantauan</h3>
                    <div class="space-y-4">
                        @forelse($pemantauanList as $p)
                            <div class="p-4 rounded-lg border border-slate-700 bg-slate-900/40 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-emerald-400">{{ $p->periode === 'semester_1' ? 'Semester 1' : 'Semester 2' }} {{ $p->tahun }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] text-slate-500">{{ $p->created_at->format('d M Y') }}</span>
                                        @if($isEditable)
                                            <button wire:click="deletePemantauan({{ $p->id }})" wire:confirm="Hapus catatan pemantauan ini?" class="text-red-400 hover:text-red-300 text-xs cursor-pointer">
                                                Hapus
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm text-slate-200 leading-relaxed">{{ $p->hasil_pelaksanaan }}</p>
                                @if($p->data_dukung_catatan)
                                    <p class="text-xs text-slate-400 italic">Catatan: {{ $p->data_dukung_catatan }}</p>
                                @endif

                                {{-- Lampiran Files --}}
                                @if($p->lampiran->isNotEmpty())
                                    <div class="pt-2 flex flex-wrap gap-2">
                                        @foreach($p->lampiran as $file)
                                            <a href="{{ asset('storage/' . $file->path_file) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-800 border border-slate-700 text-xs text-slate-300 hover:text-white hover:border-slate-600 transition-colors">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>{{ $file->nama_file }}</span>
                                                <span class="text-[10px] text-slate-500">({{ $file->ukuran_kb }} KB)</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center py-8 text-slate-500 text-xs">Belum ada riwayat pemantauan untuk risiko ini.</p>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="p-12 rounded-xl border border-slate-700 bg-slate-800/30 text-center text-slate-500 text-sm">
                    Pilih salah satu risiko di sebelah kiri untuk melihat atau mengisi pemantauan.
                </div>
            @endif
        </div>
    </div>
</div>
