<?php

class WLD_ACF_Contact_Link_Field_Base extends acf_field {
	public array $link_types = array();

	public function initialize() : void {
		$this->name       = 'wld_contact_link';
		$this->label      = __( 'Contact Link', 'theme' );
		$this->category   = 'relational';
		$this->defaults   = array(
			'class_attr'    => '',
			'link_type'     => 'phone',
			'default_value' => array(
				'url'    => '',
				'title'  => '',
				'number' => '',
				'type'   => '',
				'class'  => '',
			),
			'return_format' => 'html',
		);
		$this->link_types = array(
			'phone' => __( 'Phone', 'theme' ),
			'fax'   => __( 'Fax', 'theme' ),
			'email' => __( 'Email', 'theme' ),
		);
	}

	public function render_field_settings( array $field ) : void {
		acf_render_field_setting(
			$field,
			array(
				'label' => __( 'Class Attribute', 'theme' ),
				'type'  => 'text',
				'name'  => 'class_attr',
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label' => __( 'User Select Link Type', 'theme' ),
				'type'  => 'true_false',
				'name'  => 'select_link_type',
				'ui'    => 1,
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Link Type', 'theme' ),
				'type'    => 'radio',
				'name'    => 'link_type',
				'choices' => $this->link_types,
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'        => __( 'Return Value', 'theme' ),
				'instructions' => __( 'Specify the returned value on front end', 'theme' ),
				'type'         => 'radio',
				'name'         => 'return_format',
				'layout'       => 'horizontal',
				'choices'      => array(
					'array' => __( 'Link Array', 'theme' ),
					'html'  => __( 'Link HTML', 'theme' ),
				),
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'         => __( 'User Custom Class', 'theme' ),
				'type'          => 'true_false',
				'name'          => 'custom_class',
				'ui'            => 1,
				'default_value' => 1,
			)
		);
	}

