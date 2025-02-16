<?php
namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Assets
 *
 * Odpowiada za rejestrowanie i lokalizację zasobów (skryptów i stylów),
 * w tym zmiennej FeedbackAjax na froncie oraz w edytorze bloków.
 *
 * @package Custom_Form_Review\App
 */
class Assets {

	/**
	 * Rejestruje i lokalizuje zmienną FeedbackAjax na froncie.
	 *
	 * @return void
	 */
	public static function enqueue_frontend_assets() {
		wp_register_script( 'custom-form-review-frontend-localize', '', array(), \CUSTOM_FORM_REVIEW_PLUGIN_VERSION, true );
		wp_enqueue_script( 'custom-form-review-frontend-localize' );

		wp_localize_script(
			'custom-form-review-frontend-localize',
			'FeedbackAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'feedback_nonce' ),
			)
		);
	}

	/**
	 * Rejestruje i lokalizuje zmienną FeedbackAjax w edytorze bloków.
	 *
	 * @return void
	 */
	public static function enqueue_editor_assets() {
		wp_register_script( 'custom-form-review-editor-localize', '', array(), \CUSTOM_FORM_REVIEW_PLUGIN_VERSION, true );
		wp_enqueue_script( 'custom-form-review-editor-localize' );

		wp_localize_script(
			'custom-form-review-editor-localize',
			'FeedbackAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'feedback_nonce' ),
			)
		);
	}
}
