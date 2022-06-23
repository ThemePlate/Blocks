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
		'namespace'       => 'themeplate',
		'icon'            => 'admin-generic',
		'category'        => 'widgets',
		'template'        => '',
		'allowed_blocks'  => array(),
		'template_blocks' => array(),
		'template_lock'   => '',
	);


	protected string $title;
	protected string $name;
	protected array $config;
	protected ?Fields $fields = null;


	public function __construct( string $title, array $config = array() ) {

		$this->title  = $title;
		$this->config = $this->check( $config );

	}


	protected function check( array $config ): array {

		$config = MainHelper::fool_proof( self::DEFAULTS, $config );

		$this->name = trailingslashit( $config['namespace'] ) . sanitize_title( $this->title );

		return $config;

	}


	public function fields( array $list ): self {

		$this->fields = new Fields( $list );

		return $this;

	}


	public function init(): void {

		AssetsHelper::setup();
		FieldsHelper::setup();
		add_action( 'init', array( $this, 'register' ) );

	}


	public function register(): void {

		$args = $this->generate_args();

		$args['render_callback'] = array( self::class, 'render' );
		$args['view_script']     = $this->get_config( 'template' );
		$args['themeplate']      = array(
			'namespace' => $this->get_config( 'namespace' ),
			'template'  => $this->get_config( 'template' ),
			'fields'    => $this->fields,
		);

		if ( false === register_block_type( $this->name, $args ) ) {
			return;
		}

		add_filter( 'themeplate_blocks_collection', array( $this, 'store' ) );

	}


	public function store( array $collection ): array {

		$collection[ $this->name ] = $this->generate_args();

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


	public function get_fields(): array {

		if ( null === $this->fields ) {
			return array();
		}

		return $this->fields->get_collection();

	}


	protected function generate_args(): array {

		$config = $this->get_config();

		unset( $config['namespace'] );
		unset( $config['template'] );

		if ( empty( $config['allowed_blocks'] ) ) {
			unset( $config['allowed_blocks'] );
		}

		if ( empty( $config['template_blocks'] ) ) {
			unset( $config['template_blocks'] );
		}

		return array_merge(
			$config,
			array(
				'title'      => $this->get_title(),
				'category'   => $this->get_config( 'category' ),
				'attributes' => $this->get_attributes(),
			)
		);

	}


	protected function get_attributes(): array {

		$attributes = array(
			'innerBlockContent' => array(
				'type' => 'string',
			),
		);

		if ( null === $this->fields ) {
			return $attributes;
		}

		return array_merge( FieldsHelper::build_schema( $this->fields ), $attributes );

	}


	public static function render( array $attributes, string $content, WP_Block $block ): string {

		if ( ! file_exists( $block->block_type->view_script ) ) {
			return '';
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $attributes['innerBlockContent'] ) ) {
			$content = $attributes['innerBlockContent'];

			unset( $attributes['innerBlockContent'] );
			unset( $block->parsed_block['attrs']['innerBlockContent'] );
			unset( $block->block_type->attributes['innerBlockContent'] );
		}

		ob_start();
		include $block->block_type->view_script;

		return ob_get_clean();

	}

}
