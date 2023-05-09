const gFormIndex = require( './gFormIndex' );
let lastGFormIndex = 0;
let gFormFieldIndex = 0;

module.exports = function ( next ) {
	const currentGFormIndex = gFormIndex();

	if ( lastGFormIndex !== currentGFormIndex ) {
		lastGFormIndex = currentGFormIndex;
		gFormFieldIndex = 0;
	}

	if ( true === next ) {
		gFormFieldIndex++;
	}

	return gFormFieldIndex;
};
