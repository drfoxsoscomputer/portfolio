<section id="projects" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Projects</h2>

    @if ($user->projects->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Projects are being added.</p>
    @else
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($user->projects as $project)
                <article class="flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white transition-shadow hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
                    @if ($project->getFirstMediaUrl('screenshots'))
                        <img src="{{ $project->getFirstMediaUrl('screenshots') }}" alt="{{ $project->name }}" class="aspect-video w-full object-cover">
                    @else
                        <div class="flex aspect-video w-full items-center justify-center bg-zinc-100 text-sm text-zinc-400 dark:bg-zinc-800">
                            {{ $project->name }}
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <h3 class="text-lg font-semibold">{{ $project->name }}</h3>

                        @if ($project->description)
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $project->description }}</p>
                        @endif

                        @if (! empty($project->tech_stack))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($project->tech_stack as $tech)
                                    <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $tech }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if ($project->url || $project->repo_url)
                            <div class="mt-auto flex gap-4 pt-2 text-sm">
                                @if ($project->url)
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400">
                                        Visit site
                                    </a>
                                @endif
                                @if ($project->repo_url)
                                    <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400">
                                        Repository
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>
