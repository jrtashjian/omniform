<?php
/**
 * The FormFactory class.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Dependencies\League\Container\Container;
use OmniForm\Dependencies\Respect\Validation\Validator;
use OmniForm\Exceptions\FormNotFoundException;
use OmniForm\Exceptions\InvalidFormIdException;

/**
 * The FormFactory class.
 */
class FormFactory {
	/**
	 * The Container object.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * The Validator object.
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * The FormFactory constructor.
	 *
	 * @param Container $container The Container object.
	 * @param Validator $validator The Validator object.
	 */
	public function __construct( Container $container, Validator $validator ) {
		$this->container = $container;
		$this->validator = $validator;
	}

	/**
	 * Create a new instance of the Form class.
	 *
	 * @param string $form_content The form content.
	 *
	 * @return Form The newly created Form instance.
	 */
	public function create_with_content( $form_content ): Form {
		$form = $this->container->getNew( Form::class );
		$form->set_content( $form_content );

		return $form;
	}

	/**
	 * Create a new instance of the Form class.
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return Form The newly created Form instance.
	 *
	 * @throws FormNotFoundException If the form does not exist.
	 * @throws InvalidFormIdException If the form ID is invalid.
	 */
	public function create_with_id( $form_id ): Form {
		$form_id = (int) $form_id;

		if ( ! $form_id ) {
			throw new InvalidFormIdException(
				/* translators: %d: Form ID. */
				esc_attr( sprintf( __( 'Form ID must be an integer. &#8220;%s&#8221; is not a valid integer.', 'omniform' ), $form_id ) )
			);
		}

		$_form = get_post( $form_id );

		if ( ! $_form || 'omniform' !== $_form->post_type ) {
			throw new FormNotFoundException(
				/* translators: %d: Form ID. */
				esc_attr( sprintf( __( 'Form ID &#8220;%d&#8221; does not exist.', 'omniform' ), $form_id ) )
			);
		}

		$form = $this->container->getNew( Form::class );
		$form->set_post_data( $_form );

		return $form;
	}
}
