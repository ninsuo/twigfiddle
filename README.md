twigfiddle
==========

twigfiddle.com provides a small development environment to develop, run, store and access Twig code online.


# Installation

```sh
cd cli
composer update
cd twig
mkdir uncompressed
sh prepare.sh

cd web
composer update
echo 'create database twigfiddle' | mysql -u root
php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create
php app/console twigfiddle:import ../samples/*
```
