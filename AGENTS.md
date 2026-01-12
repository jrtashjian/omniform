# Agent Guidelines for OmniForm

## Project Overview
OmniForm is a WordPress plugin that enables users to create and manage forms using Gutenberg blocks. It supports various form types, validation rules, and integrations with CAPTCHA services like reCAPTCHA, hCaptcha, and Cloudflare Turnstile.

## Primary Instructions
- When corrected, evaluate the claim factually and respond with evidence-based reasoning.
- Do not apologize or use conciliatory language.
- Avoid unnecessary affirmations like "You're right" or "Yes."
- Maintain neutrality; avoid hyperbole or excitement, focusing programmatically on task completion.
- If uncertain, seek clarification or outline a brief plan; avoid unconfirmed speculative changes.
- Ask questions before implementation to eliminate ambiguity.
- Suggest improved alternatives when user ideas are inefficient or flawed.

## Build/Lint/Test Commands
- **Build**: `npm run build`
- **Watch mode**: `npm run start` (compiles and watches for changes)
- **Lint JS**: `npm run lint:js` (fix: `npm run lint:js:fix`)
- **Lint CSS**: `npm run lint:css` (fix: `npm run lint:css:fix`)
- **Lint PHP**: `composer run lint`
- **Format PHP**: `composer run format`
- **Test PHP**: `composer run test`
- **Single test**: `composer run test -- --filter TestClass::testMethod`

### Directory Structure
- **Root level**: Config files, build artifacts, docs.
- **includes/**: PHP source code by feature.
- **packages/**: JS packages for blocks, components, shared utilities.
- **phpunit/**: PHP unit tests mirroring includes/.
- **docs/**: API references, hooks, block guides.
- **assets/**: Static assets like CAPTCHA images.
- **.wordpress-org/**: WordPress.org plugin assets.
- **.github/**: CI/CD workflows and actions.

## Code Style Guidelines
- **PHP**: WordPress-Extra + WordPress-Docs standards, PSR-4 autoloading, PHP 7.4+ compatibility
- **JS**: @wordpress/eslint-plugin/recommended-with-formatting
- **CSS/SCSS**: @wordpress/stylelint-config/scss
- **Formatting**: Tabs for indentation (spaces for YAML), UTF-8, LF line endings, final newline
- **Naming**: WordPress conventions, prefix globals with 'omniform', camelCase for JS, PascalCase for components
- **Imports**: Group external deps, then WordPress, then internal (with blank lines between groups)
- **Error handling**: Use exceptions, validate inputs, follow WordPress error patterns
- **Documentation**: PHPDoc for PHP classes/methods, JSDoc for complex JS functions

## Testing Instructions
- Run full test suite with `composer run test` before commits.
- If lint issues occur, run `composer run format` first to auto-fix where possible before manual corrections.
- Check for PHP compatibility across supported versions.

## PR Instructions
- Title format: [Feature/Bug] Brief description
- Run `composer run lint`, `npm run lint:js`, and `composer run test` before submitting.
- Ensure all checks pass without errors before submitting.

## Security Considerations
- Sanitize all user inputs using WordPress functions like `sanitize_text_field`.
- Validate form data server-side; never rely solely on client-side validation.
- Use nonces for form submissions to prevent CSRF.
- Use prepared statements for database queries to prevent SQL injection.
- Avoid exposing sensitive data in JS; handle secrets securely.
- Follow WordPress security best practices for plugin development.
