<?php

class WLD_WC_OF_Product_Reviews_Vote {
	public const LIKE = 'like';
	public const DISLIKE = 'dislike';
	public const NAMESPACE = 'theme-product-reviews-vote/v1';

	protected static bool $is_enqueue = false;

	public static function init(): void {
		add_action(
			'comment_footer',
			array( static::class, 'output_votes_block' ),
			20
		);
		add_action(
			'rest_api_init',
			array( static::class, 'registration_rest_routes' )
		);
	}

	public static function output_votes_block( WP_Comment $comment ): void {
		printf(
			'
				<div class="votes votes_progress" data-comment-id="%d" role="group">
					<h4 class="votes__title">%s</h4>
					<button class="votes__button votes__button_type_like">
						<span class="votes__count">-</span>
						<span class="screen-reader-text">%s</span>
					</button>
					<button class="votes__button votes__button_type_dislike">
						<span class="votes__count">-</span>
						<span class="screen-reader-text">%s</span>
					</button>
				</div>
			',
			$comment->comment_ID,
			esc_html__( 'Was This Review Helpful?', 'parent-theme' ),
			esc_html__( 'Like', 'parent-theme' ),
			esc_html__( 'Dislike', 'parent-theme' )
		);

		static::enqueue_scripts();
	}

	protected static function enqueue_scripts(): void {
		if ( static::$is_enqueue || ! is_product() ) {
			return;
		}

		static::$is_enqueue = true;

		wp_enqueue_script( 'wp-api-fetch' );

		/** @noinspection JSUnresolvedVariable */
		wp_add_inline_script(
			'wp-api-fetch',
			/** @lang JavaScript */ <<<JS
			( function() {
				const PROGRESS_CLASS = 'votes_progress';
				const LIKE = 'like';
				const DISLIKE = 'dislike';

				const all = [];

				async function initBlock(block) {
					const commentId = block.dataset.commentId;
					const likeButton = block.querySelector('.votes__button_type_like');
					const dislikeButton = block.querySelector('.votes__button_type_dislike');
					const likeCount = likeButton.querySelector('.votes__count');
					const dislikeCount = dislikeButton.querySelector('.votes__count');
					const voteLocalKey = 'vote_' + commentId;

					let userVote = window.localStorage.getItem(voteLocalKey) || '';

					likeButton.addEventListener( 'click', () => click( LIKE ) );
					dislikeButton.addEventListener( 'click', () => click( DISLIKE ) );

					window.addEventListener( 'storage', async ( e ) => {
						if (e.key === voteLocalKey) {
							userVote = e.newValue;
							await setValues();
							setActive();
						}
					});

					await setValues();

					async function get() {
						return wp.apiFetch( { path: 'theme-product-reviews-vote/v1/' + commentId } );
					}

					async function update( type, direction, toggle = false ) {
						return wp.apiFetch( {
							path: 'theme-product-reviews-vote/v1/' + commentId,
							method: 'PUT',
							data: {
								type,
								direction,
								toggle
							}
						} );
					}

					async function setValues( values ) {
						const votes = values || await get();

						likeCount.innerText=votes.like;
						dislikeCount.innerText=votes.dislike;

						block.classList.remove(PROGRESS_CLASS);
					}

					function setActive() {
						if ( userVote ) {
							if ( LIKE === userVote ) {
								likeButton.classList.add('votes__button_active');
								dislikeButton.classList.remove('votes__button_active');
							} else {
								likeButton.classList.remove('votes__button_active');
								dislikeButton.classList.add('votes__button_active');
							}
						} else {
							dislikeButton.classList.add('votes__button_active');
							dislikeButton.classList.remove('votes__button_active');
						}
					}

					async function click( type ) {
						console.log('click');
						if ( block.classList.contains( PROGRESS_CLASS ) ) {
							return;
						}

						block.classList.add( PROGRESS_CLASS );
						if ( userVote ) {
							if ( type === userVote ) {
								await setValues( await update( type, 'down' ) );
								type = '';
							} else {
								await setValues( await update( type, 'up', true ) );
							}
						} else {
							await setValues( await update( type, 'up' ) );
						}

						userVote = type;
						setActive();

						window.localStorage.setItem(voteLocalKey, type);
					}
				}

				document.querySelectorAll( '.votes' ).forEach( (block) => all.push( initBlock( block ) ) );
				window.addEventListener( 'pageshow', async () => await Promise.all( all ) );
			} )();
JS
		);
	}

	public static function registration_rest_routes(): void {
		register_rest_route(
			static::NAMESPACE,
			'(?P<comment_id>\d+)',
			array(
				'methods'             => 'PUT',
				'callback'            => array( static::class, 'updated_votes_rest' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'type'      => array(
						'type'     => 'enum',
						'required' => true,
						'enum'     => array(
							static::LIKE,
							static::DISLIKE,
						),
					),
					'direction' => array(
						'type'     => 'enum',
						'required' => true,
						'enum'     => array(
							'up',
							'down',
						),
					),
					'toggle'    => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
			)
		);
		register_rest_route(
			static::NAMESPACE,
			'(?P<comment_id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( static::class, 'get_votes_rest' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public static function updated_votes_rest( WP_REST_Request $request ): WP_REST_Response {
		if ( 'up' === $request['direction'] ) {
			static::up_vote( $request['comment_id'], $request['type'] );
			if ( $request['toggle'] ) {
				static::down_vote( $request['comment_id'], static::negative_type( $request['type'] ) );
			}
		} else {
			static::down_vote( $request['comment_id'], $request['type'] );
			if ( $request['toggle'] ) {
				static::up_vote( $request['comment_id'], static::negative_type( $request['type'] ) );
			}
		}

		return new WP_REST_Response( static::get_votes( $request['comment_id'] ) );
	}

	public static function get_votes_rest( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( static::get_votes( $request['comment_id'] ) );
	}

	protected static function get_votes( int $comment_id ): array {
		return array(
			static::LIKE    => (int) get_comment_meta( $comment_id, static::LIKE, true ),
			static::DISLIKE => (int) get_comment_meta( $comment_id, static::DISLIKE, true ),
		);
	}

	protected static function get_vote( int $comment_id, string $type ): int {
		return (int) get_comment_meta( $comment_id, static::LIKE === $type ? static::LIKE : static::DISLIKE, true );
	}

	protected static function up_vote( int $comment_id, string $type ): void {
		update_comment_meta(
			$comment_id,
			static::LIKE === $type ? static::LIKE : static::DISLIKE,
			static::get_vote( $comment_id, $type ) + 1
		);
	}

	protected static function down_vote( int $comment_id, string $type ): void {
		$vote = static::get_vote( $comment_id, $type );
		if ( $vote ) {
			update_comment_meta(
				$comment_id,
				static::LIKE === $type ? static::LIKE : static::DISLIKE,
				$vote - 1
			);
		}
	}

	public static function negative_type( $type ): string {
		$negative = array(
			static::LIKE    => static::DISLIKE,
			static::DISLIKE => static::LIKE,
		);

		return $negative[ $type ];
	}
}
