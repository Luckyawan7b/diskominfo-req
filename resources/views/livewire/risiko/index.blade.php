<div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Risiko — {{ $konteks->tahun_penilaian }}</h1>
            <p class="text-sm text-slate-400 mt-1">Formulir 5-7: Identifikasi, analisis, evaluasi, dan perlakuan risiko</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('pemantauan.form', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Pemantauan
            </a>
            <a href="{{ route('risiko.peta', $konteks) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-600 text-sm text-slate-300 hover:bg-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                Peta Risiko
            </a>
            @if($isEditable)
                <a href="{{ route('risiko.form', [$konteks, 'new']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/25 hover:from-emerald-600 hover:to-teal-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Risiko
                </a>
            @endif
            <livewire:konteks.submit-konteks :konteks="$konteks" />
        </div>
    </div>

    {{-- Filter --}}
    <div class="mb-4">
        <select wire:model.live="filterStatus" class="rounded-lg border border-slate-600 bg-slate-800 text-sm text-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-700/50 bg-slate-800/50 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/50">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Kode</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Peristiwa Risiko</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Kategori</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase">K</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase">D</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Besaran</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Prioritas</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-400 uppercase">Status</th>
                    <th class="px-4 py-3 w-16"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @forelse($risikos as $r)
                    @php
                        $besaranColor = match(true) {
                            !$r->besaran_risiko                 => 'bg-slate-700 text-slate-400',
                            $r->besaran_risiko <= 4             => 'bg-emerald-500/20 text-emerald-400',
                            $r->besaran_risiko <= 9             => 'bg-amber-500/20 text-amber-400',
                            $r->besaran_risiko <= 16            => 'bg-orange-500/20 text-orange-400',
                            default                             => 'bg-red-500/20 text-red-400',
                        };
                        $statusColors = [
                            'draft' => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                            'submitted' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                            'approved' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'rejected' => 'bg-red-500/10 text-red-400 border-red-500/20',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-700/20 transition-colors {{ $r->status === 'rejected' ? 'border-l-2 border-l-red-500' : '' }}">
                        <td class="px-4 py-3 text-white font-mono font-semibold">{{ $r->kode_risiko }}</td>
                        <td class="px-4 py-3 text-slate-300 max-w-xs">
                            <div class="truncate">{{ $r->peristiwa_risiko }}</div>
                            @if($r->status === 'rejected' && $r->catatan_penolakan)
                                <div class="mt-1 text-xs text-red-400 bg-red-950/40 border border-red-500/30 rounded p-1.5 flex items-start gap-1">
                                    <svg class="w-3.5 h-3.5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Catatan Admin: {{ $r->catatan_penolakan }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-xs">{{ $r->kategoriRisiko?->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $r->level_kemungkinan ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $r->level_dampak ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold {{ $besaranColor }}">
                                {{ $r->besaran_risiko ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-slate-300">{{ $r->prioritas_risiko ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $statusColors[$r->status] ?? '' }}">
                                {{ ucfirst($r->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('risiko.form', [$konteks, $r]) }}" class="text-emerald-400 hover:text-emerald-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-slate-500 text-sm">Belum ada risiko terdaftar</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
