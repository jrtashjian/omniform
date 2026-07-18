import { Routes, Route } from 'react-router-dom';

/**
 * WordPress dependencies.
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	__experimentalVStack as VStack,
	__experimentalHStack as HStack,
	Button,
} from '@wordpress/components';
import { Page } from '@wordpress/admin-ui';
import { addQueryArgs } from '@wordpress/url';
import { close } from '@wordpress/icons';
import { EditorSnackbars } from '@wordpress/editor';

/**
 * Internal dependencies.
 */
import FormList from '../form-list';
import ResponseList from '../response-list';
import ResponsePreview from '../response-preview';
import MetricsPanel from '../metrics-panel';
import ConnectSitePanel from '../connect-site-panel';
import DataViewTopForms from './dataview-top-forms';
import DataViewLatestResponses from './dataview-latest-responses';
import { useInterceptMenuLink } from '../../hooks/use-intercept-menu-link';
import { useSyncCurrentMenu } from '../../hooks/use-sync-menu-current';
import { form } from '../../../block-library/shared/icons';

export default function App( { settings } ) {
	const [ activeItem, setActiveItem ] = useState( null );

	useInterceptMenuLink();
	useSyncCurrentMenu();

	const pageActions = (
		<>
			<Button
				variant="primary"
				onClick={ () =>
					( document.location.href = addQueryArgs( 'post-new.php', {
						post_type: 'omniform',
					} ) )
				}
			>
				{ __( 'Create Form', 'omniform' ) }
			</Button>
		</>
	);

	return (
		<>
			<EditorSnackbars />

			<div className="omniform-layout">
				<div className="omniform-layout__container">
					<div className="omniform-layout__content">
						<Routes>
							<Route
								path="/"
								element={
									<Page
										title={ __( 'OmniForm', 'omniform' ) }
										subTitle={ __( 'Here\'s what\'s happening with your forms.', 'omniform' ) }
										visual={ form }
										actions={ pageActions }
									>
										<VStack
											spacing="10"
											style={ { padding: '24px' } }
										>
											<MetricsPanel />

											<HStack
												spacing="8"
												alignment="stretch"
											>
												<div style={ { flex: 1 } }>
													<DataViewLatestResponses />
												</div>
												<div style={ { flex: 1 } }>
													<DataViewTopForms />
												</div>
											</HStack>

											<ConnectSitePanel />
										</VStack>
									</Page>
								}
							/>

							<Route path="/forms" element={ <FormList /> } />

							<Route
								path="/responses"
								element={
									<ResponseList
										setActiveItem={ setActiveItem }
									/>
								}
							/>
						</Routes>
					</div>

					<div className="omniform-layout__panel">
						{ activeItem && (
							<Page
								title={
									<HStack
										as="span"
										alignment="left"
										expanded={ false }
									>
										<img
											className="field__avatar"
											alt={ __( 'Author avatar' ) }
											src={
												activeItem.omniform_form
													.sender_gravatar
											}
										/>
										<span className="field__email">
											{
												activeItem.omniform_form
													.sender_email
											}
										</span>
									</HStack>
								}
								subTitle={ new Date(
									activeItem.date,
								).toLocaleString( 'en-US', {
									month: 'long',
									day: 'numeric',
									year: 'numeric',
									hour: 'numeric',
									minute: '2-digit',
									hour12: true,
								} ) }
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
											label={ __(
												'Close panel',
												'omniform',
											) }
											onClick={ () =>
												setActiveItem( null )
											}
										/>
									</>
								}
							>
								<ResponsePreview
								fields={
									activeItem.omniform_form?.fields ?? []
								}
							/>
							</Page>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
