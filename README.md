# php-laravel-edi — basis voor elk EDI-project

Dit is de **vaste basis** voor EDI-projecten van 2makeitwork (zoals `le_duc_edi`, maar dan in Laravel). Elk nieuw project begint met een kopie van deze repo. De EDI-bibliotheek, de tests, de migraties en de scheduler zijn daardoor in elk project gelijk.

## Wat zit erin

| Onderdeel | Waarvoor |
|---|---|
| **Laravel 13** | Framework (queues, configuratie, tests) |
| **php-edifact** (`src/EDI/`) | Bibliotheek om EDIFACT-berichten te lezen en te schrijven |
| **`src/EDI/Storage/`** | Eigen SFTP/FTP-bestandslaag en vaste mapstructuur |
| **Scheduler** (`scheduler/`) | Eén crontab-regel, zoals in `le_duc_edi` (`GO\Scheduler`) |
| **Flyway** (`flyway/`) | Databasemigraties |
| **Docker en CI** (`compose.yaml`, `.gitlab-ci.yml`) | Lokaal draaien en deployen naar development en main |

## Structuur

```
/
├── app/                         De applicatie (Docker koppelt ./app aan /var/www/html)
│   ├── src/EDI/
│   │   ├── Parser, Reader, Analyser,
│   │   │   Interpreter, Encoder,
│   │   │   ReaderException      php-edifact (NIET AANPASSEN)
│   │   ├── Storage/             Eigen SFTP/FTP-laag
│   │   └── <Flow>/              Jouw code per flow (komt per project)
│   ├── tests/
│   │   ├── EDITest/             Originele tests (NIET AANPASSEN) + EDITest/Storage
│   │   └── files/               Originele voorbeeldbestanden (NIET AANPASSEN)
│   ├── scheduler/
│   │   ├── scheduler.php        De planning
│   │   └── jobs/                Eén script per job
│   └── config/ bootstrap/ routes/ resources/ public/ storage/ artisan
│                                Laravel zelf, hier werk je zelden in
├── flyway/                      conf/ en sql/ (databasemigraties)
├── mysql/my.cnf                 Instellingen databasecontainer
├── compose.yaml
└── .gitlab-ci.yml
```

## Wat absoluut niet veranderd mag worden

Dit is dezelfde basis voor elk project. Pas deze onderdelen niet aan in een project:

1. **De bibliotheekbestanden in `app/src/EDI/`:** `Parser.php`, `Reader.php`, `Analyser.php`, `Interpreter.php`, `Encoder.php` en `ReaderException.php`. Ze zijn identiek aan [php-edifact](https://github.com/php-edifact/edifact). Dat geldt ook voor waarden en opmerkingen. Het veranderen van deze bestanden heeft licentiegevolgen (LGPL) en je raakt updates van de maker kwijt. Heb je iets nodig dat ontbreekt? Bouw het in een eigen bestand ernaast.
2. **De originele tests en voorbeeldbestanden:** `app/tests/EDITest/*Test.php` (Analyser, Encoder, Interpreter, Parser, Reader) en alles in `app/tests/files/`.
3. **`app/LICENSE` en de copyrightregels** bovenin de bibliotheekbestanden.
4. **Uitgerolde Flyway-migraties:** pas een `V{n}__*.sql` nooit aan nadat die is uitgerold. Maak een nieuw bestand met het volgende nummer.
5. **Geheimen en afgeleide bestanden:** `.env`, `vendor/` en `composer.lock` horen niet in git.

### Wel vrij aanpassen
- `app/src/EDI/Storage/` en `app/tests/EDITest/Storage/` (onze eigen bestandslaag).
- `app/scheduler/` (de planning en de jobs).
- Alles wat je zelf toevoegt: eigen mappen onder `app/src/EDI/`, eigen tests, eigen migraties.
- Instellingen in `app/config/` en de Docker- en CI-bestanden.

## Hoe een project in de praktijk verloopt

1. **Start een project:** kopieer deze repo en zet hem op de eigen GitLab van het project.
2. **Stel in:** `cd app`, `composer install`, kopieer `.env.example` naar `.env` en vul de databasegegevens en paden in. Genereer de sleutel met `php artisan key:generate`.
3. **Bouw per flow een eigen map** onder `app/src/EDI/`, bijvoorbeeld `Orders/` of `Invoices/`:
   - een *mapper* die het gelezen bericht omzet naar de gegevens van het project,
   - een *processor* die bepaalt wat er met het bericht gebeurt (database, BC, antwoord).
4. **Gebruik de bibliotheek, kopieer hem niet:** `use EDI\Parser;`, `use EDI\Encoder;`, enzovoort.
5. **Maak per job een script** in `app/scheduler/jobs/` dat jouw klassen aanroept, en zet de planning in `scheduler.php`. De stappen van een flow krijgen een minuut ná de stap waarvan ze afhankelijk zijn (zoals in `le_duc_edi`).
6. **Database:** nieuwe wijziging = nieuw Flyway-bestand in `flyway/sql/`. Flyway draait lokaal via `compose.yaml` en op de servers via de CI.
7. **Test:** schrijf eigen tests in `app/tests/EDITest/` (in een eigen bestand of eigen map), niet in de originele testbestanden.
8. **Deploy:** merge request naar `development` of `main`. De CI zet het live (handmatige stap, na goedkeuring).

### Hoe een bericht door het systeem gaat
1. `Storage` haalt een bestand op van de SFTP-server en zet het in `new/`.
2. `Parser` (en `Interpreter`) lezen het bericht.
3. De mapper van het project zet het om, de processor verwerkt het.
4. `Storage` verplaatst het bestand naar `processed/` of `failed/`.

Uitgaand is het omgekeerd: de eigen code bouwt een array en `Encoder` maakt er EDIFACT van, waarna `Storage` het verstuurt.

## Draaien en testen

```
cd app
composer install
php artisan test
```

**Bekend:** twee tests in `AnalyserTest` (`testProcess` en `testProcessWrapped`) falen op dit moment. Ze verwachten een vaste uitvoerlengte en de nieuwste `edifact-mapping` geeft een andere. Dit zit in de originele tests, die niet worden aangepast.

## Herkomst en licentie

- **php-edifact** (`app/src/EDI/`, de originele tests en `edifact-mapping`) is van Stefano Sabatini, Uldis Nelsons en Emil Vikström, onder de **LGPL-3.0-or-later**. Bron: https://github.com/php-edifact/edifact. De licentietekst staat in `app/LICENSE`.
- **Eigen code** (`Storage/`, `scheduler/`, `flyway/`) is van 2makeitwork.
- Gebruik is intern (eigen servers). Gaat de code ooit naar een klant of wordt hij gepubliceerd, dan gelden de LGPL-plichten: licentietekst meegeven, de herkomst vermelden en de bibliotheekcode beschikbaar maken.

## Nog open
- De twee falende `AnalyserTest`-tests (zie hierboven).
- De webserver moet in Docker naar `app/public` wijzen (Laravel). Dat is nog niet ingesteld in `compose.yaml`.
- `app/composer.json` zegt dat het hele project LGPL is. Dat geldt alleen voor de bibliotheekdelen.
