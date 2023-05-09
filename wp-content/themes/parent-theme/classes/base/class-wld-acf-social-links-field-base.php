<?php

class WLD_ACF_Social_Links_Field_Base extends acf_field__group {
	public array $sub_fields = array();

	public string $have_rows = 'single';
	public string $size      = 'full';

	public function initialize() : void {
		$this->name     = 'social_links';
		$this->label    = __( 'Social Links', 'theme' );
		$this->defaults = array(
			'title_enable' => false,
			'sub_fields'   => array(),
			'layout'       => 'block',
		);
		$this->category = 'layout';

		$this->add_field_filter(
			'acf/prepare_field_for_export',
			array( $this, 'prepare_field_for_export' )
		);
		$this->add_field_filter(
			'acf/prepare_field_for_import',
			array( $this, 'prepare_field_for_import' )
		);

		$this->sub_fields = array(
			array(
				'ID'                => 0,
				'key'               => 'social_links_title',
				'label'             => __( 'Title', 'theme' ),
				'name'              => 'title',
				'prefix'            => 'acf',
				'type'              => 'replace_text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'replace'           => '[:strong:], {:em:}',
				'default_value'     => '',
				'placeholder'       => '',
				'maxlength'         => '',
				'rows'              => 2,
				'_name'             => 'title',
				'_valid'            => 1,
			),
			array(
				'ID'                => 0,
				'key'               => 'social_links_links',
				'label'             => __( 'Links', 'theme' ),
				'name'              => 'items',
				'prefix'            => 'acf',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'collapsed'         => '',
				'min'               => 0,
				'max'               => 0,
				'layout'            => 'table',
				'button_label'      => __( 'Add Link', 'theme' ),
				'value'             => null,
				'sub_fields'        => array(
					array(
						'ID'                => 0,
						'key'               => 'social_links_icon',
						'label'             => __( 'Icon', 'theme' ),
						'name'              => 'icon',
						'prefix'            => 'acf',
						'type'              => 'image',
						'value'             => null,
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '30',
							'class' => '',
							'id'    => '',
						),
						'return_format'     => 'array',
						'preview_size'      => 'thumbnail',
						'library'           => 'all',
						'min_width'         => '',
						'min_height'        => '',
						'min_size'          => '',
						'max_width'         => '',
						'max_height'        => '',
						'max_size'          => '',
						'mime_types'        => '',
						'_name'             => 'icon',
						'_valid'            => 1,
					),
					array(
						'ID'                => 0,
						'key'               => 'social_links_url',
						'label'             => __( 'URL', 'theme' ),
						'name'              => 'url',
						'prefix'            => 'acf',
						'type'              => 'text',
						'value'             => null,
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'_name'             => 'url',
						'_valid'            => 1,
					),
					array(
						'ID'                => 0,
						'key'               => 'social_links_text',
						'label'             => __( 'Text', 'theme' ),
						'name'              => 'text',
						'prefix'            => 'acf',
						'type'              => 'text',
						'value'             => null,
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'_name'             => 'text',
						'_valid'            => 1,
					),
				),
				'_name'             => 'items',
				'_valid'            => 1,
			),
		);

		add_action(
			'wld_get_field_value_before',
			array( $this, 'set_size' )
		);
		add_action(
			'wld_get_field_value_before',
			array( $this, 'reset_size' )
		);
	}

	public function set_size( array $args ) : void {
		foreach ( $args as $argument ) {
			if ( is_string( $argument ) && str_starts_with( $argument, 'size_' ) ) {
				$size = str_replace( 'size_', '', $argument );
				if ( ! str_contains( $size, 'x' ) ) {
					$size .= 'x' . $size;
				}

				$this->size = $size;
				break;
			}
		}
	}

	public function reset_size() : void {
		$this->size = 'full';
	}

	public function load_field( $field ) : array {
		$field['sub_fields'] = $this->sub_fields;

		if ( ! $field['title_enable'] ) {
			unset( $field['sub_fields'][0] );
		}

		return $field;
	}

	public function render_field_settings( $field ) : void {
		acf_render_field_setting(
			$field,
			array(
				'label' => __( 'Title Enable', 'theme' ),
				'name'  => 'title_enable',
				'type'  => 'true_false',
				'ui'    => 1,
			)
		);
	}

	public function format_value( $value, $post_id, $field ) : string {
		$value = parent::format_value( $value, $post_id, $field );
		if ( is_array( $value ) ) {
			$items = $value['items'] ?? $value;
			$title = $value['title'] ?? '';

			if ( $items && is_array( $items ) ) {
				$links = '';
				foreach ( $items as $item ) {
					/** @noinspection HtmlUnknownTarget */
					$links .= sprintf(
						'
							<a href="%s" class="social-links__link" target="_blank" rel="noopener">
								%s
								<span class="social-links__text">%s</span>
							</a>
						',
						esc_url( trim( $item['url'] ) ),
						WLD_Images::get_img(
							$item['icon']['ID'] ?? 0,
							$this->size,
							array( 'class' => 'social-links__image' )
						),
						$item['text'] ?? ''
					);
				}

				return sprintf(
					'<div class="social-links">%s%s</div>',
					$field['title_enable'] ? '<h3 class="title">' . $title . '</h3>' : '',
					trim( $links )
				);
			}
		}

		return '';
	}
}
