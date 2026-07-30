<?php

namespace App\Models;

use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo que representa un enlace o asociación con el usuario.
 *
 * El modelo Link almacena hipervínculos con metadatos como etiqueta, URL y orden
 * para el perfil del portafolio del usuario. Cada enlace pertenece a un solo usuario.
 */
#[Fillable(['user_id', 'label', 'url', 'icon', 'sort_order'])]
#[Hidden([])]
class Link extends Model
{
    use HasFactory;

    /**
     * Obtiene la clase factory del modelo.
     */
    protected string $factory = LinkFactory::class;

    /**
     * Obtiene la tabla asociada al modelo.
     */
    protected $table = 'links';

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
     * Obtiene el ordenamiento por defecto del modelo.
     */
    protected array $order = ['sort_order' => 'asc'];

    /**
     * Sobrescribe la consulta base para aplicar ordenamiento por defecto.
     */
    function newQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::newQuery()->orderBy('sort_order', 'asc');
    }

    /**
     * Define la relación con el modelo User.
     * Un enlace pertenece a un solo usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}