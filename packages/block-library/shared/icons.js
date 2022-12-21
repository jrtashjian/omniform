/**
 * WordPress dependencies
 */
import { Path, SVG } from '@wordpress/primitives';

export const form = (
	<SVG viewBox="0 0 24 24">
		<Path fillRule="evenodd" d="M3.33 15.424a4.842 4.842 0 0 1 0-6.848l.207-.208v-2.42a2.421 2.421 0 0 1 2.421-2.422H8.38l.086-.086a4.842 4.842 0 0 1 6.848 0l.086.086h2.665a2.421 2.421 0 0 1 2.421 2.421v2.665a4.842 4.842 0 0 1 0 6.776v2.665a2.421 2.421 0 0 1-2.421 2.42h-2.665l-.086.087a4.842 4.842 0 0 1-6.848 0l-.086-.086H5.96a2.421 2.421 0 0 1-2.422-2.421v-2.421l-.207-.208ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z" clipRule="evenodd" />
	</SVG>
);

export const fieldInput = (
	<SVG viewBox="0 0 24 24">
		<Path d="M14 11H6v2h8v-2Z" />
		<Path fillRule="evenodd" d="M2 9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9Zm2 0h16v6H4V9Z" clipRule="evenodd" />
	</SVG>
);

export const fieldEmail = (
	<SVG xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
		<Path d="M12 1.95c-5.52 0-10 4.48-10 10s4.48 10 10 10h5v-2h-5c-4.34 0-8-3.66-8-8s3.66-8 8-8 8 3.66 8 8v1.43c0 .79-.71 1.57-1.5 1.57s-1.5-.78-1.5-1.57v-1.43c0-2.76-2.24-5-5-5s-5 2.24-5 5 2.24 5 5 5c1.38 0 2.64-.56 3.54-1.47.65.89 1.77 1.47 2.96 1.47 1.97 0 3.5-1.6 3.5-3.57v-1.43c0-5.52-4.48-10-10-10Zm0 13c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3Z" />
	</SVG>
);

export const fieldUrl = (
	<SVG xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
		<Path d="M17 7h-4v2h4c1.65 0 3 1.35 3 3s-1.35 3-3 3h-4v2h4c2.76 0 5-2.24 5-5s-2.24-5-5-5Zm-6 8H7c-1.65 0-3-1.35-3-3s1.35-3 3-3h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-2Zm-3-4h8v2H8v-2Z" />
	</SVG>
);

export const fieldNumber = (
	<SVG viewBox="0 0 24 24">
		<Path d="m20.5 10 .5-2h-4l1-4h-2l-1 4h-4l1-4h-2L9 8H5l-.5 2h4l-1 4h-4L3 16h4l-1 4h2l1-4h4l-1 4h2l1-4h4l.5-2h-4l1-4h4Zm-7 4h-4l1-4h4l-1 4Z" />
	</SVG>
);

export const fieldCheckbox = (
	<SVG viewBox="0 0 24 24">
		<Path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2Zm0 16H5V5h14v14ZM17.99 9l-1.41-1.42-6.59 6.59-2.58-2.57-1.42 1.41 4 3.99 8-8Z" />
	</SVG>
);

export const fieldRadio = (
	<SVG viewBox="0 0 24 24">
		<Path d="M17 12a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z" />
		<Path fillRule="evenodd" d="M2 12C2 6.48 6.48 2 12 2s10 4.48 10 10-4.48 10-10 10S2 17.52 2 12Zm2 0c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8-8 3.58-8 8Z" clipRule="evenodd" />
	</SVG>
);

export const fieldColor = (
	<SVG viewBox="0 0 24 24">
		<Path d="m17.66 5.41.92.92-2.69 2.69-.92-.92 2.69-2.69M17.67 3c-.26 0-.51.1-.71.29l-3.12 3.12-1.93-1.91-1.41 1.41 1.42 1.42L3 16.25V21h4.75l8.92-8.92 1.42 1.42 1.41-1.41-1.92-1.92 3.12-3.12c.4-.4.4-1.03.01-1.42l-2.34-2.34c-.2-.19-.45-.29-.7-.29ZM6.92 19 5 17.08l8.06-8.06 1.92 1.92L6.92 19Z" />
	</SVG>
);

