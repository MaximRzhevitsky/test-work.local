import './block';

( function ( $ ) {
	$.fn.wldCheckoutBlocks = function () {
		const $allBlocks = this;
		const $submitBtn = $( '#place_order' );
		const $agreeCheckbox = $( '#terms' );

		function next( $next, i, scroll ) {
			if ( $next.length ) {
				if ( $next.hasClass( 'block-done' ) ) {
					const nextI = i + 1;
					next( $allBlocks.eq( nextI ), nextI, scroll );
					return;
				}

				$next.addClass( 'block-edited' );

				const $blocks = $next.closest( '.blocks' );
				if ( $blocks.length ) {
					const allDone =
						$blocks.find( '.block:not(.block-done)' ).length === 0;

					if ( allDone ) {
						$blocks.addClass( 'block-done' );
					} else {
						$blocks.addClass( 'block-edited' );
					}
				}

				if ( $next.hasClass( 'block-static' ) ) {
					const nextI = i + 1;
					$next.addClass( 'block-done' );
					next( $allBlocks.eq( nextI ), nextI );
				}

				if ( scroll === true ) {
					$( 'html, body' ).animate(
						{
							scrollTop: $next.offset().top - 140,
						},
						1000
					);
				}

				isDisabledBtn();
			}
		}

		function save( $block ) {
			$block.wldBlock();

			const fields = {};
			$.each( $block.find( ':input' ).serializeArray(), function () {
				fields[ this.name ] = this.value;
			} );

			$.post( {
				dataType: 'json',
				url: theme.ajaxUrl,
				data: {
					nonce: theme.ajaxNonce,
					action: 'get_block_format_content',
					type: $block.data( 'block-type' ),
					fields,
				},
				success( response ) {
					$( '.woocommerce-input-wrapper .error' ).remove();
					if ( response.success ) {
						const i = $block.blockIndex + 1;
						const $newBlock = $( response.data.content );

						$block
							.attr( 'class', $newBlock.attr( 'class' ) )
							.find( '.block-format-content' )
							.replaceWith(
								$newBlock.find( '.block-format-content' )
							);

						next( $allBlocks.eq( i ), i, true );
					} else {
						response.data.forEach( ( item ) => {
							$( `#${ item.id }` )
								.closest( '.woocommerce-input-wrapper' )
								.append(
									`<span class="error">${ item.message }</span>`
								)
								.end()
								.closest( '.form-row' )
								.addClass( 'woocommerce-invalid' );
						} );
					}

					$block.wldUnblock();
				},
				error() {
					$block.wldUnblock();
					// eslint-disable-next-line no-alert
					alert( 'error' );
				},
			} );
		}

		function edit( $block ) {
			$block.addClass( 'block-edited' );
		}

		function isDisabledBtn() {
			if (
				$agreeCheckbox.prop( 'checked' ) &&
				! $( '.checkout-blocks .block-empty' ).length
			) {
				$submitBtn.removeAttr( 'disabled' );
			} else {
				$submitBtn.attr( 'disabled', true );
			}
		}

		if ( $allBlocks && $allBlocks.length ) {
			$allBlocks.each( function ( i ) {
				const $block = $( this );

				$block.blockIndex = i;

				if ( $block.hasClass( 'block-static' ) ) {
					return;
				}

				$block
					.on( 'click', '.btn-edit', () => edit( $block ) )
					.on( 'click', '.btn-save', () => save( $block ) )
					.on( 'click', () => {
						if (
							$block.hasClass( 'block-empty' ) &&
							$block.not( '.block-edited' ) &&
							( $block.prev().length === 0 ||
								( $block.prev().hasClass( 'block-done' ) &&
									$block.prev().not( '.block-edited' ) ) )
						) {
							edit( $block );
						}
					} );
			} );
		}

		$submitBtn.attr( 'disabled', true );
		$agreeCheckbox.on( 'change', function () {
			isDisabledBtn();
		} );

		return this;
	};
} )( jQuery );
