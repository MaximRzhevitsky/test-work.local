let tabPanelIndex = 0;

module.exports = function ( next ) {
	if ( true === next ) {
		tabPanelIndex++;
	}

	return tabPanelIndex;
};
