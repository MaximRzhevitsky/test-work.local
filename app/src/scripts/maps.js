// noinspection JSCheckFunctionSignatures, JSUnresolvedVariable, JSUnresolvedFunction, JSUnresolvedVariable

import { map } from '../../base/defaults/scripts/libs/map';

map( '.map', function () {
	/**
	 * @type {HTMLDivElement}
	 */
	const mapCanvas = this;
	const position = new google.maps.LatLng(
		mapCanvas.dataset.latitude,
		mapCanvas.dataset.longitude
	);
	const markerIcon = mapCanvas.dataset.icon;
	const mapZoom = mapCanvas.dataset.zoom;
	const mapObject = new google.maps.Map( mapCanvas, {
		zoom: mapZoom || 17,
		center: position,
		disableDefaultUI: true,
	} );

	new google.maps.Marker( {
		position,
		mapObject,
		icon: markerIcon || '',
	} );
} );
