# Agent Guidelines for OmniForm

## Project Overview
OmniForm is a WordPress plugin that enables users to create and manage forms using Gutenberg blocks. It supports various form types, validation rules, and integrations with CAPTCHA services like reCAPTCHA, hCaptcha, and Cloudflare Turnstile.

## Agent Behavioral Guidelines

### Communication Standards
- Respond with evidence-based reasoning when evaluating claims; avoid apologetic or conciliatory language.
- Skip unnecessary affirmations ("You're right," "Yes") and maintain neutrality—focus on programmatic task completion.
- Use clear, direct language without hyperbole or excessive enthusiasm.

### Decision-Making Process
- Before implementation, clarify requirements rather than making unconfirmed speculative changes.
- Ask targeted questions to eliminate ambiguity when instructions are unclear.
- Identify and suggest more efficient or effective alternatives if user ideas have limitations.
- Validate assumptions against project structure and existing patterns before proceeding.

## Build, Lint, and Test Workflow

### Pre-Commit Checklist
Before committing code, run the following in sequence:
1. **Format code**: `composer run format`, `npm run lint:js:fix`, and `npm run lint:css:fix` (auto-fixes most style issues)
2. **Lint**: `composer run lint`, `npm run lint:js`, and `npm run lint:css`
3. **Test**: `composer run test`

### Individual Commands

#### JavaScript / CSS
- **Build production**: `npm run build`
- **Watch mode**: `npm run start` (compiles and watches for changes, useful for development)
- **Lint JS**: `npm run lint:js`
- **Fix lint issues**: `npm run lint:js:fix`
- **Lint CSS**: `npm run lint:css`
- **Fix lint issues**: `npm run lint:css:fix`

#### PHP
- **Lint all PHP**: `composer run lint`
- **Lint single file**: `composer run lint ./path/to/file.php`
- **Format all PHP**: `composer run format` (auto-fixes formatting and basic issues)
- **Format single file**: `composer run format ./path/to/file.php`
- **Run tests**: `composer run test`
- **Test single file**: `composer run test ./path/to/file.php`

### Troubleshooting Build Issues
- If lint fails after making changes, run `composer run format` first to auto-fix styling issues
- If build fails, check for JavaScript syntax errors and ensure all imports are valid
- Clear `vendor/` and `node_modules/` and reinstall if experiencing dependency issues: `composer install && npm install`

## Directory Structure and Organization

### Root Level
- **Config files** (`package.json`, `composer.json`, `webpack.config.js`, `phpunit.xml.dist`, `.phpcs.xml.dist`): Build system and tool configurations
- **Build artifacts** (`build/`): Compiled JS, CSS, and plugin assets (do not commit individual output files)
- **Source code**: Organized in `includes/` (PHP) and `packages/` (JavaScript)

### includes/
PHP source code organized by feature:
- `Application.php`: Main plugin class
- `Analytics/`: Usage tracking and analytics
- `BlockLibrary/`: Block registration and metadata
- `Exceptions/`: Custom exception classes
- `FormTypes/`: Form type implementations
- `OAuth/`: Third-party authentication
- `Plugin/`: Plugin lifecycle (hooks, activation, deactivation)
- `Traits/`: Shared PHP traits
- `Validation/`: Validation rules and logic

### packages/
JavaScript/TypeScript source organized by type:
- `block-library/`: Individual Gutenberg block implementations
- `components/`: Shared React components
- `dashboard/`: Admin dashboard application
- `shared/`: Shared utilities and helper functions

### phpunit/
PHP unit tests mirroring the `includes/` structure:
- Tests should match the directory structure of code being tested
- Test files named `*Test.php`

### docs/
API documentation and guides:
- `api/`: Hook, filter, and function references
- `blocks/`: Individual block documentation
- `README.md`: Overview and getting started

