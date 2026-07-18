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
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies.
 */
import {
	fieldDate,
	fieldEmail,
	fieldInput,
	fieldSelect,
	fieldTextarea,
} from '../../../block-library/shared/icons';

/**
 * @param {string} type Field control type.
 * @return {import('@wordpress/components').IconType} Icon for the field type.
 */
function iconForType( type ) {
	switch ( type ) {
		case 'email':
		case 'username-email':
			return fieldEmail;
		case 'textarea':
			return fieldTextarea;
		case 'select':
		case 'radio':
		case 'checkbox':
			return fieldSelect;
		case 'date':
		case 'time':
		case 'month':
		case 'datetime-local':
			return fieldDate;
		default:
			return fieldInput;
	}
}

/**
 * @typedef {Object} ResponseField
 * @property {string} name  Field path key.
 * @property {string} label Human label.
 * @property {string} type  Control type.
 * @property {string} value Display value.
 */

/**
 * @param {Object} props
 * @param {ResponseField[]} [props.fields] Field rows from omniform_form.fields.
 */
export default function ResponsePreview( { fields = [] } ) {
	if ( ! fields.length ) {
		return (
			<Card className="omniform-response-preview" isBorderless>
				<CardBody>
					<Text variant="muted">
						{ __( 'No response fields to display.', 'omniform' ) }
					</Text>
				</CardBody>
			</Card>
		);
	}

	return (
		<Card className="omniform-response-preview" isBorderless>
			{ fields.map( ( field, index ) => (
				<Fragment key={ field.name || index }>
					{ index > 0 && <CardDivider /> }
					<CardBody>
						<VStack className="omniform-response-preview__field">
							<HStack
								className="omniform-response-preview__field-label"
								alignment="left"
							>
								<Icon icon={ iconForType( field.type ) } />
								<Text variant="muted">{ field.label }</Text>
							</HStack>
							<div className="omniform-response-preview__field-value">
								<Text>
									{ field.value ||
										__( '(empty)', 'omniform' ) }
								</Text>
							</div>
						</VStack>
					</CardBody>
				</Fragment>
			) ) }
		</Card>
	);
}
