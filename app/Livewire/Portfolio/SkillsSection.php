<?php

namespace App\Livewire\Portfolio;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class SkillsSection extends Component
{
    public User $user;

    public function render(): View
    {
        return view('livewire.portfolio.skills-section', [
            'groupedSkills' => $this->user->skills->groupBy('category'),
        ]);
    }
}
