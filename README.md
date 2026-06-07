# bitski-wp-cf7-booking

[![Version](https://img.shields.io/github/v/release/bitski/bitski-wp-cf7-booking?sort=semver)](https://github.com/bitski/bitski-wp-cf7-booking/releases)
[![License](https://img.shields.io/github/license/bitski/bitski-wp-cf7-booking)](LICENSE)
[![WordPress](https://img.shields.io/badge/WordPress-6.5%2B-blue)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-red)](https://www.php.net)

OOP-driven, modular WordPress plugin with PSR-4 autoloading and REST integration layer.

v0.1.2 | GPL v3+

## Disclaimer

This plugin is provided "as is", without warranty of any kind, either expressed or implied. The author is not
liable for any damages arising from its use. Use at your own risk.

## ✨ Features

- PHP OOP (PSR-4) + Composer autoload (`src/`): clear separation of Core, Admin, REST, Assets, and Templates.
- Modular Core Architecture: Lifecycle, Hooks, Setup, Config, Options – fully extendable.
- Admin & Settings Ready: minimal UI, Settings API, logic separated from presentation.
- REST API Layer: easy endpoint integration + standardized JSON responses.
- Assets Loader: conditional enqueue for Admin & Frontend.
- Template-ready (`templates/`): clean separation of PHP logic and templates.

## 🚀 Quick start

### Clone and install

```bash
cd wp-content/plugins
git clone https://github.com/bitski/bitski-wp-cf7-booking.git
cd bitski-wp-cf7-booking
composer install  # PSR-4 autoloader (autoloads src/ as BitskiWPCF7Booking\)
```

### Activate

WordPress → Plugins → Activate

### Quality checks

vendor/bin/phpcs

## 📋 Requirements

### Server

- PHP 8.1+ (tested up to PHP 8.4)
- WordPress 6.5+
- MySQL 5.7+ / MariaDB 10.4+

### Development

- Composer 2.7+
- PHPCS 3.8+ (phpcs.xml inklusive, PSR-12)

## 📁 Structure

### Entry points

PHP: `src/` (PSR-4 namespace: BitskiWPCF7Booking\*) → `src/core/` (Initialization order: Config → Options → Setup → Lifecycle → Hooks)

Bootstrap: bitski-wp-cf7-booking.php → bootstrap.php

### Directory structure

```
├── src/                  # PHP PSR-4 Namespace: BitskiWPCF7Booking\* (subfolders = sub-namespaces)
│   ├── admin/            # Admin pages, settings, notices
│   ├── assets/           # Assets loader: Enqueues, conditional loading
│   ├── core/             # Core plugin classes – Initialization order: Config → Options → Setup → Lifecycle → Hooks
│   ├── integration/      # Third-party integrations / adapters
│   └── rest/             # REST API endpoints
├── templates/            # Admin / frontend template partials
├── composer.json         # Composer + PSR-4 autoload configuration
├── bootstrap.php         # Internal plugin bootstrap
└── bitski-wp-cf7-booking.php # WordPress plugin entry point
```

## 👤 Author

Peter Eckerle (bitski.de)  
[Website](https://bitski.de) | [GitHub](https://github.com/bitski)
