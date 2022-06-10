<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use ThemePlate\Blocks\AssetsHelper;
use ThemePlate\Blocks\BlockType;
use PHPUnit\Framework\TestCase;

class BlockTypeTest extends TestCase {
	private BlockType $block_type;

	public function setUp(): void {
		$this->block_type = new BlockType( 'Test', '/path/to/render.php' );
	}

	public function test_firing_init_actually_add_hooks(): void {
		$this->block_type->init();
		$this->assertSame( 10, has_action( 'wp_ajax_' . AssetsHelper::ACTION, array( AssetsHelper::class, 'load' ) ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', array( AssetsHelper::class, 'enqueue' ) ) );
		$this->assertSame( 10, has_action( 'init', array( $this->block_type, 'register' ) ) );
		$this->assertSame( 10, has_filter( 'themeplate_blocks_collection', array( $this->block_type, 'store' ) ) );
	}
}
