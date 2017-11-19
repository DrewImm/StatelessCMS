# Changelog

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