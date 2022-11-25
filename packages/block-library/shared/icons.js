/**
 * WordPress dependencies
 */
import { SVG, Path } from '@wordpress/primitives';

export const form = (
	<SVG viewBox="0 0 24 24">
		<path fill="url(#form-a)" fillRule="evenodd"
			d="M3.33 15.424a4.842 4.842 0 0 1 0-6.848l.207-.208v-2.42a2.421 2.421 0 0 1 2.421-2.422H8.38l.086-.086a4.842 4.842 0 0 1 6.848 0l.086.086h2.665a2.421 2.421 0 0 1 2.421 2.421v2.665a4.842 4.842 0 0 1 0 6.776v2.665a2.421 2.421 0 0 1-2.421 2.42h-2.665l-.086.087a4.842 4.842 0 0 1-6.848 0l-.086-.086H5.96a2.421 2.421 0 0 1-2.422-2.421v-2.421l-.207-.208ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z"
			clipRule="evenodd" />
		<defs>
			<linearGradient id="form-a" x1="-.084" x2="38.232" y1="-11.948" y2="7.21" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldInput = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-input-a)" d="M14 11H6v2h8v-2Z" />
		<Path fill="url(#field-input-b)" fillRule="evenodd"
			d="M2 9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9Zm2 0h16v6H4V9Z" clipRule="evenodd" />
		<defs>
			<linearGradient id="field-input-a" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-input-b" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldEmail = (
	<SVG xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
		<Path fill="url(#field-email-a)"
			d="M12 1.95c-5.52 0-10 4.48-10 10s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57v-1.43c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57v-1.43c0-5.52-4.48-10-10-10Zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3Z" />
		<defs>
			<linearGradient id="field-email-a" x1="0" x2="38.4" y1="-12.05" y2="7.15" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldUrl = (
	<SVG xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
		<Path fill="url(#field-url-a)"
			d="M17 7h-4v2h4c1.65 0 3 1.35 3 3s-1.35 3-3 3h-4v2h4c2.76 0 5-2.24 5-5s-2.24-5-5-5Zm-6 8H7c-1.65 0-3-1.35-3-3s1.35-3 3-3h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-2Zm-3-4h8v2H8v-2Z" />
		<defs>
			<linearGradient id="field-url-a" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldNumber = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-number-a)"
			d="m20.5 10 .5-2h-4l1-4h-2l-1 4h-4l1-4h-2L9 8H5l-.5 2h4l-1 4h-4L3 16h4l-1 4h2l1-4h4l-1 4h2l1-4h4l.5-2h-4l1-4h4Zm-7 4h-4l1-4h4l-1 4Z" />
		<defs>
			<linearGradient id="field-number-a" x1="1.2" x2="34.017" y1="-7.2" y2="11.259" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldCheckbox = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-checkbox-a)"
			d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2Zm0 16H5V5h14v14ZM17.99 9l-1.41-1.42-6.59 6.59-2.58-2.57-1.42 1.41 4 3.99 8-8Z" />
		<defs>
			<linearGradient id="field-checkbox-a" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldRadio = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-radio-a)" d="M17 12a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
		<Path fill="url(#field-radio-b)" fillRule="evenodd"
			d="M2 12C2 6.48 6.48 2 12 2s10 4.48 10 10-4.48 10-10 10S2 17.52 2 12Zm2 0c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8-8 3.58-8 8Z"
			clipRule="evenodd" />
		<defs>
			<linearGradient id="field-radio-a" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-radio-b" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldColor = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-color-a)"
			d="m17.66 5.41.92.92-2.69 2.69-.92-.92 2.69-2.69M17.67 3c-.26 0-.51.1-.71.29l-3.12 3.12-1.93-1.91-1.41 1.41 1.42 1.42L3 16.25V21h4.75l8.92-8.92 1.42 1.42 1.41-1.41-1.92-1.92 3.12-3.12c.4-.4.4-1.03.01-1.42l-2.34-2.34c-.2-.19-.45-.29-.7-.29ZM6.92 19 5 17.08l8.06-8.06 1.92 1.92L6.92 19Z" />
		<defs>
			<linearGradient id="field-color-a" x1="1.2" x2="35.761" y1="-9.6" y2="7.682" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldDate = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-date-a)"
			d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2Zm0 16H5V10h14v10Zm0-12H5V6h14v2ZM9 14H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Zm-8 4H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Z" />
		<defs>
			<linearGradient id="field-date-a" x1="1.2" x2="37.125" y1="-12" y2="4.166" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldFile = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-file-a)"
			d="M18 15v3H6v-3H4v3c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3h-2ZM7 9l1.41 1.41L11 7.83V16h2V7.83l2.59 2.58L17 9l-5-5-5 5Z" />
		<defs>
			<linearGradient id="field-file-a" x1="2.4" x2="33.12" y1="-7.2" y2="8.16" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldPassword = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-password-a)"
			d="M2 17h20v2H2v-2Zm1.15-4.05L4 11.47l.85 1.48 1.3-.75-.85-1.48H7v-1.5H5.3l.85-1.47L4.85 7 4 8.47 3.15 7l-1.3.75.85 1.47H1v1.5h1.7l-.85 1.48 1.3.75Zm6.7-.75 1.3.75.85-1.48.85 1.48 1.3-.75-.85-1.48H15v-1.5h-1.7l.85-1.47-1.3-.75L12 8.47 11.15 7l-1.3.75.85 1.47H9v1.5h1.7l-.85 1.48ZM23 9.22h-1.7l.85-1.47-1.3-.75L20 8.47 19.15 7l-1.3.75.85 1.47H17v1.5h1.7l-.85 1.48 1.3.75.85-1.48.85 1.48 1.3-.75-.85-1.48H23v-1.5Z" />
		<defs>
			<linearGradient id="field-password-a" x1="-1.2" x2="27.491" y1="-1.4" y2="24.9" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldRange = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-range-a)"
			d="M16.9 11a5 5 0 0 0-9.8 0H2v2h5.1a5 5 0 0 0 9.8 0H22v-2h-5.1ZM12 15c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3Z" />
		<defs>
			<linearGradient id="field-range-a" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldSearch = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-search-a)"
			d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z" />
		<defs>
			<linearGradient id="field-search-a" x1="1.251" x2="34.832" y1="-9.243" y2="7.547" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldTel = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-tel-a)"
			d="M6.54 5c.06.89.21 1.76.45 2.59l-1.2 1.2c-.41-1.2-.67-2.47-.76-3.79h1.51m9.86 12.02c.85.24 1.72.39 2.6.45v1.49c-1.32-.09-2.59-.35-3.8-.75l1.2-1.19M7.5 3H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.49c0-.55-.45-1-1-1-1.24 0-2.45-.2-3.57-.57a.84.84 0 0 0-.31-.05c-.26 0-.51.1-.71.29l-2.2 2.2a15.149 15.149 0 0 1-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02A11.36 11.36 0 0 1 8.5 4c0-.55-.45-1-1-1Z" />
		<defs>
			<linearGradient id="field-tel-a" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldTime = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-time-a)"
			d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2ZM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8Zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7Z" />
		<defs>
			<linearGradient id="field-time-a" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldTextarea = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-textarea-a)" d="M18 9H6v2h12V9Z" />
		<Path fill="url(#field-textarea-b)" d="M6 13h8v2H6v-2Z" />
		<Path fill="url(#field-textarea-c)" fillRule="evenodd"
			d="M4 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4Zm16 2H4v10h16V7Z"
			clipRule="evenodd" />
		<defs>
			<linearGradient id="field-textarea-a" x1="0" x2="31.784" y1="-4.8" y2="17.903" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-textarea-b" x1="0" x2="31.784" y1="-4.8" y2="17.903" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-textarea-c" x1="0" x2="31.784" y1="-4.8" y2="17.903" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldSelect = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-select-a)" d="M6 7h8v2H6V7Z" />
		<Path fill="url(#field-select-b)" d="M14 11H6v2h8v-2Z" />
		<Path fill="url(#field-select-c)" d="M6 15h8v2H6v-2Z" />
		<Path fill="url(#field-select-d)" d="M18 7h-2v2h2V7Z" />
		<Path fill="url(#field-select-e)" fillRule="evenodd"
			d="M4 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12.5a1.5 1.5 0 0 0 1.5-1.5V13h2.5a1.5 1.5 0 0 0 1.5-1.5V5a2 2 0 0 0-2-2H4Zm16 8V5H4v14h12v-6.5a1.5 1.5 0 0 1 1.5-1.5H20Z"
			clipRule="evenodd" />
		<defs>
			<linearGradient id="field-select-a" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-select-b" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-select-c" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-select-d" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-select-e" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const selectOption = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#select-option-a)"
			d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7Zm-1-5C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2Zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8Z" />
		<defs>
			<linearGradient id="select-option-a" x1="0" x2="38.4" y1="-12" y2="7.2" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const selectGroup = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#select-group-a)" d="M14 10H3v2h11v-2Zm0-4H3v2h11V6Zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4ZM3 16h7v-2H3v2Z" />
		<defs>
			<linearGradient id="select-group-a" x1="1.1" x2="32.323" y1="-3.8" y2="17.387" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const fieldGroup = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#field-group-a)" d="M7 7h2v2H7V7Z" />
		<Path fill="url(#field-group-b)" d="M9 11H7v2h2v-2Z" />
		<Path fill="url(#field-group-c)" d="M7 15h2v2H7v-2Z" />
		<Path fill="url(#field-group-d)" d="M17 11h-6v2h6v-2Z" />
		<Path fill="url(#field-group-e)" d="M11 15h6v2h-6v-2Z" />
		<Path fill="url(#field-group-f)" d="M17 7h-6v2h6V7Z" />
		<Path fill="url(#field-group-g)" fillRule="evenodd"
			d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Zm14 2H5v14h14V5Z"
			clipRule="evenodd" />
		<defs>
			<linearGradient id="field-group-a" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-group-b" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-group-c" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-group-d" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-group-e" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-group-f" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="field-group-g" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);

export const Button = (
	<SVG viewBox="0 0 24 24">
		<Path fill="url(#button-a)" d="M17 11H7v2h10v-2Z" />
		<Path fill="url(#button-b)" fillRule="evenodd"
			d="M2 8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8Zm2 0h16v8H4V8Z" clipRule="evenodd" />
		<defs>
			<linearGradient id="button-a" x1="0" x2="28.328" y1="-2.4" y2="21.207" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
			<linearGradient id="button-b" x1="0" x2="28.328" y1="-2.4" y2="21.207" gradientUnits="userSpaceOnUse">
				<stop stopColor="#842ED9" />
				<stop offset=".499" stopColor="#D92E83" />
				<stop offset="1" stopColor="#FFD500" />
			</linearGradient>
		</defs>
	</SVG>
);
