<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Field;
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

		return array_merge(
			$config,
			array(
				'title'      => $this->get_title(),
				'category'   => $this->get_config( 'category' ),
				'attributes' => $this->get_attributes(),
				'themeplate' => array(
					'namespace' => $this->get_config( 'namespace' ),
					'template'  => $this->get_config( 'template' ),
					'fields'    => $this->fields,
				),
			)
		);

	}

	/**
	 * @return mixed
	 */
	protected function get_default( Field $field ) {

		$default = $field->get_config( 'default' );

		if ( 'group' === $field->get_config( 'type' ) ) {
			/**
			 * @var Fields $fields
			 */
			$fields = $field->get_config( 'fields' );

			foreach ( $fields->get_collection() as $sub_field ) {
				if ( isset( $default[ $sub_field->data_key() ] ) ) {
					continue;
				}

				if ( ! is_array( $default ) ) {
					$default = array();
				}

				$default[ $sub_field->data_key() ] = $this->get_default( $sub_field );
			}
		}

		return $default;

	}


	protected function get_attributes(): array {

		$attributes = array();

		if ( null === $this->fields ) {
			return $attributes;
		}

		foreach ( $this->fields->get_collection() as $field ) {
			$attributes[ $field->data_key() ] = array(
				'type'    => 'group' === $field->get_config( 'type' ) ? 'object' : 'string',
				'default' => $this->get_default( $field ),
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
