const gFormFieldIndex = require( './gFormFieldIndex' );
let lastGFormFieldIndex = 0;
let gFormSubFieldIndex = 0;

module.exports = function ( next ) {
	const currentGFormFieldIndex = gFormFieldIndex();

	if ( lastGFormFieldIndex !== currentGFormFieldIndex ) {
		lastGFormFieldIndex = currentGFormFieldIndex;
		gFormSubFieldIndex = 0;
	}

	if ( true === next ) {
		gFormSubFieldIndex++;
	}

	return gFormSubFieldIndex;
};
