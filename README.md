# ThemePlate Blocks

## Usage
```php
use ThemePlate\Blocks\BlockType;

/** https://developer.wordpress.org/reference/classes/wp_block_type/__construct/#parameters */
$config = array(
	'namespace' => 'my-blocks',
	'template'  => '/path/to/render.php',
);

( new BlockType( 'Custom Block', $config ) )->init();
```
