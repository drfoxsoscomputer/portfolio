<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\RelationManagers\ImagesRelationManager;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $navigationLabel = 'Proyectos';

    protected static UnitEnum|string|null $navigationGroup = 'Portafolio';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4),
                TagsInput::make('tech_stack')
                    ->label('Tecnologías'),
                TextInput::make('role')
                    ->label('Rol')
                    ->maxLength(255),
                TextInput::make('team_size')
                    ->label('Tamaño del equipo')
                    ->numeric(),
                TextInput::make('url')
                    ->label('URL del proyecto')
                    ->url()
                    ->maxLength(255),
                TextInput::make('repo_url')
                    ->label('URL del repositorio')
                    ->url()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Fecha de inicio')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Fecha de fin'),
                Toggle::make('is_current')
                    ->label('Proyecto actual')
                    ->default(false),
                Toggle::make('is_featured')
                    ->label('Destacado')
                    ->default(false),
                SpatieMediaLibraryFileUpload::make('screenshots')
                    ->label('Capturas')
                    ->collection('screenshots')
                    ->multiple()
                    ->imageEditor()
                    ->reorderable()
                    ->maxFiles(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->searchable(),
                TextColumn::make('tech_stack')
                    ->label('Tecnologías')
                    ->limit(30),
                TextColumn::make('start_date')
                    ->label('Fecha de inicio')
                    ->date(),
                ToggleColumn::make('is_current')
                    ->label('Actual')
                    ->onIcon('heroicon-s-check-circle')
                    ->offIcon('heroicon-s-x-circle'),
                ToggleColumn::make('is_featured')
                    ->label('Destacado')
                    ->onIcon('heroicon-s-star')
                    ->offIcon('heroicon-s-x-circle'),
            ])
            ->defaultSort('start_date', 'desc')
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }
}
