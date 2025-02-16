<?php
namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UserLocalizer
 *
 * Zapewnia lokalizację danych aktualnie zalogowanego użytkownika
 * w skryptach dla edytora bloków (editor) i front-endu (front).
 *
 * @package Custom_Form_Review\App
 */
class UserLocalizer {

	/**
	 * Lokalizuje dane użytkownika w edytorze bloków.
	 *
	 * @return void
	 */
	public static function localize_for_editor() {
		$current_user = wp_get_current_user();
		$user_data    = array();

		if ( $current_user->exists() ) {
			$user_data = array(
				'firstName' => $current_user->first_name,
				'lastName'  => $current_user->last_name,
				'email'     => $current_user->user_email,
			);
		}

		wp_register_script( 'custom-form-review-editor-localize', '', array(), \CUSTOM_FORM_REVIEW_PLUGIN_VERSION, true );
		wp_enqueue_script( 'custom-form-review-editor-localize' );

		wp_localize_script(
			'custom-form-review-editor-localize',
			'CustomFormReviewUser',
			$user_data
		);
	}

	/**
	 * Lokalizuje dane użytkownika w widoku front-end (po stronie klienta).
	 *
	 * @return void
	 */
	public static function localize_for_front() {
		$current_user = wp_get_current_user();
		$user_data    = array();

		if ( $current_user->exists() ) {
			$user_data = array(
				'firstName' => $current_user->first_name,
				'lastName'  => $current_user->last_name,
				'email'     => $current_user->user_email,
			);
		}

		wp_register_script( 'custom-form-review-frontend-localize', '', array(), \CUSTOM_FORM_REVIEW_PLUGIN_VERSION, true );
		wp_enqueue_script( 'custom-form-review-frontend-localize' );

		wp_localize_script(
			'custom-form-review-frontend-localize',
			'CustomFormReviewUser',
			$user_data
		);
	}
}
