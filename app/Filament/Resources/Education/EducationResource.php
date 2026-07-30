<?php

namespace App\Filament\Resources\Education;

use App\Filament\Resources\Education\Pages\CreateEducation;
use App\Filament\Resources\Education\Pages\EditEducation;
use App\Filament\Resources\Education\Pages\ListEducation;
use App\Filament\Resources\Education\RelationManagers\ImagesRelationManager;
use App\Models\Education;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class EducationResource extends Resource
{
    protected static ?string $model = Education::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Educación';

    protected static UnitEnum|string|null $navigationGroup = 'Portafolio';

    protected static ?string $recordTitleAttribute = 'institution';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('institution')
                    ->label('Institución')
                    ->required()
                    ->maxLength(255),
                TextInput::make('degree')
                    ->label('Grado')
                    ->required()
                    ->maxLength(255),
                TextInput::make('field')
                    ->label('Campo')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4),
                DatePicker::make('start_date')
                    ->label('Fecha de inicio')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Fecha de finalización'),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
                SpatieMediaLibraryFileUpload::make('certificates')
                    ->label('Certificados')
                    ->collection('certificates')
                    ->multiple()
                    ->imageEditor()
                    ->reorderable()
                    ->maxFiles(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('institution')
            ->columns([
                TextColumn::make('institution')
                    ->label('Institución')
                    ->searchable(),
                TextColumn::make('degree')
                    ->label('Grado')
                    ->searchable(),
                TextColumn::make('field')
                    ->label('Campo'),
                TextColumn::make('start_date')
                    ->label('Fecha de inicio')
                    ->date(),
                TextColumn::make('end_date')
                    ->label('Fecha de finalización')
                    ->date(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric(),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                // Filters would go here
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEducation::route('/'),
            'create' => CreateEducation::route('/create'),
            'edit' => EditEducation::route('{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }
}
