<?php
namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Blocks_Registration
 *
 * Rejestruje bloki na podstawie metadanych (block.json).
 *
 * @package Custom_Form_Review\App
 */
class Blocks_Registration {

	/**
	 * Rejestruje wszystkie wskazane bloki poprzez register_block_type_from_metadata().
	 *
	 * @return void
	 */
	public static function register() {
		$blocks = array(
			array(
				'name'    => 'form-block',
				'options' => array(
					'render_callback' => 'App\Render_Callbacks::render_custom_form_review_form_block',
				),
			),
			array(
				'name'    => 'list-block',
				'options' => array(
					'render_callback' => 'App\Render_Callbacks::render_custom_form_review_list_block',
				),
			),
		);

		foreach ( $blocks as $block ) {
			register_block_type_from_metadata(
				CUSTOM_FORM_REVIEW_PLUGIN_DIR . 'build/blocks/' . $block['name'],
				isset( $block['options'] ) ? $block['options'] : array()
			);
		}
	}
}
