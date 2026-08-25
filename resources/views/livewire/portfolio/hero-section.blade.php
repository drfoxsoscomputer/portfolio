<section id="hero" class="flex flex-col items-center gap-8 py-16 text-center sm:py-20" data-fade-in>
    @if ($user->getFirstMediaUrl('avatar'))
        <img
            src="{{ $user->getFirstMediaUrl('avatar') }}"
            alt="{{ $user->name }}"
            class="size-32 rounded-full object-cover shadow-lg ring-4 ring-amber-500/30 sm:size-40"
        >
    @else
        <div class="flex size-32 items-center justify-center rounded-full bg-amber-600 text-4xl font-bold text-white shadow-lg sm:size-40 sm:text-5xl" aria-label="{{ $user->name }}">
            {{ str($user->name)->initials() }}
        </div>
    @endif

    <div class="space-y-3">
        <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">{{ $user->name }}</h1>

        @if ($user->title)
            <p class="text-xl font-medium text-amber-600 dark:text-amber-400 sm:text-2xl">{{ $user->title }}</p>
        @endif

        @if ($user->summary)
            <p class="mx-auto max-w-2xl text-zinc-600 dark:text-zinc-400">{{ $user->summary }}</p>
        @endif

        @if ($user->location)
            <p class="inline-flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                {{ $user->location }}
            </p>
        @endif

        @if ($user->phone)
            <p class="inline-flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                </svg>
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $user->phone) }}" class="transition-colors hover:text-amber-600 dark:hover:text-amber-400">{{ $user->phone }}</a>
            </p>
        @endif
    </div>

    @if ($user->links->isNotEmpty())
        <nav class="flex flex-wrap items-center justify-center gap-4" aria-label="Social links">
            @foreach ($user->links as $link)
                <a
                    href="{{ $link->url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 text-sm text-zinc-600 transition-colors hover:text-amber-600 dark:text-zinc-400 dark:hover:text-amber-400"
                >
                    @if ($link->icon)
                        <span class="inline-flex size-8 items-center justify-center rounded-full border border-zinc-300 dark:border-zinc-700">{{ $link->icon }}</span>
                    @endif
                    {{ $link->label }}
                </a>
            @endforeach
        </nav>
    @endif
</section>
