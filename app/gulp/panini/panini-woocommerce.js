import { Panini } from 'panini';

let panini;

export let paniniWoocommerceRefresh;

export function paniniWoocommerce( options ) {
	if ( ! panini ) {
		panini = new Panini( options );
		panini.loadBuiltinHelpers();
		panini.refresh();
		paniniWoocommerceRefresh = panini.refresh.bind( panini );
	}

	return panini.render();
}
