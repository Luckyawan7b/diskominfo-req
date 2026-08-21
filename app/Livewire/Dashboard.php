<?php

namespace App\Livewire;

use App\Models\MrKonteks;
use App\Models\MrRisiko;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.hub')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Badge counts for Manajemen Risiko card
        $badgeCount = 0;

        if ($user->isAdmin()) {
            // Admin: count konteks pending review
            $badgeCount = MrKonteks::where('status', 'submitted')->count();
        } elseif ($user->isOperator() && $user->desa_id) {
            // Operator: count rejected risks in their active konteks
            $badgeCount = MrRisiko::whereHas('konteks', function ($q) use ($user) {
                $q->where('desa_id', $user->desa_id)
                  ->where('status', 'rejected');
            })->where('status', 'rejected')->count();
        }

        return view('livewire.dashboard', [
            'badgeCount' => $badgeCount,
            'modules' => $this->getModules(),
        ]);
    }

    private function getModules(): array
    {
        return [
            [
                'name'        => 'Manajemen Risiko',
                'description' => 'Identifikasi, analisis, dan penanganan risiko SPBE',
                'icon'        => 'shield-check',
                'route'       => route('konteks.index'),
                'active'      => true,
                'gradient'    => 'from-emerald-500 to-teal-600',
                'shadow'      => 'shadow-emerald-500/25',
                'bg'          => 'bg-emerald-500/10',
                'text'        => 'text-emerald-400',
                'border'      => 'border-emerald-500/20',
            ],
            [
                'name'        => 'Manajemen Pengetahuan',
                'description' => 'Pengelolaan dan berbagi pengetahuan organisasi',
                'icon'        => 'book-open',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-blue-500 to-indigo-600',
                'shadow'      => 'shadow-blue-500/25',
                'bg'          => 'bg-blue-500/10',
                'text'        => 'text-blue-400',
                'border'      => 'border-blue-500/20',
            ],
            [
                'name'        => 'Manajemen Perubahan',
                'description' => 'Perencanaan dan pelaksanaan perubahan organisasi',
                'icon'        => 'arrows-right-left',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-amber-500 to-orange-600',
                'shadow'      => 'shadow-amber-500/25',
                'bg'          => 'bg-amber-500/10',
                'text'        => 'text-amber-400',
                'border'      => 'border-amber-500/20',
            ],
            [
                'name'        => 'Manajemen Keberlangsungan',
                'description' => 'Jaminan kelangsungan layanan dan operasional',
                'icon'        => 'arrow-path',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-violet-500 to-purple-600',
                'shadow'      => 'shadow-violet-500/25',
                'bg'          => 'bg-violet-500/10',
                'text'        => 'text-violet-400',
                'border'      => 'border-violet-500/20',
            ],
            [
                'name'        => 'Manajemen Relasi',
                'description' => 'Pengelolaan hubungan dengan pemangku kepentingan',
                'icon'        => 'users',
                'route'       => null,
                'active'      => false,
                'gradient'    => 'from-rose-500 to-pink-600',
                'shadow'      => 'shadow-rose-500/25',
                'bg'          => 'bg-rose-500/10',
                'text'        => 'text-rose-400',
                'border'      => 'border-rose-500/20',
            ],
        ];
    }
}
