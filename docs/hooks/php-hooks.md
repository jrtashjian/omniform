# PHP Hooks and Filters

OmniForm provides several PHP hooks and filters prefixed with `omniform` to allow customization of plugin behavior.

## Actions

### omniform_response_created
Triggered when a form response is created (after saving to the database).

**Parameters:**
- `$response` (Response): The form response object.
- `$form` (Form): The form object.

**Usage:**
```php
add_action( 'omniform_response_created', function( $response, $form ) {
    // Custom logic, e.g., additional email notifications
    wp_remote_post( 'https://hooks.slack.com/...', [ 'body' => $response->email_content() ] );
}, 10, 2 );
```

### omniform_form_render
Triggered when a form is rendered.

**Parameters:**
- `$form_id` (int): The ID of the rendered form.

**Usage:**
```php
add_action( 'omniform_form_render', function( $form_id ) {
    // Custom logic, e.g., logging impressions
} );
```

### omniform_activate
Triggered on plugin activation.

**Parameters:** None

**Usage:**
```php
add_action( 'omniform_activate', function() {
    // Custom activation logic
} );
```

## Filters

### omniform_filtered_request_params
Filters request parameters to filter out the fields we don't want to save to the response.

**Parameters:**
- `$params` (array): The request parameters array.

**Usage:**
```php
add_filter( 'omniform_filtered_request_params', function( $params ) {
    // Modify or validate parameters
    if ( ! isset( $params['custom_field'] ) ) {
        // Custom validation logic
        return false;
    }
    return $params;
} );
```