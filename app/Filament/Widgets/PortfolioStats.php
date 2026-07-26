<?php

namespace App\Filament\Widgets;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioStats extends BaseWidget
{
    protected function getStats(): array
    {
        $profileExists = Profile::exists();

        return [
            Stat::make('Proyectos', Project::count())
                ->description('Total de proyectos registrados')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Experiencia', Experience::count())
                ->description('Posiciones laborales')
                ->icon('heroicon-o-clock'),

            Stat::make('Habilidades', Skill::count())
                ->description('Habilidades técnicas')
                ->icon('heroicon-o-code-bracket'),

            Stat::make('Educación', Education::count())
                ->description('Entradas educativas')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Idiomas', Language::count())
                ->description('Idiomas registrados')
                ->icon('heroicon-o-language'),

            Stat::make('Perfil', $profileExists ? 'Completado' : 'Pendiente')
                ->description($profileExists ? 'Perfil configurado' : 'Crea tu perfil para empezar')
                ->icon($profileExists ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                ->color($profileExists ? 'success' : 'warning'),
        ];
    }
}