### Other Important Directories
- **assets/**: Static assets (images, CAPTCHA logos, etc.)
- **.wordpress-org/**: Plugin assets for WordPress.org submission
- **.github/**: CI/CD workflows and GitHub Actions configurations

## Code Style Guidelines

### Language-Specific Standards
- **PHP**: WordPress-Extra + WordPress-Docs standards, PSR-4 autoloading, PHP 7.4+ compatibility
- **JavaScript**: @wordpress/eslint-plugin/recommended-with-formatting
- **CSS/SCSS**: @wordpress/stylelint-config/scss

### Formatting and Indentation
- **Indentation**: Tabs for PHP/JS/CSS (spaces reserved for YAML and Markdown)
- **Encoding**: UTF-8 throughout
- **Line endings**: LF (Unix-style), not CRLF
- **File endings**: All files must end with a newline character

### Naming Conventions
- **WordPress conventions**: Follow core WordPress naming for hooks, filters, and functions
- **Global symbols**: Prefix with `omniform_` (e.g., `omniform_get_forms()`)
- **JavaScript variables**: camelCase for variables and functions (e.g., `handleFormSubmit()`)
- **React Components**: PascalCase for component names (e.g., `FormBuilder`, `FieldSelector`)
- **PHP Classes**: PascalCase with namespace (e.g., `OmniForm\FormTypes\StandardForm`)

### Import Organization
Organize imports in three groups with blank lines between:
1. External dependencies (third-party libraries)
2. WordPress packages and APIs
3. Internal project imports

Example (JavaScript):
```javascript
import clsx from 'clsx';

/**
 * WordPress dependencies.
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies.
 */
