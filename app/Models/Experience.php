<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Modelo que representa entradas de experiencia laboral.
 *
 * El modelo Experience almacena información detallada del historial laboral
 * incluyendo empresa, rol, descripción, ubicación y duración del empleo.
 * Cada experiencia pertenece a un solo usuario.
 */
#[Fillable(['user_id', 'company', 'role', 'description', 'location', 'start_date', 'end_date', 'is_current'])]
#[Hidden([])]
class Experience extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * Obtiene la clase factory del modelo.
     */
    protected string $factory = ExperienceFactory::class;

    /**
     * Obtiene la tabla asociada al modelo.
     */
    protected $table = 'experiences';

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
     * Sobrescribe la consulta base para aplicar ordenamiento por start_date (descendente).
     */
    public function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('start_date', 'desc');
    }

    /**
     * Define la relación con el modelo User.
     * Una experiencia pertenece a un solo usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(50)
                    ->height(50);
            });
    }
}
