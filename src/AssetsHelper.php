<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

class AssetsHelper {

	public const ACTION  = 'themeplate_blocks_script';
	public const HANDLE  = 'themeplate-blocks-script';
	public const VERSION = '0.1.0';


	public static function setup(): void {

		add_action( 'wp_ajax_' . self::ACTION, array( self::class, 'load' ) );
		add_action( 'enqueue_block_editor_assets', array( self::class, 'enqueue' ) );

	}


	public static function load(): void {

		check_ajax_referer( self::ACTION );
		header( 'Content-Type: text/javascript' );
		include dirname( __DIR__ ) . '/assets/script.js';
		exit;

	}


	public static function enqueue(): void {

		wp_enqueue_script(
			self::HANDLE,
			add_query_arg(
				array(
					'action'   => self::ACTION,
					'_wpnonce' => wp_create_nonce( self::ACTION ),
				),
				admin_url( 'admin-ajax.php' )
			),
			array( 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-element' ),
			self::VERSION,
			true
		);

		wp_localize_script(
			self::HANDLE,
			'ThemePlate_Blocks',
			apply_filters( 'themeplate_blocks_collection', array() )
		);

	}

}
