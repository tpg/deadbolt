# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v2.3.0 - 2025-04-13

- [Bug] fix two missing methods identified in #31. Methods were previously removed, but documentation was not updated. Methods have been returned.
- [Improvement] Added support for PHP 8.4 and Laravel 12.
- [Improvement] Updated NPM dependencies to address potential vulnerabilities
- [Change] Removed `styleci` in favour of Laravel Pint.

## v2.2.5 - 2022-11-29

This is a really, really, really tiny release, just to update the JS dependencies.

- Updated `typescript` dependency to ^4.9.0.
- Updated `mocha` dependency to ^10.1.0.

## [2.2.4] 2022-06-07

### Added

- Added a `has` method to `UserCollection` which is really just an alias of `anyHave`.

## [2.2.3] 2022-06-06

### Added

- Added a `describe` method to the `User` class to get user permissions with descriptions.

## [2.2.2] 2022-01-27

### Changed

- Changed dependency version limits to support later versions of Laravel.

## [2.2.1] 2022-01-25

### Fixed

- Changed dependencies to get around the security vulnerability in nanoid.

## [2.2.0] 2021-11-04

### Changed

- Slight breaking change in the JavaScript helper. The constructor will now only accept a permissions array or JSON encoded string.
- You don't need to pass a whole user object into the `Deadbolt()` constructor.

## [2.1.0] 2021-09-01

### Added

- Added a simple JavaScript library to aid in using permissions on the front-end (ESM).
- Cleaned up some PHP tests.
- Added a basic MochaJS test for the new JavaScript class.
- Updated the README with info about the JavaScript implementation.
- [fixed] A bug that would cause an exception when using the `HasPermissions` trait.

## [2.0.2] 2021-08-18

### Fixed

- Made a change to `composer.json` causing problems with the `Deadbolt` alias. #22

## [2.0.1] 2021-08-11

### Changed

- The `permissions` column in the default migration is now nullable by default.

## [2.0.0] 2021-07-16

### Removed

- The entire groups system has been removed. Deadbolt is supposed to be simple, but groups makes it more complex than it needs to be. Going back to basics for version 2.

### Changed

- The `save()` method no longer needs to be called when persisting permission changes.

## [1.1.1] 2021-02-25

### Changed

- Some documentation clean up.
- Made some changes to `composer.json` dependencies.
- Removed the old `travis.yml` config.

## [1.1.0] 2020-12-04

### Added

- The `groups()` method can now take a single boolean value to include the permission descriptions keyed by the permission names.

## [1.0.0] 2020-08-20

### Changed

- Version 1.0 release. Just a version bump.

## [0.3.1] 2020-03-09

### Changed

- Added strict type declaration.

## [0.3.0] 2020-03-09

### Added

- Can now work with multiple users at the same time.
- Added a `users` method to the `DeadboltService` which accepts an iterable collection or array of users.
- Added a `UserCollection` class to represent multiple users.
- There are a few collection specific methods available for testing permissions.
- Permissions can now have descriptions in the default config file.

### Changed

- Renamed the `roles` to `groups` as they're not actually roles.
- Updated `README` documentation and linked to two articles in the wiki.

## [0.2.7] 2020-03-06

### Added

- Added a new `sync` method to the `Deadbolt\User` class which will sync the provided permissions with the user. Any current permissions are revoked.
- Readme updated to include the `sync` documentation.

## [0.2.6] 2020-03-04

### Changed

- The `Deadbolt\Deadbolt` class has been renamed to `Deadbolt\DeadboltService` to avoid confusion with IDE auto-completion as `Deadbolt\Facades\Deadbolt` has the same class name.

## [0.2.5] 2020-03-04

### Changed

- Updated dependencies to support Laravel 7.
- Fixed a bug in the `User::saved()` method that was not pulling the column name correctly.
- Fixed a bug in the `User::saved()` method that was causing problems with cast permissions columns.

## [0.2.4] 2020-03-03

### Changed

- Fixed a bug that was not assigning permissions correctly when the attribute was cast to `json`.
- Fixed a bug that was not using the `column` config option correctly.
- Renamed the `User::mergePermissions` method to `User::assignPermissions`.

## [0.2.3] 2020-03-02

### Changed

- Fixed a bug that was not sourcing roles from the supplied driver correctly.

## [0.2.2] 2020-02-27

### Changed

- Fixed a bug that was causing problems if the `permissions` attribute is cast to `json`.

## [0.2.1] 2020-02-27

### Added

- Updated the `Deadbolt::permissions()` to accept role names.

## [0.2] 2020-02-26

### Additions

- New driver based permissions and roles source

## [0.1] 2020-02-25

- New Facade based API. Keeps things cleaner.
