<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Helper\MainHelper;
use WP_Block;

class BlockType {

	public const DEFAULTS = array(
		'namespace' => 'themeplate',
		'icon'      => 'admin-generic',
		'category'  => 'widgets',
		'template'  => '',
	);


	protected string $title;
	protected array $config;


	public function __construct( string $title, array $config = array() ) {

		$this->title  = $title;
		$this->config = $this->check( $config );

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

		AssetsHelper::setup();
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'themeplate_blocks_collection', array( $this, 'store' ) );

	}


	public function register(): void {

		register_block_type(
			$this->get_config( 'name' ),
			array(
				'render_callback' => array( self::class, 'render' ),
				'view_script'     => $this->get_config( 'template' ),
			)
		);

	}


	public function store( array $collection ): array {

		$collection[ $this->get_config( 'name' ) ] = array_merge(
			$this->get_config(),
			array(
				'title'      => $this->get_title(),
				'category'   => $this->get_config( 'category' ),
				'attributes' => array(),
			)
		);

		return $collection;

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


	public static function render( array $attributes, string $content, WP_Block $block ): string {

		if ( ! file_exists( $block->block_type->view_script ) ) {
			return '';
		}

		ob_start();
		include $block->block_type->view_script;
		return ob_get_clean();

	}

}
