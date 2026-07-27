<?php

namespace App\Filament\Pages;

use App\Models\Profile;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

class ManageProfile extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static string | \UnitEnum | null $navigationGroup = 'Perfil';

    protected static ?string $slug = 'profile';

    public ?array $data = [];

    public function mount(): void
    {
        $profile = Profile::first();

        if ($profile) {
            $this->form->fill($profile->toArray());
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->fullWidth(false)
                    ->sticky(false)
                    ->key('form-actions'),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(Profile::class)
            ->operation('edit')
            ->statePath('data')
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('title')
                    ->label('Título profesional')
                    ->required()
                    ->maxLength(255),
                Textarea::make('summary')
                    ->label('Resumen profesional')
                    ->rows(4),
                TextInput::make('location')
                    ->label('Ubicación')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->required()
                    ->email()
                    ->maxLength(255),
                TextInput::make('avatar')
                    ->label('URL del avatar')
                    ->maxLength(255),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $profile = Profile::first();

        if ($profile) {
            $profile->update($data);
        } else {
            Profile::create($data);
        }

        Notification::make()
            ->title('Perfil guardado correctamente')
            ->success()
            ->send();
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar cambios')
                ->submit('save'),
        ];
    }
}
