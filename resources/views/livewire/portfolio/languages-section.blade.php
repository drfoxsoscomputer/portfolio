<section id="languages" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Languages</h2>

    @if ($user->languages->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Languages are being updated.</p>
    @else
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach ($user->languages as $language)
                <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        @if ($language->getFirstMediaUrl('flags'))
                            <img src="{{ $language->getFirstMediaUrl('flags') }}" alt="{{ $language->name }}" class="size-6 rounded-full object-cover">
                        @endif
                        <h3 class="font-semibold">{{ $language->name }}</h3>
                        <span class="ml-auto text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $language->level }}</span>
                    </div>

                    @php $percent = \App\Livewire\Portfolio\LanguagesSection::levelPercent($language->level); @endphp
                    <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full rounded-full bg-amber-600" data-level-percent="{{ $percent }}" x-bind:style="'width: ' + {{ $percent }} + '%'"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
