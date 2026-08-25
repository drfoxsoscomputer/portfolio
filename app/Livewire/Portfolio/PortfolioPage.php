<?php

namespace App\Livewire\Portfolio;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portfolio')]
class PortfolioPage extends Component
{
    public User $user;

    /**
     * Load the portfolio owner with every relation the sections need.
     */
    public function mount(): void
    {
        $this->user = User::with([
            'links',
            'projects',
            'experiences',
            'skills',
            'educations',
            'languages',
            'courses',
            'media',
        ])->first() ?? new User;
    }

    public function render(): View
    {
        return view('livewire.portfolio.portfolio-page')
            ->title($this->user->name !== null ? $this->user->name.' · Portfolio' : config('app.name'));
    }
}
