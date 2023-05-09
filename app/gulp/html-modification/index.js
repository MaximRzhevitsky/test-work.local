const through = require( 'through2' );
const File = require( 'vinyl' );
const beautifyHtml = require( 'js-beautify' ).html;

module.exports = function () {
	/**
	 * @param {File}                      file
	 * @param {string=}                   encoding - ignored if file contains a Buffer
	 * @param {function(Error?, object?)} callback - Call this function (optionally with an error argument and data) when you are done processing the supplied chunk.
	 */
	const transform = function ( file, encoding, callback ) {
		const content = file.contents.toString();
		const [ head, body ] = content.split( /<(?=body)/ );
		const links = new Set();

		const newBody = body.replace(
			/(?<linkTag><link[^>]* href=['"]*(?<url>[^'"]+)['"][^>]*\/?>)/g,
			( ...match ) => {
				const groups = match.pop();

				links.add( groups.linkTag );

				return '<!-- ' + groups.url + ' -->';
			}
		);

		const newHead = head.replace(
			/<\/head>/,
			`	${ Array.from( links ).join( '\t\r\n' ) }\n</head>`
		);

		file.contents = Buffer.from(
			beautifyHtml( newHead + '<' + newBody, {
				indent_size: 4,
				indent_char: ' ',
				indent_level: 0,
				indent_with_tabs: false,
				preserve_newlines: true,
				max_preserve_newlines: 1,
				jslint_happy: false,
				space_after_named_function: false,
				space_after_anon_function: false,
				brace_style: 'collapse',
				keep_array_indentation: false,
				keep_function_indentation: false,
				space_before_conditional: true,
				break_chained_methods: false,
				eval_code: false,
				unescape_strings: false,
				wrap_line_length: 0,
				indent_empty_lines: false,
				templating: [ 'auto' ],
			} )
		);

		callback( null, file );
	};

	return through.obj( transform );
};
