let accordionIndex = 0;

module.exports = function ( next ) {
	if ( true === next ) {
		accordionIndex++;
	}

	return accordionIndex;
};
