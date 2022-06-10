<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Helper\MainHelper;

class BlockType {

	public const DEFAULTS = array(
		'namespace' => 'themeplate',
		'icon'      => 'admin-generic',
		'category'  => 'widgets',
	);

	protected string $title;
	protected string $template;
	protected array $config;


	public function __construct( string $title, string $template, array $config = array() ) {

		$this->title    = $title;
		$this->template = $template;
		$this->config   = $this->check( $config );

	}


	protected function check( array $config ): array {

		$config = MainHelper::fool_proof( self::DEFAULTS, $config );

		if ( empty( $config['name'] ) ) {
			$config['name'] = $this->title;
		}

		$config['name'] = trailingslashit( $config['namespace'] ) . sanitize_title( $config['name'] );

		return $config;

	}


	public function init(): void {

		add_action( 'init', array( $this, 'register' ) );
		add_action( 'wp_ajax_themeplate_block_script-' . $this->get_config( 'name' ), array( $this, 'block_script' ) );

	}


	public function register(): void {

		wp_register_script(
			$this->get_config( 'name' ),
			add_query_arg(
				array(
					'action'   => 'themeplate_block_script-' . $this->get_config( 'name' ),
					'_wpnonce' => wp_create_nonce( $this->get_config( 'name' ) ),
				),
				admin_url( 'admin-ajax.php' )
			),
			array( 'wp-blocks', 'wp-element', 'wp-components' ),
			microtime( true ),
			true
		);

		register_block_type(
			$this->get_config( 'name' ),
			array(
				'render_callback' => array( $this, 'render_block' ),
				'editor_script'   => $this->get_config( 'name' ),
			)
		);

	}


	public function block_script() {

		check_ajax_referer( $this->get_config( 'name' ) );
		header( 'Content-Type: text/javascript' );
		?>

( function ( registerBlockType, createElement, Fragment, ServerSideRender ) {
	registerBlockType( '<?php echo esc_js( $this->get_config( 'name' ) ); ?>', {
		title: '<?php echo esc_js( $this->get_title() ); ?>',
		icon: '<?php echo esc_js( $this->get_config( 'icon' ) ); ?>',
		category: '<?php echo esc_js( $this->get_config( 'category' ) ); ?>',

		edit: function ( props ) {
			return createElement(
				Fragment,
				null,
				createElement(
					ServerSideRender,
					{
						block: '<?php echo esc_js( $this->get_config( 'name' ) ); ?>',
					}
				)
			);
		},
	} );
} )(
	wp.blocks.registerBlockType,
	wp.element.createElement,
	wp.element.Fragment,
	wp.components.ServerSideRender
);

		<?php
		exit;

	}


	public function get_title(): string {

		return $this->title;

	}


	public function get_config( string $key = '' ) {

		if ( '' === $key ) {
			return $this->config;
		}

		return $this->config[ $key ] ?? '';

	}


	public function render_block(): string {

		ob_start();
		include( $this->template );
		return ob_get_clean();

	}

}
