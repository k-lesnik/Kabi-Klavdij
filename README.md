# Laravel FURS Sync Aplikacija

## Zahteve

Projekt uporablja **PHP 8.3** in zahteva, da ima PHP omogočeno **PDO SQLite** razširitev, saj je ta potrebna za delovanje Laravel frameworka. V `php.ini` mora biti omogočena vrstica:

```ini
extension=pdo_sqlite
```

Po želji lahko uporabite tudi drugo vrsto baze (npr. MySQL ali PostgreSQL). To lahko prilagodite v `.env` datoteki.

## Opis aplikacije

Aplikacija omogoča sinhronizacijo podatkov iz FURS-ovega strežnika, njihovo obdelavo in prikaz v spletni aplikaciji.

### Scheduler in ukaz za sinhronizacijo

Scheduler je nastavljen v `app/Console/Kernel.php` tako, da se sinhronizacija avtomatsko sproži vsak dan ob polnoči (cron-like funkcionalnost). Ker Laravel scheduler lokalno brez dodatne nastavitve ne deluje, lahko sinhronizacijo poženete ročno z ukazom:

```sh
php artisan app:sync-furs
```

Ukaz se nahaja v `app/Console/Commands/SyncFurs.php` in kliče `sync` metodo v `FursController` (`app/Http/Controllers/FursController.php`).

### FursController

V `FursController` se nahajata dve glavni metodi:

- `index()`: prebere lokalno `.txt` datoteko in vrne pogled s podatki ali z napako, če datoteke ni.
- `sync()`: sproži prenos ZIP datoteke s FURS strežnika, jo razširi in shrani podatke. Večina logike je prenesena v servisno plast (`Services/HandleFursDataService.php`), v skladu z načelom minimalne logike v kontrolerjih.

### HandleFursDataService

V tem servisu se izvaja:

- prenos ZIP datoteke iz FURS strežnika,
- razširjanje arhiva,
- zapis podatkov v lokalno `.txt` datoteko.

Zapis podatkov poteka preko **queue-ov**, kar omogoča obdelavo v manjših sklopih (trenutno po 100 vrstic), s čimer se zmanjša poraba sistemskih virov. Za to je zadolžen `Jobs/WriteFursDataToFile.php`.

### Prikaz podatkov

Podatki se prikazujejo v tabeli prek Blade predloge, ki se nahaja v `resources/views/furs/index.blade.php`. Videz izpisa ni bil prioriteten, saj po navodilih ni bil del ocenjevanja.

## Pomanjkljivosti in možnosti izboljšav

- Laravel je zasnovan okoli uporabe modelov in baz podatkov. Trenutno se podatki zapisujejo v `.txt` datoteko, kar ni optimalno.
- Če bi uporabili bazo, bi lahko implementirali paginacijo, boljšo obdelavo in iskanje po podatkih.
- Čeprav navodila ne dovolijo uporabo baze, jo Laravel vseeno potrebuje za osnovno delovanje, zato sem jo rabil vključiti v nalogo. Upam da bo vseeno v redu.
- Možna nadaljnja optimizacija bi bila ločitev prenosa ZIP datoteke in zapisa podatkov — npr. prenos ob 23:30, zapis ob 00:00.

## Namestitev projekta

1. V korensko mapo projekta kopirajte `.env` datoteko, ki sem vam jo poslal po e-pošti.
2. Poženite naslednje ukaze:

```sh
composer install
php artisan migrate
php artisan serve
php artisan queue:listen
php artisan app:sync-furs
```