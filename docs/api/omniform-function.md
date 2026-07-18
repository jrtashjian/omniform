# omniform() Function

The `omniform()` function is a global helper that returns the main application instance (`\OmniForm\Application`). Use it when extending the plugin from outside OmniForm’s own internals.

## Overview

```php
$app = omniform();
```

## Forms

Returns domain `\OmniForm\Form\Form` (content, title, status, id, notifications).

### Load a form by ID

```php
$form = omniform()->form( $form_id );
```

### Create a form from block content

```php
$form = omniform()->form_from_content( $serialized_blocks );
```

## Utility methods

### Version

```php
$version = omniform()->version();
```

### Base path / URL

```php
$path = omniform()->base_path( 'build/dashboard/index.js' );
$url  = omniform()->base_url( 'assets/' );
```

## Service providers

Register custom service providers when you need to wire additional services:

```php
omniform()->register( new MyCustomServiceProvider() );
```

Resolve services not exposed as typed methods via the container:

```php
$analytics = omniform()->container()->get( \OmniForm\Analytics\AnalyticsManager::class );
$form_types = omniform()->container()->get( \OmniForm\FormTypes\FormTypesManager::class );
```

For most customizations, prefer [hooks](../hooks/php-hooks.md) instead of container access.

## Notes

- `omniform()` returns `Application`, not a DI container.
- Prefer `form()` / `form_from_content()` for form access.
- `container()` is for advanced wiring only.
- Methods like `set_base_path()` are internal and not intended for public use.
