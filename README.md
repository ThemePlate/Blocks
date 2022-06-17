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
```

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
```

### Structured *(Bulk)* Definition
```
/path/to/blocks/
├── first-block/
│  ├── config.php
│  └── markup.php
├── second-block/
    ├── config.php
    └── markup.php
```

```php
use ThemePlate\Blocks\CustomBlocks;

( new CustomBlocks( 'My Blocks', '/path/to/blocks' ) )->init();
```

#### */block/config.php
```php
<?php

use ThemePlate\Blocks\BlockType;

return ( new BlockType( 'My custom block' ) )->fields( $list );
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
