let tabIndex = 0;

module.exports = function ( next ) {
	if ( true === next ) {
		tabIndex++;
	}

	return tabIndex;
};
