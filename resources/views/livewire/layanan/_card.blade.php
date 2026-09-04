@php
    $statusColors = [
        'berjalan'     => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
        'direncanakan' => 'bg-blue-500/15 text-blue-400 border-blue-500/25',
        'dihentikan'   => 'bg-red-500/15 text-red-400 border-red-500/25',
    ];
    $statusLabels = [
        'berjalan' => 'Berjalan', 'direncanakan' => 'Direncanakan', 'dihentikan' => 'Dihentikan',
    ];
    $progress        = $layanan->progress ?? ['selesai' => 0, 'total' => 2];
    $progressPercent = $progress['total'] > 0 ? round(($progress['selesai'] / $progress['total']) * 100) : 0;
    $progressColor   = $progressPercent >= 100 ? 'bg-emerald-500' : ($progressPercent > 0 ? 'bg-amber-500' : 'bg-slate-600');
    $progressTextCol = $progressPercent >= 100 ? 'text-emerald-400' : ($progressPercent > 0 ? 'text-amber-400' : 'text-slate-500');
    $cardBorder      = $isPrioritas ? 'border-amber-500/30 hover:border-amber-500/60' : 'border-slate-700/50 hover:border-slate-600';
    $cardBg          = $isPrioritas ? 'bg-slate-800/80' : 'bg-slate-800/50';
@endphp

<div class="rounded-2xl border {{ $cardBorder }} {{ $cardBg }} p-5 flex flex-col transition-all duration-200 hover:shadow-xl hover:shadow-black/20 hover:-translate-y-0.5">

    {{-- Top row: status badges --}}
    <div class="flex items-start justify-between mb-3">
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border
                {{ $statusColors[$layanan->status_layanan] ?? 'bg-slate-700 text-slate-400 border-slate-600' }}">
                {{ $statusLabels[$layanan->status_layanan] ?? ucfirst($layanan->status_layanan) }}
            </span>
            @if($layanan->target_pengguna)
                <span class="text-[11px] text-slate-500 font-medium">{{ $layanan->target_pengguna }}</span>
            @endif
        </div>
        @if($layanan->mrKonteks && $layanan->mrKonteks->status === 'submitted')
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 shrink-0 ml-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Terkirim
            </span>
        @endif
    </div>

    {{-- Nama Layanan --}}
    <h3 class="text-base font-bold text-white mb-1.5 line-clamp-2 leading-snug" title="{{ $layanan->nama_layanan }}">
        {{ $layanan->nama_layanan }}
    </h3>

    {{-- Deskripsi --}}
    <p class="text-sm text-slate-400 line-clamp-2 leading-relaxed flex-1">
        {{ $layanan->deskripsi_layanan ?: 'Belum ada deskripsi.' }}
    </p>

    {{-- Progress modul --}}
    <div class="mt-4 mb-1">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs text-slate-500">Progres Modul</span>
            <span class="text-xs font-semibold {{ $progressTextCol }}">
                {{ $progress['selesai'] }}/{{ $progress['total'] }} modul selesai
            </span>
        </div>
        <div class="h-1.5 rounded-full bg-slate-700/60 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $progressColor }}"
                 style="width: {{ $progressPercent }}%">
            </div>
        </div>
    </div>

    {{-- Divider & Actions --}}
    <div class="mt-4 pt-4 border-t border-slate-700/40 flex items-center gap-2">
        <a href="{{ route('layanan.dashboard', $layanan->id) }}"
           class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-xs font-semibold text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
            </svg>
            Buka Dashboard
        </a>
        <a href="{{ route('layanan.edit', $layanan->id) }}"
           class="inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 hover:text-white text-xs font-semibold transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>
</div>
