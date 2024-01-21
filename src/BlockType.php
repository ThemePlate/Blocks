<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Fields;
use ThemePlate\Core\Helper\MainHelper;
use WP_Block;
use WP_Block_Type;

class BlockType {

	public const DEPRECATED = array(
		'namespace'       => 'themeplate',
		'icon'            => 'admin-generic',
		'category'        => 'widgets',
		'template'        => '',
		'inner_blocks'    => true,
		'allowed_blocks'  => array(),
		'template_blocks' => array(),
		'template_lock'   => '',
	);

	public const DEFAULTS = array(
		'render_template' => '',
		'custom_fields'   => array(),
		'inner_blocks'    => true,
		'allowed_blocks'  => array(),
		'template_blocks' => array(),
		'template_lock'   => '',
	);


	protected string $title; // deprecated
	protected string $name;  // deprecated
	protected string $path;
	protected array $config;
	protected bool $deprecated = true;
	protected ?Fields $fields = null;
	protected ?WP_Block_Type $block = null;


	public function __construct( string $path, array $config = null ) {

		if ( ! file_exists( $path ) ) {
			_deprecated_argument( __METHOD__, '1.6.0', 'Pass the path to metadata definition.' );

			$this->title  = $path;
			$this->config = $this->check( $config );
		} else {
			$this->path = trailingslashit( $path );

			$this->deprecated = false;
		}

		if ( null !== $config ) {
			_deprecated_argument( __METHOD__, '1.6.0', 'Define custom config in file.' );
		}

	}


	protected function check( array $config ): array {

		$config = MainHelper::fool_proof( self::DEPRECATED, $config );

		$this->name = trailingslashit( $config['namespace'] ) . sanitize_title( $this->title );

		return $config;

	}


	public function fields( array $list ): self {

		_deprecated_function( __METHOD__, '1.6.0', 'Pass in the config under "custom_fields" key.' );

		$this->fields = new Fields( $list );

		return $this;

	}


	public function init(): void {

		AssetsHelper::setup();
		FieldsHelper::setup();

		if ( ! $this->deprecated ) {
			$this->setup();

			if ( ! file_exists( $this->path . CustomBlocks::JSON_FILE ) ) {
				return;
			}
		}

		add_action( 'init', array( $this, 'register' ) );

	}


	protected function setup() {

		$config = array();
		$c_file = $this->path . CustomBlocks::CONFIG_FILE;
		$m_file = $this->path . CustomBlocks::MARKUP_FILE;

		if ( file_exists( $c_file ) ) {
			$config = require $c_file;
		}

		$this->config = MainHelper::fool_proof( self::DEFAULTS, $config );
		$this->fields = new Fields( $this->config['custom_fields'] );

		if ( empty( $this->config['render_template'] ) && file_exists( $m_file ) ) {
			$this->config['render_template'] = $m_file;
		}

	}


	public function register(): void {

		$args = $this->generate_args();

		$args['render_callback'] = array( self::class, 'render' );
		$args['render_template'] = $this->get_config( 'template' );
		$args['themeplate']      = array(
			'namespace' => $this->get_config( 'namespace' ),
			'template'  => $this->get_config( 'template' ),
			'fields'    => $this->fields,
		);

		$block_type  = $this->deprecated ? $this->name : $this->path;
		$this->block = register_block_type( $block_type, $args ) ?: null;

		if ( false === $this->block ) {
			return;
		}

		add_filter( 'themeplate_blocks_collection', array( $this, 'store' ) );

	}


	public function store( array $collection ): array {

		$key = $this->deprecated ? $this->name : $this->block->name;

		$collection[ $key ] = $this->generate_args();

		return $collection;

	}


	public function get_title(): string {

		_deprecated_function( __METHOD__, '1.6.0' );

		if ( ! $this->deprecated ) {
			return '';
		}

		return $this->title;

	}


	public function get_name(): string {

		_deprecated_function( __METHOD__, '1.6.0' );

		if ( ! $this->deprecated ) {
			return '';
		}

		return $this->name;

	}


	public function get_config( string $key = '' ) {

		if ( '' === $key ) {
			return $this->config;
		}

		if ( ! $this->deprecated ) {
			if ( 'template' === $key ) {
				$key = 'render_template';
			}
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

		if ( $this->deprecated ) {
			$config['title']    = $this->get_title();
			$config['category'] = $this->get_config( 'category' );
		}

		return array_merge(
			$config,
			array(
				'attributes' => $this->get_attributes(),
			)
		);

	}


	protected function get_attributes(): array {

		$attributes = array();

		if ( null === $this->fields ) {
			return $attributes;
		}

		return array_merge( FieldsHelper::build_schema( $this->fields ), $attributes );

	}


	public static function render( array $attributes, string $content, WP_Block $block ): string {

		if ( empty( $block->block_type->render_template ) ) {
			return '';
		}

		$callback = json_decode( $block->block_type->render_template );

		if ( ! file_exists( $block->block_type->render_template ) && ! is_callable( $callback ) ) {
			return '';
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$content = '<ThemePlateInnerBlocks></ThemePlateInnerBlocks>';
		}

		if ( is_callable( $callback ) ) {
			return call_user_func( $callback, $attributes, $content, $block );
		}

		ob_start();
		include $block->block_type->render_template;

		return ob_get_clean();

	}

}
