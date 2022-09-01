# PluginWP Foundation

🚧 [**UNDER DEVELOPMENT**](https://github.com/jrtashjian/pluginwp/issues/1) 🚧

This code serves as a starting point for building WordPress plugin using React and the [block editor components](https://github.com/WordPress/gutenberg/tree/trunk/packages). I also wanted to build WordPress plugins in a more modern PHP way by introducing a portion of the [Service Container](https://laravel.com/docs/8.x/container) and [Service Providers](https://laravel.com/docs/8.x/providers) that are used in the [Laravel framework](https://laravel.com/).

## Installation

### Requirements

- [Node.js](https://nodejs.org)
- [Composer](https://getcomposer.org)

### Quick Start

Clone this repository and do a case-sensitive search & replace on the codebase to replace references of "PluginWP" to your own.

```
git clone https://github.com/jrtashjian/pluginwp.git yourpluginslug
```

Replace the following strings within the `includes/`, `packages/`, and `phpunit/` directories as well as the `composer.json`, `package.json`, `phpcs.xml`, `phpunit.xml.dist`, `webpack.config.js`, and `pluginwp.php` file.

| Search for        | Replace with         |
| ----------------- | -------------------- |
| `PluginWP Author` | `Actual Author Name` |
| `PluginWP`        | `YourPluginSlug`     |
| `pluginwp`        | `yourpluginslug`     |

You should also rename the main plugin file `pluginwp.php` to `yourpluginslug.php`.

### Setup

Install the necessary Node.js and Composer dependencies:

```
$ composer install
$ npm install
```

### Available CLI commands

- `composer lint` : checks all PHP files for syntax errors.
- `composer format` : fixes all automatically fixable syntax errors.
- `npm run wp-env` : exposes all commands available in [`@wordpress/env`](https://github.com/WordPress/gutenberg/tree/wp/6.0/packages/env)
- `npm run build` : compiles all scripts and styles distribution.
- `npm run dev` : compiles all scripts and styles for development.

Now go build something!