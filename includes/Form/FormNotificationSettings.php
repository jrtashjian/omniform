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
	 * @param list<string> $recipients Email addresses.
	 * @param string       $subject    Email subject line.
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
	 * @return list<string>
	 */
	public function recipients(): array {
		return $this->recipients;
	}

	public function subject(): string {
		return $this->subject;
	}

	public function has_recipients(): bool {
		return array() !== $this->recipients;
	}
}
