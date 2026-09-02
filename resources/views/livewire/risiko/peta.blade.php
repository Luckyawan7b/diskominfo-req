<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Peta Risiko (Heatmap 5×5) — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Visualisasi sebaran risiko SPBE berdasarkan level kemungkinan dan dampak</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('risiko.index', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                Daftar Risiko
            </a>
        </div>
    </div>


    {{-- Selera Risiko Info Card --}}
    <div class="mb-6 p-4 rounded-xl border border-slate-700 bg-slate-800/40 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold">
                {{ $konteks->selera_risiko }}
            </div>
            <div>
                <p class="text-sm font-semibold text-white">Batas Selera Risiko: {{ $konteks->selera_risiko }}</p>
                <p class="text-xs text-slate-400">Risiko dengan besaran > {{ $konteks->selera_risiko }} memerlukan rencana mitigasi prioritas.</p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-500"></span><span class="text-slate-300">Rendah (1-4)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500"></span><span class="text-slate-300">Sedang (5-9)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-orange-500"></span><span class="text-slate-300">Tinggi (10-16)</span></div>
            <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-500"></span><span class="text-slate-300">Sangat Tinggi (17-25)</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Matrix 5x5 --}}
        <div class="lg:col-span-7 rounded-xl border border-slate-700/50 bg-slate-800/50 p-6">
            <div class="relative">
                {{-- Y-Axis Label --}}
                <div class="absolute -left-7 top-1/2 -translate-y-1/2 -rotate-90 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Kemungkinan →
                </div>

                <div class="pl-4">
                    {{-- 5x5 Matrix Rows --}}
                    <div class="space-y-2">
                        @foreach([5, 4, 3, 2, 1] as $k)
                            <div class="flex items-center gap-2">
                                <span class="w-6 text-xs font-bold text-slate-400 text-right">{{ $k }}</span>
                                <div class="grid grid-cols-5 gap-2 flex-1">
                                    @foreach([1, 2, 3, 4, 5] as $d)
                                        @php
                                            $cell = $matrix[$k][$d];
                                            $isSelected = ($selectedK === $k && $selectedD === $d);
                                            $colorBg = match(true) {
                                                $cell['besaran'] <= 4  => 'bg-emerald-600/30 hover:bg-emerald-600/50 border-emerald-500/40 text-emerald-300',
                                                $cell['besaran'] <= 9  => 'bg-amber-600/30 hover:bg-amber-600/50 border-amber-500/40 text-amber-300',
                                                $cell['besaran'] <= 16 => 'bg-orange-600/30 hover:bg-orange-600/50 border-orange-500/40 text-orange-300',
                                                default                => 'bg-red-600/30 hover:bg-red-600/50 border-red-500/40 text-red-300',
                                            };
                                        @endphp
                                        <button wire:click="selectCell({{ $k }}, {{ $d }})"
                                            class="h-16 rounded-xl border p-2 flex flex-col items-center justify-between transition-all cursor-pointer {{ $colorBg }} {{ $isSelected ? 'ring-2 ring-white scale-105 shadow-xl' : '' }}">
                                            <span class="text-[10px] opacity-70 font-mono font-medium">{{ $cell['besaran'] }}</span>
                                            @if($cell['count'] > 0)
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white text-slate-900 font-bold text-xs shadow">
                                                    {{ $cell['count'] }}
                                                </span>
                                            @else
                                                <span class="text-xs opacity-20">-</span>
                                            @endif
                                            <span></span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- X-Axis Labels --}}
                    <div class="flex items-center gap-2 mt-3">
                        <span class="w-6"></span>
                        <div class="grid grid-cols-5 gap-2 flex-1 text-center">
                            @foreach([1, 2, 3, 4, 5] as $d)
                                <span class="text-xs font-bold text-slate-400">{{ $d }}</span>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-center text-xs font-semibold text-slate-400 uppercase tracking-wider mt-2">Dampak →</p>
                </div>
            </div>
        </div>

        {{-- Detail List of Selected Risks --}}
        <div class="lg:col-span-5 rounded-xl border border-slate-700/50 bg-slate-800/50 p-6 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">
                    @if($selectedK && $selectedD)
                        Risiko di K={{ $selectedK }}, D={{ $selectedD }} (Besaran: {{ $matrix[$selectedK][$selectedD]['besaran'] }})
                    @else
                        Semua Risiko ({{ $filteredRisikos->count() }})
                    @endif
                </h3>
                @if($selectedK && $selectedD)
                    <button wire:click="selectCell(null, null)" class="text-xs text-emerald-400 hover:underline cursor-pointer">
                        Reset Filter
                    </button>
                @endif
            </div>

            <div class="space-y-3 overflow-y-auto max-h-96 flex-1 pr-1">
                @forelse($filteredRisikos as $item)
                    <a href="{{ route('risiko.form', [$konteks, $item]) }}" class="block p-3.5 rounded-lg border border-slate-700 bg-slate-900/40 hover:bg-slate-700/30 transition-colors">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-mono font-bold text-emerald-400">{{ $item->kode_risiko }}</span>
                            <span class="text-xs text-slate-400">Besaran: <strong class="text-white">{{ $item->besaran_risiko }}</strong></span>
                        </div>
                        <p class="text-sm text-slate-200 line-clamp-2 leading-relaxed">{{ $item->peristiwa_risiko }}</p>
                        <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                            <span>{{ $item->kategoriRisiko?->nama_kategori ?? 'Tanpa kategori' }}</span>
                            <span class="capitalize">{{ $item->status }}</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12 text-slate-500 text-sm">
                        Tidak ada risiko pada sel matriks ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
