# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Ongoing maintenance and internal improvements

## [1.0.0] - 2026-05-29

### Added

- PHP OOP (PSR-4 autoloading) with modular `src/` namespace structure
- Core architecture: Config, Setup, Hooks, Lifecycle, Options
- Admin settings page skeleton with Settings API integration
- REST API layer with example `/health` endpoint
- Assets loader with conditional Admin/Frontend enqueue support
- Integration layer for third-party plugin adapters
- Template-ready structure (`templates/`)

### Changed

- README.md restructured for public OSS release
- Bootstrap flow and internal architecture stabilized

### Fixed

- PHPCS / PSR-12 compliance improvements
- Security review: sanitization, escaping, capability checks

### Security

- REST permission callback baseline
- WordPress capability checks for Admin access
