<?php

namespace App\Filament\Resources\Links;

use App\Filament\Resources\Links\Pages\ManageLinks;
use App\Models\Link;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedLink;

    protected static ?string $navigationLabel = 'Enlaces de Contacto';

    protected static UnitEnum|string|null $navigationGroup = 'Perfil';

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Etiqueta')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->required()
                    ->maxLength(255),
                Select::make('icon')
                    ->label('Plataforma')
                    ->options([
                        'github' => 'GitHub',
                        'linkedin' => 'LinkedIn',
                        'twitter' => 'Twitter / X',
                        'instagram' => 'Instagram',
                        'youtube' => 'YouTube',
                        'globe' => 'Web Personal',
                        'other' => 'Otro',
                    ])
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')
                    ->label('Etiqueta')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('icon')
                    ->label('Plataforma')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'github' => 'heroicon-o-code-bracket',
                        'linkedin' => 'heroicon-o-briefcase',
                        'twitter' => 'heroicon-o-chat-bubble-left-right',
                        'instagram' => 'heroicon-o-camera',
                        'youtube' => 'heroicon-o-video-camera',
                        'globe' => 'heroicon-o-globe-alt',
                        default => 'heroicon-o-link',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'github' => 'GitHub',
                        'linkedin' => 'LinkedIn',
                        'twitter' => 'Twitter / X',
                        'instagram' => 'Instagram',
                        'youtube' => 'YouTube',
                        'globe' => 'Web Personal',
                        default => 'Otro',
                    }),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
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
            'index' => ManageLinks::route('/'),
        ];
    }
}
