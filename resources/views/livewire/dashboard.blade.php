<div class="py-12 sm:py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Sistem Pemerintahan Berbasis Elektronik</h1>
            <p class="text-slate-400 text-lg">Pilih modul yang ingin Anda kelola</p>
        </div>

        {{-- Module grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($modules as $index => $module)
                @if($module['active'])
                    {{-- Active module card --}}
                    <a href="{{ $module['route'] }}"
                       class="group relative rounded-2xl border {{ $module['border'] }} {{ $module['bg'] }} p-6 transition-all duration-300 hover:scale-[1.03] hover:shadow-2xl hover:{{ $module['shadow'] }} cursor-pointer {{ $index === 0 ? 'sm:col-span-2 lg:col-span-1' : '' }}">

                        {{-- Badge --}}
                        @if($index === 0 && $badgeCount > 0)
                            <div class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center shadow-lg shadow-red-500/30 animate-pulse">
                                {{ $badgeCount }}
                            </div>
                        @endif

                        {{-- Icon --}}
                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $module['gradient'] }} flex items-center justify-center shadow-lg {{ $module['shadow'] }} mb-4 group-hover:scale-110 transition-transform duration-300">
                            @include('partials.icons.' . $module['icon'], ['class' => 'w-7 h-7 text-white'])
                        </div>

                        <h3 class="text-lg font-semibold text-white mb-1">{{ $module['name'] }}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">{{ $module['description'] }}</p>

                        {{-- Arrow indicator --}}
                        <div class="mt-4 flex items-center gap-1 {{ $module['text'] }} text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <span>Buka Modul</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </a>
                @else
                    {{-- Coming soon card --}}
                    <div class="relative rounded-2xl border border-slate-700/30 bg-slate-800/30 p-6 opacity-50 cursor-not-allowed">
                        {{-- Coming soon badge --}}
                        <div class="absolute top-4 right-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700/50 text-slate-400 border border-slate-600/30">
                                Segera Hadir
                            </span>
                        </div>

                        {{-- Icon --}}
                        <div class="w-14 h-14 rounded-xl bg-slate-700/50 flex items-center justify-center mb-4">
                            @include('partials.icons.' . $module['icon'], ['class' => 'w-7 h-7 text-slate-500'])
                        </div>

                        <h3 class="text-lg font-semibold text-slate-400 mb-1">{{ $module['name'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $module['description'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- User summary bar --}}
        <div class="mt-10 text-center">
            <p class="text-sm text-slate-500">
                Masuk sebagai <span class="text-slate-300 font-medium">{{ auth()->user()->name }}</span>
                <span class="text-slate-600 mx-1">•</span>
                <span class="text-slate-400">{{ auth()->user()->role->label }}</span>
                @if(auth()->user()->desa)
                    <span class="text-slate-600 mx-1">•</span>
                    <span class="text-slate-400">{{ auth()->user()->desa->nama_desa }}</span>
                @endif
            </p>
        </div>
    </div>
</div>
