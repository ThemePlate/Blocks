<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

class CustomBlocks {

	public const CONFIG_FILE = 'config.php';
	public const MARKUP_FILE = 'markup.php';
	public const JSON_FILE   = 'block.json';


	protected string $category;
	protected string $cat_slug;
	protected string $location;
	protected array $handled;
	protected bool $deprecated = false;


	public function __construct( string $location, string $deprecated = null ) {

		if ( null !== $deprecated ) {
			$this->category   = $location;
			$this->cat_slug   = sanitize_title( $location );
			$this->location   = trailingslashit( $deprecated );
			$this->deprecated = true;
		} else {
			$this->location = trailingslashit( $location );
		}

	}


	public function category_slug(): string {

		if ( ! $this->deprecated ) {
			return '';
		}

		_deprecated_function( __METHOD__, '1.6.0' );

		return $this->cat_slug;

	}


	public function init(): void {

		foreach ( glob( $this->location . '*/' ) as $path ) {
			if ( file_exists( $path . self::JSON_FILE ) ) {
				( new BlockType( $path ) )->init();

				continue;
			}

			$block = require $path . self::CONFIG_FILE;

			if ( $block instanceof BlockType ) {
				$this->handled[ $block->get_name() ] = $path;

				add_filter( 'register_block_type_args', array( $this, 'modify_attributes' ), 10, 2 );
				$block->init();
			}
		}

		if ( $this->deprecated ) {
			add_filter( 'block_categories_all', array( $this, 'block_category' ) );
		}

	}


	public function modify_attributes( array $args, string $block_name ): array {

		if ( in_array( $block_name, array_keys( $this->handled ), true ) ) {
			$args['category'] = $this->cat_slug;

			$args['themeplate']['markup'] = $this->handled[ $block_name ] . self::MARKUP_FILE;
		}

		return $args;

	}


	public function block_category( array $categories ): array {

		if ( ! $this->deprecated ) {
			return $categories;
		}

		_deprecated_function( __METHOD__, '1.6.0' );

		if ( in_array( $this->cat_slug, array_column( $categories, 'slug' ) ) ) {
			return $categories;
		}

		return array_merge(
			$categories,
			array(
				array(
					'title' => $this->category,
					'slug'  => $this->cat_slug,
				),
			)
		);

	}

}
