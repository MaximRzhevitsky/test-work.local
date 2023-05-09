<?php

class WLD_GF_Custom_Merge_Tags_Base {
	public static function init() : void {
		add_filter(
			'gform_custom_merge_tags',
			array( static::class, 'add' )
		);
		add_filter(
			'gform_replace_merge_tags',
			array( static::class, 'replace' )
		);
		add_filter(
			'gform_notification_disable_from_warning',
			array( static::class, 'disable_from_warning' )
		);
		add_filter(
			'gform_notification_settings_fields',
			array( static::class, 'set_defaults' )
		);
		add_filter(
			'gform_default_notification',
			static function ( $enabled ) {
				add_filter(
					'gform_form_update_meta',
					array( static::class, 'set_subject_for_default_notification' ),
					10,
					3
				);

				return $enabled;
			}
		);
	}

	public static function add( array $merge_tags ) : array {
		$merge_tags[] = array(
			'label' => esc_html__( 'Domain', 'theme' ),
			'tag'   => '{domain}',
		);
		$merge_tags[] = array(
			'label' => esc_html__( 'Site Name', 'theme' ),
			'tag'   => '{site_name}',
		);
		$merge_tags[] = array(
			'label' => esc_html__( 'No Reply Email', 'theme' ),
			'tag'   => '{no_reply_email}',
		);

		return $merge_tags;
	}

	public static function replace( string $text ) : string {
		$domain         = str_replace( array( 'https://', 'www.' ), '', home_url( '', 'https' ) );
		$site_name      = wp_strip_all_tags( get_bloginfo( 'name' ) );
		$no_reply_email = 'noreply@' . $domain;

		return str_replace(
			array( '{domain}', '{site_name}', '{no_reply_email}' ),
			array( $domain, $site_name, $no_reply_email ),
			$text
		);
	}

	public static function disable_from_warning() : bool {
		return true;
	}

	public static function set_defaults( array $fields_groups ) : array {
		// phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
		$group_title = esc_html__( 'Notifications', 'gravityforms' );
		foreach ( $fields_groups as &$fields_group ) {
			if ( $group_title === $fields_group['title'] ) {
				foreach ( $fields_group['fields'] as &$field ) {
					$field_name = $field['name'] ?? '';
					if ( 'fromName' === $field_name ) {
						$field['default_value'] = '{site_name} Web Site';
					} elseif ( 'from' === $field_name ) {
						$field['default_value'] = '{no_reply_email}';
					} elseif ( 'subject' === $field_name ) {
						$field['default_value'] = '{site_name}: {form_title}';
					}
				}
			}
		}

		return $fields_groups;
	}

	/** @noinspection PhpUnusedParameterInspection */
	public static function set_subject_for_default_notification( array $form_meta, $form_id, string $meta_name ) : array {
		if ( 'notifications' === $meta_name ) {
			$default_notification                     = array_shift( $form_meta );
			$default_notification['subject']          = '{site_name}: {form_title}';
			$form_meta[ $default_notification['id'] ] = $default_notification;

			remove_filter( 'gform_form_update_meta', array( static::class, 'set_subject_for_default_notification' ) );
		}

		return $form_meta;
	}
}
