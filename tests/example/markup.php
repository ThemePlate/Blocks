<?php

/**
 * @package ThemePlate
 */

/**
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
?>

<div <?php echo get_block_wrapper_attributes( array( 'class' => 'test_container' ) ); ?>>
	<pre><?php print_r( $attributes ); ?></pre>
</div>
