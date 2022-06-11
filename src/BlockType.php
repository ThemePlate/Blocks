<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Fields;
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
	protected ?Fields $fields = null;


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


	public function fields( array $list ): self {

		$this->fields = new Fields( $list );

		return $this;

	}


	public function init(): void {

		AssetsHelper::setup();
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'themeplate_blocks_collection', array( $this, 'store' ) );

	}


	public function register(): void {

		$args = $this->generate_args();

		$args['render_callback'] = array( self::class, 'render' );
		$args['view_script']     = $this->get_config( 'template' );

		register_block_type( $this->get_config( 'name' ), $args );

	}


	public function store( array $collection ): array {

		$collection[ $this->get_config( 'name' ) ] = $this->generate_args();

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


	protected function generate_args(): array {

		return array_merge(
			$this->get_config(),
			array(
				'title'      => $this->get_title(),
				'category'   => $this->get_config( 'category' ),
				'attributes' => $this->get_attributes(),
			)
		);

	}


	protected function get_attributes(): array {

		$attributes = array();

		if ( null === $this->fields ) {
			return $attributes;
		}

		foreach ( $this->fields->get_collection() as $field ) {
			$attributes[ $field->data_key() ] = array(
				'type'    => 'string',
				'default' => $field->get_config( 'default' ),
				'label'   => $field->get_config( 'title' ),
			);
		}

		return $attributes;

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
