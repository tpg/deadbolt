[![Run Tests](https://github.com/tpg/deadbolt/actions/workflows/php.yml/badge.svg?branch=2.x)](https://github.com/tpg/deadbolt/actions/workflows/php.yml)

# Dead simple user permissions for Laravel

> Version 2 brings a number of changes to the library, but it's not ready for use yet. This document is by no means a final version and will likely change before version 2 is finally released.

## Why another authorization package?

There are plenty of authorization packages around. But I wanted something that was way simpler than what was on offer and something that I could use easily with my current stack which includes Vue and Inertia. I've used many of the current top authorization packages, and will continue to use them in the future, but in some cases they're just a little over the top.

Deadbolt is "dead" simple. you define your permissions in the config file (just so you have some source of truth), and you can assign them to your users. The only required database change is a `permissions` column on your `users` table. No need for any additional migrations or complicated configurations.

Deadbolt is simple by design. If you need something more feature rich, there are plenty of other choices. If deadbolt doesn't fit the bill, then my go to package is Spatie's [laravel-permission](https://github.com/spatie/laravel-permission) package.

## Installation

Deadbolt can be installed via Composer:

```
composer require thepublicgood/deadbolt=2.x-dev
```

## Getting Started

Deadbolt works by setting permissions in a JSON array in a column on your users table. So before you can use Deadbolt, you'll need to add that column. Deadbolt comes with a simple Artisan command that will do this for you:

```
php ./artisan deadbolt:install
```

This will do two things...

1. Create a new migration named `add_deadbolt_permissions_column`,
2. Place a copy of the Deadbolt config at `config/permissions.php`.

You can alter the migration if your table names are different, but this will add a column named `permissions` to your `users` table.

You can now go ahead and define your permissions in the `permissions.php` config file. That's all you need to do to get Deadbolt installed.
