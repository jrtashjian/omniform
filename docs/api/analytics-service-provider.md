# AnalyticsServiceProvider

The `AnalyticsServiceProvider` is a service provider that registers the `AnalyticsManager` service for tracking and retrieving form analytics data.

## Overview

The service provider extends `AbstractServiceProvider` and implements `BootableServiceProviderInterface`, allowing it to register services within the OmniForm plugin's dependency injection container.

## Services Provided

### AnalyticsManager

The service provider registers a shared instance of `AnalyticsManager`, which provides methods for recording form events and retrieving analytics data.

```php
$analytics_manager = omniform()->get( \OmniForm\Analytics\AnalyticsManager::class );
```

## Database Schema

The service provider manages two database tables for analytics:

- `omniform_stats_events` - Stores form events (impressions, submissions)
- `omniform_stats_visitors` - Stores anonymized visitor identifiers

## Usage Examples

### Recording Events

```php
$analytics = omniform()->get( \OmniForm\Analytics\AnalyticsManager::class );

// Record when a form is viewed
$analytics->record_impression( $form_id );

// Record successful submissions
$analytics->record_submission_success( $form_id );

// Record failed submissions
$analytics->record_submission_failure( $form_id );
```

### Retrieving Analytics

```php
$analytics = omniform()->get( \OmniForm\Analytics\AnalyticsManager::class );

// Get total impressions
$total_impressions = $analytics->get_impression_count( $form_id );

// Get unique impressions (unique visitors)
$unique_impressions = $analytics->get_impression_count( $form_id, true );

// Get total submissions
$total_submissions = $analytics->get_submission_count( $form_id );

// Get unique submissions
$unique_submissions = $analytics->get_submission_count( $form_id, true );

// Get failed submissions
$failed_submissions = $analytics->get_failed_submission_count( $form_id );

// Calculate conversion rate
$conversion_rate = $analytics->get_conversion_rate( $form_id );
```

## Event Types

The system tracks three types of events:

- `IMPRESSION` - When a form is viewed
- `SUBMISSION_SUCCESS` - When a form is successfully submitted
- `SUBMISSION_FAILURE` - When a form submission fails validation

## Notes

- Analytics data uses daily salts for visitor anonymization
- All analytics operations are performed through the `AnalyticsManager` service