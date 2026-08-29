<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $user->name }} - Curriculum</title>
    <style>
        @page {
            size: letter;
            margin: 1in;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5px;
            color: #000;
            line-height: 1.4;
        }

        header {
            text-align: center;
            margin-bottom: 14px;
        }

        .name {
            font-size: 21px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 2px 0;
        }

        .role {
            font-size: 12.5px;
            color: #374151;
            margin: 0 0 5px 0;
        }

        .contact-line {
            font-size: 10px;
            color: #000;
            margin: 1px 0;
        }

        h2 {
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e40af;
            border-bottom: 1px solid #1e40af;
            padding-bottom: 2px;
            margin: 11px 0 5px 0;
        }

        .entry {
            margin-bottom: 6px;
        }

        .entry-title {
            font-weight: bold;
            margin: 0;
        }

        .entry-date {
            font-size: 10px;
            font-style: italic;
            color: #4b5563;
        }

        .entry-subtitle {
            font-size: 10px;
            font-style: italic;
            color: #4b5563;
            margin: 0;
        }

        ul.bullets {
            margin: 1px 0 0 0;
            padding-left: 0;
            list-style-type: disc;
            list-style-position: outside;
        }

        ul.bullets li {
            font-size: 11px;
            color: #000;
            margin-left: 16px;
        }

        .link-line {
            font-size: 10px;
            color: #1d4ed8;
            margin: 1px 0;
        }

        .skill-category {
            margin: 2px 0;
        }

        .last-updated {
            font-size: 9px;
            color: #4b5563;
            margin-top: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <p class="name">{{ $user->name }}</p>

        @if ($user->title)
            <p class="role">{{ $user->title }}</p>
        @endif

        @php
            $contact = collect([$user->location, $user->phone, $user->email])->filter()->implode(' | ');
        @endphp
        @if ($contact)
            <p class="contact-line">{{ $contact }}</p>
        @endif

        @php
            $links = $user->links
                ->map(fn ($link) => \App\Support\CvFormatter::cleanUrl($link->url))
                ->filter()->implode(' | ');
        @endphp
        @if ($links)
            <p class="contact-line">{{ $links }}</p>
        @endif
    </header>

    @if ($user->summary)
        <section>
            <h2>Perfil Profesional</h2>
            <p>{{ $user->summary }}</p>
        </section>
    @endif

    @if ($user->projects->isNotEmpty())
        <section>
            <h2>Proyectos</h2>

            @foreach ($user->projects->sortByDesc('start_date')->take(4) as $project)
                <div class="entry">
                    <p class="entry-title">
                        {{ $project->name }}
                        &mdash; {{ \App\Support\CvFormatter::teamLabel($project->team_size) }}
                        <span class="entry-date">
                            | {{ \App\Support\CvFormatter::dateRange($project->start_date, $project->end_date, $project->is_current) }}
                        </span>
                    </p>
                    @if ($project->tech_stack)
                        <p class="entry-subtitle">{{ implode(' · ', $project->tech_stack) }}</p>
                    @endif
                    @if ($project->description)
                        <ul class="bullets">
                            @foreach (\Illuminate\Support\Str::of($project->description)->split('/\R+/')->filter()->take(5) as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($project->repo_url)
                        <p class="link-line">Repositorio: {{ \App\Support\CvFormatter::cleanUrl($project->repo_url) }}</p>
                    @endif
                    @if ($project->url)
                        <p class="link-line">Demo: {{ \App\Support\CvFormatter::cleanUrl($project->url) }}</p>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->experiences->isNotEmpty())
        <section>
            <h2>Experiencia Profesional</h2>

            @foreach ($user->experiences->sortByDesc('start_date')->take(3) as $experience)
                <div class="entry">
                    <p class="entry-title">
                        {{ $experience->role }}
                        <span class="entry-date">
                            | {{ \App\Support\CvFormatter::dateRange($experience->start_date, $experience->end_date, $experience->is_current) }}
                        </span>
                    </p>
                    <p class="entry-subtitle">
                        {{ $experience->company }}
                        @if ($experience->location)
                            / {{ $experience->location }}
                        @endif
                    </p>
                    @if ($experience->description)
                        <ul class="bullets">
                            @foreach (\Illuminate\Support\Str::of($experience->description)->split('/\R+/')->filter()->take(5) as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->skills->isNotEmpty())
        @php
            $skillsByCategory = $user->skills->sortBy('sort_order')->groupBy('category');
            $categoryLabels = \App\Support\CvFormatter::skillCategoryLabels();
            $categoryKeys = array_keys($categoryLabels);
            $categories = $skillsByCategory->sortBy(
                static fn ($group, $category) => ($position = array_search($category, $categoryKeys, true)) !== false ? $position : PHP_INT_MAX,
            );
        @endphp
        <section>
            <h2>Tecnologías</h2>

            @foreach ($categories as $category => $skills)
                <p class="skill-category">
                    <strong>{{ $categoryLabels[$category] ?? $category }}:</strong>
                    {{ $skills->take(5)->pluck('name')->implode(', ') }}
                </p>
            @endforeach
        </section>
    @endif

    @if ($user->educations->isNotEmpty())
        <section>
            <h2>Educación</h2>

            @foreach ($user->educations->sortBy('sort_order')->take(3) as $education)
                <div class="entry">
                    <p class="entry-title">
                        {{ $education->degree }}
                        @if ($education->field)
                            en {{ $education->field }}
                        @endif
                        <span class="entry-date">
                            | {{ \App\Support\CvFormatter::dateRange($education->start_date, $education->end_date, $education->is_current, yearOnly: true) }}
                        </span>
                    </p>
                    <p class="entry-subtitle">
                        {{ $education->institution }}
                        @php($hours = \App\Support\CvFormatter::extractHours($education->description))
                        @if ($hours)
                            &middot; {{ $hours }}
                        @endif
                    </p>
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->courses->isNotEmpty())
        <section>
            <h2>Formación Complementaria</h2>

            @foreach ($user->courses->sortByDesc('date')->take(5) as $course)
                <div class="entry">
                    <p class="entry-title">
                        {{ $course->name }}
                        @if ($course->date)
                            <span class="entry-date">| {{ \App\Support\CvFormatter::date($course->date) }}</span>
                        @endif
                    </p>
                    @if ($course->institution)
                        <p class="entry-subtitle">{{ $course->institution }}</p>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    @if ($user->languages->isNotEmpty())
        <section>
            <h2>Idiomas</h2>

            @foreach ($user->languages->sortBy('sort_order')->take(3) as $language)
                <p>{{ $language->name }}: {{ \App\Support\CvFormatter::languageLabel($language->level) }}</p>
            @endforeach
        </section>
    @endif

    @if (isset($lastUpdated) && $lastUpdated !== null)
        <p class="last-updated">Última actualización: {{ \App\Support\CvFormatter::verboseDate($lastUpdated) }}</p>
    @endif
</body>
</html>