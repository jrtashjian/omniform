/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { SVG, Path } from '@wordpress/primitives';

const variations = [
	{
		name: 'field-email',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M12 1.95c-5.52 0-10 4.48-10 10s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57v-1.43c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57v-1.43c0-5.52-4.48-10-10-10Zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3Z" />
				<defs>
					<linearGradient id="a" x1="0" x2="38.4" y1="-12.05" y2="7.15" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omnigroup' ),
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M17 7h-4v2h4c1.65 0 3 1.35 3 3s-1.35 3-3 3h-4v2h4c2.76 0 5-2.24 5-5s-2.24-5-5-5Zm-6 8H7c-1.65 0-3-1.35-3-3s1.35-3 3-3h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-2Zm-3-4h8v2H8v-2Z" />
				<defs>
					<linearGradient id="a" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting an web address.', 'omnigroup' ),
		attributes: { type: 'url' },
	},
	{
		name: 'field-number',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="m20.5 10 .5-2h-4l1-4h-2l-1 4h-4l1-4h-2L9 8H5l-.5 2h4l-1 4h-4L3 16h4l-1 4h2l1-4h4l-1 4h2l1-4h4l.5-2h-4l1-4h4Zm-7 4h-4l1-4h4l-1 4Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="34.017" y1="-7.2" y2="11.259" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a number value.', 'omnigroup' ),
		attributes: { type: 'number' },
	},
	{
		name: 'field-checkbox',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2Zm0 16H5V5h14v14ZM17.99 9l-1.41-1.42-6.59 6.59-2.58-2.57-1.42 1.41 4 3.99 8-8Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where multiple choices can be made.', 'omnigroup' ),
		attributes: { type: 'checkbox' },
	},
	{
		name: 'field-radio',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)" d="M17 12a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
				<path fill="url(#b)" fillRule="evenodd"
					d="M2 12C2 6.48 6.48 2 12 2s10 4.48 10 10-4.48 10-10 10S2 17.52 2 12Zm2 0c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8-8 3.58-8 8Z"
					clipRule="evenodd" />
				<defs>
					<linearGradient id="a" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
					<linearGradient id="b" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where a single choice can be made.', 'omnigroup' ),
		attributes: { type: 'radio' },
	},
	{
		name: 'field-color',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="m17.66 5.41.92.92-2.69 2.69-.92-.92 2.69-2.69M17.67 3c-.26 0-.51.1-.71.29l-3.12 3.12-1.93-1.91-1.41 1.41 1.42 1.42L3 16.25V21h4.75l8.92-8.92 1.42 1.42 1.41-1.41-1.92-1.92 3.12-3.12c.4-.4.4-1.03.01-1.42l-2.34-2.34c-.2-.19-.45-.29-.7-.29ZM6.92 19 5 17.08l8.06-8.06 1.92 1.92L6.92 19Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="35.761" y1="-9.6" y2="7.682" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Color Picker', 'omniform' ),
		description: __( 'A field for collecting a color value from a color picker.', 'omnigroup' ),
		attributes: { type: 'color' },
	},
	{
		name: 'field-date',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2Zm0 16H5V10h14v10Zm0-12H5V6h14v2ZM9 14H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Zm-8 4H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="37.125" y1="-12" y2="4.166" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omnigroup' ),
		attributes: { type: 'date' },
	},
	{
		name: 'field-datetime-local',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2Zm0 16H5V10h14v10Zm0-12H5V6h14v2ZM9 14H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Zm-8 4H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="37.125" y1="-12" y2="4.166" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localize date and time.', 'omnigroup' ),
		attributes: { type: 'datetime-local' },
	},
	{
		name: 'field-file',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M18 15v3H6v-3H4v3c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3h-2ZM7 9l1.41 1.41L11 7.83V16h2V7.83l2.59 2.58L17 9l-5-5-5 5Z" />
				<defs>
					<linearGradient id="a" x1="2.4" x2="33.12" y1="-7.2" y2="8.16" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'File Upload', 'omniform' ),
		description: __( 'A field for uploading files.', 'omnigroup' ),
		attributes: { type: 'file' },
	},
	{
		name: 'field-month',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2Zm0 16H5V10h14v10Zm0-12H5V6h14v2ZM9 14H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Zm-8 4H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="37.125" y1="-12" y2="4.166" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omnigroup' ),
		attributes: { type: 'month' },
	},
	{
		name: 'field-password',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M2 17h20v2H2v-2Zm1.15-4.05L4 11.47l.85 1.48 1.3-.75-.85-1.48H7v-1.5H5.3l.85-1.47L4.85 7 4 8.47 3.15 7l-1.3.75.85 1.47H1v1.5h1.7l-.85 1.48 1.3.75Zm6.7-.75 1.3.75.85-1.48.85 1.48 1.3-.75-.85-1.48H15v-1.5h-1.7l.85-1.47-1.3-.75L12 8.47 11.15 7l-1.3.75.85 1.47H9v1.5h1.7l-.85 1.48ZM23 9.22h-1.7l.85-1.47-1.3-.75L20 8.47 19.15 7l-1.3.75.85 1.47H17v1.5h1.7l-.85 1.48 1.3.75.85-1.48.85 1.48 1.3-.75-.85-1.48H23v-1.5Z" />
				<defs>
					<linearGradient id="a" x1="-1.2" x2="27.491" y1="-1.4" y2="24.9" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Password', 'omniform' ),
		description: __( 'A field for collecting a password.', 'omnigroup' ),
		attributes: { type: 'password' },
	},
	{
		name: 'field-range',
		title: __( 'Range', 'omniform' ),
		description: __( 'A field for selecting a number from a range of numbers.', 'omnigroup' ),
		attributes: { type: 'range' },
	},
	{
		name: 'field-search',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z" />
				<defs>
					<linearGradient id="a" x1="1.251" x2="34.832" y1="-9.243" y2="7.547" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Search', 'omniform' ),
		description: __( 'A field for collecting a search query.', 'omnigroup' ),
		attributes: { type: 'search' },
	},
	{
		name: 'field-tel',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M6.54 5c.06.89.21 1.76.45 2.59l-1.2 1.2c-.41-1.2-.67-2.47-.76-3.79h1.51m9.86 12.02c.85.24 1.72.39 2.6.45v1.49c-1.32-.09-2.59-.35-3.8-.75l1.2-1.19M7.5 3H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.49c0-.55-.45-1-1-1-1.24 0-2.45-.2-3.57-.57a.84.84 0 0 0-.31-.05c-.26 0-.51.1-.71.29l-2.2 2.2a15.149 15.149 0 0 1-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02A11.36 11.36 0 0 1 8.5 4c0-.55-.45-1-1-1Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Telephone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omnigroup' ),
		attributes: { type: 'tel' },
	},
	{
		name: 'field-time',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2ZM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8Zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7Z" />
				<defs>
					<linearGradient id="a" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omnigroup' ),
		attributes: { type: 'time' },
	},
	{
		name: 'field-week',
		icon: (
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
				<path fill="url(#a)"
					d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2Zm0 16H5V10h14v10Zm0-12H5V6h14v2ZM9 14H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Zm-8 4H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Z" />
				<defs>
					<linearGradient id="a" x1="1.2" x2="37.125" y1="-12" y2="4.166" gradientUnits="userSpaceOnUse">
						<stop stopColor="#842ED9" />
						<stop offset=".499" stopColor="#D92E83" />
						<stop offset="1" stopColor="#FFD500" />
					</linearGradient>
				</defs>
			</svg>
		),
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omnigroup' ),
		attributes: { type: 'week' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
	variationAttributes.type;
} );

export default variations;
