<section id="experience" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Experience</h2>

    @if ($user->experiences->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Work experience is being updated.</p>
    @else
        <ol class="mt-8 space-y-8 border-l-2 border-zinc-200 pl-6 dark:border-zinc-800">
            @foreach ($user->experiences as $experience)
                <li class="relative">
                    <span class="absolute -left-[31px] top-1.5 size-3 rounded-full border-2 border-amber-600 bg-white dark:bg-zinc-950"></span>

                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-3">
                            @if ($experience->getFirstMediaUrl('logos'))
                                <img src="{{ $experience->getFirstMediaUrl('logos') }}" alt="{{ $experience->company }}" class="size-8 rounded object-contain">
                            @endif
                            <h3 class="text-lg font-semibold">{{ $experience->company }}</h3>
                        </div>

                        <p class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $experience->role }}</p>

                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $experience->start_date->format('M Y') }}
                            &rarr;
                            {{ $experience->is_current || $experience->end_date === null ? 'Present' : $experience->end_date->format('M Y') }}
                            @if ($experience->location)
                                &middot; {{ $experience->location }}
                            @endif
                        </p>

                        @if ($experience->description)
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $experience->description }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</section>
