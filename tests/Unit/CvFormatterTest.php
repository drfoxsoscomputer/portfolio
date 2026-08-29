<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use App\Support\CvFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CvFormatterTest extends TestCase
{
    use RefreshDatabase;

    public function test_date_formats_month_and_year_in_spanish(): void
    {
        $this->assertSame('Nov. 2023', CvFormatter::date(Carbon::parse('2023-11-15')));
        $this->assertSame('Ago. 2016', CvFormatter::date(Carbon::parse('2016-08-01')));
        $this->assertSame('Dic. 2023', CvFormatter::date(Carbon::parse('2023-12-31')));
    }

    public function test_date_returns_year_when_month_is_january(): void
    {
        $this->assertSame('2020', CvFormatter::date(Carbon::parse('2020-01-01')));
    }

    public function test_date_returns_null_when_date_is_missing(): void
    {
        $this->assertNull(CvFormatter::date(null));
    }

    public function test_date_range_formats_projects_with_months(): void
    {
        $range = CvFormatter::dateRange(
            Carbon::parse('2023-11-15'),
            Carbon::parse('2023-12-15'),
            isCurrent: false,
            yearOnly: false,
        );

        $this->assertSame('Nov. 2023 – Dic. 2023', $range);
    }

    public function test_date_range_uses_presente_for_current_entries(): void
    {
        $range = CvFormatter::dateRange(
            Carbon::parse('2020-01-01'),
            null,
            isCurrent: true,
            yearOnly: false,
        );

        $this->assertSame('2020 – Presente', $range);
    }

    public function test_date_range_supports_year_only_for_education(): void
    {
        $range = CvFormatter::dateRange(
            Carbon::parse('2002-01-01'),
            Carbon::parse('2005-12-31'),
            isCurrent: false,
            yearOnly: true,
        );

        $this->assertSame('2002 – 2005', $range);
    }

    public function test_team_label_detects_team_size(): void
    {
        $this->assertSame('Equipo de 7 desarrolladores', CvFormatter::teamLabel(7));
        $this->assertSame('Desarrollo individual', CvFormatter::teamLabel(1));
        $this->assertSame('Desarrollo individual', CvFormatter::teamLabel(null));
    }

    public function test_clean_url_strips_scheme_and_trailing_slash(): void
    {
        $this->assertSame(
            'github.com/drfoxsoscomputer/gameworld',
            CvFormatter::cleanUrl('https://github.com/drfoxsoscomputer/gameworld/'),
        );
        $this->assertNull(CvFormatter::cleanUrl(null));
    }

    public function test_skill_category_labels_map_database_categories(): void
    {
        $labels = CvFormatter::skillCategoryLabels();

        $this->assertSame('Lenguajes', $labels['Lenguajes']);
        $this->assertSame('Frameworks y librerías', $labels['Frameworks']);
        $this->assertSame('Base de datos', $labels['DB']);
        $this->assertSame('Herramientas', $labels['Tools']);
        $this->assertSame('Metodologías', $labels['Metodologías']);
    }

    public function test_language_level_labels_are_lowercase_spanish(): void
    {
        $this->assertSame('nativo', CvFormatter::languageLevel('Native'));
        $this->assertSame('nativo', CvFormatter::languageLevel('Nativo'));
        $this->assertSame('fluido', CvFormatter::languageLevel('Fluent'));
        $this->assertSame('intermedio', CvFormatter::languageLevel('Intermediate'));
    }

    public function test_language_label_adds_technical_reading_note_for_basic(): void
    {
        $this->assertSame('básico (lectura técnica)', CvFormatter::languageLabel('Básico'));
        $this->assertSame('básico (lectura técnica)', CvFormatter::languageLabel('Basic'));
        $this->assertSame('nativo', CvFormatter::languageLabel('Native'));
    }

    public function test_extract_hours_finds_duration_in_description(): void
    {
        $this->assertSame(
            '800 horas',
            CvFormatter::extractHours('Bootcamp intensivo de 800 horas en desarrollo full stack'),
        );
        $this->assertNull(CvFormatter::extractHours('Curso de Laravel avanzado'));
        $this->assertNull(CvFormatter::extractHours(null));
    }

    public function test_last_updated_considers_newest_related_record(): void
    {
        $course = Course::factory()->create([
            'user_id' => User::factory()->create()->id,
            'updated_at' => Carbon::now()->addMinutes(30)->startOfMinute(),
        ]);

        $touched = $course->fresh()->updated_at;

        $lastUpdated = CvFormatter::lastUpdated($course->user);

        $this->assertNotNull($lastUpdated);
        $this->assertTrue($lastUpdated->equalTo($touched));
        $this->assertTrue($lastUpdated->gte($course->user->updated_at));
    }

    public function test_verbose_date_uses_full_spanish_month(): void
    {
        $this->assertSame('Diciembre 2025', CvFormatter::verboseDate(Carbon::parse('2025-12-01')));
        $this->assertSame('Agosto 2026', CvFormatter::verboseDate(Carbon::parse('2026-08-15')));
    }
}
