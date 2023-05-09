import { Panini } from 'panini';

let panini;

export let paniniDefaultsRefresh;

export function paniniDefaults( options ) {
	if ( ! panini ) {
		panini = new Panini( options );
		panini.loadBuiltinHelpers();
		panini.refresh();
		paniniDefaultsRefresh = panini.refresh.bind( panini );
	}

	return panini.render();
}
