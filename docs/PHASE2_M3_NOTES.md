# Phase2 M3 - Compatibility Hardening

## Changes

- Customizer preview sync in `header.php` now uses `IRO_OPTIONS_THEME_MOD_KEY` instead of hardcoded `iro_options`.
- Added filter gate for the generated fallback section:
  - `shinonomeiro_enable_migrated_legacy_section` (default `true`)

## Why

- Prevent key mismatch during preview/writeback.
- Preserve rollback safety by allowing operators to disable generated migrated section quickly via filter.

## Rollback switches retained

- `shinonomeiro_enable_legacy_options_menu`
- `SHINONOMEIRO_ENABLE_LEGACY_OPTIONS_MENU`
- `shinonomeiro_enable_migrated_legacy_section`
