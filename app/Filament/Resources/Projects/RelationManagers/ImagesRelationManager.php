<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\Image;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Imágenes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->maxLength(255),
                TextInput::make('alt_text')
                    ->label('Texto alternativo'),
                Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'screenshot' => 'Captura de pantalla',
                        'logo' => 'Logo',
                    ])
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('alt_text')
                    ->label('Texto alternativo'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'screenshot' => 'Captura',
                        'logo' => 'Logo',
                        default => $state,
                    }),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
