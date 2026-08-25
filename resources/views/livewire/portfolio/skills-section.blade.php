<section id="skills" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Skills</h2>

    @if ($groupedSkills->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Skills are being updated.</p>
    @else
        <div class="mt-6 flex flex-wrap gap-2 print:hidden" x-data="{ filter: 'All' }">
            <button
                type="button"
                class="rounded-full border px-4 py-1.5 text-sm transition-colors"
                :class="filter === 'All' ? 'border-amber-600 bg-amber-600 text-white' : 'border-zinc-300 text-zinc-600 hover:border-amber-600 dark:border-zinc-700 dark:text-zinc-400'"
                @click="filter = 'All'"
            >
                All
            </button>
            @foreach ($groupedSkills as $category => $skills)
                <button
                    type="button"
                    class="rounded-full border px-4 py-1.5 text-sm transition-colors"
                    :class="filter === '{{ $category }}' ? 'border-amber-600 bg-amber-600 text-white' : 'border-zinc-300 text-zinc-600 hover:border-amber-600 dark:border-zinc-700 dark:text-zinc-400'"
                    @click="filter = '{{ $category }}'"
                >
                    {{ $category }}
                </button>
            @endforeach
        </div>

        <div class="mt-8 space-y-8">
            @foreach ($groupedSkills as $category => $skills)
                <div x-data="{ filter: 'All' }" x-show="filter === 'All' || filter === '{{ $category }}'">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $category }}</h3>
                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach ($skills as $skill)
                            <span class="inline-flex items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm dark:border-zinc-800 dark:bg-zinc-900">
                                @if ($skill->getFirstMediaUrl('icons'))
                                    <img src="{{ $skill->getFirstMediaUrl('icons') }}" alt="{{ $skill->name }}" class="size-5 object-contain">
                                @endif
                                {{ $skill->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
