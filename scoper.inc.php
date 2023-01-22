<?php
/**
 * PHP-Scoper is a tool which essentially moves any body of code, including all
 * dependencies such as vendor directories, to a new and distinct namespace.
 *
 * @package OmniForm
 */

use Isolated\Symfony\Component\Finder\Finder;

// You can do your own things here, e.g. collecting symbols to expose dynamically
// or files to exclude.
// However beware that this file is executed by PHP-Scoper, hence if you are using
// the PHAR it will be loaded by the PHAR. So it is highly recommended to avoid
// to auto-load any code here: it can result in a conflict or even corrupt
// the PHP-Scoper analysis.

return array(
	// The prefix configuration. If a non-null value is used, a random prefix
	// will be generated instead.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#prefix.
	'prefix'     => 'OmniForm\Dependencies',

	// The base output directory for the prefixed files.
	// This will be overridden by the 'output-dir' command line option if present.
	'output-dir' => 'includes/Dependencies',

	// By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
	// directory. You can however define which files should be scoped by defining a collection of Finders in the
	// following configuration key.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#finders-and-paths.
	'finders'    => array(
		Finder::create()
			->files()
			->ignoreVCS( true )
			->notName(
				array(
					'README.md',
					'/.*\\.dist/',
					'Makefile',
					'composer.json',
					'composer.lock',
				)
			)
			->exclude(
				array(
					'doc',
					'test',
					'test_old',
					'tests',
					'Tests',
					'vendor-bin',
					'composer',
				)
			)
			->in( 'vendor' ),
	),
);
