# OmniForm: Form Building inside the Block Editor

[![OmniForm](.wordpress-org/banner-1544x500.png)](https://omniform.io/)

OmniForm is a powerful WordPress plugin that allows you to create and manage forms within your WordPress site.

## Requirements

- PHP 7.4+
- [WordPress](http://wordpress.org/) 6.2+

## Installation

[Download the latest release from Github](https://github.com/jrtashjian/omniform/releases/latest).

You can upload and install the archived (zip) plugin via the WordPress dashboard (`Plugins` > `Add New` -> `Upload Plugin`) or manually inside of the `wp-content/plugins` directory, and activate on the Plugins dashboard.

## Development

Clone this repository:
```
git clone https://github.com/jrtashjian/omniform.git
```

Install the necessary Node.js and Composer dependencies:
```
composer install && npm install
```

Run the development build which will watch for changes:
```
npm run dev
```