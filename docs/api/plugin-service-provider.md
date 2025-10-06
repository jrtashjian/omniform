# PluginServiceProvider

The `PluginServiceProvider` is the core service provider that registers essential OmniForm services and bootstraps the plugin's functionality within WordPress.

## Overview

The service provider extends `AbstractServiceProvider` and implements `BootableServiceProviderInterface`, registering core services and setting up WordPress integrations including custom post types and form processing.

## Services Provided

Access services through the `omniform()` helper function. See [omniform() Helper Function documentation](omniform-function.md) for usage examples.

### Form

A shared instance of the `Form` class for form validation and processing.

```php
$form = omniform()->get( \OmniForm\Plugin\Form::class );
```

### FormFactory

Creates and manages form instances.

```php
$form_factory = omniform()->get( \OmniForm\Plugin\FormFactory::class );
$form = $form_factory->create_with_id( $form_id );
```

### ResponseFactory

Creates form response objects.

```php
$response_factory = omniform()->get( \OmniForm\Plugin\ResponseFactory::class );
$response = $response_factory->create_with_id( $response_id );
```

### QueryBuilder

Provides database query building functionality using WordPress's `$wpdb`.

```php
$query_builder = omniform()->get( \OmniForm\Plugin\QueryBuilder::class );
```

### QueryBuilderFactory

Creates new QueryBuilder instances.

```php
$query_builder_factory = omniform()->get( \OmniForm\Plugin\QueryBuilderFactory::class );
$query_builder = $query_builder_factory->create();
```

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
