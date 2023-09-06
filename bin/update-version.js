const fs = require( 'fs' );
const packageJson = require( '../package.json' );

const filesToUpdate = [
	{
		filePath: 'readme.txt',
		searchPattern: /Stable tag.*/,
		replacePattern: 'Stable tag: ' + packageJson.version,
	},
	{
		filePath: 'omniform.php',
		searchPattern: /Version.*/,
		replacePattern: 'Version: ' + packageJson.version,
	},
	{
		filePath: 'includes/Application.php',
		searchPattern: /const VERSION.*/,
		replacePattern: 'const VERSION = \'' + packageJson.version + '\';',
	},
];

// Update the version number in the files.
filesToUpdate.forEach( ( file ) => {
	fs.readFile( file.filePath, 'utf8', ( readError, data ) => {
		if ( readError ) {
			throw new Error( readError );
		}

		// Replace the version number.
		const result = data.replace( file.searchPattern, file.replacePattern );

		// Write the new version number to the file.
		fs.writeFile( file.filePath, result, 'utf8', ( writeError ) => {
			if ( writeError ) {
				throw new Error( writeError );
			}
		} );
	} );
} );
