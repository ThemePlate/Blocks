# ThemePlate Blocks

## Usage
```php
use ThemePlate\Blocks\BlockType;

/** https://developer.wordpress.org/reference/classes/wp_block_type/__construct/#parameters */
$config = array(
	'namespace' => 'my-blocks',
	'template'  => '/path/to/render.php',
);

( new BlockType( 'Custom Block', $config ) )->fields( $list )->init();

/** >= 1.6.0 */
( new BlockType( __DIR__ . '/tests/example' ) )->init()
```

> Check out [example block](/tests/example)

### Restrict inner blocks and prefill components
```php
$config = array(
	'allowed_blocks'  => array(
		'core/image',
		'core/heading',
		'core/paragraph',
	),
	'template_blocks' => array(
		array( 'core/image', array() ),
		array( 'core/heading', array( 'placeholder' => 'Insert title here' ) ),
		array( 'core/paragraph', array( 'placeholder' => 'Insert content copy' ) ),
	),
);

( new BlockType( 'My custom block', $config ) )->fields( $list )->init();

/** >= 1.6.0 */
// return in the config.php file beside block.json
return $config;
```

> Disable nested blocks by setting `$config` key `inner_blocks` to `false`

### Structured *(Bulk)* Definition
```
/path/to/blocks/
├── first-block/
│  ├── block.json // >= 1.6.0
│  ├── config.php
│  └── markup.php
├── second-block/
    ├── block.json // >= 1.6.0
    ├── config.php
    └── markup.php
```

```php
use ThemePlate\Blocks\CustomBlocks;

( new CustomBlocks( 'My Blocks', '/path/to/blocks' ) )->init();

/** >= 1.6.0 */
( new CustomBlocks( '/path/to/blocks' ) )->init()
```

#### */block/block.json
```json
{
	...
	https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/
	...
}
```

#### */block/config.php
```php
<?php

use ThemePlate\Blocks\BlockType;

return ( new BlockType( 'My custom block' ) )->fields( $list );

/** >= 1.6.0 */
return array(
	...
	/** https://developer.wordpress.org/reference/classes/wp_block_type/__construct/#parameters */
	...
);
```

#### */block/markup.php
```php
<?php
/**
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<?php echo $content; ?>

	<pre><?php print_r( $attributes ); ?></pre>
	<pre><?php print_r( $block ); ?></pre>
</div>
```
