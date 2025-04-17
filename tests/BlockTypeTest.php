<?php

/**
 * @package ThemePlate
 */

namespace Tests;

use Brain\Monkey;
use Mockery;
use ThemePlate\Blocks\AssetsHelper;
use ThemePlate\Blocks\BlockType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ThemePlate\Blocks\FieldsHelper;
use ThemePlate\Blocks\RenderHelper;
use WP_Block;
use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\when;
use function Brain\Monkey\Functions\stubTranslationFunctions;

class BlockTypeTest extends TestCase {
	public const ARGS = array(
		'title'      => 'Test',
		'themeplate' => array(
			'markup' => self::CONFIG['template'],
			'fields' => null,
		),
	);

	public const CONFIG = array(
		'namespace' => 'my-blocks',
		'template'  => '/path/to/render.php',
	);

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		when( 'sanitize_title' )->justReturn( strtolower( self::ARGS['title'] ) );
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_firing_init_actually_add_hooks(): void {
		$block_type = ( new BlockType( self::ARGS['title'] ) )->config( self::CONFIG );

		$block_type->init();
		$this->assertSame( 10, has_action( 'wp_ajax_' . AssetsHelper::ACTION, array( AssetsHelper::class, 'load' ) ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', array( AssetsHelper::class, 'enqueue' ) ) );
		$this->assertSame( 10, has_action( 'wp_ajax_' . FieldsHelper::ACTION, array( FieldsHelper::class, 'load' ) ) );
		$this->assertSame( 10, has_action( 'enqueue_block_editor_assets', array( AssetsHelper::class, 'enqueue' ) ) );
		$this->assertSame( 10, has_action( 'init', array( $block_type, 'register' ) ) );

		stubTranslationFunctions();
		( new BlockType( __DIR__ . '/example' ) )->init();
	}

	/** @return array<int, array<int, bool|string>> */
	public function for_fired_deprecations(): array {
		return array(
			array(
				true,
				'Pass the path to metadata definition.',
			),
			array(
				false,
				'Define custom config in file.',
			),
		);
	}

	/**
	 * @dataProvider for_fired_deprecations
	 */
	public function test_fired_deprecations( bool $deprecated, string $message ): void {
		stubEscapeFunctions();
		stubTranslationFunctions();
		expect( '_deprecated_argument' )->once()->with(
			BlockType::class . '::__construct',
			'1.6.0',
			$message
		);
		( new BlockType( $deprecated ? self::ARGS['title'] : __DIR__ . '/example', array() ) )->init();
		$this->expectNotToPerformAssertions();
	}

	/** @param array<string, mixed> $actual */
	public function assert_in_args( array $actual ): bool {
		foreach ( self::ARGS as $key => $value ) {
			$this->assertArrayHasKey( $key, $actual );
			$this->assertSame( $value, $actual[ $key ] );
		}

		return true;
	}

	/** @return array<int, array<int, bool|string>> */
	public function for_one_liner(): array {
		return array(
			array(
				true,
				'',
			),
			array(
				false,
				'my-blocks/test',
			),
		);
	}

	/**
	 * @dataProvider for_one_liner
	 */
	public function test_one_liner( bool $structured, string $name ): void {
		stubEscapeFunctions();
		stubTranslationFunctions();
		expect( '_deprecated_function' )->times( (int) $structured )->with(
			BlockType::class . '::fields',
			'1.6.0',
			'Pass in the config under "custom_fields" key.'
		);

		$path = $structured ? __DIR__ . '/example' : self::ARGS['title'];

		$block_type = ( new BlockType( $path ) )->fields( array() );

		if ( ! $structured ) {
			$block_type->config( array( 'namespace' => 'my-blocks' ) );
		}

		$this->assertSame( $name, $block_type->get_name() );
	}

	public function test_register_has_wanted_config(): void {
		expect( '_deprecated_function' )->withAnyArgs()->once();

		$block_type = ( new BlockType( self::ARGS['title'] ) )->config( self::CONFIG );

		expect( 'register_block_type' )->once()->with(
			$block_type->get_name(),
			Mockery::on(
				fn ( array $actual ): bool => $this->assert_in_args( $actual )
			)
		);

		$block_type->register();
	}

	/** @return array<int, array<int, mixed>> */
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
	 * @param mixed[] $values
	 * @dataProvider for_register_with_blocks_set
	 */
	public function test_register_with_blocks_set( string $key, array $values ): void {
		expect( '_deprecated_function' )->withAnyArgs()->once();

		$config = self::CONFIG;
		$args   = self::ARGS;

		$config[ $key ] = $values;
		$args[ $key ]   = $values;

		$block_type = ( new BlockType( $args['title'] ) )->config( $config );

		expect( 'register_block_type' )->once()->with(
			$block_type->get_name(),
			Mockery::on(
				fn ( array $actual ): bool => $this->assert_in_args( $actual )
			)
		);

		$block_type->register();
	}

	public function test_register_with_no_inner_blocks(): void {
		expect( '_deprecated_function' )->withAnyArgs()->once();

		$config = self::CONFIG;

		$config['inner_blocks'] = false;

		$block_type = ( new BlockType( self::ARGS['title'] ) )->config( $config );

		expect( 'register_block_type' )->once()->with(
			$block_type->get_name(),
			Mockery::on(
				fn ( array $actual ): bool => $this->assert_in_args( $actual )
			)
		);

		$block_type->register();
	}

	public static function block_callback(): string {
		return 'TEST';
	}

	public function test_render_with_callback(): void {
		$config = self::CONFIG;

		$config['template'] = array( self::class, 'block_callback' );

		( new BlockType( self::ARGS['title'] ) )->config( $config );

		/** @var WP_Block&MockObject $block */
		$block = $this->getMockBuilder( WP_Block::class )->getMock();

		// @phpstan-ignore assign.propertyType
		$block->block_type = (object) array( BlockType::CUSTOM_KEY => array( 'markup' => wp_json_encode( $config['template'] ) ) );

		$this->assertSame( self::block_callback(), RenderHelper::callback( array(), '', $block ) );
	}
}
