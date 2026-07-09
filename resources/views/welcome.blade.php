<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evenementen-planner</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-zinc-950 text-zinc-100">

    <header class="border-b border-zinc-800 bg-zinc-900/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-8 py-5">

            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-amber-500 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6 text-black"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3M4 11h16M6 5h12a2 2 0 012 2v11a2 2 0 01-2
                              2H6a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-xl font-bold">
                        Evenementen-planner
                    </h1>

                    <p class="text-sm text-zinc-400">
                        Plan en beheer evenementen
                    </p>
                </div>
            </div>

            <a href="{{ url('/admin/login') }}"
               class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-300 transition hover:border-amber-500 hover:text-amber-400">
                Beheerder login
            </a>

        </div>
    </header>

    <main class="flex min-h-[calc(100vh-145px)] items-center justify-center px-6">

        <div class="w-full max-w-3xl">

            <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-14 shadow-2xl">

                <div class="mb-6 flex justify-center">
                    <div class="rounded-2xl bg-amber-500/10 p-5 ring-1 ring-amber-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="h-14 w-14 text-amber-500"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="1.8">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M8 7V3m8 4V3M4 11h16M6 5h12a2 2 0 012 2v11a2 2 0 01-2
                                  2H6a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                        </svg>
                    </div>
                </div>

                <h2 class="text-center text-5xl font-bold tracking-tight">
                    Welkom
                </h2>

                <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-zinc-400">
                    Bekijk alle aankomende evenementen, meld jezelf eenvoudig aan
                    en beheer evenementen via het administratiepaneel.
                </p>

                <div class="mt-12 flex justify-center gap-4">

                    <a href="{{ route('events.index') }}"
                       class="rounded-lg bg-amber-500 px-8 py-3 font-semibold text-black transition hover:bg-amber-400">
                        Bekijk evenementen
                    </a>

                    <a href="{{ url('/admin/login') }}"
                       class="rounded-lg border border-zinc-700 bg-zinc-900 px-8 py-3 font-semibold text-zinc-200 transition hover:border-amber-500 hover:text-amber-400">
                        Admin
                    </a>

                </div>

            </div>

        </div>

    </main>

    <footer class="border-t border-zinc-800 py-6 text-center text-sm text-zinc-500">
        © {{ date('Y') }} Evenementen-planner
    </footer>

</body>

</html>