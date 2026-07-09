<?php

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    public ?int $selectedEventId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc,dns|max:255')]
    public string $email = '';

    #[Validate('required|string|max:30|regex:/^\+?[0-9][0-9\s().-]{7,20}$/')]
    public string $phone = '';

    public ?string $successMessage = null;

    public function selectEvent(int $eventId): void
    {
        $event = Event::upcoming()->findOrFail($eventId);

        if ($event->isFull()) {
            return;
        }

        $this->selectedEventId = $event->id;
        $this->successMessage = null;
        $this->resetErrorBag();
    }

    public function register(): void
    {
        $validated = $this->validate();

        if ($this->selectedEventId === null) {
            return;
        }

        $event = Event::upcoming()->findOrFail($this->selectedEventId);

        if ($event->isFull()) {
            $this->addError('selectedEventId', 'Dit evenement is helaas vol.');

            return;
        }

        if ($event->registrations()->where('email', $validated['email'])->exists()) {
            $this->addError('email', 'Dit e-mailadres is al aangemeld voor dit evenement.');

            return;
        }

        if ($event->registrations()->where('phone', $validated['phone'])->exists()) {
            $this->addError('phone', 'Dit telefoonnummer is al aangemeld voor dit evenement.');

            return;
        }

        try {
            EventRegistration::create([
                ...$validated,
                'event_id' => $event->id,
            ]);
        } catch (QueryException) {
            $this->addError('email', 'Dit e-mailadres of telefoonnummer is al aangemeld voor dit evenement.');

            return;
        }

        $this->successMessage = "Je bent aangemeld voor {$event->title}.";
        $this->reset('selectedEventId', 'name', 'email', 'phone');
        $this->resetErrorBag();
    }

    public function cancelRegistration(): void
    {
        $this->reset('selectedEventId', 'name', 'email', 'phone');
        $this->resetErrorBag();
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Vul een geldig e-mailadres in.',
            'phone.regex' => 'Vul een geldig telefoonnummer in.',
        ];
    }

    public function with(): array
    {
        return [
            'events' => Event::query()
                ->upcoming()
                ->withCount('registrations')
                ->get(),
            'selectedEvent' => $this->selectedEventId
                ? Event::query()->withCount('registrations')->find($this->selectedEventId)
                : null,
        ];
    }
}; ?>

