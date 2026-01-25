/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	Card,
	CardBody,
	CardDivider,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */

export default function ResponsePreview( { id } ) {
	return (
		<Card isBorderless>
			<CardBody>
				PREVIEW: { id }
			</CardBody>

			<CardDivider />

			<CardBody>
				PREVIEW
			</CardBody>

			<CardDivider />

			<CardBody>
				PREVIEW
			</CardBody>

			<CardDivider />

			<CardBody>
				PREVIEW
			</CardBody>
		</Card>
	);
}
