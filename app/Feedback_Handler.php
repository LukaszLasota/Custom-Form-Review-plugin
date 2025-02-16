<?php
namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Feedback_Handler
 *
 * Zajmuje się obsługą formularza opinii, w tym walidacją
 * i zapisem danych do bazy przez AJAX.
 *
 * @package Custom_Form_Review\App
 */
class Feedback_Handler {

	/**
	 * Nazwa niestandardowej tabeli w bazie danych.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Konstruktor klasy.
	 *
	 * @param string $table_name Nazwa niestandardowej tabeli.
	 */
	public function __construct( $table_name ) {
		$this->table_name = $table_name;
	}

	/**
	 * Obsługuje żądanie AJAX związane z dodaniem nowego wpisu (feedback).
	 *
	 * @return void
	 */
	public function ajax_submit_feedback() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'feedback_nonce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Błąd weryfikacji nonce.', 'custom-form-review' ) )
			);
			wp_die();
		}

		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
		$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$subject    = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		if ( mb_strlen( $subject ) > 255 ) {
			wp_send_json_error(
				array( 'message' => __( 'Temat jest za długi.', 'custom-form-review' ) )
			);
			wp_die();
		}

		if ( ! is_email( $email ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Podano niepoprawny adres email.', 'custom-form-review' ) )
			);
			wp_die();
		}

		if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Wszystkie pola są wymagane.', 'custom-form-review' ) )
			);
			wp_die();
		}

		global $wpdb;

		/*
		 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
		 */
		$inserted = $wpdb->insert(
			$this->table_name,
			array(
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'email'      => $email,
				'subject'    => $subject,
				'message'    => $message,
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);
		// phpcs:enable

		if ( false === $inserted ) {
			wp_send_json_error(
				array( 'message' => __( 'Wystąpił błąd przy zapisywaniu opinii. Spróbuj ponownie.', 'custom-form-review' ) )
			);
			wp_die();
		}

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `" . $this->table_name . "`" );
		// phpcs:enable

		$per_page    = 10;
		$total_pages = ceil( $total / $per_page );

		for ( $p = 1; $p <= $total_pages; $p++ ) {
			wp_cache_delete( 'cfr_entries_page_' . $p, 'custom-form-review' );
		}

		wp_send_json_success(
			array( 'message' => __( 'Dziękujemy za przesłanie swojej opinii.', 'custom-form-review' ) )
		);
		wp_die();
	}
}
