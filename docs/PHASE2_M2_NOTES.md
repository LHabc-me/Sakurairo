# Phase2 M2 - Legacy Field Bridge

This PR introduces the M2 bridge layer for remaining unmapped CSF options.

## What changed

- Added `inc/customizer-migrated-fields.php` (auto-generated key list) for all previously unmapped CSF keys from `docs/PHASE2_FIELD_MAPPING.md`.
- Added a generated Customizer section: `iro_global / iro_legacy_migrated_options`.
- For each unmapped key, registers a fallback control (`legacy_<key>`) with `iro_key=<key>` so values are saved back into the unified options store through existing save hooks.
- Added JSON-or-text sanitize callback for migrated complex fields.
- Added safer default conversion for array values on text-like controls.
- Updated `docs/PHASE2_FIELD_MAPPING.md` entries from `M2+ TBD` to `M2` mapped fallback controls.

## Compatibility

- Legacy options menu rollback switch remains unchanged.
- Existing mapped controls are untouched.
- Save path is still `IRO_OPTIONS_KEY` via `customize_save_after` merge logic.
