# Plugin Service Provider

The `PluginServiceProvider` is the core service provider that registers essential OmniForm services and bootstraps the plugin's functionality within WordPress.

## Overview

The service provider extends `AbstractServiceProvider` and implements `BootableServiceProviderInterface`, registering core services and setting up WordPress integrations including custom post types and form processing.

## Services Provided

Access forms through the `omniform()` helper. See [omniform() Helper Function documentation](omniform-function.md) for usage examples.

### Form

Load or create form instances.

```php
$form = omniform()->form( $form_id );
$form = omniform()->form_from_content( $serialized_blocks );
```

### Form submission (domain path)

REST submissions go through `FormSubmitter` (validate → persist domain `Response` → `omniform_response_created`). Responses are loaded via `ResponseRepository` (dual-reads domain and legacy JSON).

## Post Types

The service provider registers two custom post types:

- `omniform` - The main form post type with REST API support
- `omniform_response` - Stores form submission responses

## Analytics Integration

Automatically tracks form impressions and submissions using the AnalyticsManager. See [AnalyticsServiceProvider documentation](analytics-service-provider.md) for details.

## Email Notifications

Sends email notifications when form responses are created, using the form's configured notification settings.

## Settings

Registers the following CAPTCHA service settings:

- `omniform_hcaptcha_site_key` - hCaptcha site key
- `omniform_hcaptcha_secret_key` - hCaptcha secret key
- `omniform_recaptchav2_site_key` - reCAPTCHA v2 site key
- `omniform_recaptchav2_secret_key` - reCAPTCHA v2 secret key
- `omniform_recaptchav3_site_key` - reCAPTCHA v3 site key
- `omniform_recaptchav3_secret_key` - reCAPTCHA v3 secret key
- `omniform_turnstile_site_key` - Turnstile site key
- `omniform_turnstile_secret_key` - Turnstile secret key

## Hooks

### Actions

- `omniform_response_created` - Triggered when a form response is saved
- `omniform_form_render` - Triggered when a form is rendered

## Notes

- Forms are rendered using block content on singular pages
