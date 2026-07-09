<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Models\EventRegistration;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Ingeschreven mensen';

    protected static ?string $modelLabel = 'inschrijving';

    protected static ?string $pluralModelLabel = 'inschrijvingen';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Naam')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mailadres')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: EventRegistration::class,
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where('event_id', $this->getOwnerRecord()->getKey()),
                    )
                    ->validationMessages([
                        'unique' => 'Dit e-mailadres is al aangemeld voor dit evenement.',
                    ]),
                TextInput::make('phone')
                    ->label('Telefoon')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: EventRegistration::class,
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where('event_id', $this->getOwnerRecord()->getKey()),
                    )
                    ->validationMessages([
                        'unique' => 'Dit telefoonnummer is al aangemeld voor dit evenement.',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mailadres')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label('Telefoon')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('created_at')
                    ->label('Ingeschreven op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
