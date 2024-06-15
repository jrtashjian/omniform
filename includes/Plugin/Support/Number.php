<?php
/**
 * The Number class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin\Support;

/**
 * The Number class.
 */
class Number {
	/**
	 * The locale to use for formatting.
	 *
	 * @var string
	 */
	protected static $locale = 'en_US';

	/**
	 * Format the given number according to the locale.
	 *
	 * @param int|float $number The number to format.
	 * @param int|null  $precision The number of decimal places to include.
	 *
	 * @return string|false The formatted number, or false on error.
	 */
	public static function format( $number, $precision = null ) {
		// Fallback for environments without the NumberFormatter class (e.g. WordPress Playground).
		if ( ! class_exists( '\NumberFormatter' ) ) {
			return number_format( $number, $precision );
		}

		$formatter = new \NumberFormatter( static::$locale, \NumberFormatter::DECIMAL );

		if ( null !== $precision ) {
			$formatter->setAttribute( \NumberFormatter::FRACTION_DIGITS, $precision );
		}

		return $formatter->format( $number );
	}

	/**
	 * Format the given number as a percentage according to the locale.
	 *
	 * @param int|float $number The number to format.
	 * @param int|null  $precision The number of decimal places to include.
	 *
	 * @return string|false The formatted number, or false on error.
	 */
	public static function percentage( $number, $precision = 0 ) {
		// Fallback for environments without the NumberFormatter class (e.g. WordPress Playground).
		if ( ! class_exists( '\NumberFormatter' ) ) {
			return round( $number * 100, $precision ) . '%';
		}

		$formatter = new \NumberFormatter( static::$locale, \NumberFormatter::PERCENT );

		$formatter->setAttribute( \NumberFormatter::FRACTION_DIGITS, $precision );

		return $formatter->format( $number );
	}

	/**
	 * Set the locale to use for formatting.
	 *
	 * @param string $locale The locale to use.
	 */
	public static function use_locale( $locale ) {
		static::$locale = $locale;
	}
}
