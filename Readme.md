# Evenementen-planner

Laravel-applicatie voor het beheren van evenementen, gebouwd met de TALL-stack (TailwindCSS, AlpineJS, Laravel, Livewire) en FilamentPHP voor het beheerdersdashboard.

## Gebruikte versies

- Laravel 13
- TailwindCSS 4 (via `@tailwindcss/vite`)
- Livewire 4 (single-file components)
- FilamentPHP 5
- AlpineJS

> **Let op:** dit project gebruikt Livewire 4, uitgebracht in januari 2026. Livewire 4 introduceert *single-file components*: in plaats van een aparte PHP-class (`app/Livewire/...`) en Blade-view, staan PHP-logica en template nu samen in één bestand onder `resources/views/components/`, herkenbaar aan het ⚡-symbool in de bestandsnaam. Componenten worden gerouteerd met `Route::livewire()` in plaats van een class-referentie.

## Vereisten

- PHP 8.2+
- Composer
- Node.js + npm
- MySQL (bijv. via XAMPP)

## Installatie (voor wie dit project clone't)

### 1. Repository clonen en dependencies installeren

```bash
git clone <repo-url> evenementen-planner
cd evenementen-planner
composer install
npm install
```

### 2. Environment configureren

```bash
cp .env.example .env
php artisan key:generate
```

Vul in `.env` de databasegegevens in:
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

> Als de database `evenementen_planner` nog niet bestaat, vraagt Laravel automatisch of deze aangemaakt mag worden (`Would you like to create it? [yes]`). Dit is voldoende, een aparte `CREATE DATABASE`-stap is niet nodig.

### 4. Filament-admin-gebruiker aanmaken

```bash
php artisan make:filament-user
```
Vul naam, e-mailadres en wachtwoord in. Hiermee log je in op `/admin/login`.

### 5. Assets bouwen en server starten

```bash
npm run dev      # tijdens development
# of
npm run build    # voor productie

php artisan serve
```

De publieke site draait op `http://localhost:8000`, het beheerdersdashboard op `http://localhost:8000/admin`.

---

## Structuur publiek vs. beheer

| Route | Onderdeel | Technologie | Voor wie |
|---|---|---|---|
| `/` | Welkomstpagina | Blade | Bezoekers |
| `/events` | Evenementenlijst + aanmelden | Livewire 4 (los van Filament) | Bezoekers |
| `/admin` | Beheerdersdashboard (CRUD op events) | FilamentPHP | Beheerders |
| `/admin/login` | Inlogpagina beheerder | FilamentPHP (automatisch gegenereerd) | Beheerders |

Filament en de publieke Livewire-pagina's zijn bewust gescheiden: Filament is bedoeld voor beheer (CRUD via een admin-panel), niet voor publieke, bezoekersgerichte interfaces. Beide gebruiken wel hetzelfde `Event`-model en dezelfde database, zodat een evenement dat een beheerder aanmaakt in `/admin` direct zichtbaar is op `/events`, zonder dubbele logica.

---

## Wat is er tot nu toe gedaan (en waarom)

