# Block Library Service Provider

The `BlockLibraryServiceProvider` is a service provider that registers the OmniForm block library, including blocks, patterns, categories, and global styles.

## Overview

The service provider extends `AbstractServiceProvider` and implements `BootableServiceProviderInterface`, bootstrapping the block library during WordPress initialization.

## Blocks

The service provider registers the following blocks:

- `omniform/form` - The main form container block
- `omniform/field` - Field wrapper block
- `omniform/label` - Label block
- `omniform/input` - Text input block
- `omniform/hidden` - Hidden input block
- `omniform/textarea` - Textarea block
- `omniform/select` - Select dropdown block
- `omniform/button` - Submit button block
- `omniform/fieldset` - Fieldset block
- `omniform/select-group` - Select group block
- `omniform/select-option` - Select option block
- `omniform/captcha` - CAPTCHA block
- `omniform/response-notification` - Response notification block
- `omniform/post-comments-form-title` - Comments form title block
- `omniform/post-comments-form-cancel-reply-link` - Cancel reply link block
- `omniform/conditional-group` - Conditional group block

The form block includes variations for each published OmniForm post, allowing easy insertion of existing forms.

## Patterns

Registers block patterns from the `BlockPatterns/` directory, categorized under "OmniForm". Patterns are available for:

- Standard forms within the form editor
- Standalone forms for use in posts, pages, and templates

## Categories

Adds the following block categories:

- `omniform` - Main OmniForm category
- `omniform-standard-fields` - Standard form fields
- `omniform-advanced-fields` - Advanced form fields
- `omniform-grouped-fields` - Grouped form fields
- `omniform-conditional-groups` - Conditional form groups

## Global Styles

Provides default global styles for OmniForm blocks, with theme-specific overrides for popular themes like [Twenty Twenty-Four](https://wordpress.org/themes/twentytwentyfour/), [Twenty Twenty-Five](https://wordpress.org/themes/twentytwentyfive/), [Kanso](https://wordpress.org/themes/kanso/), [Ollie](https://wordpress.org/themes/ollie/), and [Rockbase](https://rockbase.co/).

Styles include consistent typography, spacing, borders, and focus states for form elements.

## Notes

- Global styles are applied via the `wp_theme_json_data_blocks` filter
- Theme-specific styles are automatically applied based on the active theme
