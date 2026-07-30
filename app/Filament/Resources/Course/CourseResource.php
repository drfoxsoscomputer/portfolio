<?php

namespace App\Filament\Resources\Course;

use App\Filament\Resources\Course\Pages\CreateCourse;
use App\Filament\Resources\Course\Pages\EditCourse;
use App\Filament\Resources\Course\Pages\ListCourses;
use App\Models\Course;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Cursos';

    protected static UnitEnum|string|null $navigationGroup = 'Perfil';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'courses';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('institution')
                    ->label('Institución')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('date')
                    ->label('Fecha')
                    ->required(),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4),
                TextInput::make('url_certificate')
                    ->label('URL del certificado')
                    ->url(),
                TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),
                SpatieMediaLibraryFileUpload::make('certificates')
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
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('institution')
                    ->label('Institución')
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order', 'asc')
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
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'edit' => EditCourse::route('{record}/edit'),
        ];
    }
}