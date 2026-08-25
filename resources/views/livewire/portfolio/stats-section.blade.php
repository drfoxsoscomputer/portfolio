<section id="stats" class="border-y border-zinc-200 py-10 dark:border-zinc-800" data-fade-in>
    <dl class="grid grid-cols-1 gap-8 text-center sm:grid-cols-3">
        <div
            data-stat="years"
            class="space-y-1"
            x-data="{ target: {{ $years }}, display: 0, started: false }"
            x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting || started) return;
                        started = true;
                        const duration = 800;
                        const startTime = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - startTime) / duration, 1);
                            display = Math.round(progress * target);
                            if (progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                        observer.disconnect();
                    });
                });
                observer.observe($el);
            "
        >
            <dd>
                <span data-count="{{ $years }}" class="text-4xl font-bold text-amber-600 dark:text-amber-400" x-text="display">{{ $years }}</span>
                <span class="text-4xl font-bold text-amber-600 dark:text-amber-400">+</span>
            </dd>
            <dt class="text-sm uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Years of experience</dt>
        </div>

        <div
            data-stat="projects"
            class="space-y-1"
            x-data="{ target: {{ $projectCount }}, display: 0, started: false }"
            x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting || started) return;
                        started = true;
                        const duration = 800;
                        const startTime = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - startTime) / duration, 1);
                            display = Math.round(progress * target);
                            if (progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                        observer.disconnect();
                    });
                });
                observer.observe($el);
            "
        >
            <dd>
                <span data-count="{{ $projectCount }}" class="text-4xl font-bold text-amber-600 dark:text-amber-400" x-text="display">{{ $projectCount }}</span>
                <span class="text-4xl font-bold text-amber-600 dark:text-amber-400">+</span>
            </dd>
            <dt class="text-sm uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Projects</dt>
        </div>

        <div
            data-stat="skills"
            class="space-y-1"
            x-data="{ target: {{ $skillCount }}, display: 0, started: false }"
            x-init="
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting || started) return;
                        started = true;
                        const duration = 800;
                        const startTime = performance.now();
                        const step = (now) => {
                            const progress = Math.min((now - startTime) / duration, 1);
                            display = Math.round(progress * target);
                            if (progress < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                        observer.disconnect();
                    });
                });
                observer.observe($el);
            "
        >
            <dd>
                <span data-count="{{ $skillCount }}" class="text-4xl font-bold text-amber-600 dark:text-amber-400" x-text="display">{{ $skillCount }}</span>
                <span class="text-4xl font-bold text-amber-600 dark:text-amber-400">+</span>
            </dd>
            <dt class="text-sm uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Skills</dt>
        </div>
    </dl>
</section>
