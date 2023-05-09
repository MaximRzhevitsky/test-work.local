<?php
/**
 * @noinspection HtmlUnknownTarget
 * phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
 */

class WLD_Delay_Scripts_Base {
	protected const SEPARATOR = '~|~';

	protected static array $delay_scripts_by_timeout  = array();
	protected static array $delay_scripts_by_activity = array();
	protected static array $delay_scripts_by_view     = array();
	protected static array $dependencies              = array();

	public static function init() : void {
		add_action(
			'wp_print_scripts',
			array( static::class, 'dequeue_scripts' ),
			9
		);
		add_action(
			'wp_print_footer_scripts',
			array( static::class, 'dequeue_scripts' ),
			9
		);
		add_action(
			'wp_head',
			array( static::class, 'the_helpers_scripts' ),
			5
		);
		add_action(
			'wp_footer',
			array( static::class, 'the_delay_script' ),
			100
		);
	}

	/** @noinspection PhpUnused */
	public static function enqueue_timeout( string $handle, int $timeout_in_milliseconds = 6000 ) : void {
		if ( ! isset( static::$delay_scripts_by_timeout[ $timeout_in_milliseconds ] ) ) {
			static::$delay_scripts_by_timeout[ $timeout_in_milliseconds ] = array();
		}

		static::$delay_scripts_by_timeout[ $timeout_in_milliseconds ][ $handle ] = array();
	}

	public static function enqueue_activity( string $handle, int $timeout_in_milliseconds = 0 ) : void {
		if ( ! isset( static::$delay_scripts_by_activity[ $timeout_in_milliseconds ] ) ) {
			static::$delay_scripts_by_activity[ $timeout_in_milliseconds ] = array();
		}

		static::$delay_scripts_by_activity[ $timeout_in_milliseconds ][ $handle ] = array();
	}

	public static function enqueue_view( string $handle, string $selector, int $timeout_in_milliseconds = 0 ) : void {
		if ( ! isset( static::$delay_scripts_by_view[ $timeout_in_milliseconds ] ) ) {
			static::$delay_scripts_by_view[ $timeout_in_milliseconds ] = array();
		}

		static::$delay_scripts_by_view[ $timeout_in_milliseconds ][ $handle . static::SEPARATOR . $selector ] = array();
	}

	public static function dequeue_scripts() : void {
		static::set_delay_scripts_data( static::$delay_scripts_by_timeout );
		static::set_delay_scripts_data( static::$delay_scripts_by_activity );
		static::set_delay_scripts_data( static::$delay_scripts_by_view );
		static::dequeue_delay_scripts( static::$delay_scripts_by_timeout );
		static::dequeue_delay_scripts( static::$delay_scripts_by_activity );
		static::dequeue_delay_scripts( static::$delay_scripts_by_view );
	}

	protected static function get_script_data( $handle ) : array {
		global $wp_scripts;

		$extra  = '';
		$before = '';
		$after  = '';
		$script = $wp_scripts->registered[ $handle ];
		if ( isset( $script->extra['data'] ) ) {
			$extra = $script->extra['data'];
		}
		if ( isset( $script->extra['before'] ) ) {
			$before = trim( implode( "\n", $script->extra['before'] ), "\n" );
		}
		if ( isset( $script->extra['after'] ) ) {
			$after = trim( implode( "\n", $script->extra['after'] ), "\n" );
		}

		$ver = $script->ver ?: '';
		if ( ! empty( $script->args ) ) {
			$ver = $ver ? $ver . '&amp;' . $script->args : $script->args;
		}

		$src = $script->src;
		if ( $src && ! empty( $ver ) ) {
			$src = add_query_arg( 'ver', $ver, $src );
		}

		foreach ( $script->deps as $dependency_handle ) {
			if ( ! isset( static::$dependencies[ $dependency_handle ] ) ) {
				static::$dependencies[ $dependency_handle ] = static::get_script_data( $dependency_handle );
			}
		}

		return array(
			'handle'       => $handle,
			'extra'        => $extra,
			'before'       => $before,
			'src'          => $src,
			'after'        => $after,
			'dependencies' => $script->deps,
		);
	}

