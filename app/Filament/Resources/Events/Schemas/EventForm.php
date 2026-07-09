<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->columnSpanFull(),

                TextInput::make('location')
                    ->maxLength(255),

                DateTimePicker::make('starts_at')
                    ->required()
                    ->minDate(fn (string $operation) => $operation === 'create' ? now() : null),

                DateTimePicker::make('ends_at')
                    ->afterOrEqual('starts_at')
                    ->validationMessages([
                        'after_or_equal' => 'De einddatum mag niet eerder zijn dan de startdatum.',
                    ]),

                TextInput::make('capacity')
                    ->numeric()
                    ->minValue(1),
            ]);
    }
}
