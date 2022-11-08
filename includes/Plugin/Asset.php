<?php
/**
 * The Asset class.
 *
 * @package InquiryWP
 */

namespace InquiryWP\Plugin;

/**
 * The Asset class.
 */
class Asset {
	/**
	 * The asset handle.
	 *
	 * @var string
	 */
	protected $handle;

	/**
	 * The asset slug.
	 *
	 * @var string
	 */
	protected $slug;

	/**
	 * The asset package.
	 *
	 * @var string
	 */
	protected $package;

	/**
	 * Constructor.
	 *
	 * @param string $handle The asset handle.
	 * @param string $slug The asset slulg.
	 */
	public function __construct( $handle = 'index', $slug = 'index' ) {
		$this->handle = $handle;
		$this->slug   = $slug;
	}

	/**
	 * Get the asset handle.
	 *
	 * @return string
	 */
	public function getHandle() {
		return $this->handle;
	}

	public function setPackageName( $package_name ) {
		return $this->package = $package_name;
	}

	/**
	 * Returns an array of dependencies and version string from the passed PHP file.
	 * Returns defaults if the file does not exist.
	 *
	 * @param string $filepath The path to the '.asset.php' file.
	 *
	 * @return array
	 */
	protected function loadAssetFile( $filepath ) {
		$default_asset_file = array(
			'dependencies' => array(),
			'version'      => inquirywp()->version(),
		);

		return file_exists( $filepath ) ? include $filepath : $default_asset_file;
	}

	/**
	 * Get tthe asset file path from the given filename partial.
	 *
	 * @param string $filename $the filename excluding '.asset.php'.
	 *
	 * @return string
	 */
	protected function getAssetFilePath( $filename ) {
		return inquirywp()->basePath( 'build/' . $this->package . '/' . $filename . '.asset.php' );
	}

	/**
	 * Get the public URL to the asset file.
	 *
	 * @param string $filename The asset's filename.
	 *
	 * @return string
	 */
	protected function getAssetUrl( $filename ) {
		return inquirywp()->baseUrl( 'build/' . $this->package . '/' . $filename );
	}

	/**
	 * Hook into WordPress to register or enqueue the script.
	 *
	 * @param bool $enqueue Set the script to be enqueued or registered.
	 */
	protected function scriptAction( $enqueue = false ) {
		$asset_file = $this->loadAssetFile(
			$this->getAssetFilePath(
				$this->slug
			)
		);

		$func = $enqueue ? 'wp_enqueue_script' : 'wp_register_script';

		$func(
			$this->handle,
			$this->getAssetUrl( $this->slug . '.js' ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}

	/**
	 * Hook into WordPress to register or enqueue the style.
	 *
	 * @param bool $enqueue Set the style to be enqueued or registered.
	 */
	protected function styleAction( $enqueue = false ) {
		$asset_file = $this->loadAssetFile(
			$this->getAssetFilePath(
				$this->slug
			)
		);

		$func = $enqueue ? 'wp_enqueue_style' : 'wp_register_style';

		$func(
			$this->handle,
			$this->getAssetUrl( 'style-' . $this->slug . '.css' ),
			array( 'wp-components' ),
			$asset_file['version'],
		);
	}

	/**
	 * Enqueue a script.
	 */
	public function enqueueScript() {
		$this->scriptAction( true );
	}

	/**
	 * Enqueue a style.
	 */
	public function enqueueStyle() {
		$this->styleAction( true );
	}
}