| Stap | Commando | Toelichting |
|---|---|---|
| Project opzetten | `laravel new evenementen-planner` | Startpunt met Laravel, inclusief het Livewire-starterkit (vandaar dat `livewire/livewire`, `laravel/fortify` en `livewire/flux` al aanwezig waren). |
| Tailwind | `npm install -D @tailwindcss/vite` | Laravel gebruikt Tailwind v4, dat via een Vite-plugin werkt in plaats van een los `tailwind.config.js` + PostCSS-setup zoals bij v3. Dit scheelt configuratiebestanden en sluit aan bij de huidige Laravel-standaard. |
| Livewire | `composer require livewire/livewire` | Voor de interactieve, server-side gerenderde componenten (bijv. de aanmeldknop voor bezoekers), zonder dat hier een aparte front-end/API-laag voor nodig is. |
| AlpineJS | `npm install alpinejs` | Voor lichte client-side interacties (bijv. modals/dropdowns) die geen server-roundtrip nodig hebben. |
| FilamentPHP | `composer require filament/filament -W`<br>`php artisan filament:install --panels` | Genereert het admin-panel op `/admin`, inclusief kant-en-klare authenticatie (`/admin/login`). Filament is gekozen omdat het CRUD-schermen (formulieren, tabellen) automatisch kan genereren vanuit het model, wat veel handmatige Blade/Livewire-code voor het beheer bespaart. |
| Admin-gebruiker | `php artisan make:filament-user` | Nodig om in te kunnen loggen op het dashboard. |
| Event-model | `php artisan make:model Event -m` | Model + migratie voor de kernentiteit van de applicatie: het evenement. |
| Events-migratie uitgewerkt | — | Velden toegevoegd: `title`, `description`, `location`, `starts_at`, `ends_at`, `capacity`. |
| Aanmeldingen (pivot) | `php artisan make:migration create_event_user_table` | Many-to-many koppeltabel tussen `events` en `users`, zodat aanmeldingen via Eloquent's ingebouwde relaties (`belongsToMany`) worden afgehandeld in plaats van handmatige join-logica. |
| Model-helpers | — | Query scope `scopeUpcoming()` en methodes `isFull()` / `hasAttendee()` toegevoegd aan `Event.php`, zodat business-logica op één centrale plek staat in plaats van verspreid over controllers/views — dit maakt de code beter onderhoudbaar en testbaar. |
| Filament resource | `php artisan make:filament-resource Event --generate` | Genereert de CRUD-structuur (`EventResource`, `EventForm`, `EventsTable`, pages) voor het admin-dashboard. `EventForm` en `EventsTable` zijn vervolgens handmatig ingevuld met de daadwerkelijke velden (title, location, starts_at, ends_at, capacity), aangezien `--generate` een lege basis oplevert wanneer de migratie nog niet volledig was uitgewerkt op het moment van genereren. Als titelattribuut is `title` gebruikt (`$recordTitleAttribute`). |
| Publieke welkomstpagina | `resources/views/welcome.blade.php` | Landingspagina met uitleg over het platform, een knop naar de evenementenlijst (`/events`) en een knop naar de beheerderslogin (`/admin/login`). Gestyled met Tailwind. |
| Publieke evenementenlijst | `php artisan make:livewire EventList` | Livewire 4 single-file component (`resources/views/components/⚡event-list.blade.php`) dat aankomende evenementen toont (via de `upcoming()`-scope) en een aanmeldknop per evenement, met status "Vol" / "Je bent aangemeld" op basis van de model-helpers. Gerouteerd met `Route::livewire('/events', 'event-list')`. |

### Nog te doen
- Validatie verder verfijnen (bijv. `ends_at` na `starts_at`)
- Policies/authorisatie voor het admin-panel (wie mag welke evenementen beheren)
- Foutafhandeling en meldingen bij aanmelden (bijv. flash-message na succesvolle aanmelding)
- Testen (Feature tests voor aanmelden, capaciteit, en Filament CRUD)

---

## Projectstructuur (relevant voor deze opdracht)

```
app/
  Models/
    Event.php                          Model met relaties (attendees) en helpers (upcoming, isFull, hasAttendee)
  Filament/
    Resources/Events/
      EventResource.php                Resource-configuratie (model, navigatie, pages)
      Schemas/EventForm.php            Formuliervelden voor aanmaken/bewerken
      Tables/EventsTable.php           Kolommen voor het overzicht in /admin
      Pages/                           Gegenereerde CRUD-pagina's (List, Create, Edit)
database/
  migrations/
    ..._create_events_table.php        Schema voor evenementen
    ..._create_event_user_table.php    Pivot-tabel voor aanmeldingen
resources/
  views/
    welcome.blade.php                  Publieke welkomstpagina
    components/
      ⚡event-list.blade.php            Publiek Livewire 4-component: lijst + aanmeldknop
  css/app.css                          Tailwind v4 entry point
routes/
  web.php                              Routes voor welkomstpagina en events-lijst
```