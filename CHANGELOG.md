# Changelog

## [1.4.0] - 2018-02-26

v1.4.0 plans to add a View and Model object, and several other minor additions and bugfixes.

### Additions

### Fixes

- [Issue #71] - Controller::start() - Undefined $default404

## [1.3.0] - 2018-02-23

v1.3 plans to move the show() method from user-space to kernel-space, allowing developers to create lightweight Controller modules, as well as removing the need for check404() functions etc

### Additions

- [Issue #70] - Create Controller::start()
- [Issue #69] - Create Request::isEndpoint()
- [Issue #68] - Allow users to set default views for 404
- [Issue #67] - Create Controller::show()

## [1.2.3] - 2018-02-18

v1.2.3 Addresses documentation, cleaning up the tutorial, and making sure everything is up-to-date and in sync with the entire project.

### Documentation

- [Issue #66] Readme - Folder structure should be updated
- Updated year in LICENSE.md

## [1.2.2] - 2018-02-13

### Fixed

- [Issue #65] Request - filter_input does not properly retrieve variables on some shared hosting

## [1.2.1] - 2018-02-10

This update allows for multiple forms per view, MenuItems to get ".active" based
on broad match, and minor other bugfixes

### Fixed

 - [Issue #59] Form - Check which form submission
 - [Issue #60] MenuItem - Broad active match
 - [Issue #61] Form::isValid() - Input validation should be optional
 - [Issue #62] FormInput::show() should run `htmlspecialchars_decode`
 - [Issue #63] FormInput::show() should prepend an underscore in the label's `for` field


## [1.2.0] - 2018-02-02

### Added

- [Issue #57] Add Response::refresh()
- [Issue #56] Create Request::setPayloadKey()
- [Issue #45] Passing arguments to validators and filters
- [Issue #44] FormInput should have a hint
- [Issue #37] Controller should allow nesting

### Fixed

- [Issue #55] Form::isValid() should show form name instead of "This field"
- [Issue #54] (FormInput) Required check edge-case `0`
- [Issue #53] FormInput::isValid() should check $_FILES for empty
- [Issue #52] FormInput::isValid() should clean-up slug before output
- [Issue #51] FormInput::show() specific display for `file` type
- [Issue #50] FormInput::show() select inputs should cast to string
- [Issue #49] FormInput::show() should trim square brackets off ID attribute
- [Issue #48] FormInput::show() should not run `file` type inputs through htmlspecialchars()
- [Issue #47] FormInput::show should add valid state
- [Issue #46] FormInput::setValue() doesn't work
- [Issue #42] Form::isValid should hold $valid in object scope instead of method scope
- [Issue #40] Database::delete() foreach does not iterate $i
- [Issue #39] Database::update() foreach loop does not iterate $i
- [Issue #38] Controller::route() and show() should have default methods
- [Issue #36] Check camel case/snake case members

### Changed

- [Issue #43] FormInput::show() should not display line breaks


## [1.1.0] - 2018-01-23

### Added
 - [Issue #34] Created Database::cleanTable() to sanitize table names

### Fixed
 - [Issue #35] Controller protected members must be public

## [1.0.0] - 2018-01-17
### Breaking Changes
 - Form member variables have changed for cleanliness.  Please check the Form reference.
 - Form constructor parameters have been replaced with a single associative array parameter.
 - FormInput member variables have changed for cleanliness.  Please check the FormInput reference.
 - FormInput constructor parameters have been replaced with a single associative array parameter.
 - MenuInput member variables have changed for cleanliness.  Please check the FormInput reference.
 - MenuInput constructor parameters have been replaced with 3 parameters - label, href, and a data array.
 - Session attribute for pepper length has changed from `pepperLength` to `pepper_length` for comformity.
 - 

### Changed
 - Cleaned up Form::isValid() to be more accurate, clean, and concise.
 - Cleaned up FormInput methods for performance and cleaner output.
 - Menu constructor now checks for falsiness of arguments before overriding object members.
 - Changed Menu class to have a default attribute of "class" => "nav"
 - Cleaned up MenuItem markup generated.
 - Response::header() will now throw an error if it does not recognize the response code argument.
 - Changed default Session name to "session".
 - Removed tests that fail due soley to PHPUnit, in order to achieve clean testing output.
 - [Issue #28] Updated coding and documentation style

### Added
 - Controller class
 - Database::nRowsWhere() to select a count(*) including a sql WHERE statement.
 - Form and FormInput validate and filter functionality.
 - Form::onInvalidInput(), Form::onInvalidForm().
 - Form `id` markup parameter, to allow the form to be targeted in script or style.
 - FormInput::setValue() to set a form input's value after submission.
 - FormInput description, placeholder, validate, and filter attributes.
 - FormInput "html" attribute, to override a field's html output.
 - Layout class
 - Added MenuItem `submenu` attribute to properly allow nested menus
 - Added MenuItem `link_attributes` attribute to allow more flexibity to menu links.

### Fixed
 - Fixed a bug where Session::__construct() would leave an attribute array in $cipherKey.
 - Changed "inval" to "intval" in FormInput.php ln 255 col 45
 - [Issue #31] Database methods break on single where payload
 - [Issue #30] Database::nRows returns wrong value
 - [Issue #29] Doxygen input folder outdated
 - [Issue #27] Broken Github Links

## [0.3.2] - 2017-12-24
### Changed
 - [Issue #23] Composer license
 - [Issue #25] Composer Package

## [0.3.1] - 2017-12-19
### Added
 - [Issue #21] Create license and code of conduct
 
### Changed
 - [Issue #16] Long constructors should accept array of parameters
 - [Issue #18] MenuIcon::show() should remove font awesome class for upwards compatability
 - [Issue #19] MenuItem::show() should allow for nested menus

### Fixed
 - [Issue #15] MenuItem should append active class before output
 - [Issue #17] Menu::show() closes the `<ul>` tag after outputting menu items

## [0.3.0] - 2017-12-7
### Added
 - [Issue #13] FormInput should allow for removing of line breaks
 - [Issue #14] Missing clean script

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
