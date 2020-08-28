# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
## Added
- The `groups()` method can now take a single boolean value to include the permission descriptions keyed by the permission names.

## [1.0.0] 20-08-2020
## Changed
- Version 1.0 release. Just a version bump.

## [0.3.1] 09-03-2020
### Changed
- Added strict type declaration.

## [0.3.0] 09-03-2020
### Added
- Can now work with multiple users at the same time.
- Added a `users` method to the `DeadboltService` which accepts an iterable collection or array of users.
- Added a `UserCollection` class to represent multiple users.
- There are a few collection specific methods available for testing permissions.
- Permissions can now have descriptions in the default config file.

### Changed
- Renamed the `roles` to `groups` as they're not actually roles.
- Updated `README` documentation and linked to two articles in the wiki.

## [0.2.7] 06-03-2020
### Added
- Added a new `sync` method to the `Deadbolt\User` class which will sync the provided permissions with the user. Any current permissions are revoked.
- Readme updated to include the `sync` documentation.

## [0.2.6] 04-03-2020
### Changed
- The `Deadbolt\Deadbolt` class has been renamed to `Deadbolt\DeadboltService` to avoid confusion with IDE auto-completion as `Deadbolt\Facades\Deadbolt` has the same class name. 

## [0.2.5] 04-03-2020
### Changed
- Updated dependencies to support Laravel 7.
- Fixed a bug in the `User::saved()` method that was not pulling the column name correctly.
- Fixed a bug in the `User::saved()` method that was causing problems with cast permissions columns.

## [0.2.4] 03-03-2020
### Changed
- Fixed a bug that was not assigning permissions correctly when the attribute was cast to `json`.
- Fixed a bug that was not using the `column` config option correctly.
- Renamed the `User::mergePermissions` method to `User::assignPermissions`.

## [0.2.3] 02-03-2020
### Changed
- Fixed a bug that was not sourcing roles from the supplied driver correctly.

## [0.2.2] 27-02-2020
### Changed
- Fixed a bug that was causing problems if the `permissions` attribute is cast to `json`.

## [0.2.1] 27-02-2020
### Added
- Updated the `Deadbolt::permissions()` to accept role names.

## [0.2] 26-02-2020
### Additions
- New driver based permissions and roles source
    
## [0.1] 25-02-2020
- New Facade based API. Keeps things cleaner.