export const fieldDate = (
	<SVG viewBox="0 0 24 24">
		<Path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2Zm0 16H5V10h14v10Zm0-12H5V6h14v2ZM9 14H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Zm-8 4H7v-2h2v2Zm4 0h-2v-2h2v2Zm4 0h-2v-2h2v2Z" />
	</SVG>
);

export const fieldFile = (
	<SVG viewBox="0 0 24 24">
		<Path d="M18 15v3H6v-3H4v3c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-3h-2ZM7 9l1.41 1.41L11 7.83V16h2V7.83l2.59 2.58L17 9l-5-5-5 5Z" />
	</SVG>
);

export const fieldPassword = (
	<SVG viewBox="0 0 24 24">
		<Path d="M2 17h20v2H2v-2Zm1.15-4.05L4 11.47l.85 1.48 1.3-.75-.85-1.48H7v-1.5H5.3l.85-1.47L4.85 7 4 8.47 3.15 7l-1.3.75.85 1.47H1v1.5h1.7l-.85 1.48 1.3.75Zm6.7-.75 1.3.75.85-1.48.85 1.48 1.3-.75-.85-1.48H15v-1.5h-1.7l.85-1.47-1.3-.75L12 8.47 11.15 7l-1.3.75.85 1.47H9v1.5h1.7l-.85 1.48ZM23 9.22h-1.7l.85-1.47-1.3-.75L20 8.47 19.15 7l-1.3.75.85 1.47H17v1.5h1.7l-.85 1.48 1.3.75.85-1.48.85 1.48 1.3-.75-.85-1.48H23v-1.5Z" />
	</SVG>
);

export const fieldRange = (
	<SVG viewBox="0 0 24 24">
		<Path d="M16.9 11a5 5 0 0 0-9.8 0H2v2h5.1a5 5 0 0 0 9.8 0H22v-2h-5.1ZM12 15c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3Z" />
	</SVG>
);

export const fieldSearch = (
	<SVG viewBox="0 0 24 24">
		<Path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z" />
	</SVG>
);

export const fieldTel = (
	<SVG viewBox="0 0 24 24">
		<Path d="M6.54 5c.06.89.21 1.76.45 2.59l-1.2 1.2c-.41-1.2-.67-2.47-.76-3.79h1.51m9.86 12.02c.85.24 1.72.39 2.6.45v1.49c-1.32-.09-2.59-.35-3.8-.75l1.2-1.19M7.5 3H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.49c0-.55-.45-1-1-1-1.24 0-2.45-.2-3.57-.57a.84.84 0 0 0-.31-.05c-.26 0-.51.1-.71.29l-2.2 2.2a15.149 15.149 0 0 1-6.59-6.59l2.2-2.2c.28-.28.36-.67.25-1.02A11.36 11.36 0 0 1 8.5 4c0-.55-.45-1-1-1Z" />
	</SVG>
);

export const fieldTime = (
	<SVG viewBox="0 0 24 24">
		<Path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2ZM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8Zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7Z" />
	</SVG>
);

export const fieldHidden = (
	<SVG viewBox="0 0 24 24">
		<Path fillRule="evenodd" d="M4.273 3 3 4.273 5.727 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h11.727l4.003 4.003 1.273-1.273L4.273 3ZM4 9h3.727l2 2H6v2h5.727l2 2H4V9Z" clipRule="evenodd" />
		<Path d="M18.819 15H20V9h-7.181l-2-2H20a2 2 0 0 1 2 2v6a2 2 0 0 1-1.305 1.876L18.819 15Z" />
	</SVG>
);

export const fieldHoneypot = (
	<SVG viewBox="0 0 24 24">
		<Path d="m13.62 8 1.8-3-1.8-3h-3.58l-1.8 3 1.8 3h3.58Zm-3.58 1-1.8 3 1.8 3h3.58l1.8-3-1.8-3h-3.58Zm6.24 2.51h3.59l1.79-3-1.79-3h-3.59l-1.8 3 1.8 3Zm3.59 1h-3.59l-1.8 3 1.8 3h3.59l1.79-3-1.79-3Zm-12.49-1 1.8-3-1.8-3H3.79L2 8.51l1.79 3h3.59Zm0 1H3.79l-1.79 3 1.79 3h3.59l1.8-3-1.8-3ZM10.04 16l-1.8 3 1.8 3h3.58l1.8-3-1.8-3h-3.58Z" />
	</SVG>
);

