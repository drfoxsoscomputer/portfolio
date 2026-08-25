<?php

namespace App\Filament\Resources\ContactRequests;

use App\Filament\Resources\ContactRequests\Pages\ManageContactRequests;
use App\Models\ContactRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ContactRequestResource extends Resource
{
    protected static ?string $model = ContactRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?string $navigationLabel = 'Mensajes';

    protected static UnitEnum|string|null $navigationGroup = 'Portafolio';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Contact requests are only created by visitors through the public form.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nombre'),
                TextEntry::make('email')
                    ->label('Correo electrónico')
                    ->copyable(),
                TextEntry::make('message')
                    ->label('Mensaje')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('Mensaje')
                    ->limit(60),
                TextColumn::make('read_at')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state !== null ? 'Leído' : 'Nuevo')
                    ->color(fn (?string $state): string => $state !== null ? 'gray' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Recibido')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'unread' => 'Sin leer',
                        'read' => 'Leídos',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'unread' => $query->whereNull('read_at'),
                        'read' => $query->whereNotNull('read_at'),
                        default => $query,
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
                Action::make('markAsRead')
                    ->label('Marcar como leído')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->requiresConfirmation()
                    ->visible(fn (ContactRequest $record): bool => $record->read_at === null)
                    ->action(fn (ContactRequest $record) => $record->markAsRead()),
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

    public static function getNavigationBadge(): ?string
    {
        $unreadCount = ContactRequest::unread()->count();

        return $unreadCount > 0 ? (string) $unreadCount : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactRequests::route('/'),
        ];
    }
}
