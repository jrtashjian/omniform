# JavaScript Hooks

OmniForm provides JavaScript filters prefixed with `omniform` for extending frontend functionality.

## Filters

### omniform.prepareFormElementForSubmission
Validates form elements before submission, used for CAPTCHA.

**Parameters:**
- `formElement` (Element): The form element being validated.

**Usage:**
```javascript
wp.hooks.addFilter(
    'omniform.prepareFormElementForSubmission',
    'my-plugin/captcha-validation',
    ( formElement ) => {
        // Custom validation logic
        return formElement;
    }
);
```