/**
 * WordPress dependencies.
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	__experimentalHStack as HStack,
	Button,
} from '@wordpress/components';
import { Page } from '@wordpress/admin-ui';
import { close } from '@wordpress/icons';
import { EditorSnackbars } from '@wordpress/editor';

/**
 * Internal dependencies.
 */
import FormList from '../form-list';
import ResponseList from '../response-list';
import ResponsePreview from '../response-preview';

export default function App( { settings } ) {
	const [ activeItem, setActiveItem ] = useState( null );

	return (
		<>
			<EditorSnackbars />

			<div className="omniform-layout">
				<div className="omniform-layout__container">
					<div className="omniform-layout__content">
						{ settings.screen === 'dashboard' && (
							<Page title={ __( 'OmniForm', 'omniform' ) } />
						) }

						{ settings.screen === 'forms' && (
							<FormList />
						) }

						{ settings.screen === 'responses' && (
							<ResponseList setActiveItem={ setActiveItem } />
						) }
					</div>

					<div className="omniform-layout__panel">
						{ activeItem && (
							<Page
								title={
									<HStack as="span" alightment="left" expanded={ false }>
										<img
											className="field__avatar"
											alt={ __( 'Author avatar' ) }
											src={ activeItem.omniform_form.sender_gravatar }
										/>
										<span className="field__email">
											{ activeItem.omniform_form.sender_email }
										</span>
									</HStack>
								}
								subTitle={ ( new Date( activeItem.date ) ).toLocaleString( 'en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true } ) }
								actions={
									<>
										<Button
											__next40pxDefaultSize
											variant="secondary"
										>
											Action Button
										</Button>
										<Button
											icon={ close }
											label={ __( 'Close panel', 'omniform' ) }
											onClick={ () => setActiveItem( null ) }
										/>
									</>
								}
							>
								<ResponsePreview id={ activeItem.id } />
							</Page>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
