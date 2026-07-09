# Evenementen-planner

Laravel-applicatie voor het beheren van evenementen. Beheerders maken en beheren evenementen via FilamentPHP, bezoekers bekijken publiek aankomende evenementen en kunnen zich aanmelden zonder account.

## Stack

- Laravel 13
- FilamentPHP 5
- Livewire 4 single-file components
- TailwindCSS 4
- AlpineJS
- MySQL

## Functionaliteiten

- Publieke welkomstpagina met links naar evenementen en beheer.
- Publieke evenementenlijst op `/events`.
- Bezoekers kunnen zich aanmelden met naam, e-mailadres en telefoonnummer.
- Inloggen is voor bezoekers niet nodig.
- Dubbele inschrijving met hetzelfde e-mailadres per evenement wordt voorkomen.
- Capaciteit per evenement wordt bewaakt op basis van publieke inschrijvingen.
- Beheerders beheren evenementen via `/admin`.
- Admin kan geen nieuw evenement in het verleden aanmaken.
- E-mail en telefoonnummer worden server-side gevalideerd.
- Admin-header is gestyled in dezelfde stijl als de welkompagina, met een link terug naar de welkompagina.

## Installatie

### 1. Dependencies installeren

```bash
composer install
npm install
```

### 2. Environment instellen

```bash
cp .env.example .env
php artisan key:generate
```

Vul in `.env` je databasegegevens in, bijvoorbeeld:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evenementen_planner
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database migreren

```bash
php artisan migrate
```

### 4. Admin-gebruiker aanmaken

```bash
php artisan make:filament-user
```

Daarna kun je inloggen op `/admin/login`.

### 5. Assets bouwen of dev-server starten

Tijdens development:

```bash
npm run dev
php artisan serve
```

Voor productie-assets:

```bash
npm run build
```

## Routes

| Route | Doel |
| --- | --- |
| `/` | Welkompagina |
| `/events` | Publieke evenementenlijst en inschrijfformulier |
| `/admin` | Filament beheerdersdashboard |
| `/admin/login` | Login voor beheerders |

## Belangrijke bestanden

```text
app/
  Models/
    Event.php
    EventRegistration.php
  Filament/Resources/Events/
    EventResource.php
    Schemas/EventForm.php
    Tables/EventsTable.php
database/
  migrations/
    2026_07_09_112720_create_events_table.php
    2026_07_09_162700_create_event_registrations_table.php
resources/
  views/
    welcome.blade.php
    components/⚡event-list.blade.php
    filament/admin/brand.blade.php
    filament/admin/welcome-link.blade.php
  css/
    app.css
    filament/admin/theme.css
routes/
  web.php
```

## Architectuurkeuzes

FilamentPHP wordt alleen gebruikt voor het beheer, omdat het snel en onderhoudbaar CRUD-schermen oplevert voor beheerders. De publieke bezoekersflow is bewust gebouwd als Livewire component, zodat bezoekers zonder account kunnen aanmelden en de interface los blijft van het admin-panel.

Publieke inschrijvingen staan in een aparte `event_registrations` tabel. Dat past beter dan een user-pivot, omdat bezoekers geen account nodig hebben maar wel contactgegevens moeten achterlaten. Het `Event` model bevat de relatie en capaciteitlogica, zodat deze regels centraal blijven.
