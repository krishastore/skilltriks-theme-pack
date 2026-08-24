<?php // phpcs:disable
/**
 * PHPStan bootstrap for skilltriks-theme-pack.
 *
 * Two jobs:
 *  1. Define the WordPress core constants PHPStan cannot infer (same set as the
 *     parent `skilltriks` repo, so behaviour stays consistent across repos).
 *  2. Define the GLOBAL constants that come from the required parent plugin
 *     (`Requires Plugins: skilltriks`). Without these, every template that
 *     builds an asset URL reports a false "Constant STLMS_ASSETS not found".
 *
 * Namespaced parent-plugin symbols (\ST\Lms\*) are handled by ignoreErrors in
 * phpstan.neon instead — see the note there.
 */

// ── WordPress core ───────────────────────────────────────────────
define( 'WPINC', 'wp-includes' );
define( 'COOKIEHASH', md5( 'https://stlms.test' ) );
define( 'COOKIEPATH', '/' );
define( 'COOKIE_DOMAIN', false );

// ── Provided at runtime by the parent `skilltriks` plugin ────────
define( 'STLMS_ASSETS', 'https://stlms.test/wp-content/plugins/skilltriks/assets' );
