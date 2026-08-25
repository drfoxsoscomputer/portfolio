<section id="education" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Education</h2>

    @if ($user->educations->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Education is being updated.</p>
    @else
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach ($user->educations as $education)
                <article class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                    <h3 class="text-lg font-semibold">{{ $education->institution }}</h3>

                    <p class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $education->degree }}</p>

                    @if ($education->field)
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $education->field }}</p>
                    @endif

                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                        @if ($education->start_date)
                            {{ $education->start_date->format('M Y') }}
                            &rarr;
                            {{ $education->is_current || $education->end_date === null ? 'Present' : $education->end_date->format('M Y') }}
                        @endif
                    </p>

                    @if ($education->description)
                        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $education->description }}</p>
                    @endif

                    @if ($education->getMedia('certificates')->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach ($education->getMedia('certificates') as $certificate)
                                <a href="{{ $certificate->getUrl() }}" target="_blank" rel="noopener noreferrer" class="text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400">
                                    View certificate
                                </a>
                            @endforeach
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>
