const $select = $('.gfield select');

if ($select.length) {
	require.ensure(
		[],
		(require) => {
			require('select2');
			require('select2/dist/css/select2.min.css');

			$select.select2({
				dropdownCssClass: 'gfield-select-dropdown',
			});
		},
		'select2'
	);
}
