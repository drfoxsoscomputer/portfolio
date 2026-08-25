<?php

namespace App\Livewire\Portfolio;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class StatsSection extends Component
{
    public User $user;

    /**
     * Calculate the total years of experience from the earliest experience start date.
     */
    public static function yearsOfExperience(Collection $experiences): int
    {
        $start = $experiences->pluck('start_date')->filter()->min();

        return $start === null ? 0 : abs((int) now()->startOfDay()->diffInYears($start));
    }

    public function render(): View
    {
        return view('livewire.portfolio.stats-section', [
            'years' => self::yearsOfExperience($this->user->experiences),
            'projectCount' => $this->user->projects->count(),
            'skillCount' => $this->user->skills->count(),
        ]);
    }
}
