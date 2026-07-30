<?php

namespace App\Models;

use Database\Factories\EducationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Modelo que representa entradas de formación académica.
 *
 * El modelo Education almacena información educativa incluyendo
 * institución, título, campo de estudio y duración. Cada formación pertenece
 * a un solo usuario y puede tener imágenes asociadas.
 */
#[Fillable(['user_id', 'institution', 'degree', 'field', 'description', 'start_date', 'end_date', 'is_current', 'sort_order'])]
#[Hidden([])]
class Education extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Obtiene la clase factory del modelo.
     */
    protected string $factory = EducationFactory::class;

    /**
     * Obtiene la tabla asociada al modelo.
     */
    protected $table = 'education';

    /**
     * Obtiene la clave primaria del modelo.
     */
    protected $primaryKey = 'id';

    /**
     * Indica si el ID del modelo es autoincremental.
     */
    public $incrementing = true;

    /**
     * Número de modelos a devolver por consulta.
     */
    protected $perPage = 15;

    /**
     * Sobrescribe la consulta base para aplicar ordenamiento por sort_order (ascendente).
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('sort_order', 'asc');
    }

    /**
     * Define la relación con el modelo User.
     * Una formación pertenece a un solo usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define la relación con el modelo Image.
     * Una formación puede tener múltiples imágenes mediante relación polimórfica.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Registra las colecciones de medios para Spatie Media Library.
     * Define la colección 'certificates' para almacenar certificados educativos.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('certificates')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->hasResponsiveImages();
    }
}
