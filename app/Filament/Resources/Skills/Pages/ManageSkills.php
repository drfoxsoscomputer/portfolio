<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use App\Models\Profile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSkills extends ManageRecords
{
    protected static string $resource = SkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva habilidad'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['profile_id'] = Profile::first()?->id;

        return $data;
    }
}
