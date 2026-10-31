<x-layouts.public>
    {{-- HERO --}}
    <section class="relative overflow-hidden bg-primary text-white">
        <div aria-hidden="true" class="pointer-events-none absolute -top-40 right-0 size-[36rem] rounded-full bg-secondary/25 blur-3xl"></div>
        <div aria-hidden="true" class="pointer-events-none absolute -bottom-48 -left-24 size-[30rem] rounded-full bg-gold/15 blur-3xl"></div>

        <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 md:py-24 lg:grid-cols-2">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold tracking-wide text-gold ring-1 ring-white/15">
                    <span aria-hidden="true">✦</span> {{ $brand->get('flagship_module') }}
                </p>
                <h1 class="mt-6 text-4xl leading-[1.1] font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    {{ $brand->get('slogan') }}
                </h1>
                <p class="mt-6 max-w-xl text-lg text-white/80">
                    {{ __('Ask a question or snap a photo of it. :product explains the idea step by step, gives you practice questions on the same topic and shows what to revise next for WAEC, NECO and JAMB.', ['product' => $brand->get('product_name')]) }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-gold px-6 py-3 text-base">{{ __('Create a free account') }}</a>
                    <a href="#institutions" class="btn border border-white/25 px-6 py-3 text-base text-white hover:bg-white/10">{{ __('For schools') }}</a>
                </div>
                <p class="mt-4 text-sm text-white/60">{{ __('No card needed to start. Works on any phone browser.') }}</p>
            </div>

            {{-- Illustrative example of the tutor's explanation format --}}
            <figure class="relative mx-auto w-full max-w-md rounded-2xl bg-surface p-5 text-ink shadow-2xl shadow-black/30 ring-1 ring-black/5">
                <figcaption class="mb-4 flex items-center justify-between text-xs font-medium text-muted">
                    <span class="flex items-center gap-2"><x-app-logo-icon class="size-5" /> {{ __('Example explanation') }}</span>
                    <span class="rounded-full bg-surface-alt px-2 py-0.5">{{ __('Mathematics · Guide Me') }}</span>
                </figcaption>
                <div class="rounded-xl bg-surface-alt p-3 text-sm">
                    <span class="font-semibold">{{ __('Question:') }}</span> {{ __('Solve 2x + 7 = 19') }}
                </div>
                <ol class="mt-4 space-y-3 text-sm">
                    <li class="flex gap-3"><span class="grid size-6 shrink-0 place-items-center rounded-full bg-secondary-strong text-xs font-bold text-white">1</span><span>{{ __('We want x on its own. Subtract 7 from both sides:') }} <code class="font-semibold">2x = 12</code></span></li>
                    <li class="flex gap-3"><span class="grid size-6 shrink-0 place-items-center rounded-full bg-secondary-strong text-xs font-bold text-white">2</span><span>{{ __('Divide both sides by 2:') }} <code class="font-semibold">x = 6</code></span></li>
                    <li class="flex gap-3"><span class="grid size-6 shrink-0 place-items-center rounded-full bg-gold text-xs font-bold text-gold-foreground">✓</span><span>{{ __('Check: 2(6) + 7 = 19. Correct.') }}</span></li>
                </ol>
                <div class="mt-4 rounded-xl border border-dashed border-line p-3 text-xs text-ink-soft">
                    <span class="font-semibold text-ink">{{ __('Try one:') }}</span> {{ __('Solve 3x − 5 = 16') }}
                </div>
            </figure>
        </div>
    </section>

    {{-- TUTOR MODES --}}
    <section id="tutor" class="mx-auto max-w-7xl scroll-mt-20 px-4 py-20 sm:px-6">
        <div class="max-w-2xl">
            <p class="eyebrow">{{ $brand->get('flagship_module') }}</p>
            <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-primary sm:text-4xl">{{ __('A tutor that helps you learn, not just copy answers') }}</h2>
            <p class="mt-4 text-lg text-ink-soft">{{ __('Choose how you want help. Ask for a hint when you are stuck, or a full worked solution when you need to see the whole method.') }}</p>
        </div>

        @php($modes = [
            ['Teach Me', 'Learn a concept from the basics, with examples.'],
            ['Guide Me', 'Get hints one step at a time and work it out yourself.'],
            ['Solve It', 'See a worked solution with the reason for every step.'],
            ['Explain Simply', 'Plain language and everyday examples.'],
            ['Exam Mode', 'Practise under timed, exam-style conditions.'],
            ['Quiz Me', 'Short practice sets on the topic you choose.'],
            ['Review My Mistakes', 'Understand why an answer was wrong.'],
            ['Study Plan', 'A schedule built around your exam date and free time.'],
        ])
        <ul class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($modes as [$name, $desc])
                <li class="card transition hover:-translate-y-0.5 hover:shadow-lg">
                    <h3 class="font-bold text-primary">{{ __($name) }}</h3>
                    <p class="mt-2 text-sm text-ink-soft">{{ __($desc) }}</p>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- EXAMS --}}
    <section id="exams" class="scroll-mt-20 bg-surface-alt">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
            <div class="grid gap-12 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <p class="eyebrow">{{ __('Exam preparation') }}</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-primary sm:text-4xl">{{ __('Practise for the exams you are actually writing') }}</h2>
                    <p class="mt-4 text-ink-soft">{{ __('Organised by Nigerian subjects and syllabus topics, so your practice matches what you are studying in class.') }}</p>
                    <p class="mt-6 rounded-xl border border-line bg-surface p-4 text-sm text-ink-soft">
                        <strong class="text-ink">{{ __('Please note:') }}</strong>
                        {{ __('Practice exams on :product are for revision. They are not official examinations, and good practice scores do not guarantee a particular grade.', ['product' => $brand->get('product_name')]) }}
                    </p>
                </div>
                <div class="grid gap-4 sm:grid-cols-3 lg:col-span-3">
                    @foreach ([['WAEC', 'Senior secondary (SSCE) subjects, objective and theory practice.'], ['NECO', 'SSCE revision by subject and topic.'], ['JAMB / UTME', 'Timed CBT-style practice across your four subjects.']] as [$exam, $text])
                        <div class="card">
                            <p class="text-2xl font-extrabold text-primary">{{ $exam }}</p>
                            <p class="mt-3 text-sm text-ink-soft">{{ __($text) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <p class="eyebrow">{{ __('How it works') }}</p>
        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-primary sm:text-4xl">{{ __('Ask, understand, practise, improve') }}</h2>
        <ol class="mt-12 grid gap-8 md:grid-cols-4">
            @foreach ([
                ['Ask', 'Type a question or upload a clear photo of it.'],
                ['Understand', 'Follow a step-by-step explanation you can question further.'],
                ['Practise', 'Answer related questions and get feedback straight away.'],
                ['Improve', 'See which topics need more work and what to do next.'],
            ] as $i => [$step, $text])
                <li>
                    <span class="grid size-10 place-items-center rounded-xl bg-primary text-sm font-bold text-white">{{ $i + 1 }}</span>
                    <h3 class="mt-4 font-bold text-ink">{{ __($step) }}</h3>
                    <p class="mt-2 text-sm text-ink-soft">{{ __($text) }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- INSTITUTIONS --}}
    <section id="institutions" class="scroll-mt-20 bg-primary text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-3">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gold uppercase">{{ __('For institutions') }}</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight">{{ __('Built for schools, teachers and CBT centres') }}</h2>
            </div>
            @foreach ([
                ['Schools', 'Enrol classes, assign practice and mock exams, and see subject-level progress for your students.'],
                ['CBT centres', 'Schedule timed mock exams with access codes, server-side timing and exportable results.'],
            ] as [$who, $text])
                <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                    <h3 class="text-lg font-bold">{{ __($who) }}</h3>
                    <p class="mt-2 text-sm text-white/75">{{ __($text) }}</p>
                    <a href="mailto:{{ $brand->get('support_email') }}?subject={{ rawurlencode($who.' enquiry') }}" class="mt-5 inline-block text-sm font-semibold text-gold hover:underline">{{ __('Talk to us') }} →</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="mx-auto max-w-3xl scroll-mt-20 px-4 py-20 sm:px-6">
        <h2 class="text-3xl font-extrabold tracking-tight text-primary">{{ __('Frequently asked questions') }}</h2>
        <div class="mt-8 divide-y divide-line border-y border-line">
            @foreach ([
                ['Is the AI always right?', 'No. AI explanations can contain mistakes, so every answer can be reported for review. Treat explanations as study help and check them against your teacher and textbooks.'],
                ['Is this an official WAEC, NECO or JAMB product?', 'No. :product is an independent learning platform. It is not affiliated with or endorsed by any examination body.'],
                ['Can a parent or school pay for a student?', 'Parents, guardians and schools will be able to sponsor student accounts, so students do not need their own bank card.'],
                ['What happens to my data?', 'We aim to collect only what is needed to run your account. The privacy policy explains what we collect, why, and your choices.'],
            ] as [$q, $a])
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-ink">
                        {{ __($q) }}
                        <span aria-hidden="true" class="text-xl text-muted transition group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 text-ink-soft">{{ __($a, ['product' => $brand->get('product_name')]) }}</p>
                </details>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="flex flex-col items-start gap-6 rounded-3xl bg-gold p-10 text-gold-foreground md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">{{ __('Start learning smarter today') }}</h2>
                <p class="mt-2 text-gold-foreground/80">{{ __('Create a free student account in under a minute.') }}</p>
            </div>
            <a href="{{ route('register') }}" class="btn-primary px-6 py-3 text-base">{{ __('Create a free account') }}</a>
        </div>
    </section>
</x-layouts.public>
