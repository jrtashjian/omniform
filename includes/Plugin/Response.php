<?php
/**
 * The Response class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

/**
 * The Response class.
 */
class Response implements \JsonSerializable {
	/**
	 * The date.
	 *
	 * @var string
	 */
	protected $date;

	/**
	 * Request params.
	 *
	 * @var array
	 */
	protected $request_params;

	/**
	 * Original form fields.
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Original form fieldsets.
	 *
	 * @var array
	 */
	protected $groups;

	/**
	 * Set the post data.
	 *
	 * @param \WP_Post $post_data The post data.
	 */
	public function set_post_data( \WP_Post $post_data ) {
	}

	/**
	 * Set the date.
	 *
	 * @param string $date The date.
	 */
	public function set_date( $date ) {
		$this->date = $date;
	}

	/**
	 * Set the request params.
	 *
	 * @param array $request_params The request params.
	 */
	public function set_request_params( array $request_params ) {
		$this->request_params = $request_params;
	}

	/**
	 * Get the request params.
	 *
	 * @return array The request params.
	 */
	public function get_request_params() {
		return $this->request_params;
	}

	/**
	 * Set the fields.
	 *
	 * @param array $fields The fields.
	 */
	public function set_fields( $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Set the groups.
	 *
	 * @param array $groups The groups.
	 */
	public function set_groups( $groups ) {
		$this->groups = $groups;
	}

	/**
	 * Get the response text content.
	 *
	 * @return string The response text content.
	 */
	public function text_content() {
		$response_data = $this->get_response_data();
		$message       = array();

		foreach ( $response_data['fields'] as $name => $label ) {
			$value     = implode( ', ', (array) $response_data['content']->get( $name, '' ) );
			$message[] = sprintf(
				'<strong>%s:</strong> %s',
				esc_html( $label ),
				wp_kses_post( nl2br( $value ), array() )
			);
		}

		return implode( '<br />', $message );
	}

	/**
	 * Get the response email content.
	 *
	 * @return string The response email content.
	 */
	public function email_content() {
		$response_data = $this->get_response_data();
		$message       = array();

		foreach ( $response_data['fields'] as $name => $label ) {
			$value     = implode( ', ', (array) $response_data['content']->get( $name, '' ) );
			$message[] = $label . ': ' . wp_kses( $value, array() );
		}

		$message[] = '';
		$message[] = '---';
		/* translators: %s: Site URL. */
		$message[] = sprintf( esc_html__( 'This email was sent to notify you of a response made through the contact form on %s.', 'omniform' ), esc_url( get_bloginfo( 'url' ) ) );
		$message[] = esc_html__( 'Time: ', 'omniform' ) . sanitize_text_field( $this->date );
		$message[] = esc_html__( 'IP Address: ', 'omniform' ) . sanitize_text_field( $this->request_params['_omniform_user_ip'] );
		$message[] = esc_html__( 'Form URL: ', 'omniform' ) . esc_url( $this->request_params['_wp_http_referer'] );

		return esc_html( implode( "\n", $message ) );
	}

	/**
	 * Get the response data.
	 *
	 * @return array The response data.
	 */
	public function get_response_data() {
		$content = new \OmniForm\Dependencies\Dflydev\DotAccessData\Data(
			array_filter( $this->request_params, array( $this, 'filter_request_params' ), ARRAY_FILTER_USE_KEY )
		);

		$fields = array_combine(
			array_keys( $this->flatten( $content->export() ) ),
			array_keys( $this->flatten( $content->export() ) )
		);

		// Map the fields to the original field names.
		$fields = array_map(
			function ( $field ) {
				return $this->fields[ $field ] ?? $field;
			},
			$fields
		);

		return array(
			'content' => $content,
			'fields'  => $fields,
		);
	}

	/**
	 * Filter out the fields we don't want to save.
	 *
	 * @param string $key The key to filter.
	 *
	 * @return array
	 */
	private function filter_request_params( $key ) {
		/**
		 * Filter out the fields we don't want to save.
		 *
		 * @param string[] $filtered_request_params The filtered request params.
		 */
		$filtered_request_params = apply_filters( 'omniform_filtered_request_params', array( 'id', 'rest_route', 'wp_rest', '_locale', '_wp_http_referer', '_wpnonce', 'omniform_hash', '_omniform_user_ip' ) );

		return ! in_array( $key, $filtered_request_params, true );
	}

	/**
	 * Flatten an array.
	 *
	 * @link https://github.com/dflydev/dflydev-dot-access-data/issues/16#issuecomment-699638023
	 *
	 * @param array  $data The array to flatten.
	 * @param string $path_prefix The path prefix.
	 *
	 * @return array The flattened array.
	 */
	private function flatten( array $data, string $path_prefix = '' ) {
		$ret = array();

		foreach ( $data as $key => $value ) {
			$full_key = ltrim( $path_prefix . '.' . $key, '.' );
			if ( is_array( $value ) && \OmniForm\Dependencies\Dflydev\DotAccessData\Util::isAssoc( $value ) ) {
				$ret += $this->flatten( $value, $full_key );
			} else {
				$ret[ $full_key ] = $value;
			}
		}

		return $ret;
	}

	/**
	 * Get the response data.
	 *
	 * @return array The response data.
	 */
	public function jsonSerialize(): array {
		return array(
			'response' => array_filter( $this->request_params, array( $this, 'filter_request_params' ), ARRAY_FILTER_USE_KEY ),
			'fields'   => array_filter( $this->fields, array( $this, 'filter_request_params' ), ARRAY_FILTER_USE_KEY ),
			'groups'   => $this->groups,
		);
	}
}