import { formTypes } from '../lib/form-types';
```

### Error Handling and Validation
- Use exceptions for errors; follow WordPress exception patterns
- Validate all user inputs immediately
- Use WordPress sanitization functions (`sanitize_text_field()`, `rest_validate_request_arg()`, etc.)
- Never trust client-side validation alone

### Documentation
- **PHPDoc**: All public classes, methods, and functions require PHPDoc blocks
- **JSDoc**: Document complex functions and all utility exports
- **Block Documentation**: Create `.md` files in `docs/blocks/` for new block types
- **Comments**: Explain *why*, not *what*—code should be self-explanatory

## Testing Instructions

### Test Organization
- All PHP tests reside in `phpunit/unit/` mirroring the `includes/` directory structure
- Test file names follow the pattern: `ClassName` → `ClassNameTest.php`
- Use PHPUnit fixtures and mocks for WordPress-specific dependencies

### Test Execution
- Run full test suite with `composer run test` before commits
- Test single files with `composer run test ./phpunit/unit/path/to/FileTest.php`
- Tests must pass across all supported PHP versions (7.4+)

### Pre-Commit Requirements
1. All tests must pass (`composer run test`)
2. If lint issues occur, run `composer run format` first to auto-fix where possible
3. Manual corrections should only be needed for complex style violations
4. Never commit code with failing tests or lint errors

### Debugging Tests
- Add `--verbose` flag for detailed output: `composer run test -- --verbose`
- Use `XDEBUG_MODE=debug` environment variable when debugging with a debugger
- Isolate failing tests by running single test methods rather than entire files

## PR Instructions

### Preparation Checklist
- Title format: `[Feature/Bug] Brief description`
- Branch naming: Use `feature/description` or `fix/description` format
- Run the pre-commit checklist before submitting:
  1. `composer run format`, `npm run lint:js:fix`, and `npm run lint:css:fix`
  2. `composer run lint`, `npm run lint:js`, and `npm run lint:css`
  3. `composer run test`
- Ensure all checks pass without errors before submitting

### PR Description
- Include clear context about what changed and why
- Reference related issues with GitHub issue links
- Highlight breaking changes, if any
- Include testing instructions if non-obvious

## Security Considerations

### Input Validation & Sanitization
- Sanitize all user inputs using WordPress functions like `sanitize_text_field()`, `sanitize_textarea_field()`, `sanitize_email()`
- Validate form data server-side; never rely solely on client-side validation
- Use `rest_validate_request_arg()` for REST API endpoints
- Never trust data from `$_GET`, `$_POST`, or `$_REQUEST` without sanitization

### Database & Queries
- Use prepared statements for all database queries to prevent SQL injection
- Use `$wpdb->prepare()` with placeholders: `$wpdb->prepare( "SELECT * FROM table WHERE id = %d", $id )`
- Never concatenate user input directly into SQL queries

### CSRF Protection
- Use nonces for form submissions to prevent CSRF attacks
- Generate nonces with `wp_create_nonce()` and verify with `wp_verify_nonce()`
- Include nonce in all admin forms and AJAX requests

### XSS Prevention
- Escape all output to the frontend using `esc_html()`, `esc_attr()`, `esc_js()`, or `wp_kses_post()`
- Use appropriate escaping based on context (HTML, attributes, JavaScript, URLs)
- Never output raw user data directly to the page

### Data Privacy & Storage
- Minimize storage of personal data; collect only what's necessary
- Implement data deletion mechanisms for user compliance with privacy regulations
- Handle sensitive data (passwords, tokens) securely; never store plaintext
- Follow WordPress data handling best practices

### API & Integrations
- Validate all responses from external APIs before using
- Use secure HTTPS connections for all external API calls
- Never expose API keys or secrets in client-side code
- Implement rate limiting and error handling for external integrations

### General Best Practices
- Follow WordPress security best practices and guidelines
- Keep dependencies updated to patch known vulnerabilities
- Avoid using deprecated WordPress functions; prefer modern alternatives
- Review security warnings from linters and address them before merge

## Implementation Patterns

### Block Implementation
- Block structure: `packages/block-library/{block-name}/` with corresponding registration in `includes/BlockLibrary/`
- Test pattern: Mirror structure under `phpunit/unit/BlockLibrary/{block-name}Test.php`
- Verify: Tests pass, code follows naming conventions, imports grouped correctly

### Form Type Implementation
- Extend base class in `includes/FormTypes/`
- Register in form types registry at registration point
- Test structure: `phpunit/unit/FormTypes/{FormTypeNameTest.php`
- Always verify full test suite passes and build succeeds

### Required Patterns When Implementing
- **Tests first**: Ensure test organization mirrors source code structure
- **Naming consistency**: Match conventions defined in Code Style Guidelines
- **Import grouping**: External → WordPress → Internal
- **Sanitization**: All user inputs must be sanitized with WordPress functions
- **Prepared statements**: Database queries must use `$wpdb->prepare()`
- **Nonces**: Forms and AJAX requests must include nonce protection
- **Run all checks**: Format, lint, and test before finalizing

## Critical Implementation Checks

### Before Committing Any Changes
- Verify directory structure matches existing patterns (blocks under `block-library/`, types under `FormTypes/`, etc.)
- Check naming conventions: camelCase for JS, PascalCase for components, `omniform_` prefix for globals
- Verify imports are organized (external → WordPress → internal)
- Confirm all user inputs use WordPress sanitization functions
- Verify database queries use prepared statements with `$wpdb->prepare()`
- Check forms and AJAX include nonces
- Verify accessibility: form fields have labels, interactive elements are keyboard-accessible
- Check CSS follows BEM naming convention
- Verify test structure mirrors source code structure
- Run full lint, format, and test suite

### Performance Considerations
- Lazy-load blocks and components when possible
- Check bundle size impact with `npm run build`
- Avoid unnecessary React re-renders
- Use CSS custom properties for theming

### Accessibility Requirements
- All form fields must have associated `<label>` elements
- Use ARIA attributes (`aria-label`, `aria-describedby`, etc.) for complex components
- All interactive elements must be keyboard-accessible
- Maintain WCAG 2.1 AA compliance

## Maintaining This File

### Instructions for LLMs
This file serves as the agent's operational knowledge base and should evolve as the project develops. When working on OmniForm:

**Update AGENTS.md when you discover:**
- New patterns or best practices not currently documented
- Improvements to existing workflows that work better than documented approaches
- Pitfalls or edge cases encountered during implementation
- New directories, build processes, or configuration changes
- Security considerations specific to newly implemented features
- More efficient command sequences or debugging techniques

**How to update:**
1. Identify the relevant section (or create a new section if needed)
2. Make the change directly to this file using proper Markdown formatting
3. Ensure changes are terse, technical, and actionable (no verbose explanations)
4. Include specific code patterns, function names, command syntax, or file paths
5. Commit with message: `docs: update AGENTS.md with [specific finding/improvement]`

**Content guidelines:**
- Be direct and technical; assume LLM understanding of WordPress/web development basics
- Use bullets and code blocks; minimize prose
- Include exact commands to run, exact file paths, exact code patterns
- Structure as decision trees or pattern matching where possible
- Remove redundancy; link to existing sections rather than repeat
