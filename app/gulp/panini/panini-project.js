import { Panini } from 'panini';

let panini;

export let paniniProjectRefresh;

export function paniniProject( options ) {
	if ( ! panini ) {
		panini = new Panini( options );
		panini.loadBuiltinHelpers();
		panini.refresh();
		paniniProjectRefresh = panini.refresh.bind( panini );
	}

	return panini.render();
}
