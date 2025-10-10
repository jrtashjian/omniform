# Form Types Service Provider

The `FormTypesServiceProvider` is a service provider that registers the `FormTypesManager` service for managing different types of OmniForm forms.

## Overview

The service provider extends `AbstractServiceProvider` and implements `BootableServiceProviderInterface`, registering services and bootstrapping WordPress hooks for form type management.

## Services Provided

### FormTypesManager

The service provider registers a shared instance of `FormTypesManager`, which manages form type definitions and provides methods for adding and retrieving form types.

```php
$form_types_manager = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );
```

## Form Types

Form types are defined as arrays with the following structure:

```php
array(
    'type'        => 'unique_identifier',
    'label'       => 'Display Label',
    'description' => 'Description of the form type',
    'icon'        => 'icon_slug', // Optional
)
```

## Usage Examples

### Retrieving Form Types

```php
$form_types_manager = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );

// Get all available form types
$form_types = $form_types_manager->get_form_types();

// Get the default form type
$default_type = $form_types_manager->get_default_form_type();

// Validate a form type (returns default if invalid)
$validated_type = $form_types_manager->validate_form_type( 'some_type' );
```

### Adding Custom Form Types

```php
$form_types_manager = omniform()->get( \OmniForm\FormTypes\FormTypesManager::class );

$form_types_manager->add_form_type(
    array(
        'type'        => 'survey',
        'label'       => 'Survey',
        'description' => 'A survey form',
        'icon'        => 'clipboard',
    )
);
```

## Hooks

### omniform_register_form_types

Fires after default form types are registered, allowing developers to add custom form types.

```php
add_action( 'omniform_register_form_types', function( $form_types_manager ) {
    $form_types_manager->add_form_type(
        array(
            'type'        => 'survey',
            'label'       => 'Survey',
            'description' => 'A survey form',
        )
    );
} );
```

## Taxonomy

The service provider registers the `omniform_type` taxonomy for categorizing forms by type. This taxonomy is not public and is used internally for form management.

## Block Editor Integration

Form types are automatically made available to the block editor through the `block_editor_settings_all` filter, allowing form blocks to access type information.

## Default Form Types

The system includes two default form types:

- `standard` - A standard form
- `custom` - A custom form

## Notes

- Form types are registered during the `init` hook
- Custom form types should be added via the `omniform_register_form_types` action
- The taxonomy terms are automatically created for each registered form type
