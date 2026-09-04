<div class="py-8 sm:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-slate-400 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('layanan.index') }}" class="hover:text-white transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Daftar Layanan
            </a>
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white font-medium truncate max-w-xs">{{ $layanan->nama_layanan }}</span>
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-300">Dashboard Modul</span>
        </nav>

        {{-- Flash success --}}
        @if (session('success'))
            <div class="mb-6 px-4 py-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Dashboard Modul Manajemen</h1>
                    <p class="text-slate-400 mt-1">Layanan: <span class="text-white font-semibold">{{ $layanan->nama_layanan }}</span></p>
                </div>
                <a href="{{ route('layanan.edit', $layanan->id) }}"
                   class="shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border border-slate-700 text-slate-300 hover:bg-slate-800 hover:text-white text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Profil Layanan
                </a>
            </div>

            {{-- Info layanan --}}
            <div class="mt-4 flex flex-wrap items-center gap-3">
                @php
                    $statusColors = [
                        'berjalan'     => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'direncanakan' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        'dihentikan'   => 'bg-red-500/10 text-red-400 border-red-500/20',
                    ];
                    $statusLabels = [
                        'berjalan' => 'Berjalan', 'direncanakan' => 'Direncanakan', 'dihentikan' => 'Dihentikan',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $statusColors[$layanan->status_layanan] ?? 'bg-slate-700 text-slate-400' }}">
                    {{ $statusLabels[$layanan->status_layanan] ?? $layanan->status_layanan }}
                </span>
                @if($layanan->is_prioritas)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        ⭐ Layanan Prioritas
                    </span>
                @endif
                <span class="text-xs text-slate-500">{{ $layanan->target_pengguna ?? '-' }}</span>
            </div>
        </div>

        {{-- Banner: Semua Selesai → Submit --}}
        @if(auth()->user()->isOperator())
            @if($isSubmitted)
                <div class="mb-8 rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-emerald-300">Laporan Terkirim ✓</p>
                        <p class="text-sm text-emerald-400/80 mt-0.5">Data layanan ini telah dikirimkan. Anda masih bisa memperbarui data kapan saja.</p>
                    </div>
                </div>
            @elseif($allActiveModulesDone)
                <div class="mb-8 rounded-xl border border-blue-500/40 bg-blue-500/10 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-blue-300">Semua modul sudah diisi!</p>
                            <p class="text-sm text-blue-400/80 mt-0.5">Laporan layanan siap untuk dikirimkan.</p>
                        </div>
                    </div>
                    <button wire:click="openSubmitModal"
                        class="shrink-0 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-semibold text-white transition-all shadow-lg shadow-blue-500/25 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Laporan
                    </button>
                </div>
            @endif
        @endif

        {{-- Module Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($modules as $module)
                @if($module['active'])
                    @php
                        $moduleStatus = $module['status'];
                        $statusIcon = match($moduleStatus['status'] ?? 'empty') {
                            'done'        => 'check',
                            'in_progress' => 'clock',
                            default       => 'plus',
                        };
                        $statusBadgeClass = match($moduleStatus['status'] ?? 'empty') {
                            'done'        => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                            'in_progress' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                            default       => 'bg-slate-700/50 text-slate-400 border-slate-600/30',
                        };
                        $actionLabel = match($moduleStatus['status'] ?? 'empty') {
                            'done'        => 'Lihat / Edit',
                            'in_progress' => 'Lanjutkan',
                            default       => 'Mulai Pengisian',
                        };
                    @endphp

                    @if(isset($module['action']))
                        <button wire:click="{{ $module['action'] }}"
                           class="group relative rounded-2xl border {{ $module['border'] }} {{ $module['bg'] }} p-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl hover:{{ $module['shadow'] }} cursor-pointer text-left w-full flex flex-col">
                    @else
                        <a href="{{ $module['route'] }}"
                           class="group relative rounded-2xl border {{ $module['border'] }} {{ $module['bg'] }} p-6 transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl hover:{{ $module['shadow'] }} cursor-pointer flex flex-col">
                    @endif

                        {{-- Status badge --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $module['gradient'] }} flex items-center justify-center shadow-lg {{ $module['shadow'] }} group-hover:scale-110 transition-transform duration-300">
                                @include('partials.icons.' . $module['icon'], ['class' => 'w-6 h-6 text-white'])
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $statusBadgeClass }}">
                                @if($statusIcon === 'check')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif($statusIcon === 'clock')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                @endif
                                {{ $moduleStatus['label'] }}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-white mb-1">{{ $module['name'] }}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed flex-1">{{ $module['description'] }}</p>

                        {{-- Summary data (jika ada) --}}
                        @if(!empty($moduleStatus['summary']))
                            <p class="mt-2 text-xs {{ $module['text'] }} opacity-80">{{ $moduleStatus['summary'] }}</p>
                        @endif

                        {{-- CTA --}}
                        <div class="mt-5 flex items-center gap-1.5 {{ $module['text'] }} text-sm font-semibold">
                            <span>{{ $actionLabel }}</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>

                    @if(isset($module['action']))
                        </button>
                    @else
                        </a>
                    @endif

                @else
                    {{-- Coming soon --}}
                    <div class="relative rounded-2xl border border-slate-700/30 bg-slate-800/30 p-6 opacity-50 cursor-not-allowed flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-700/50 flex items-center justify-center">
                                @include('partials.icons.' . $module['icon'], ['class' => 'w-6 h-6 text-slate-500'])
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-700/50 text-slate-400 border border-slate-600/30">
                                Segera Hadir
                            </span>
                        </div>
                        <h3 class="text-base font-semibold text-slate-400 mb-1">{{ $module['name'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $module['description'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Submit Modal --}}
        @if($showSubmitModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$set('showSubmitModal', false)"></div>
                <div class="relative bg-slate-800 border border-slate-700/50 rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-blue-500/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Kirim Laporan Layanan</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $layanan->nama_layanan }}</p>
                        </div>
                    </div>

                    @if(count($validationErrors) > 0)
                        <div class="mb-5 rounded-xl bg-red-500/10 border border-red-500/20 p-4">
                            <h4 class="text-sm font-semibold text-red-400 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Data belum lengkap:
                            </h4>
                            <ul class="space-y-1">
                                @foreach($validationErrors as $error)
                                    <li class="flex items-start gap-2 text-xs text-red-300/90">
                                        <span class="mt-0.5 shrink-0">•</span>
                                        <span>{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="flex justify-end">
                            <button wire:click="$set('showSubmitModal', false)"
                                class="px-5 py-2.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-sm font-semibold text-white transition-colors cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    @else
                        <p class="text-sm text-slate-300 mb-6 leading-relaxed">
                            Anda akan mengirimkan laporan untuk layanan <strong class="text-white">{{ $layanan->nama_layanan }}</strong>.
                            <br><br>
                            <span class="text-slate-400 text-xs">Data tetap dapat diperbarui kapan saja setelah pengiriman.</span>
                        </p>
                        <div class="flex gap-3">
                            <button wire:click="$set('showSubmitModal', false)"
                                class="flex-1 px-5 py-2.5 rounded-lg border border-slate-600 text-sm font-medium text-slate-300 hover:bg-slate-700 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button wire:click="submitLayanan"
                                class="flex-1 px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-bold text-white transition-colors cursor-pointer">
                                Ya, Kirimkan
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- User bar --}}
        <div class="mt-10 text-center">
            <p class="text-xs text-slate-600">
                {{ auth()->user()->name }}
                @if(auth()->user()->dinas)
                    · {{ auth()->user()->dinas->nama_dinas }}
                @endif
            </p>
        </div>
    </div>
</div>


