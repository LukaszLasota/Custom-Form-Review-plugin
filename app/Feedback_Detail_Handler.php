<?php
namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Feedback_Detail_Handler
 *
 * Obsługuje żądania AJAX dotyczące pobierania szczegółów wpisu (feedback).
 *
 * @package Custom_Form_Review\App
 */
class Feedback_Detail_Handler {

	/**
	 * Nazwa niestandardowej tabeli.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Konstruktor klasy.
	 *
	 * @param string $table_name Nazwa niestandardowej tabeli w bazie danych.
	 */
	public function __construct( $table_name ) {
		$this->table_name = $table_name;
	}

	/**
	 * Obsługuje żądanie AJAX, zwracając szczegóły danego wpisu feedback.
	 *
	 * @return void
	 */
	public function ajax_get_feedback_detail() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Nie masz uprawnień do przeglądania zawartości tej strony.', 'custom-form-review' ),
				)
			);
			wp_die();
		}

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'feedback_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Błąd weryfikacji nonce.', 'custom-form-review' ),
				)
			);
			wp_die();
		}

		$entry_id = isset( $_POST['entry_id'] ) ? absint( $_POST['entry_id'] ) : 0;
		if ( 0 === $entry_id ) {
			wp_send_json_error(
				array(
					'message' => __( 'Nieprawidłowy identyfikator zgłoszenia.', 'custom-form-review' ),
				)
			);
			wp_die();
		}

		global $wpdb;

		$cache_key = 'cfr_detail_' . $entry_id;
		$entry     = wp_cache_get( $cache_key, 'custom-form-review' );

		if ( false === $entry ) {
			/*
			 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
			 */
			$sql   = sprintf( 'SELECT * FROM `%s` WHERE id = %%d', $this->table_name );
			$entry = $wpdb->get_row( $wpdb->prepare( $sql, $entry_id ) );
			// phpcs:enable

			if ( $entry ) {
				wp_cache_set( $cache_key, $entry, 'custom-form-review', 300 );
			}
		}

		if ( null === $entry ) {
			wp_send_json_error(
				array(
					'message' => __( 'Zgłoszenie nie zostało znalezione.', 'custom-form-review' ),
				)
			);
			wp_die();
		}

		wp_send_json_success( $entry );
		wp_die();
	}
}
