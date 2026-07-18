<?php
/**
 * Form definition.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Immutable definition of a form.
 */
final class Form {
	/**
	 * Constructor.
	 *
	 * @param string                        $content       Block markup that defines the form.
	 * @param string                        $title         Human-readable title.
	 * @param string                        $status        Publication status (e.g. publish, draft).
	 * @param int|null                      $id            Persisted form id, if any.
	 * @param FormNotificationSettings|null $notifications Notify recipients/subject, if known.
	 *
	 * @throws \InvalidArgumentException If id is non-positive when provided.
	 */
	public function __construct(
		private readonly string $content,
		private readonly string $title = '',
		private readonly string $status = 'publish',
		private readonly ?int $id = null,
		private readonly ?FormNotificationSettings $notifications = null,
	) {
		if ( null !== $this->id && $this->id < 1 ) {
			throw new \InvalidArgumentException( 'Form id must be a positive integer when provided.' );
		}
	}

	/**
	 * Content-only form (no persisted identity).
	 *
	 * @param string $content Block markup that defines the form.
	 */
	public static function from_content( string $content ): self {
		return new self( content: $content );
	}

	/**
	 * Persisted form id, or null when the form is content-only.
	 */
	public function id(): ?int {
		return $this->id;
	}

	/**
	 * Whether this form has a persisted identity.
	 */
	public function is_persisted(): bool {
		return null !== $this->id;
	}

	/**
	 * Form title.
	 */
	public function title(): string {
		return $this->title;
	}

	/**
	 * Block markup that defines the form.
	 */
	public function content(): string {
		return $this->content;
	}

	/**
	 * Publication status.
	 */
	public function status(): string {
		return $this->status;
	}

	/**
	 * Whether the form is published.
	 */
	public function is_published(): bool {
		return 'publish' === $this->status;
	}

	/**
	 * Notification settings when loaded from persistence; null for content-only forms.
	 */
	public function notifications(): ?FormNotificationSettings {
		return $this->notifications;
	}
}
