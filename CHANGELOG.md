# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.0] - 2021-10-12

Version `0.2.0` includes a number of breaking changes, intended to make the library easier to use and maintain.

### Removed

- Removed the `setIndex()` method. It's better if you can't change a low-level component.
- Removed the `getName()` and `getText()` methods. You already have that information and probably don't need the library to echo it back.

### Changed

- Updated the library for PHP 7.4+.
- The `$text` and `$name` constructor arguments are now required for `Text` and `File` chunkers, respectively. You shouldn't be able to instantiate a chunker without either.
- Added a `$size` argument to chunker constructors (i.e., removed `setSize()` and `setEncoding()` methods; added `$size` argument). Once a chunker has started chunking, changing the encoding or chunk size will break it. It seemed like a good idea to help prevent you from accidentally making a mistake.
- Modernized the classes with property type hints, argument type hints, return type hints, guard clauses, etc.
- Changed `File` tests from using concrete files and folders to using a virtual file system with [bovigo/vfsStream](https://github.com/bovigo/vfsStream).
- Updated the README to better explain the problem this library solves.
- Removed lots and lots of unnecessary comments :).

### Added

- Added `ext-mbstring` requirement to `composer.json`.
- Added [slevomat/coding-standard](https://github.com/slevomat/coding-standard) to enforce coding standards.
- Added [roave/security-advisories](https://github.com/Roave/SecurityAdvisories) to exclude dependencies with known vulnerabilities.
- Added continuous integration with [CircleCI](https://circleci.com/gh/jstewmc/usps-address).
- Added code coverage analysis with [CodeCov](https://codecov.io/gh/jstewmc/usps-address).

## [0.1.0] - 2015-07-03

The initial release.
