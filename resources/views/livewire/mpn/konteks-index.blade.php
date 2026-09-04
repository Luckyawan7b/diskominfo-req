<div>
    {{-- ─── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Pengetahuan</h1>
            <p class="text-sm text-slate-400 mt-1">Daftar Konteks MPN Tahun {{ $tahun }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Dashboard
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── Dinas List ─────────────────────────────────────────────────────────── --}}
    <div class="space-y-3">
        @forelse($dinasList as $dinas)
            @php
                $konteks = $dinas->mpnKonteks->first();
                $statusLabel = $konteks ? ($konteks->status === 'final' ? 'Final' : 'Draft') : 'Belum Dimulai';
                $statusColor = match(true) {
                    $konteks?->status === 'final' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                    $konteks !== null             => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                    default                       => 'bg-slate-700/40 text-slate-400 border-slate-600/30',
                };
                $pengetahuanCount = $konteks
                    ? $konteks->layanan()->withCount('pengetahuan')->get()->sum('pengetahuan_count')
                    : 0;
                $layananCount = $konteks ? $konteks->layanan()->count() : 0;
            @endphp

            <div class="rounded-xl border border-slate-700/50 bg-slate-800/40 p-5 flex flex-col sm:flex-row sm:items-center gap-4 hover:border-blue-500/30 hover:bg-slate-800/60 transition-all">
                {{-- Icon + Info --}}
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-white text-sm">{{ $dinas->nama_dinas }}</p>
                            <span class="px-2 py-0.5 rounded bg-slate-700/60 text-slate-400 font-mono text-[10px]">{{ $dinas->alias }}</span>
                        </div>
                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
                            <span>{{ $layananCount }} Layanan</span>
                            <span class="text-slate-600">·</span>
                            <span>{{ $pengetahuanCount }} Pengetahuan</span>
                            @if($konteks)
                                <span class="text-slate-600">·</span>
                                <span>Diperbarui: {{ $konteks->updated_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Status + Action --}}
                <div class="flex items-center gap-3 shrink-0">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                    <button
                        wire:click="createOrOpen({{ $dinas->id }})"
                        wire:loading.attr="disabled"
                        wire:target="createOrOpen({{ $dinas->id }})"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-medium text-white transition-colors disabled:opacity-60 cursor-pointer">
                        <svg class="w-3.5 h-3.5" wire:loading.remove wire:target="createOrOpen({{ $dinas->id }})" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $konteks ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"/>
                        </svg>
                        <svg class="w-4 h-4 animate-spin" wire:loading wire:target="createOrOpen({{ $dinas->id }})" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        {{ $konteks ? 'Buka Form 1' : 'Mulai MPN' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-700/40 px-4 py-16 text-center bg-slate-800/20">
                <div class="w-12 h-12 rounded-xl bg-slate-700/50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="font-semibold text-slate-300 mb-1">Tidak ada data dinas</p>
                <p class="text-xs text-slate-500">Pastikan akun Anda sudah terhubung ke entitas dinas.</p>
            </div>
        @endforelse
    </div>
</div>
