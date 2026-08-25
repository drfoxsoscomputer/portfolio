<section id="links" class="py-20 print:py-8" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Find me elsewhere</h2>

    @if ($user->links->isEmpty())
        <p class="mt-6 text-zinc-500 dark:text-zinc-400">Links are being added.</p>
    @else
        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($user->links as $link)
                <a
                    href="{{ $link->url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-white px-5 py-4 font-medium transition-colors hover:border-amber-600 hover:text-amber-600 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-amber-400 dark:hover:text-amber-400"
                >
                    @if ($link->icon)
                        <span class="inline-flex size-8 items-center justify-center rounded-full border border-zinc-300 dark:border-zinc-700">{{ $link->icon }}</span>
                    @endif
                    {{ $link->label }}
                </a>
            @endforeach
        </div>
    @endif
</section>
