# AGENTS.md

## Setup
- `composer global require humbug/php-scoper && composer run phpscoper` before first `composer install`
- `npm install && npx wp-env start` for local WordPress + PHP tests
- `.wp-env.override.json` is gitignored — use for local overrides

## Commands
- `npm run build` / `npm run start` — `wp-scripts` with `--webpack-src-dir=packages`
- `npm run test:unit:php` — starts wp-env with xdebug, then runs PHPUnit inside the test container
- `npm run test:unit:php:base` — runs PHPUnit without restarting wp-env
- `npm run test:unit:php:coverage` — coverage report in `phpunit/coverage/`
- `npm run test:php` — runs `lint:php` → `lint:php:prefixed` → `test:unit:php`
- `npm run lint:php` / `lint:php:prefixed` — PHPCS; prefixed checks `vendor_prefixed/`
- `npm run format:php` — PHPCBF via wp-env
- `npm run lint:js` / `lint:css` — JS (ESLint) and SCSS (stylelint) linting
- `npm run mailhog` — starts MailHog on ports 1025/8025 for email testing
- `npm version <patch|minor|major>` — bumps version in `readme.txt`, `omniform.php`, `includes/Application.php` via `bin/update-version.js`

## PHP
- Source in `includes/`, unit tests in `phpunit/unit/`. Tests use WP_Mock (no WordPress test suite).
- CI runs tests with `composer test` (no wp-env); local runs with `npm run test:unit:php`.
- `php-scoper` prefixes third-party deps into `OmniForm\Dependencies\` under `vendor_prefixed/`.
  Re-run `composer run phpscoper` after changing Composer dependencies.
- `composer.json` pins `platform.php: 7.4` — `composer install` skips packages requiring PHP >= 8.0.
- Autoload: `OmniForm\` → `includes/` (PSR-4), `OmniForm\Tests\Unit\` → `phpunit/unit/` (dev).

## JS / Blocks
- Entry points: `packages/*/index.js`. Webpack scans `packages/` via `--webpack-src-dir`.
- The `dashboard` package is additionally exported as `window.omniform.dashboard` (see `webpack.config.js`).
- Package directories: `dashboard/`, `block-library/` (17 block subdirectories), `components/`.
- Blocks are registered server-side in `includes/BlockLibrary/`.
