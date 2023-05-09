const through = require( 'through2' );
const File = require( 'vinyl' );
const { dirname } = require( 'path' );

module.exports = function () {
	/**
	 * @param {File}                      file
	 * @param {string=}                   encoding - ignored if file contains a Buffer
	 * @param {function(Error?, object?)} callback - Call this function (optionally with an error argument and data) when you are done processing the supplied chunk.
	 */
	const transform = function ( file, encoding, callback ) {
		file.base = dirname( file.path );
		callback( null, file );
	};

	return through.obj( transform );
};
