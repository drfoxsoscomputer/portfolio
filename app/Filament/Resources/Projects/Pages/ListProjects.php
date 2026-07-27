<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Profile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo proyecto')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['profile_id'] = Profile::first()?->id;

                    return $data;
                }),
        ];
    }
}
