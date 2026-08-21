<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white">{{ $konteks->desa->nama_desa ?? 'Desa' }} — Tahun {{ $konteks->tahun_penilaian }}</h1>
                @php
                    $statusColors = [
                        'draft'     => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                        'submitted' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        'approved'  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'rejected'  => 'bg-red-500/10 text-red-400 border-red-500/20',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusColors[$konteks->status] ?? '' }}">
                    Status: {{ ucfirst($konteks->status) }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">UPR: {{ $konteks->nama_upr ?: '-' }} | Selera Risiko: {{ $konteks->selera_risiko }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.review.index') }}" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                ← Kembali ke Daftar Review
            </a>
            @if($konteks->status !== 'approved')
                <button wire:click="approveAll" wire:confirm="Setujui (Approve) semua baris risiko dalam dokumen ini sekaligus?"
                    class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition-all cursor-pointer">
                    ✓ Approve Semua
                </button>
            @endif
        </div>
    </div>

    {{-- Detail Info Card --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/40 p-5 mb-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <div>
            <span class="text-slate-500 uppercase font-semibold">Tugas UPR:</span>
            <p class="text-slate-300 mt-1">{{ $konteks->tugas_upr ?: '-' }}</p>
        </div>
        <div>
            <span class="text-slate-500 uppercase font-semibold">Fungsi UPR:</span>
            <p class="text-slate-300 mt-1">{{ $konteks->fungsi_upr ?: '-' }}</p>
        </div>
        <div>
            <span class="text-slate-500 uppercase font-semibold">Struktur Pelaksana:</span>
            <p class="text-slate-300 mt-1">Pemilik: {{ $konteks->strukturPelaksana?->pemilik_risiko ?: '-' }}</p>
            <p class="text-slate-300">Koordinator: {{ $konteks->strukturPelaksana?->koordinator_risiko ?: '-' }}</p>
        </div>
    </div>

    {{-- List Risks with Review Actions --}}
    <h2 class="text-lg font-bold text-white mb-4">Daftar Risiko yang Diajukan ({{ $risikos->count() }})</h2>

    <div class="space-y-4">
        @forelse($risikos as $r)
            @php
                $besaranColor = match(true) {
                    !$r->besaran_risiko   => 'bg-slate-700 text-slate-400',
                    $r->besaran_risiko <= 4 => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                    $r->besaran_risiko <= 9 => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                    $r->besaran_risiko <= 16 => 'bg-orange-500/20 text-orange-400 border-orange-500/30',
                    default                 => 'bg-red-500/20 text-red-400 border-red-500/30',
                };
            @endphp
            <div class="rounded-xl border border-slate-700/60 bg-slate-800/50 p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-700/50 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-mono font-bold text-white bg-slate-700/60 px-2.5 py-1 rounded">{{ $r->kode_risiko }}</span>
                        <span class="text-xs text-slate-400">{{ $r->kategoriRisiko?->nama_kategori ?? 'Tanpa Kategori' }}</span>
                        <span class="text-xs px-2 py-0.5 rounded border {{ $besaranColor }} font-semibold">
                            K={{ $r->level_kemungkinan }} × D={{ $r->level_dampak }} (Besaran: {{ $r->besaran_risiko }})
                        </span>
                    </div>

                    {{-- Actions per Row --}}
                    <div class="flex items-center gap-2">
                        @if($r->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Disetujui
                            </span>
                        @elseif($r->status === 'rejected')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-500/20 text-red-400 text-xs font-semibold">
                                ✗ Ditolak
                            </span>
                        @endif

                        <button wire:click="approveRisk({{ $r->id }})"
                            class="px-3 py-1.5 rounded-lg bg-emerald-600/80 hover:bg-emerald-600 text-white text-xs font-medium transition-colors cursor-pointer">
                            ✓ Approve
                        </button>
                        <button wire:click="openRejectModal({{ $r->id }})"
                            class="px-3 py-1.5 rounded-lg bg-red-600/80 hover:bg-red-600 text-white text-xs font-medium transition-colors cursor-pointer">
                            ✗ Reject
                        </button>
                    </div>
                </div>

                {{-- Content Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <strong class="text-slate-300">Peristiwa Risiko:</strong>
                        <p class="text-slate-200 mt-0.5 text-sm">{{ $r->peristiwa_risiko }}</p>
                    </div>
                    <div>
                        <strong class="text-slate-300">Penyebab / Akar Masalah:</strong>
                        <p class="text-slate-400 mt-0.5">{{ $r->penyebab ?: '-' }}</p>
                    </div>
                    <div>
                        <strong class="text-slate-300">Dampak:</strong>
                        <p class="text-slate-400 mt-0.5">{{ $r->dampak_risiko ?: '-' }}</p>
                    </div>
                    <div>
                        <strong class="text-slate-300">Rencana Perlakuan ({{ $r->perlakuan?->keputusan_perlakuan ?? '-' }}):</strong>
                        <p class="text-slate-400 mt-0.5">{{ $r->perlakuan?->deskripsi_detail_perlakuan ?: '-' }}</p>
                        <p class="text-slate-500 mt-0.5 text-[11px]">Target: {{ $r->perlakuan?->waktu_rencana_perlakuan ?: '-' }} | PIC: {{ $r->perlakuan?->penanggung_jawab ?: '-' }}</p>
                    </div>
                </div>

                {{-- Rejection note if present --}}
                @if($r->catatan_penolakan)
                    <div class="p-3 rounded-lg bg-red-950/40 border border-red-500/30 text-xs text-red-300">
                        <strong>Catatan Penolakan:</strong> {{ $r->catatan_penolakan }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-slate-500 text-sm">
                Belum ada risiko yang terdaftar.
            </div>
        @endforelse
    </div>

    {{-- Reject Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showRejectModal', false)"></div>
            <div class="relative bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-base font-semibold text-white">Tolak Baris Risiko</h3>
                <p class="text-xs text-slate-400">Berikan catatan penolakan agar operator desa dapat melakukan perbaikan.</p>

                <form wire:submit="submitRejectRisk" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Catatan Penolakan <span class="text-red-400">*</span></label>
                        <textarea wire:model="catatan_penolakan" rows="4" class="w-full rounded-lg border border-slate-600 bg-slate-700/50 px-3 py-2 text-white text-sm focus:ring-2 focus:ring-red-500 focus:outline-none" placeholder="Tuliskan alasan penolakan dan instruksi perbaikan..."></textarea>
                        @error('catatan_penolakan') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showRejectModal', false)" class="px-4 py-2 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-sm font-semibold text-white cursor-pointer">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
