<?php
/**
 * Form notification settings.
 *
 * @package OmniForm
 */

namespace OmniForm\Form;

/**
 * Who to notify and with what subject when a response is created.
 */
final class FormNotificationSettings {
	/**
	 * Constructor.
	 *
	 * @param array  $recipients Email addresses.
	 * @param string $subject    Email subject line.
	 *
	 * @throws \InvalidArgumentException If a recipient is empty or subject is empty.
	 */
	public function __construct(
		private readonly array $recipients,
		private readonly string $subject,
	) {
		foreach ( $this->recipients as $recipient ) {
			if ( ! is_string( $recipient ) || '' === $recipient ) {
				throw new \InvalidArgumentException( 'Notification recipients must be non-empty strings.' );
			}
		}

		if ( '' === $this->subject ) {
			throw new \InvalidArgumentException( 'Notification subject cannot be empty.' );
		}
	}

	/**
	 * Email recipients.
	 *
	 * @return list<string>
	 */
	public function recipients(): array {
		return $this->recipients;
	}

	/**
	 * Email subject line.
	 *
	 * @return string
	 */
	public function subject(): string {
		return $this->subject;
	}

	/**
	 * Whether any recipients are defined.
	 *
	 * @return bool
	 */
	public function has_recipients(): bool {
		return array() !== $this->recipients;
	}
}
