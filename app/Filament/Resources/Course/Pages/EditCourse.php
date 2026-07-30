<?php

namespace App\Filament\Resources\Course\Pages;

use App\Filament\Resources\Course\CourseResource;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}