<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Field;
use ThemePlate\Core\Fields;
use ThemePlate\Core\Helper\MainHelper;
use WP_Block_Type_Registry;

class FieldsHelper extends \ThemePlate\Core\Helper\FieldsHelper {

	public const ACTION = 'themeplate_blocks_fields';


	public static function setup(): void {

		add_action( 'wp_ajax_' . self::ACTION, array( self::class, 'load' ) );

	}


	public static function load(): void {

		check_ajax_referer( AssetsHelper::ACTION );

		$block = WP_Block_Type_Registry::get_instance()->get_registered( $_POST['block'] );

		if ( null === $block ) {
			wp_send_json_error();
		}

		$response = self::prepare( $block->themeplate['fields'] );

		wp_send_json_success( $response );

	}


	protected static function prepare( ?Fields $fields ): array {

		$prepared = array();

		if ( null === $fields ) {
			return $prepared;
		}

		foreach ( $fields->get_collection() as $field ) {
			$config = $field->get_config();

			$config['key']   = $field->data_key();
			$config['class'] = $field->get_classname();

			if ( 'group' === $field->get_config( 'type' ) ) {
				$config['fields'] = self::prepare( $field->get_config( 'fields' ) );
			}

			if (
				! empty( $config['options'] ) &&
				in_array( $field->get_config( 'type' ), array( 'checkbox', 'checklist', 'radio', 'radiolist', 'select', 'select2' ), true )
			) {
				$is_sequential = MainHelper::is_sequential( $config['options'] );

				$config['options'] = array_map(
					function ( $value, $label ) use ( $is_sequential ) {
						if ( $is_sequential ) {
							$value = (string) ( $value + 1 );
						}

						return compact( 'value', 'label' );
					},
					array_keys( $config['options'] ),
					$config['options']
				);
			}

			$prepared[] = $config;
		}

		return $prepared;

	}


	public static function get_schema_type( Field $field ): string {

		switch ( $field->get_config( 'type' ) ) {
			default:
				return parent::get_schema_type( $field );

			case 'file':
				return 'object';
		}

	}

}
