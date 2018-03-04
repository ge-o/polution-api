Polution API for Cologne
------------------------

This API provides polution data from LANUV (Landesamt für Natur, Umwelt und Verbraucherschutz) for the 4 measure stations in Cologne. 

The value are available for every hour for the 4 stations the polution NO2.

You can find the original data under: https://www.lanuv.nrw.de/umwelt/luft/immissionen/berichte-und-trends/einzelwerte-kontinuierlicher-messungen/

I buildt this at the hackathon of the OpenData Day Cologne 2018. 

## Whats next

- Enhance the import to other polution types
- Enhance data for other stations in NRW

## Installation

for local development just start the Vagrant machine by

```
vagrant up
```

### Setup local

```
vagrant ssh
cp .env.dist .env
composer install
bin/console doctrine:database:create
bin/console doctrine:migration:migrate
```

Get Data

```
# Setup stations
bin/console app:station

# get Data
bin/console app:import
```

### Setup production

Directory `/public` must be the document root

```
vagrant ssh
cp .env.dist .env
# enter live data in .env
composer install
bin/console doctrine:database:create
bin/console doctrine:migration:migrate
```

```
# Setup stations
bin/console app:station

# get Data
bin/console app:import
```
Setup `bin/console app:import` as a cronjob