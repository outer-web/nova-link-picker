# Changelog

All notable changes to `nova-link-picker` will be documented in this file.

## 1.1.0 - 2020-12-10

### Added

- Added `entity_class` in the config file to allow for easy overwriting of the entity class.
- Added `resolveRoute` method to the `Link` class to allow for easy overwriting if you do not want to use the built-in Laravel route helper.

### Changed

- Made internal methods of the `Link` class usage private to make the class clearer.

## 1.0.0 - 2020-12-09

- Initial release
