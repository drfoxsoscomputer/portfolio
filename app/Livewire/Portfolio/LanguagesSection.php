<?php

namespace App\Livewire\Portfolio;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class LanguagesSection extends Component
{
    public User $user;

    /**
     * Map a language proficiency level to a percentage for the level bar.
     */
    public static function levelPercent(string $level): int
    {
        return match (mb_strtolower($level)) {
            'native', 'nativo' => 100,
            'fluent', 'fluido' => 90,
            'advanced', 'avanzado' => 75,
            'intermediate', 'intermedio' => 60,
            'beginner', 'basic', 'basico', 'básico' => 40,
            default => 50,
        };
    }

    public function render(): View
    {
        return view('livewire.portfolio.languages-section');
    }
}
