<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use Brain\Monkey;
use Mockery;
use ThemePlate\Blocks\AssetsHelper;
use ThemePlate\Blocks\BlockType;
use PHPUnit\Framework\TestCase;
use ThemePlate\Blocks\FieldsHelper;
use WP_Block;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\when;

class BlockTypeTest extends TestCase {
	private array $args   = array();
	private array $config = array(
		'namespace' => 'my-blocks',
		'template'  => '/path/to/render.php',
	);

	public function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		when( 'sanitize_title' )->justReturn( 'test' );

		$this->args = array(
			'title'           => 'Test',
			'attributes'      => array( 'innerBlockContent' => array( 'type' => 'string' ) ),
			'render_template' => $this->config['template'],
			'themeplate'      => array(
				'namespace' => $this->config['namespace'],
				'template'  => $this->config['template'],
				'fields'    => null,
			),
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_firing_init_actually_add_hooks(): void {
		$block_type = new BlockType( $this->args['title'], $this->config );

		$block_type->init();
		$this->assertSame( 10, has_action( 'wp_ajax_' . AssetsHelper::ACTION, array( AssetsHelper::class, 'load' ) ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', array( AssetsHelper::class, 'enqueue' ) ) );
		$this->assertSame( 10, has_action( 'wp_ajax_' . FieldsHelper::ACTION, array( FieldsHelper::class, 'load' ) ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', array( AssetsHelper::class, 'enqueue' ) ) );
		$this->assertSame( 10, has_action( 'init', array( $block_type, 'register' ) ) );
	}

	public function assert_in_args( $actual ): bool {
		foreach ( $this->args as $key => $value ) {
			$this->assertArrayHasKey( $key, $actual );
			$this->assertSame( $value, $actual[ $key ] );
		}

		return true;
	}

	public function test_register_has_wanted_config(): void {
		$block_type = new BlockType( $this->args['title'], $this->config );

		expect( 'register_block_type' )->once()->with(
			$block_type->get_name(),
			Mockery::on( array( $this, 'assert_in_args' ) )
		);

		$block_type->register();
	}

	public function for_register_with_blocks_set(): array {
		return array(
			array(
				'allowed_blocks',
				array(
					'core/image',
					'core/heading',
					'core/paragraph',
				),
			),
			array(
				'template_blocks',
				array(
					array( 'core/image', array() ),
					array( 'core/heading', array( 'placeholder' => 'Insert title here' ) ),
					array( 'core/paragraph', array( 'placeholder' => 'Insert content copy' ) ),
				),
			),
		);
	}

	/**
	 * @dataProvider for_register_with_blocks_set
	 */
	public function test_register_with_blocks_set( string $key, array $values ): void {
		$this->config[ $key ] = $values;
		$this->args[ $key ]   = $values;

		$block_type = new BlockType( $this->args['title'], $this->config );

		expect( 'register_block_type' )->once()->with(
			$block_type->get_name(),
			Mockery::on( array( $this, 'assert_in_args' ) )
		);

		$block_type->register();
	}

	public function test_register_with_no_inner_blocks(): void {
		$this->config['inner_blocks'] = false;

		$block_type = new BlockType( $this->args['title'], $this->config );

		unset( $this->args['attributes']['innerBlockContent'] );
		expect( 'register_block_type' )->once()->with(
			$block_type->get_name(),
			Mockery::on( array( $this, 'assert_in_args' ) )
		);

		$block_type->register();
	}

	public static function block_callback(): string {
		return 'TEST';
	}

	public function test_render_with_callback(): void {
		$this->config['template'] = array( self::class, 'block_callback' );

		$block_type = new BlockType( $this->args['title'], $this->config );

		$block = $this->getMockBuilder( WP_Block::class )->getMock();

		$block->block_type = (object) array( 'render_template' => wp_json_encode( $this->config['template'] ) );

		$this->assertSame( self::block_callback(), $block_type->render( $this->args['attributes'], '', $block ) );
	}
}
