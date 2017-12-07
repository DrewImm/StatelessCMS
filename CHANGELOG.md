# Changelog

## [0.2.1] - 2017-12-7
### Changed
 - [Issue #10] Menu constructor default parameters

### Fixed
 - [Issue #11] Menu::show() ul tag is missing closing carot
 - [Issue #12] MenuItem::show() never closes list item

## [0.2.0] - 2017-11-26
### Added
 - Composer/Packagist support

### Changed
 - Readme examples now show directory in vendor directory
 - Updated build script to new directory structure

### Fixed
 - Removed old composer files from build
 - [Issue #4] Remove session_start()
 - [Issue #6] Autoloader loads wrong directory
 - [Issue #7] Response::header() results in 500 error
 - [Issue #8] Request::getPath() Trailing Slash
 - [Issue #9] FormInput->show() should encode htmlspecialchars before displaying value

## [0.1.1] - 2017-11-19
### Changed
 - Build directory structure
 - Readme to reflect new build directory structure

### Removed
 - install.sh
 - Composer (was used for phpunit)

### Fixed
 - Examples in README.md

## [0.1.0] - 2017-11-18
### Added
 - Response singleton
 - Response::header() sends headers by status code, i.e. `404` = "404 Not Found"
 - Response::redirect() sends a `Location:` header to a specified url and exits
 - Response::sendToken() sends a Javascript Web Token along with the response body
 - CONTRIBUTING.md
 - README.md
 - Restyled changelog according to http://keepachangelog.com/en/1.0.0/

## [0.0.3] - 2017-11-06
### Added
 - Default empty value for database prefix.

## [0.0.2] - 2017-11-06
### Added
 - FormInput html type, to allow for plain html as form inputs

## [0.0.1] - 2017-11-05
### Added
 - Autoloader
 - Initial StatelessCMS Port