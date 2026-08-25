<!DOCTYPE html>
<html lang="en" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ?? config('app.name', 'Portfolio') }}</title>

        {{-- Apply the saved theme before paint to avoid a flash of the wrong theme. --}}
        <script>
            (() => {
                const saved = localStorage.getItem('theme');
                document.documentElement.classList.toggle('dark', saved === null || saved === 'dark');
            })();
        </script>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-zinc-900 antialiased transition-colors dark:bg-zinc-950 dark:text-zinc-100">
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8 print:max-w-none">
            {{ $slot }}
        </div>

        {{-- Scroll fade-in animations via IntersectionObserver (vanilla JS). --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const elements = document.querySelectorAll('[data-fade-in]');

                if (!('IntersectionObserver' in window) || elements.length === 0) {
                    elements.forEach((element) => element.classList.add('is-visible'));

                    return;
                }

                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (!entry.isIntersecting) {
                                return;
                            }

                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        });
                    },
                    { threshold: 0.1 }
                );

                elements.forEach((element) => observer.observe(element));
            });
        </script>
    </body>
</html>
