let tabsIndex = 0;

module.exports = function ( next ) {
	if ( true === next ) {
		tabsIndex++;
	}

	return tabsIndex;
};
