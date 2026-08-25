<section id="contact" class="py-20 print:hidden" data-fade-in>
    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Get in touch</h2>

    @if ($sent)
        <div class="mt-6 rounded-lg border border-green-600/30 bg-green-600/10 px-4 py-3 text-sm text-green-700 dark:text-green-400" role="status">
            Thank you! Your message has been sent. I will get back to you soon.
        </div>
    @else
        <form wire:submit="submit" class="mt-8 max-w-2xl space-y-5" novalidate>
            <div>
                <label for="contact-name" class="block text-sm font-medium">Name</label>
                <input
                    id="contact-name"
                    type="text"
                    wire:model="name"
                    class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact-email" class="block text-sm font-medium">Email</label>
                <input
                    id="contact-email"
                    type="email"
                    wire:model="email"
                    class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact-message" class="block text-sm font-medium">Message</label>
                <textarea
                    id="contact-message"
                    wire:model="message"
                    rows="5"
                    class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900"
                ></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="rounded-md bg-amber-600 px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-amber-700"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60"
            >
                Send message
            </button>
        </form>
    @endif
</section>
