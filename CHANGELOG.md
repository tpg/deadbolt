# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
## Removed
- The entire groups system has been removed. Deadbolt is supposed to be simple, but groups makes it more complex than it needs to be. Going back to basics for version 2.

## [1.1.1] 2021-02-25
## Changed
- Some documentation clean up.
- Made some changes to `composer.json` dependencies.
- Removed the old `travis.yml` config.

## [1.1.0] 2020-12-04
## Added
- The `groups()` method can now take a single boolean value to include the permission descriptions keyed by the permission names.

## [1.0.0] 2020-08-20
## Changed
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
