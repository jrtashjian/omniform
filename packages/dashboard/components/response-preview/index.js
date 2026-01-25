/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	__experimentalText as Text,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	Card,
	CardBody,
	CardDivider,
	Icon,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import {
	fieldInput,
	fieldTextarea,
} from '../../../block-library/shared/icons';

export default function ResponsePreview( { id } ) {
	return (
		<Card
			className="omniform-response-preview"
			isBorderless
		>
			<CardBody>
				PREVIEW: { id }
			</CardBody>

			<CardDivider />

			<CardBody>
				<VStack className="omniform-response-preview__field">
					<HStack
						className="omniform-response-preview__field-label"
						alignment="left"
					>
						<Icon icon={ fieldInput } />
						<Text variant="muted">{ __( 'Form Label', 'omniform' ) }</Text>
					</HStack>
					<div className="omniform-response-preview__field-value">
						<Text>PREVIEW</Text>
					</div>
				</VStack>
			</CardBody>

			<CardDivider />

			<CardBody>
				<VStack className="omniform-response-preview__field">
					<HStack
						className="omniform-response-preview__field-label"
						alignment="left"
					>
						<Icon icon={ fieldTextarea } />
						<Text variant="muted">{ __( 'Form Label', 'omniform' ) }</Text>
					</HStack>
					<div className="omniform-response-preview__field-value">
						<Text>PREVIEW</Text>
					</div>
				</VStack>
			</CardBody>

			<CardDivider />

			<CardBody>
				PREVIEW
			</CardBody>
		</Card>
	);
}
