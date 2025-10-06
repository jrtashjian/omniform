# omniform() Helper Function

The `omniform()` function is a global helper that returns the main application container instance (`\OmniForm\Application`), providing access to core services and utilities within the OmniForm plugin.

## Overview

```php
$container = omniform();
```

This function allows developers to interact with various OmniForm services programmatically.

## Service Retrieval

### FormFactory
Retrieve the FormFactory service to create or manage form instances.

```php
$form_factory = omniform()->get( \OmniForm\Plugin\FormFactory::class );
$form = $form_factory->create_with_id( $form_id );
```

### AnalyticsManager
Access analytics tracking for form submissions and impressions.

```php
$analytics = omniform()->get( \OmniForm\Analytics\AnalyticsManager::class );
$analytics->record_submission_success( $form_id );
$impressions = $analytics->get_impression_count( $form_id );
```

### ResponseFactory
Create form response objects.

```php
$response_factory = omniform()->get( \OmniForm\Plugin\ResponseFactory::class );
$response = $response_factory->create_with_form( $form );
```

### FormTypesManager
Manage different form types.

```php
$form_types = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );
// Use for registering or retrieving form types
```

## Utility Methods

### Version
Get the current plugin version.

```php
$version = omniform()->version();
```

### Base Path
Get the plugin's base directory path.

```php
$path = omniform()->base_path();
```

## Service Providers

You can register custom service providers to extend OmniForm's functionality.

```php
omniform()->addServiceProvider( new MyCustomServiceProvider() );
```

## Notes

Some methods like `set_base_path()` are internal and not intended for public use. Stick to the service retrieval, utility methods, and service provider registration for custom development. For most customizations, use [hooks](../hooks/php-hooks.md) instead.
