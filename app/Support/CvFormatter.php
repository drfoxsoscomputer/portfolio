<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Helpers de formato para el CV descargable, alineados con el formato
 * convencional del PDF guía y optimizados para compatibilidad ATS.
 */
final class CvFormatter
{
    /** @var array<int, string> Abreviaturas de mes en español. */
    private const SHORT_MONTHS = [
        1 => 'Ene.',
        2 => 'Feb.',
        3 => 'Mar.',
        4 => 'Abr.',
        5 => 'May.',
        6 => 'Jun.',
        7 => 'Jul.',
        8 => 'Ago.',
        9 => 'Sep.',
        10 => 'Oct.',
        11 => 'Nov.',
        12 => 'Dic.',
    ];

    /** @var array<int, string> Nombres completos de mes en español. */
    private const FULL_MONTHS = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre',
    ];

    /** @var array<string, string> Etiquetas de nivel de idioma en español. */
    private const LANGUAGE_LEVELS = [
        'native' => 'nativo',
        'nativo' => 'nativo',
        'fluent' => 'fluido',
        'fluido' => 'fluido',
        'advanced' => 'avanzado',
        'avanzado' => 'avanzado',
        'intermediate' => 'intermedio',
        'intermedio' => 'intermedio',
        'basic' => 'básico',
        'basico' => 'básico',
        'básico' => 'básico',
    ];

    /**
     * Formatea una fecha como mes abreviado en español y año.
     * Cuando el mes es enero, se devuelve solo el año (convención del guía).
     */
    public static function date(?Carbon $date): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($date->month === 1) {
            return (string) $date->year;
        }

        return self::SHORT_MONTHS[$date->month].' '.$date->year;
    }

    /**
     * Formatea un rango de fechas: "Nov. 2023 – Dic. 2023", "2020 – Presente".
     * En modo año (educación): "2002 – 2005".
     */
    public static function dateRange(
        ?Carbon $start,
        ?Carbon $end,
        bool $isCurrent = false,
        bool $yearOnly = false,
    ): string {
        $format = static fn (?Carbon $date): ?string => $yearOnly
            ? ($date?->year !== null ? (string) $date->year : null)
            : self::date($date);

        $startLabel = $format($start) ?? '¿Fecha inicial?';
        $endLabel = $isCurrent ? 'Presente' : ($format($end) ?? 'Actual');

        return $startLabel.' – '.$endLabel;
    }

    /**
     * Etiqueta de equipo: "Equipo de 7 desarrolladores" o "Desarrollo individual".
     */
    public static function teamLabel(?int $teamSize): string
    {
        if ($teamSize !== null && $teamSize > 1) {
            return sprintf('Equipo de %d desarrolladores', $teamSize);
        }

        return 'Desarrollo individual';
    }

    /**
     * Deja una URL legible: sin esquema y sin barra final.
     */
    public static function cleanUrl(?string $url): ?string
    {
        if ($url === null || $url === '') {
            return null;
        }

        return rtrim((string) preg_replace('#^https?://#i', '', $url), '/');
    }

    /**
     * Devuelve el texto de categoría del CV para cada categoría de skills.
     */
    public static function skillCategoryLabels(): array
    {
        return [
            'Lenguajes' => 'Lenguajes',
            'Frameworks' => 'Frameworks y librerías',
            'DB' => 'Base de datos',
            'Tools' => 'Herramientas',
            'Metodologías' => 'Metodologías',
        ];
    }

    /**
     * Nivel de idioma en minúsculas y español.
     */
    public static function languageLevel(?string $level): string
    {
        $key = mb_strtolower((string) $level);

        return self::LANGUAGE_LEVELS[$key] ?? (string) $level;
    }

    /**
     * Etiqueta completa del nivel de idioma. El nivel básico suma la nota
     * de lectura técnica, tal como figura en el PDF guía.
     */
    public static function languageLabel(?string $level): string
    {
        $label = self::languageLevel($level);

        if ($label === 'básico') {
            $label .= ' (lectura técnica)';
        }

        return $label;
    }

    /**
     * Extrae las horas de una descripción educativa: "· 800 horas".
     */
    public static function extractHours(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        if (preg_match('/\b(\d[\d.\s]*\s+horas?)\b/i', $description, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Fecha de actualización visible del CV: mes en español y año.
     */
    public static function verboseDate(?Carbon $date): ?string
    {
        if ($date === null) {
            return null;
        }

        return self::FULL_MONTHS[$date->month].' '.$date->year;
    }

    /**
     * Fecha de la última modificación del contenido del perfil.
     * Considera user, links, proyectos, experiencias, skills, educación,
     * idiomas y cursos.
     */
    public static function lastUpdated(User $user): ?Carbon
    {
        $dates = collect([$user->updated_at]);

        foreach (['links', 'projects', 'experiences', 'skills', 'educations', 'languages', 'courses'] as $relation) {
            foreach ($user->{$relation} ?? [] as $model) {
                $dates->push($model->updated_at);
            }
        }

        return $dates->filter()->max();
    }
}
