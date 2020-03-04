# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.5] 04-03-2020
### Changes
- Updated dependencies to support Laravel 7.

## [0.2.4] 03-03-2020
### Changes
- Fixed a bug that was not assigning permissions correctly when the attribute was cast to `json`.
- Fixed a bug that was not using the `column` config option correctly.
- Renamed the `User::mergePermissions` method to `User::assignPermissions`.

## [0.2.3] 02-03-2020
### Changes
- Fixed a bug that was not sourcing roles from the supplied driver correctly.

## [0.2.2] 27-02-2020
### Changes
- Fixed a bug that was causing problems if the `permissions` attribute is cast to `json`.

## [0.2.1] 27-02-2020
### Additions
- Updated the `Deadbolt::permissions()` to accept role names.

## [0.2] 26-02-2020
### Additions
- New driver based permissions and roles source
    
## [0.1] 25-02-2020
- New Facade based API. Keeps things cleaner.
