<div>
    {{-- Print-only CV-style contact block so printed/PDF output starts like an ATS-friendly resume. --}}
    <div class="hidden print:block">
        <h1 class="text-2xl font-bold">{{ $user->name ?? 'Portfolio' }}</h1>

        @if ($user->title)
            <p>{{ $user->title }}</p>
        @endif

        <p class="mt-1 text-sm">
            @if ($user->phone)
                {{ $user->phone }} (WhatsApp)
                @if ($user->location || $user->email)
                    &middot;
                @endif
            @endif
            @if ($user->location)
                {{ $user->location }}
                @if ($user->email)
                    &middot;
                @endif
            @endif
            {{ $user->email }}
        </p>

        @if ($user->links->isNotEmpty())
            <p class="text-sm">
                @foreach ($user->links as $link)
                    {{ $link->label }}: {{ $link->url }}@if (! $loop->last) &middot; @endif
                @endforeach
            </p>
        @endif

        <hr class="my-3 border-zinc-400">
    </div>

    <header
        class="flex items-center justify-between py-6 print:hidden"
        x-data="{ dark: document.documentElement.classList.contains('dark') }"
    >
        <p class="text-sm font-semibold tracking-wide text-zinc-500 dark:text-zinc-400">
            {{ $user->name ?? 'Portfolio' }}
        </p>
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-zinc-300 px-3 py-1.5 text-sm text-zinc-700 transition-colors hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
            @click="
                dark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', dark ? 'dark' : 'light');
            "
            aria-label="Toggle color theme"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" x-show="dark">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4" x-show="!dark" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
            <span class="hidden sm:inline" x-text="dark ? 'Light mode' : 'Dark mode'">Dark mode</span>
        </button>
    </header>

    <main>
        <livewire:portfolio.hero-section :user="$user" />
        <livewire:portfolio.stats-section :user="$user" />
        <livewire:portfolio.skills-section :user="$user" />
        <livewire:portfolio.projects-section :user="$user" />
        <livewire:portfolio.experience-section :user="$user" />
        <livewire:portfolio.education-section :user="$user" />
        <livewire:portfolio.courses-section :user="$user" />
        <livewire:portfolio.languages-section :user="$user" />
        <livewire:portfolio.linktree-section :user="$user" />
        <livewire:portfolio.contact-form :user="$user" />
    </main>

    <footer class="border-t border-zinc-200 py-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400 print:hidden">
        &copy; {{ date('Y') }} {{ $user->name ?? 'Portfolio' }}. Built with Laravel and Livewire.
    </footer>
</div>
