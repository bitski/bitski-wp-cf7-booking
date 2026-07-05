# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Ongoing maintenance and internal improvements

## [0.4.0] - 2026-07-05

### Added

- CapacityManager for reservation capacity checks in the domain layer
- Integration test coverage for multiple overlapping reservations

## [0.3.0] - 2026-06-24

### Added

- ReservationRepository infrastructure foundation
- Reservations database table lifecycle
- Manual dependency wiring between BookingService and ReservationRepository
- Reservation persistence through BookingService
- `save()` method for storing reservations
- `findOverlappingReservations()` method for detecting reservation overlaps
- Database index on `start_at` for reservation lookup performance
- PHPUnit integration test setup and bootstrap
- ReservationRepository integration test infrastructure

### Fixed

- Corrected WordPress bootstrap path for integration tests
- Corrected `InvalidArgumentException` import in CF7Adapter


## [0.2.0] - 2026-06-08

### Added

- Reservation domain entity with core properties:
  `name`, `phone`, `email`, `startAt`, `durationMinutes`, `guestCount`, `message`
- Domain layer foundation for BookingService integration (Phase 2.1 + 2.2

## [0.1.0] - 2026-05-29

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