	public function render_field( array $field ) : void {
		$type        = '';
		$placeholder = '';
		$prepend     = '';
		$value       = wp_parse_args(
			$field['value'],
			array(
				'link_type' => $field['link_type'],
				'url'       => '',
				'title'     => '',
				'number'    => '',
				'type'      => '',
				'class'     => '',
			)
		);
		switch ( $value['link_type'] ) {
			case 'phone':
				$type        = 'tel';
				$placeholder = '+1234567890';
				$prepend     = 'tel:';
				break;
			case 'fax':
				$type        = 'tel';
				$placeholder = '+1234567890';
				$prepend     = 'fax:';
				break;
			case 'email':
				$type        = 'email';
				$placeholder = 'example@example.com';
				$prepend     = 'mailto:';
				break;
		}
		?>
		<div class="acf-wld-contact-link">
			<div class="wld-contact-link-wrap">
				<div class="wld-contact-link-value">
					<div>
						<label for="<?php echo esc_attr( $field['id'] ); ?>_title">
							<?php esc_html_e( 'Title:', 'theme' ); ?>
						</label>
						<div class="wld-contact-link-input">
							<div class="acf-input">
								<div class="acf-input-append" data-name="paste">
									<span class="dashicons dashicons-admin-page"></span>
								</div>
								<div class="acf-input-wrap">
									<input type="text"
										   name="<?php echo esc_attr( $field['name'] ); ?>[title]"
										   id="<?php echo esc_attr( $field['id'] ); ?>_title"
										   value="<?php echo esc_attr( $value['title'] ); ?>"
										   data-name="title">
								</div>
							</div>
						</div>
						<br class="clear">
					</div>
					<div>
						<label for="<?php echo esc_attr( $field['id'] ); ?>_number">
							<?php esc_html_e( 'Number:', 'theme' ); ?>
						</label>
						<div class="wld-contact-link-input">
							<div class="acf-input">
								<div class="acf-input-prepend"
									 data-name="prepend"><?php echo esc_html( $prepend ); ?></div>
								<div class="acf-input-wrap">
									<input type="<?php echo esc_attr( $type ); ?>"
										   name="<?php echo esc_attr( $field['name'] ); ?>[number]"
										   id="<?php echo esc_attr( $field['id'] ); ?>_number"
										   value="<?php echo esc_attr( $value['number'] ); ?>"
										   placeholder="<?php echo esc_attr( $placeholder ); ?>"
										   data-name="number">
								</div>
							</div>
						</div>
						<br class="clear">
					</div>
					<?php if ( isset( $field['select_link_type'] ) && 1 === $field['select_link_type'] ) : ?>
						<div>
							<label><?php esc_html_e( 'Type :', 'theme' ); ?></label>
							<?php foreach ( $this->link_types as $key => $label ) : ?>
								<label>
									<input type="radio" <?php checked( $key, $value['link_type'] ); ?>
										   name="<?php echo esc_attr( $field['name'] ); ?>[link_type]"
										   value="<?php echo esc_attr( $key ); ?>" data-name="link_type">
									<?php echo esc_html( $label ); ?>
								</label>
							<?php endforeach; ?>
							<br class="clear">
						</div>
					<?php else : ?>
						<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[link_type]"
							   value="<?php echo esc_attr( $value['link_type'] ); ?>" data-name="link_type">
					<?php endif; ?>
					<?php if ( isset( $field['custom_class'] ) && 1 === $field['custom_class'] ) : ?>
						<div>
							<label for="<?php echo esc_attr( $field['id'] ); ?>_class">
								<?php esc_html_e( 'Class:', 'theme' ); ?>
							</label>
							<div class="wld-contact-link-input">
								<input type="text"
									   name="<?php echo esc_attr( $field['name'] ); ?>[class]"
									   id="<?php echo esc_attr( $field['id'] ); ?>_class"
									   value="<?php echo esc_attr( $value['class'] ); ?>"
									   data-name="class">
							</div>
							<br class="clear">
						</div>
					<?php else : ?>
						<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[class]"
							   value="<?php echo esc_attr( $value['class'] ); ?>" data-name="class">
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		if ( WLD_Theme::never() ) { // The condition is never fulfilled, only for IDE
			echo '<div class="acf-field acf-field-wld-contact-link"></div>';
		}
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function update_value( $value, $post_id, array $field ) {
		if ( 1 === $field['select_link_type'] ) {
			$selector           = $field['ID'] ?: $field['key'];
			$field              = (array) acf_get_field( $selector );
			$field['link_type'] = $value['link_type'] ?? 'phone';
			acf_update_field( $field );
		}
		if ( empty( $value['title'] ) && empty( $value['number'] ) ) {
			$value = null;
		}

		return $value;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function format_value( $value, $post_id, array $field ) : array|string|null {
		// Fix for < 1.0.7
		if ( ! isset( $value['class'] ) ) {
			$value['class'] = '';
		}
		if ( empty( $value['link_type'] ) ) {
			$value['link_type'] = $field['link_type'];
		}
		if ( empty( $value['number'] ) ) {
			$value = null;
		} else {
			$attr   = array();
			$class  = '';
			$before = '';
			$after  = '';
			$html   = 'html' === $field['return_format'];

			if ( $field['class_attr'] ) {
				$class .= ' ' . $field['class_attr'];
			}

			if ( $value['class'] ) {
				$class .= ' ' . $value['class'];
			}
			$class = trim( $class );

			if ( $html && preg_match( '/(.*){(.*)}(.*)/', $value['title'], $matches ) ) {
				// This is not a valid error, it is not short array.
				// phpcs:ignore Generic.Arrays.DisallowShortArraySyntax.Found
				[ , $before, $value['title'], $after ] = $matches;
			}

			switch ( $value['link_type'] ) {
				case 'phone':
					$attr['href'] = 'tel:' . $value['number'];
					break;
				case 'fax':
					$attr['href'] = 'fax:' . $value['number'];
					break;
				case 'email':
					$attr['href']   = 'mailto:' . antispambot( $value['number'] );
					$value['title'] = antispambot( $value['title'] );
					break;
			}

			if ( $html ) {
				if ( $class ) {
					$attr['class'] = $class;
				}

				$attr = acf_esc_attrs( $attr );
				/** @noinspection HtmlUnknownAttribute */
				$value = "$before<a $attr>{$value['title']}</a>$after";
			} else {
				$value = array(
					'url'    => $attr['href'],
					'title'  => $value['title'],
					'number' => $value['number'],
					'type'   => $value['link_type'],
					'class'  => $class,
				);
			}
		}

		return $value;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function validate_value( $valid, $value, array $field, string $input ) {
		if ( ! empty( $value['title'] ) && empty( $value['number'] ) ) {
			$valid = __( 'Number can not be empty!', 'theme' );
		}

		return $valid;
	}

	public function input_admin_enqueue_scripts() : void {
		$url = WLD_File::get_parent_url();
		wp_enqueue_script(
			'wld-acf-contact-link-field',
			$url . 'js/wld-acf-contact-link-field.js',
			array( 'acf-input', 'wplink' ),
			WLD_Theme::get_version(),
			true
		);
		wp_enqueue_style(
			'wld-acf-contact-link-field',
			$url . 'css/wld-acf-contact-link-field.css',
			array( 'acf-input' ),
			WLD_Theme::get_version()
		);
	}
}
