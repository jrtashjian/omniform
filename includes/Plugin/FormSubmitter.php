<?php
/**
 * Domain-path form submission orchestration.
 *
 * @package OmniForm
 */

namespace OmniForm\Plugin;

use OmniForm\Exceptions\FormNotFoundException;
use OmniForm\Exceptions\InvalidFormIdException;
use OmniForm\Form\Form;
use OmniForm\Form\Response;
use OmniForm\Form\SubmissionValidator;

/**
 * Validates and persists a form submission using domain objects and adapters.
 *
 * Does not send email or analytics; callers (REST, blocks) compose those.
 */
class FormSubmitter {
	public function __construct(
		private readonly FormRepository $forms,
		private readonly BlockFormSchemaParser $parser,
		private readonly SubmissionFactory $submissions,
		private readonly SubmissionValidator $validator,
		private readonly ResponseRepository $responses,
	) {}

	/**
	 * Submit request data against a form.
	 *
	 * @param int                  $form_id Form post ID.
	 * @param array<string, mixed> $params  Request parameters.
	 * @param array<string, mixed> $files   $_FILES-shaped uploads.
	 * @param array<string, mixed> $meta    Persistence meta (user_ip, referer).
	 */
	public function submit(
		int $form_id,
		array $params,
		array $files = array(),
		array $meta = array(),
	): FormSubmitResult {
		try {
			$form = $this->forms->get( $form_id );
		} catch ( InvalidFormIdException | FormNotFoundException $exception ) {
			return FormSubmitResult::failed( 'form_not_found', $exception->getMessage() );
		}

		if ( ! $form->is_published() ) {
			return FormSubmitResult::failed(
				'form_not_published',
				__( 'The form is not published.', 'omniform' )
			);
		}

		return $this->process( $form, $params, $files, $meta );
	}

	/**
	 * @param array<string, mixed> $params Request parameters.
	 * @param array<string, mixed> $files  $_FILES-shaped uploads.
	 * @param array<string, mixed> $meta   Persistence meta.
	 */
	private function process(
		Form $form,
		array $params,
		array $files,
		array $meta,
	): FormSubmitResult {
		$schema     = $this->parser->parse( $form->content() );
		$submission = $this->submissions->from_request( $params, $files, $schema );
		$validation = $this->validator->validate( $schema, $submission );

		if ( $validation->is_invalid() ) {
			return FormSubmitResult::validation_failed( $validation );
		}

		$response = new Response( $schema, $submission );
		$form_id  = (int) $form->id();

		try {
			$response_id = $this->responses->save( $response, $form_id, $meta );
		} catch ( \Throwable $exception ) {
			return FormSubmitResult::failed( 'persist_failed', $exception->getMessage() );
		}

		/**
		 * Fires after a response has been created.
		 *
		 * @param Response             $response Domain response snapshot.
		 * @param Form                 $form     Domain form.
		 * @param array<string, mixed> $context  response_id, user_ip, referer, time.
		 */
		do_action(
			'omniform_response_created',
			$response,
			$form,
			array(
				'response_id' => $response_id,
				'user_ip'     => isset( $meta['user_ip'] ) ? (string) $meta['user_ip'] : '',
				'referer'     => isset( $meta['referer'] ) ? (string) $meta['referer'] : '',
				'time'        => (string) current_time( 'mysql' ),
			)
		);

		return FormSubmitResult::success( $response, $response_id );
	}
}
