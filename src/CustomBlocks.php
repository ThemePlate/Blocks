<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

class CustomBlocks {

	public const CONFIG_FILE = 'config.php';
	public const MARKUP_FILE = 'markup.php';


	protected string $category;
	protected string $cat_slug;
	protected string $location;


	public function __construct( string $category, string $location ) {

		$this->category = $category;
		$this->cat_slug = sanitize_title( $category );
		$this->location = trailingslashit( $location );

	}


	public function category_slug(): string {

		return $this->cat_slug;

	}


	public function init(): void {

		foreach ( glob( $this->location . '*/' ) as $path ) {
			if ( file_exists( $path . 'block.json' ) ) {
				( new BlockType( $path ) )->init();

				continue;
			}

			$block = require $path . self::CONFIG_FILE;

			if ( $block instanceof BlockType ) {
				$config = array_merge(
					$block->get_config(),
					array(
						'category' => $this->category_slug(),
						'template' => $path . self::MARKUP_FILE,
					)
				);

				( new BlockType( $block->get_title(), $config ) )->fields( $block->get_fields() )->init();
			}
		}

		add_filter( 'block_categories_all', array( $this, 'block_category' ) );

	}


	public function block_category( array $categories ): array {

		return array_merge(
			$categories,
			array(
				array(
					'title' => $this->category,
					'slug'  => $this->category_slug(),
				),
			)
		);

	}

}
