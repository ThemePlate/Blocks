<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use WP_Block;

class RenderHelper {

	public const INNER_BLOCKS_TAG = 'ThemePlateInnerBlocks';


	/** @param array<string, mixed> $attributes */
	public static function callback( array $attributes, string $content, WP_Block $block ): string {

		if ( ! property_exists( $block->block_type, BlockType::CUSTOM_KEY ) ) {
			return '';
		}

		$themeplate = $block->block_type->{ BlockType::CUSTOM_KEY };

		if ( empty( $themeplate['markup'] ) ) {
			return '';
		}

		$callback = json_decode( $themeplate['markup'] );

		if ( ! file_exists( $themeplate['markup'] ) && ! is_callable( $callback ) ) {
			return '';
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$content = '<' . self::INNER_BLOCKS_TAG . '></' . self::INNER_BLOCKS_TAG . '>';
		}

		if ( is_callable( $callback ) ) {
			return (string) call_user_func( $callback, $attributes, $content, $block );
		}

		unset( $themeplate );
		ob_start();

		include $block->block_type->{ BlockType::CUSTOM_KEY }['markup'];

		return (string) ob_get_clean();

	}

}
