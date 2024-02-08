<?php

namespace Tests;

use ThemePlate\Blocks\FieldsHelper;
use PHPUnit\Framework\TestCase;
use ThemePlate\Core\Fields;
use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;

class FieldsHelperTest extends TestCase {

	public function test_prepare_response() {
		stubEscapeFunctions();
		stubTranslationFunctions();

		$config = require __DIR__ . '/example/config.php';
		$fields = $config['custom_fields'];
		$actual = FieldsHelper::prepare( new Fields( $fields ) );
		$counts = array(
			'config' => array_count_values( array_column( $fields, 'location' ) ),
			'actual' => array_map(
				function( $location ) {
					return count( $location );
				},
				$actual
			),
		);

		if ( ! isset( $counts['config']['default'] ) ) {
			$counts['config']['default'] = count( $fields ) - array_sum( $counts['config'] );
		}

		foreach ( $counts['actual'] as $location => $count ) {
			if ( 'default' === $location ) {
				// unknown locations to default
				$count -= array_sum( array_diff_key( $counts['config'], $counts['actual'] ) );
			}

			$this->assertSame( $counts['config'][ $location ], $count );
		}
	}
}
