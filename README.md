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

return ( new BlockType( 'My custom block' ) )->fields( $list );
```

#### */block/markup.php
```php
<?php
/**
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */
?>

<pre><?php print_r( $attributes ); ?></pre>
<pre><?php print_r( $block ); ?></pre>
```
