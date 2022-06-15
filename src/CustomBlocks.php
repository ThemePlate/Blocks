<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

class CustomBlocks {

	public const CONFIG_FILE = 'config.php';
	public const MARKUP_FILE = 'markup.php';


	protected string $category;
	protected string $location;


	public function __construct( string $category, string $location ) {

		$this->category = $category;
		$this->location = trailingslashit( $location );

	}


	public function init(): void {

		foreach ( glob( $this->location . '*/' . self::CONFIG_FILE ) as $config ) {
			$folder = basename( dirname( $config ) );
			$block  = require $config;

			if ( $block instanceof BlockType ) {
				$config = array_merge(
					$block->get_config(),
					array(
						'category' => sanitize_title( $this->category ),
						'template' => $this->location . trailingslashit( $folder ) . self::MARKUP_FILE,
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
					'slug'  => sanitize_title( $this->category ),
				),
			)
		);

	}

}
