# Facebook Anonymous Publisher Firewall

[![Build Status](https://travis-ci.org/Facebook-Anonymous-Publisher/firewall.svg?branch=master)](https://travis-ci.org/Facebook-Anonymous-Publisher/firewall)
[![Test Coverage](https://codeclimate.com/github/Facebook-Anonymous-Publisher/firewall/badges/coverage.svg)](https://codeclimate.com/github/Facebook-Anonymous-Publisher/firewall/coverage)
[![Code Climate](https://codeclimate.com/github/Facebook-Anonymous-Publisher/firewall/badges/gpa.svg)](https://codeclimate.com/github/Facebook-Anonymous-Publisher/firewall)
[![StyleCI](https://styleci.io/repos/71771462/shield)](https://styleci.io/repos/71771462)
[![Latest Stable Version](https://poser.pugx.org/facebook-anonymous-publisher/firewall/v/stable?format=flat-square)](https://packagist.org/packages/facebook-anonymous-publisher/firewall)
[![Total Downloads](https://poser.pugx.org/facebook-anonymous-publisher/firewall/downloads?format=flat-square)](https://packagist.org/packages/facebook-anonymous-publisher/firewall)
[![License](https://poser.pugx.org/facebook-anonymous-publisher/firewall/license?format=flat-square)](https://packagist.org/packages/facebook-anonymous-publisher/firewall)

This product includes GeoLite2 data created by MaxMind, available from http://www.maxmind.com.

## Install

Install using composer

```bash
composer require facebook-anonymous-publisher/firewall
```

Add the service provider in `config/app.php`

```php
FacebookAnonymousPublisher\Firewall\FirewallServiceProvider::class,
```

Run the database migrations

```bash
php artisan migrate
```

## CHANGELOG

Please see [CHANGELOG](CHANGELOG.md) for details.

## UPGRADE

Please see [UPGRADE](UPGRADE.md) for details.
