@props(['konteks', 'activeStep' => 'Konteks'])

<div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2">
    @php
        $steps = [
            ['label' => 'Konteks', 'route' => route('konteks.form', $konteks)],
            ['label' => 'Sasaran', 'route' => route('sasaran.form', $konteks)],
            ['label' => 'Struktur', 'route' => route('struktur.form', $konteks)],
            ['label' => 'Risiko', 'route' => route('risiko.index', $konteks)],
            ['label' => 'Peta Risiko', 'route' => route('risiko.peta', $konteks)],
        ];
    @endphp
    @foreach($steps as $i => $step)
        @php
            $isActive = $step['label'] === $activeStep;
        @endphp
        @if($i > 0)
            <div class="w-8 h-px bg-slate-700 shrink-0"></div>
        @endif
        <a href="{{ $step['route'] }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors {{ $isActive ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-slate-200 border border-slate-700 hover:border-slate-600' }}">
            <span class="w-5 h-5 rounded-full {{ $isActive ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-400' }} flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
            {{ $step['label'] }}
        </a>
    @endforeach
</div>
