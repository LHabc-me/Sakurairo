# Phase2 M6 - Independent by Default

## Goal

Make Shinonomeiro runtime **fully independent by default**:

- Default read/write store: `shinonomeiro_options`
- Default Customizer preview store: `IRO_OPTIONS_THEME_MOD_KEY` (`shinonomeiro_options`)
- No automatic fallback read from legacy `iro_options`

## What changed

1. Removed automatic runtime fallback from `iro_options` in `iro_get_options_store()`.
2. Added explicit one-time migration entrypoints:
   - **Constant trigger (CLI/config friendly)**: set `IRO_ENABLE_LEGACY_IMPORT` to `true`, then load the theme once.
   - **Admin trigger**: visit
     `/wp-admin/?iro_migrate_legacy_options=1&_wpnonce=<nonce>`
     (nonce action: `iro_migrate_legacy_options`, admin only)
3. Migration writes merged data into `shinonomeiro_options` and updates theme mod store under `IRO_OPTIONS_THEME_MOD_KEY`.
4. Recorded migration marker option: `iro_legacy_import_done`.

## Migration steps (recommended)

1. Backup DB.
2. Enable one trigger (constant or admin action).
3. Run migration once.
4. Verify values in Customizer and front-end.
5. Remove trigger.

## Rollback

If migration causes issues:

1. Restore DB backup, or
2. Overwrite `shinonomeiro_options` with known-good snapshot, and
3. Remove `IRO_ENABLE_LEGACY_IMPORT` trigger / stop using admin migration URL.

## Independence verification

- Change a value in legacy `iro_options` only (without re-running migration).
- Confirm front-end and Customizer values in Shinonomeiro do **not** change.
- Change value in `shinonomeiro_options` and confirm Shinonomeiro reflects it.