	public static function the_helpers_scripts() : void { ?>
		<script id="theme-script-action-inline-js">
			function themeUserActiveAction( callback, autoStartTimeOut = 0, force = false ) {
				if ( force ) {
					callback();

					return;
				}

				let autoStartTimeOutId = null;
				let init = false;
				if ( autoStartTimeOut ) {
					document.addEventListener( 'DOMContentLoaded', () => autoStartTimeOutId = setTimeout( runCallback, autoStartTimeOut ) );
				}

				function runCallback() {
					if ( autoStartTimeOutId ) {
						clearTimeout( autoStartTimeOutId );
					}

					if ( init ) {
						return;
					}

					init = true;

					document.removeEventListener( 'scroll', runCallback );
					document.removeEventListener( 'mousemove', runCallback );
					document.removeEventListener( 'touchstart', runCallback );

					callback();
				}

				document.addEventListener( 'scroll', runCallback );
				document.addEventListener( 'mousemove', runCallback );
				document.addEventListener( 'touchstart', runCallback );
			}

			function themeLoadScript( src, id ) {
				if ( document.getElementById( id ) ) {
					return;
				}
				const script = document.createElement( 'script' );
				script.src = src;
				script.id = id;
				document.head.appendChild( script );
			}

			function themeUserActiveActionLoadScript( src, id, timeOut = 0 ) {
				themeUserActiveAction( () => themeLoadScript( src, id ), timeOut );
			}

			function themeViewAction( callback, selector, autoStartTimeOut = 0, force = false ) {
				if ( force ) {
					callback();

					return;
				}

				let intersectionObserverInit = false;
				let autoStartTimeOutId = null;
				const observer = new IntersectionObserver(
					( [entry] ) => {
						if ( intersectionObserverInit || entry.intersectionRatio > 0 ) {
							runCallback();
						} else {
							intersectionObserverInit = true;
						}
					},
					{
						rootMargin: '600px 0px 600px 0px',
						threshold: [0.01],
					}
				);

				function runCallback() {
					if ( autoStartTimeOutId ) {
						clearTimeout( autoStartTimeOutId );
					}

					observer.disconnect();
					callback();
				}

				const element = document.querySelector( selector );
				if ( element ) {
					observer.observe( element );
					if ( autoStartTimeOut ) {
						autoStartTimeOutId = setTimeout( runCallback, autoStartTimeOut );
					}
				}
			}

			function themeViewActionLoadScript( selector, src, id, timeOut = 0 ) {
				themeViewAction( () => themeLoadScript( src, id ), selector, timeOut );
			}
		</script>
		<?php
	}

	public static function the_delay_script() : void {
		static::clear_delay_scripts( static::$delay_scripts_by_timeout );
		static::clear_delay_scripts( static::$delay_scripts_by_activity );
		static::clear_delay_scripts( static::$delay_scripts_by_view );

		if (
			! empty( static::$delay_scripts_by_timeout ) ||
			! empty( static::$delay_scripts_by_activity ) ||
			! empty( static::$delay_scripts_by_view )
		) {
			printf(
				'
			<script
				src="%s"
			 	data-scripts-by-timeout="%s"
			 	data-scripts-by-activity="%s"
			 	data-scripts-by-view="%s"
			 	data-dependencies="%s"
			 	id="theme-delay-scripts-js"
			 	type="module"
			 	async
			></script>',
				esc_url( WLD_File::get_assets_url( 'js/modules/delay-scripts.js' ) ),
				esc_attr( wp_json_encode( static::$delay_scripts_by_timeout, JSON_FORCE_OBJECT ) ),
				esc_attr( wp_json_encode( static::$delay_scripts_by_activity, JSON_FORCE_OBJECT ) ),
				esc_attr( wp_json_encode( static::$delay_scripts_by_view, JSON_FORCE_OBJECT ) ),
				esc_attr( wp_json_encode( static::$dependencies ) )
			);
		}
	}

	protected static function clear_delay_scripts( array &$delay_scripts ) : void {
		foreach ( $delay_scripts as $timeout => $scripts ) {
			$delay_scripts[ $timeout ] = array_filter( $scripts );
		}
		$delay_scripts = array_filter( $delay_scripts );
	}

	protected static function set_delay_scripts_data( array &$delay_scripts ) : void {
		global $wp_scripts;

		foreach ( $delay_scripts as $timeout => $scripts ) {
			foreach ( $scripts as $key => $data ) {
				if ( str_contains( $key, static::SEPARATOR ) ) {
					$handle = explode( static::SEPARATOR, $key )[0];
				} else {
					$handle = $key;
				}
				if ( in_array( $handle, $wp_scripts->queue ?? array(), true ) ) {
					$delay_scripts[ $timeout ][ $key ] = static::get_script_data( $handle );
				}
			}
		}
	}

	protected static function dequeue_delay_scripts( array $delay_scripts ) : void {
		foreach ( $delay_scripts as $scripts ) {
			foreach ( $scripts as $script ) {
				if ( isset( $script['handle'] ) ) {
					wp_dequeue_script( $script['handle'] );
				}
			}
		}
	}
}
