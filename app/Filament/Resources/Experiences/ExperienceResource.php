<?php

namespace App\Filament\Resources\Experiences;

use App\Filament\Resources\Experiences\Pages\ManageExperiences;
use App\Models\Experience;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Experiencia';

    protected static UnitEnum|string|null $navigationGroup = 'Portafolio';

    protected static ?string $recordTitleAttribute = 'role';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company')
                    ->label('Empresa')
                    ->required()
                    ->maxLength(255),
                TextInput::make('role')
                    ->label('Rol')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4),
                TextInput::make('location')
                    ->label('Ubicación')
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Fecha de inicio')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Fecha de fin'),
                Toggle::make('is_current')
                    ->label('Experiencia actual')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('role')
            ->columns([
                TextColumn::make('company')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->searchable(),
                TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Fecha de inicio')
                    ->date(),
                TextColumn::make('end_date')
                    ->label('Fecha de fin')
                    ->date(),
                ToggleColumn::make('is_current')
                    ->label('Actual')
                    ->onIcon('heroicon-s-check-circle')
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
            'index' => ManageExperiences::route('/'),
        ];
    }
}