export const fieldTextarea = (
	<SVG viewBox="0 0 24 24">
		<Path d="M18 9H6v2h12V9Z" />
		<Path d="M6 13h8v2H6v-2Z" />
		<Path fillRule="evenodd" d="M4 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4Zm16 2H4v10h16V7Z" clipRule="evenodd" />
	</SVG>
);

export const fieldSelect = (
	<SVG viewBox="0 0 24 24">
		<Path d="M6 7h8v2H6V7Z" />
		<Path d="M14 11H6v2h8v-2Z" />
		<Path d="M6 15h8v2H6v-2Z" />
		<Path d="M18 7h-2v2h2V7Z" />
		<Path fillRule="evenodd" d="M4 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12.5a1.5 1.5 0 0 0 1.5-1.5V13h2.5a1.5 1.5 0 0 0 1.5-1.5V5a2 2 0 0 0-2-2H4Zm16 8V5H4v14h12v-6.5a1.5 1.5 0 0 1 1.5-1.5H20Z" clipRule="evenodd" />
	</SVG>
);

export const selectOption = (
	<SVG viewBox="0 0 24 24">
		<Path d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7Zm-1-5C6.49 2 2 6.49 2 12s4.49 10 10 10 10-4.49 10-10S17.51 2 12 2Zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8Z" />
	</SVG>
);

export const selectGroup = (
	<SVG viewBox="0 0 24 24">
		<Path d="M14 10H3v2h11v-2Zm0-4H3v2h11V6Zm4 8v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4ZM3 16h7v-2H3v2Z" />
	</SVG>
);

export const fieldGroup = (
	<SVG viewBox="0 0 24 24">
		<Path d="M7 7h2v2H7V7Z" />
		<Path d="M9 11H7v2h2v-2Z" />
		<Path d="M7 15h2v2H7v-2Z" />
		<Path d="M17 11h-6v2h6v-2Z" />
		<Path d="M11 15h6v2h-6v-2Z" />
		<Path d="M17 7h-6v2h6V7Z" />
		<Path fillRule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Zm14 2H5v14h14V5Z" clipRule="evenodd" />
	</SVG>
);

export const Button = (
	<SVG viewBox="0 0 24 24">
		<Path d="M17 11H7v2h10v-2Z" />
		<Path fillRule="evenodd" d="M2 8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8Zm2 0h16v8H4V8Z" clipRule="evenodd" />
	</SVG>
);

export const Required = (
	<SVG viewBox="0 0 24 24">
		<Path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1 15h-2v-2h2v2Zm0-4h-2V7h2v6Z" />
	</SVG>
);

export const typeGeneral = (
	<SVG viewBox="0 0 24 24">
		<Path fill-rule="evenodd" d="M13 9.5h5v-2h-5v2Zm0 7h5v-2h-5v2Zm6 4.5H5c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v14c0 1.1-.9 2-2 2ZM6 11h5V6H6v5Zm1-4h3v3H7V7ZM6 18h5v-5H6v5Zm1-4h3v3H7v-3Z" clip-rule="evenodd" />
	</SVG>
);

export const typeSearch = (
	<SVG viewBox="0 0 24 24">
		<Path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5Zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14Z" />
	</SVG>
);

export const typeLock = (
	<SVG viewBox="0 0 24 24">
		<Path d="M18 11c.7 0 1.37.1 2 .29V10c0-1.1-.9-2-2-2h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h6.26A6.995 6.995 0 0 1 18 11ZM8.9 6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2H8.9V6Z" />
		<Path d="M18 13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5Zm0 2c.83 0 1.5.67 1.5 1.5S18.83 18 18 18s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5Zm0 6c-1.03 0-1.94-.52-2.48-1.32.73-.42 1.57-.68 2.48-.68.91 0 1.75.26 2.48.68-.54.8-1.45 1.32-2.48 1.32Z" />
	</SVG>
);
