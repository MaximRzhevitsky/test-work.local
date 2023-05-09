import TomSelect from '../npm-proxy/tom-select.js';

const settings = {
	plugins: [ 'checkbox_options' ],
};
new TomSelect( '#ex-checkbox-options', settings );

new TomSelect( '#example-select', {
	create: true,
	sortField: {
		field: 'text',
		direction: 'asc',
	},
} );
