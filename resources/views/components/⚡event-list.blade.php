<?php use App\Models\Event;
use Livewire\Component;
new class extends Component {
    public function signup(int $eventId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $event = Event::findOrFail($eventId);
        if ($event->isFull() || $event->hasAttendee(auth()->user())) {
            return;
        }
        $event->attendees()->attach(auth()->id());
    }
    public function with(): array
    {
        return ['events' => Event::upcoming()->get(),];
    }
}; ?>
<div class="space-y-4"> @forelse($events as $event) <x-filament::section :heading="$event->title"
    :description="$event->starts_at->translatedFormat('d F Y, H:i')"> @if($event->location)
    <p class="text-sm text-gray-500"> {{ $event->location }} </p> @endif <x-slot name="footerActions">
        @if($event->isFull()) <x-filament::badge color="gray"> Vol </x-filament::badge>
        @elseif(auth()->check() && $event->hasAttendee(auth()->user())) <x-filament::badge color="success"> Je bent
        aangemeld </x-filament::badge> @else <x-filament::button wire:click="signup({{ $event->id }})">
            Aanmelden </x-filament::button> @endif </x-slot>
</x-filament::section> @empty <x-filament::section> Er zijn momenteel geen aankomende evenementen.
    </x-filament::section> @endforelse </div>