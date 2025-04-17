<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

class AssetsHelper {

	public const ACTION  = 'themeplate_blocks_assets';
	public const HANDLE  = 'themeplate-blocks-assets';
	public const FILTER  = 'themeplate_blocks_collection';
	public const VERSION = '0.1.0';


	public static function setup(): void {

		add_action( 'wp_ajax_' . self::ACTION, array( self::class, 'load' ) );
		add_action( 'enqueue_block_editor_assets', array( self::class, 'enqueue' ) );

	}


	public static function load(): void {

		check_ajax_referer( self::ACTION );

		if ( empty( $_GET['enqueue'] ) ) {
			wp_die();
		}

		$filename = dirname( __DIR__ ) . '/assets/build/index.' . $_GET['enqueue'];

		if ( ! file_exists( $filename ) ) {
			wp_die();
		}

		$file_info = wp_check_filetype( basename( $filename ) );

		header( 'Content-Type: ' . $file_info['type'] );
		ob_start();
		include $filename;
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_die( (string) ob_get_clean() );

	}


	public static function enqueue(): void {

		wp_enqueue_script(
			self::HANDLE,
			add_query_arg(
				array(
					'action'   => self::ACTION,
					'enqueue'  => 'js',
					'_wpnonce' => wp_create_nonce( self::ACTION ),
				),
				admin_url( 'admin-ajax.php' )
			),
			array( 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-element' ),
			self::VERSION,
			true
		);

		wp_enqueue_style(
			self::HANDLE,
			add_query_arg(
				array(
					'action'   => self::ACTION,
					'enqueue'  => 'css',
					'_wpnonce' => wp_create_nonce( self::ACTION ),
				),
				admin_url( 'admin-ajax.php' )
			),
			array(),
			self::VERSION
		);

		wp_localize_script(
			self::HANDLE,
			'ThemePlate_Blocks',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'_wpnonce'   => wp_create_nonce( self::ACTION ),
				'collection' => apply_filters( self::FILTER, array() ),
				'locations'  => FieldsHelper::LOCATIONS,
			),
		);

	}

}