<div class="min-h-screen bg-zinc-950 text-zinc-100">
    <header class="border-b border-zinc-800 bg-zinc-900/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500 text-black">
                    <x-filament::icon icon="heroicon-o-calendar-days" class="h-6 w-6" />
                </div>

                <div>
                    <h1 class="text-xl font-bold">Evenementen-planner</h1>
                    <p class="text-sm text-zinc-400">Aankomende evenementen</p>
                </div>
            </a>

            <a href="{{ url('/admin/login') }}"
                class="rounded-lg border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-300 transition hover:border-amber-500 hover:text-amber-400">
                Beheerder login
            </a>
        </div>
    </header>

    <main class="mx-auto grid max-w-7xl gap-8 px-6 py-10 lg:grid-cols-[1fr_380px]">
        <section class="space-y-5">
            <div>
                <p class="text-sm font-medium text-amber-400">Publieke inschrijving</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight">Bekijk evenementen</h2>
                <p class="mt-3 max-w-2xl text-zinc-400">
                    Kies een aankomend evenement en laat je gegevens achter. Inloggen is niet nodig.
                </p>
            </div>

            @if ($successMessage)
                <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ $successMessage }}
                </div>
            @endif

            @error('selectedEventId')
                <div class="rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    {{ $message }}
                </div>
            @enderror

            <div class="space-y-4">
                @forelse ($events as $event)
                    <article
                        class="rounded-lg border border-zinc-800 bg-zinc-900 p-6 shadow-lg shadow-black/20 transition hover:border-amber-500/60">
                        <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                            <div class="space-y-3">
                                <div>
                                    <h3 class="text-xl font-semibold text-zinc-50">{{ $event->title }}</h3>
                                    <p class="mt-1 text-sm text-zinc-400">
                                        {{ $event->starts_at->translatedFormat('d F Y, H:i') }}
                                        @if ($event->ends_at)
                                            tot {{ $event->ends_at->translatedFormat('d F Y, H:i') }}
                                        @endif
                                    </p>
                                </div>

                                @if ($event->location)
                                    <p class="flex items-center gap-2 text-sm text-zinc-300">
                                        <x-filament::icon icon="heroicon-o-map-pin" class="h-4 w-4 text-amber-400" />
                                        {{ $event->location }}
                                    </p>
                                @endif

                                @if ($event->description)
                                    <p class="max-w-3xl text-sm leading-6 text-zinc-400">{{ $event->description }}</p>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col items-start gap-3 md:items-end">
                                <span class="rounded-md bg-zinc-800 px-3 py-1 text-xs font-medium text-zinc-300">
                                    {{ $event->registrations_count }}
                                    @if ($event->capacity)
                                        / {{ $event->capacity }}
                                    @endif
                                    aangemeld
                                </span>

                                @if ($event->isFull())
                                    <span class="rounded-md bg-zinc-800 px-4 py-2 text-sm font-semibold text-zinc-400">
                                        Vol
                                    </span>
                                @else
                                    <button type="button" wire:click="selectEvent({{ $event->id }})"
                                        class="rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-amber-400">
                                        Inschrijven
                                    </button>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-lg border border-zinc-800 bg-zinc-900 p-8 text-zinc-400">
                        Er zijn momenteel geen aankomende evenementen.
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="lg:sticky lg:top-8 lg:self-start">
            <form wire:submit="register" class="rounded-lg border border-zinc-800 bg-zinc-900 p-6 shadow-xl shadow-black/20">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-50">Inschrijven</h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        @if ($selectedEvent)
                            Voor {{ $selectedEvent->title }}
                        @else
                            Selecteer eerst een evenement.
                        @endif
                    </p>
                </div>

                <div class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Naam</span>
                        <input type="text" wire:model="name" @disabled(! $selectedEvent)
                            class="mt-2 w-full rounded-lg border-zinc-700 bg-zinc-950 px-3 py-2 text-zinc-100 outline-none transition placeholder:text-zinc-600 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="Je naam">
                        @error('name')
                            <span class="mt-1 block text-sm text-red-300">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">E-mail</span>
                        <input type="email" wire:model="email" @disabled(! $selectedEvent)
                            class="mt-2 w-full rounded-lg border-zinc-700 bg-zinc-950 px-3 py-2 text-zinc-100 outline-none transition placeholder:text-zinc-600 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="naam@example.com">
                        @error('email')
                            <span class="mt-1 block text-sm text-red-300">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-zinc-200">Telefoon</span>
                        <input type="tel" wire:model="phone" @disabled(! $selectedEvent)
                            class="mt-2 w-full rounded-lg border-zinc-700 bg-zinc-950 px-3 py-2 text-zinc-100 outline-none transition placeholder:text-zinc-600 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30 disabled:cursor-not-allowed disabled:opacity-50"
                            placeholder="06 12345678">
                        @error('phone')
                            <span class="mt-1 block text-sm text-red-300">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" @disabled(! $selectedEvent)
                        class="flex-1 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-black transition hover:bg-amber-400 disabled:cursor-not-allowed disabled:opacity-50">
                        Aanmelden
                    </button>

                    @if ($selectedEvent)
                        <button type="button" wire:click="cancelRegistration"
                            class="rounded-lg border border-zinc-700 px-5 py-2.5 text-sm font-semibold text-zinc-200 transition hover:border-amber-500 hover:text-amber-400">
                            Annuleer
                        </button>
                    @endif
                </div>
            </form>
        </aside>
    </main>
</div>
