# Welcome

This is the project's repository, come here if you found a bug, if you want to request new features or if you want to contribute.

If you need help to use twigfiddle, please read the twigfiddle's [help page](https://twigfiddle.com/about) or
ask a question on the dedicated [mailing list](https://groups.google.com/forum/#!forum/twigfiddle).

# Some words about the project

The project is made of 2 applications:

- cli directory contains the fiddle runner, an application built using Symfony components to execute a fiddle.

- web directory contains the web application, built using the Symfony framework

About the other directories:

- resources directory contains specification documentations, logo source and original integrated design.

- samples contains exported fiddles (see app/console custom commands)

- environment is the default directory containing fiddles at runtime

- debug is the default directory where crashed fiddles are stored

# Installation

Here are instructions to get started with the application.
Of course, replace paths to fit with your own environment.

```sh
# Clone the project
sudo mkdir -p /fuz/twigfiddle.com
sudo chown www-data:www-data /fuz/twigfiddle.com
sudo su www-data
git clone https://github.com/ninsuo/twigfiddle.git ./

# install Composer
php -r "readfile('https://getcomposer.org/installer');" | php
sudo mv composer.phar /usr/bin/composer
sudo chmod 755 /usr/bin/composer

# Install the fiddle runner and prepare all twig versions
cd cli
composer install
cd twig
sh prepare.sh
cd ../../

# Install the web application
# composer update will fail when trying to remove apc cache, that's normal at this step
cd web
composer install

# Install the database
# You should create it yourself, from mysql, type:
# CREATE DATABASE twigfiddle
# GRANT ALL PRIVILEGES ON twigfiddle.* To '<user>'@'127.0.0.1' IDENTIFIED BY '<password>';
php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create
php app/console twigfiddle:import ../samples/*

# Check that everything is working properly
composer update
php app/check.php
phpunit tests

# Launch the application
php app/console server:start
```

# Automatically download, install and configure new Twig releases

Simply run the following command:

```sh
php cli/run-prod.php twigfiddle:release:watcher
```

# Upgrade for installs before 01/11/2016

User provider have been refactored ( see #18 ), you need to run the following queries to upgrade twigfiddle schema.

```sql
alter table user add column nickname varchar(255) not null after username;
update user set nickname = username;
update user set username = concat('["', resource_owner, '","', resource_owner_id, '"]');
```

# Configure external services

Don't panic, that's optional.

- Google Login: https://console.developers.google.com/project
- Facebook Login: https://developers.facebook.com/apps/
- Twitter Login: https://apps.twitter.com/
- SensioLabs Connect Login: https://connect.sensiolabs.com/account/apps
- GitHub Login: https://github.com/settings/developers
- reCaptcha: https://www.google.com/recaptcha/admin
- Google Analytics: https://www.google.com/analytics/web/
