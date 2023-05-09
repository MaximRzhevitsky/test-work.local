module.exports = ( options = { atRuleName: 'modifier' } ) => {
	return {
		postcssPlugin: 'postcss-at-modifier',
		AtRule: {
			[ options.atRuleName ]: ( atRule, { Rule } ) => {
				let block = atRule;
				while (
					block &&
					block.parent &&
					block.parent.type !== 'root'
				) {
					block = block.parent;
				}

				const parent = atRule.parent;
				const [ modName, modValue ] = atRule.params
					.replace( /^\(| |\)$/g, '' )
					.split( ',' );
				const [ blockName, elementName ] = block.selector.split( '__' );
				const blockSelectorWithModification =
					blockName +
					'_' +
					modName +
					( modValue ? '_' + modValue : '' );

				const nodes = [];
				const declarations = [];

				if ( elementName ) {
					nodes.push( ...atRule.nodes );
				} else {
					atRule.each( ( node ) => {
						if ( 'decl' === node.type || 'atrule' === node.type ) {
							declarations.push( node );
						} else {
							nodes.push( node );
						}
					} );
				}

				if ( nodes.length ) {
					const blockWithModification = new Rule( {
						selector:
							blockSelectorWithModification +
							' ' +
							block.selector,
						nodes,
						source: atRule.source,
					} );

					atRule.root().insertAfter( block, blockWithModification );
				}

				if ( declarations.length ) {
					const blockWithModification = new Rule( {
						selector: blockSelectorWithModification,
						nodes: declarations,
						source: atRule.source,
					} );

					atRule.root().insertAfter( block, blockWithModification );
				}

				atRule.remove();

				if ( 0 === parent.nodes.length ) {
					parent.remove();
				}
			},
		},
	};
};

module.exports.postcss = true;
