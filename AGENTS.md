# Agent Guidelines for OmniForm

## Build/Lint/Test Commands
- **Build**: `npm run build`
- **Watch mode**: `npm run start` (compiles and watches for changes)
- **Lint JS**: `npm run lint:js` (fix: `npm run lint:js:fix`)
- **Lint CSS**: `npm run lint:css` (fix: `npm run lint:css:fix`)
- **Lint PHP**: `npm run lint:php` (prefixed: `npm run lint:php:prefixed`)
- **Format PHP**: `npm run format:php`
- **Test PHP**: `npm run test:php` (lint + unit tests)
- **Unit tests**: `npm run test:unit:php`
- **Single test**: `wp-env run --env-cwd="wp-content/plugins/${PWD##*/}" tests-wordpress vendor/bin/phpunit -c phpunit.xml.dist --verbose --filter TestClass::testMethod`

## Code Style Guidelines
- **PHP**: WordPress-Extra + WordPress-Docs standards, PSR-4 autoloading, PHP 7.4+ compatibility
- **JS**: @wordpress/eslint-plugin/recommended-with-formatting
- **CSS/SCSS**: @wordpress/stylelint-config/scss
- **Formatting**: Tabs for indentation (spaces for YAML), UTF-8, LF line endings, final newline
- **Naming**: WordPress conventions, prefix globals with 'omniform', camelCase for JS, PascalCase for components
- **Imports**: Group external deps, then WordPress, then internal (with blank lines between groups)
- **Error handling**: Use exceptions, validate inputs, follow WordPress error patterns
- **Documentation**: PHPDoc for PHP classes/methods, JSDoc for complex JS functions