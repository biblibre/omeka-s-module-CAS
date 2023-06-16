# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.6.0] - 2023-06-16

This version adds compatibility with Omeka S 4. The minimum Omeka S version
required is still 3.0.0

## [0.5.0] - 2022-12-08

### Changed

- Added `redirect_url` query parameter to navigation link so that users are
  redirected back to the same page after a successful login

## [0.4.2] - 2022-06-23

### Fixed

- Fixed URL building when `cas_url` setting have leading space (#1)

## [0.4.1] - 2022-05-09

### Fixed

- Fixed 'service' parameter sent when validating the ticket

## [0.4.0] - 2022-04-29

### Added

- Added ability to pass a 'gateway' parameter to /cas/login that will be passed
  to the CAS Server login URL

### Fixed

- Search user by email before trying to create it (and fail because email should be unique)
- Add login link to user bar using javascript to make it work when other
  modules override the user bar template

## [0.3.0] - 2022-04-11

### Added

- Allow to use a CAS attribute as identifier instead of the 'user' in the CAS
  response
- Allow to use CAS attributes as name and email when creating a new Omeka S
  user account
- Add site navigation link
- Add option to show CAS login link in the user bar

## [0.2.0] - 2021-07-02
### BREAKING CHANGES

- CAS module v0.2.0 is no longer compatible with Omeka S 2.x

### Added

- Added compatibility with Omeka S 3.x


## [0.1.0] - 2021-07-02

Initial release

[0.6.0]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.6.0
[0.5.0]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.5.0
[0.4.2]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.4.2
[0.4.1]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.4.1
[0.4.0]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.4.0
[0.3.0]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.3.0
[0.2.0]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.2.0
[0.1.0]: https://github.com/biblibre/omeka-s-module-CAS/releases/tag/v0.1.0
