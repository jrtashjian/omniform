/**
 * WordPress dependencies.
 */
import {
	__experimentalText as Text,
	__experimentalHeading as Heading,
	__experimentalVStack as VStack,
	__experimentalHStack as HStack,
	Button,
	Card,
	Icon,
} from '@wordpress/components';
import { info } from '@wordpress/icons';

export default function ConnectSitePanel() {
	return (
		<Card style={ { padding: '25px' } }>
			<HStack>
				<VStack spacing={ 4 }>
					<Heading>
						Make your forms more powerful and worry-free
					</Heading>
					<Text>
						Connect your site and let us handle the heavy lifting. Your forms will simply work better.
					</Text>
					<HStack alignment="start" justify="stretch">
						<VStack>
							<HStack alignment="left">
								<Icon icon={ info } />
								<Text>Email notiofications that actually arrive</Text>
							</HStack>
							<HStack alignment="left">
								<Icon icon={ info } />
								<Text>Secure file uploads</Text>
							</HStack>
							<HStack alignment="left">
								<Icon icon={ info } />
								<Text>Clear insights about your forms</Text>
							</HStack>
						</VStack>
						<VStack>
							<HStack alignment="left">
								<Icon icon={ info } />
								<Text>Smart spam blocking</Text>
							</HStack>
							<HStack alignment="left">
								<Icon icon={ info } />
								<Text>Easy connections to other tools</Text>
							</HStack>
						</VStack>
					</HStack>
				</VStack>
				<VStack spacing={ 4 }>
					<Button variant="primary">Connect Now</Button>
				</VStack>
			</HStack>
		</Card>
	);
}
