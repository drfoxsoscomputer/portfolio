<?php

namespace App\Livewire\Portfolio;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class ExperienceSection extends Component
{
    public User $user;

    public function render(): View
    {
        return view('livewire.portfolio.experience-section');
    }
}
