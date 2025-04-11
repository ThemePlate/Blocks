<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\Blocks;

use ThemePlate\Core\Field;
use ThemePlate\Core\Fields;
use ThemePlate\Core\Helper\FieldsHelper as CoreFieldsHelper;
use ThemePlate\Core\Helper\MainHelper;
use WP_Block_Type_Registry;

class FieldsHelper extends CoreFieldsHelper {

	public const ACTION = 'themeplate_blocks_fields';

	public const LOCATIONS = array(
		'default',
		'styles',
		'advanced',
	);


	public static function setup(): void {

		add_action( 'wp_ajax_' . self::ACTION, array( self::class, 'load' ) );

	}


	public static function load(): void {

		check_ajax_referer( AssetsHelper::ACTION );

		$block = WP_Block_Type_Registry::get_instance()->get_registered( $_POST['block'] );

		if ( null === $block || ! property_exists( $block, 'themeplate' ) ) {
			wp_send_json_error();
			return;
		}

		$response = self::prepare( $block->themeplate['fields'] );

		wp_send_json_success( $response );

	}


	public static function is_choice_type( Field $field ): bool {

		$fields = array( 'checkbox', 'checklist', 'radio', 'radiolist', 'select', 'select2' );

		return in_array( $field->get_config( 'type' ), $fields, true );

	}


	/** @return array<string, array{}|mixed> */
	public static function prepare( ?Fields $fields ): array {

		$prepared = array_fill_keys( self::LOCATIONS, array() );

		if ( ! $fields instanceof Fields ) {
			return $prepared;
		}

		foreach ( $fields->get_collection() as $field ) {
			$config = $field->get_config();

			$config['key']   = $field->data_key();
			$config['class'] = $field->get_classname();

			if ( 'group' === $field->get_config( 'type' ) ) {
				$config['fields'] = self::prepare( $field->get_config( 'fields' ) );
				$config['fields'] = $config['fields']['default'];
			} elseif ( self::is_choice_type( $field ) && ! empty( $config['options'] ) ) {
				$is_sequential = MainHelper::is_sequential( $config['options'] );

				$config['options'] = array_map(
					function ( $value, $label ) use ( $is_sequential ): array {
						if ( $is_sequential ) {
							$value = (string) ( (int) $value + 1 );
						}

						return compact( 'value', 'label' );
					},
					array_keys( $config['options'] ),
					$config['options']
				);
			}

			$location = $field->get_config( 'location' );

			if ( ! in_array( $location, self::LOCATIONS, true ) ) {
				$location = 'default';
			}

			$prepared[ $location ][] = $config;
		}

		return $prepared;

	}


	/**
	 * @return array{}|array{
	 *     type: string,
	 *     default: array<int|string, mixed>|string,
	 *     properties?: array<string, mixed>,
	 *     items?: array<string, mixed>
	 * }
	 */
	public static function get_schema( Field $field ): array {

		$schema = parent::get_schema( $field );

		if ( 'file' !== $field->get_config( 'type' ) ) {
			return $schema;
		}

		$properties = array();

		foreach ( array( 'id', 'url', 'type', 'title' ) as $key ) {
			$properties[ $key ] = array(
				'type'    => 'string',
				'default' => '',
			);
		}

		$schema['properties'] = $properties;

		return $schema;

	}


	public static function get_schema_type( Field $field ): string {

		switch ( $field->get_config( 'type' ) ) {
			default:
				return parent::get_schema_type( $field );

			case 'file':
				return 'object';
		}

	}


	/**
	 * @return mixed
	 */
	public static function get_default_value( Field $field ) {

		$default = parent::get_default_value( $field );

		if ( 'group' === $field->get_config( 'type' ) ) {
			$fields = static::group_fields( $field->get_config( 'fields' ) );

			foreach ( $fields->get_collection() as $sub_field ) {
				if ( 'file' !== $sub_field->get_config( 'type' ) ) {
					continue;
				}

				static::adjust_file_value( $default[ $sub_field->data_key() ] );
			}
		} elseif ( 'file' === $field->get_config( 'type' ) ) {
			self::adjust_file_value( $default );
		}

		return $default;

	}


	/**
	 * @param mixed $default_value
	 */
	protected static function adjust_file_value( &$default_value ): void {

		$is_array = is_array( $default_value );

		$default_value = array_map(
			fn( $value ): array => array(
				'id'    => $value,
				'url'   => '',
				'type'  => '',
				'title' => '',
			),
			(array) $default_value
		);

		if ( ! $is_array ) {
			$default_value = $default_value[0];
		}

	}

}
