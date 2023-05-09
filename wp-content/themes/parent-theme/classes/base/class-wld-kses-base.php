<?php

class WLD_KSES_Base {
	protected static array $allowed_tags = array(
		'dialog' => array(
			'open' => true,
		),
		'iframe' => array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'allowfullscreen' => true,
			'loading'         => true,
			'title'           => true,
		),
		'img'    => array(
			'decoding'   => true,
			'importance' => true,
			'sizes'      => true,
			'srcset'     => true,
		),
		'form'   => array(
			'action'         => true,
			'accept'         => true,
			'accept-charset' => true,
			'enctype'        => true,
			'method'         => true,
			'name'           => true,
			'target'         => true,
		),
		'input'  => array(
			'accept'          => true,
			'accesskey'       => true,
			'align'           => true,
			'alt'             => true,
			'autocomplete'    => true,
			'autofocus'       => true,
			'checked'         => true,
			'contenteditable' => true,
			'dirname'         => true,
			'disabled'        => true,
			'draggable'       => true,
			'dropzone'        => true,
			'form'            => true,
			'formaction'      => true,
			'formenctype'     => true,
			'formmethod'      => true,
			'formnovalidate'  => true,
			'formtarget'      => true,
			'height'          => true,
			'hidden'          => true,
			'lang'            => true,
			'list'            => true,
			'max'             => true,
			'maxlength'       => true,
			'min'             => true,
			'multiple'        => true,
			'name'            => true,
			'pattern'         => true,
			'placeholder'     => true,
			'readonly'        => true,
			'required'        => true,
			'size'            => true,
			'spellcheck'      => true,
			'src'             => true,
			'step'            => true,
			'translate'       => true,
			'type'            => true,
			'value'           => true,
			'width'           => true,
		),
		'select' => array(
			'accesskey'       => true,
			'autofocus'       => true,
			'contenteditable' => true,
			'disabled'        => true,
			'draggable'       => true,
			'dropzone'        => true,
			'form'            => true,
			'hidden'          => true,
			'lang'            => true,
			'multiple'        => true,
			'name'            => true,
			'onblur'          => true,
			'onchange'        => true,
			'oncontextmenu'   => true,
			'onfocus'         => true,
			'oninput'         => true,
			'oninvalid'       => true,
			'onreset'         => true,
			'onsearch'        => true,
			'onselect'        => true,
			'onsubmit'        => true,
			'required'        => true,
			'size'            => true,
			'spellcheck'      => true,
			'translate'       => true,
		),
		'option' => array(
			'disabled' => true,
			'label'    => true,
			'selected' => true,
			'value'    => true,
		),
		'button' => array(
			'aria-expanded' => true,
			'aria-controls' => true,
		),
	);

	public static function init() : void {
		static::$allowed_tags = array_map(
			array( static::class, 'add_global_attributes' ),
			static::$allowed_tags
		);

		add_filter(
			'wp_kses_allowed_html',
			array( static::class, 'wp_kses_allowed_html_hook' ),
			10,
			2
		);
	}

	public static function wp_kses_allowed_html_hook( array $allowed_tags, string $context ) : array {
		static $_allowed_tags = null;

		if ( 'post' === $context ) {
			if ( null === $_allowed_tags ) {
				$_allowed_tags = $allowed_tags;
				foreach ( static::$allowed_tags as $allowed_tag => $allowed_attributes ) {
					if ( ! isset( $_allowed_tags[ $allowed_tag ] ) ) {
						$_allowed_tags[ $allowed_tag ] = $allowed_attributes;
					} else {
						$_allowed_tags[ $allowed_tag ] = array_merge(
							$allowed_attributes,
							$allowed_tags[ $allowed_tag ]
						);
					}
				}
				$exclude_tabindex = array(
					'br'     => true,
					'dialog' => true,
					'option' => true,
				);
				foreach ( $_allowed_tags as $allowed_tag => $allowed_attributes ) {
					if ( ! isset( $exclude_tabindex[ $allowed_tag ] ) ) {
						$_allowed_tags[ $allowed_tag ]['tabindex'] = true;
					}
				}
			}

			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$allowed_tags = $_allowed_tags;
		}

		return $allowed_tags;
	}

	public static function get_by_tag( $tag, $included_wrapper_tags = true ) : array {
		static $allowed_html = null;

		if ( null === $allowed_html ) {
			$allowed_html = wp_kses_allowed_html( 'post' );
		}

		if ( $included_wrapper_tags ) {
			return array(
				$tag     => $allowed_html[ $tag ] ?? array(),
				'div'    => $allowed_html['div'] ?? array(),
				'span'   => $allowed_html['span'] ?? array(),
				'strong' => $allowed_html['strong'] ?? array(),
				'em'     => $allowed_html['em'] ?? array(),
			);
		}

		return array(
			$tag => $allowed_html[ $tag ] ?? array(),
		);
	}

	protected static function add_global_attributes( $tag_attributes ) {
		/**
		 * @see _wp_add_global_attributes()
		 * @noinspection DuplicatedCode
		 */
		$global_attributes = array(
			'aria-describedby' => true,
			'aria-details'     => true,
			'aria-label'       => true,
			'aria-labelledby'  => true,
			'aria-hidden'      => true,
			'class'            => true,
			'data-*'           => true,
			'dir'              => true,
			'id'               => true,
			'lang'             => true,
			'style'            => true,
			'title'            => true,
			'role'             => true,
			'xml:lang'         => true,
		);

		if ( true === $tag_attributes ) {
			$tag_attributes = array();
		}

		if ( is_array( $tag_attributes ) ) {
			return array_merge( $tag_attributes, $global_attributes );
		}

		return $tag_attributes;
	}
}
