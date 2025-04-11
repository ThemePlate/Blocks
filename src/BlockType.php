<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Fields;
use ThemePlate\Core\Helper\MainHelper;
use WP_Block;
use WP_Block_Type;
use WP_Theme_JSON_Resolver;

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
	protected bool $deprecated      = true;
	protected ?Fields $fields       = null;
	protected ?WP_Block_Type $block = null;


	public function __construct( string $path, ?array $config = null ) {

		if ( ! file_exists( $path ) ) {
			$this->title  = $path;
			$this->path   = '';
			$this->config = $this->check( $config ?? array() );
		} else {
			$this->name = '';
			$this->path = trailingslashit( $path );

			$this->deprecated = false;
		}

		if ( null !== $config ) {
			$message = $this->deprecated ? 'Pass the path to metadata definition.' : 'Define custom config in file.';

			_deprecated_argument( __METHOD__, '1.6.0', esc_html( $message ) );
		}

	}


	protected function check( array $config = array() ): array {

		$config = MainHelper::fool_proof( self::DEPRECATED, $config );

		$this->name = trailingslashit( $config['namespace'] ) . sanitize_title( $this->title );

		return $config;

	}


	public function fields( array $collection ): self {

		if ( ! $this->deprecated ) {
			_deprecated_function( __METHOD__, '1.6.0', 'Pass in the config under "custom_fields" key.' );
		}

		$this->fields = new Fields( $collection );

		return $this;

	}


	public function init(): void {

		AssetsHelper::setup();
		FieldsHelper::setup();

		if ( ! $this->deprecated ) {
			$this->config( array() );

			if ( ! file_exists( $this->path . CustomBlocks::JSON_FILE ) ) {
				return;
			}
		}

		if ( did_action( 'init' ) ) {
			$this->register();
		} else {
			add_action( 'init', array( $this, 'register' ) ); // @codeCoverageIgnore
		}

	}


	public function config( array $config ): self {

		if ( $this->deprecated ) {
			$this->config = $this->check( $config );

			return $this;
		}

		$c_file = $this->path . CustomBlocks::CONFIG_FILE;
		$m_file = $this->path . CustomBlocks::MARKUP_FILE;

		if ( file_exists( $c_file ) ) {
			$config = array_merge_recursive( require $c_file, $config );
		}

		if ( isset( $config['custom_fields'] ) ) {
			$this->fields = new Fields( $config['custom_fields'] );
		}

		$this->config = MainHelper::fool_proof( self::DEFAULTS, $config );

		if ( empty( $this->config['render_template'] ) && file_exists( $m_file ) ) {
			$this->config['render_template'] = $m_file;
		}

		return $this;

	}


	public function set_name_from_metadata( array $settings, array $metadata ): array {

		if ( $metadata['file'] === $this->path . CustomBlocks::JSON_FILE ) {
			$this->name = $metadata['name'];
		}

		return $settings;

	}


	public function modify_attributes( array $args, string $block_name ): array {

		if ( $block_name === $this->name ) {
			if ( empty( $args['api_version'] ) ) {
				$args['api_version'] = 2;
			}

			if ( $this->fields instanceof Fields ) {
				$args['attributes'] = array_merge( $args['attributes'], FieldsHelper::build_schema( $this->fields ) );
			}

			$this->handle_alignment( $args );
		}

		return $args;

	}


	protected function handle_alignment( array &$args ): void {

		$settings = WP_Theme_JSON_Resolver::get_theme_data()->get_settings();

		if ( empty( $settings['layout'] ) || empty( $settings['layout']['contentSize'] ) ) {
			return;
		}

		if ( empty( $args['supports']['align'] ) ) {
			$args['supports']['align'] = array( 'full' );

			if ( isset( $settings['layout']['wideSize'] ) ) {
				$args['supports']['align'][] = 'wide';
			}
		}

		if ( empty( $args['attributes']['align'] ) ) {
			$args['attributes']['align'] = array(
				'type'    => 'string',
				'default' => 'full',
			);
		}

	}


	public function register(): void {

		$args = $this->generate_args();

		$args['render_callback'] = array( self::class, 'render' );
		$args['themeplate']      = array(
			'markup' => $this->get_config( 'template' ),
			'fields' => $this->fields,
		);

		if ( ! $this->deprecated ) {
			add_filter( 'block_type_metadata_settings', array( $this, 'set_name_from_metadata' ), 10, 2 );
		}

		add_filter( 'register_block_type_args', array( $this, 'modify_attributes' ), 10, 2 );

		$block_type  = $this->deprecated ? $this->name : $this->path;
		$this->block = register_block_type( $block_type, $args ) ?: null; // phpcs:ignore Universal.Operators.DisallowShortTernary

		if ( ! $this->deprecated ) {
			remove_filter( 'block_type_metadata_settings', array( $this, 'set_name_from_metadata' ) );
		}

		remove_filter( 'register_block_type_args', array( $this, 'modify_attributes' ) );

		if ( null === $this->block ) {
			return;
		}

		add_filter( 'themeplate_blocks_collection', array( $this, 'store' ) );
		add_filter( 'render_block_data', array( $this, 'defaults' ) );

	}


	public function store( array $collection ): array {

		if ( ! $this->block instanceof WP_Block_Type ) {
			return $collection;
		}

		$key = $this->deprecated ? $this->name : $this->block->name;

		$collection[ $key ] = $this->generate_args();

		return $collection;

	}


	public function defaults( array $parsed ): array {

		if ( ! $this->block instanceof WP_Block_Type ) {
			return $parsed;
		}

		if ( $parsed['blockName'] !== $this->block->name ) {
			return $parsed;
		}

		if ( null === $this->block->attributes ) {
			return $parsed;
		}

		foreach ( $this->block->attributes as $key => $value ) {
			if ( ! isset( $parsed['attrs'][ $key ] ) && isset( $value['default'] ) ) {
				$parsed['attrs'][ $key ] = $value['default'];
			}
		}

		return $parsed;

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

		if ( ! $this->deprecated && 'template' === $key ) {
			$key = 'render_template';
		}

		return $this->config[ $key ] ?? '';

	}


	public function get_fields(): array {

		if ( ! $this->fields instanceof Fields ) {
			return array();
		}

		return $this->fields->get_collection();

	}


	protected function generate_args(): array {

		$config = $this->get_config();

		if ( empty( $config['allowed_blocks'] ) ) {
			unset( $config['allowed_blocks'] );
		}

		if ( empty( $config['template_blocks'] ) ) {
			unset( $config['template_blocks'] );
		}

		if ( $this->deprecated ) {
			unset( $config['namespace'] );
			unset( $config['template'] );

			$config['title'] = $this->title;
		} else {
			unset( $config['render_template'] );
			unset( $config['custom_fields'] );
		}

		return $config;

	}


	public static function render( array $attributes, string $content, WP_Block $block ): string {

		if ( ! property_exists( $block->block_type, 'themeplate' ) ) {
			return '';
		}

		$themeplate = $block->block_type->themeplate;

		if ( empty( $themeplate['markup'] ) ) {
			return '';
		}

		$callback = json_decode( $themeplate['markup'] );

		if ( ! file_exists( $themeplate['markup'] ) && ! is_callable( $callback ) ) {
			return '';
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$content = '<ThemePlateInnerBlocks></ThemePlateInnerBlocks>';
		}

		if ( is_callable( $callback ) ) {
			return (string) call_user_func( $callback, $attributes, $content, $block );
		}

		unset( $themeplate );
		ob_start();

		include $block->block_type->themeplate['markup'];

		return (string) ob_get_clean();

	}

}
