<?php

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use App\Models\Profile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageExperiences extends ManageRecords
{
    protected static string $resource = ExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva experiencia'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['profile_id'] = Profile::first()?->id;

        return $data;
    }
}
