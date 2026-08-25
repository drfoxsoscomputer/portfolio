<section id="courses" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Courses</h2>

    @if ($user->courses->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Courses are being added.</p>
    @else
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($user->courses as $course)
                <article class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $course->name }}</h3>
                        <p class="text-sm font-medium text-amber-600 dark:text-amber-400">{{ $course->institution }}</p>
                    </div>

                    @if ($course->date)
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $course->date->format('M Y') }}</p>
                    @endif

                    @if ($course->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $course->description }}</p>
                    @endif

                    @if ($course->url_certificate)
                        <a href="{{ $course->url_certificate }}" target="_blank" rel="noopener noreferrer" class="mt-auto text-sm font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400">
                            View certificate
                        </a>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
</section>
