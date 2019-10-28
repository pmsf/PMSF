# PMSF-ALT--PokeMap-Standalone-Frontend

This fork is different is so many ways that its impossible to name them all. Main key features are Manual submissions, support for RDM and MAD backends. 

> Current Version 2.0 - Second release! with OpenStreetMap engine

PokeMap Standalone Frontend or PMSF for short is a PHP Map Interface for the RDM Scanner designed to be completely standalone and able to run on any traditional web server. Manual submissions are supported on a Monocle Hydro base database with small additions, have a look at the cleandb.sql and sql.sql for changes.

It supports all the common database engines, including MySQL, MariaDB
A special Database structure based on Hydro Monocle is needed.

## Get Started
Join our [Discord](https://discord.gg/yGujp8D) channel for more info about installation.

## Create your own Sprite Repository
Image naming convention `pokemon_icon_{XXX}_{YY}.png`
Where XXX is pokemon id 001 - 807
Where YY is pokemon form: 00 is normal

## Backend settings

* PMSF manual
```
$map = "monocle";
$fork = "pmsf";
```

* RDM Real device map
```
$map = "rdm";
$fork = "default" OR $fork = "beta";
```

* MAD Map a Droid 
```
$map = "rocketmap";
$fork = "mad";
```

## Webhooks
Current tested support for
* [PokeAlarm](https://github.com/PokeAlarm/PokeAlarm)
* [PoracleJS](https://github.com/KartulUdus/PoracleJS)

## Feedback
* Create an issue if you have any bugs, suggestions or improvements!

* [Discord](https://discord.gg/yGujp8D) channel

## Thanks
* Thanks [MAD](https://github.com/Map-A-Droid/MAD) for their Real android Device scanner.

* Thanks [RDM](https://github.com/123FLO321/RealDeviceMap) for their Real iPhone Device scanner.

* Thanks [PMSF](https://github.com/Glennmen/PMSF) for the basis for this fork.

* Thanks [MSF](https://github.com/Nuro/MSF) for the great basis for this project.

* Noctem for the awesome [Monocle Scanner](https://github.com/Noctem/Monocle)!

* [RocketMap](https://github.com/RocketMap/RocketMap) for their scanner.

* [Medoo](http://medoo.in) for the ORM framework.
