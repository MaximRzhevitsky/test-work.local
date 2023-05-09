module.exports = {
	...require( '@wordpress/prettier-config' ),
	overrides: [
		{
			files: '*.{css,sass,scss,pcss}',
			options: {
				singleQuote: false,
				parenSpacing: false,
			},
		},
	],
};
