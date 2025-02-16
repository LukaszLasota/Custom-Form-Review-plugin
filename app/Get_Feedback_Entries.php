<?php
namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Get_Feedback_Entries
 *
 * Obsługuje żądania AJAX dotyczące pobierania listy wpisów (feedback)
 * z niestandardowej tabeli w bazie danych.
 *
 * @package Custom_Form_Review\App
 */
class Get_Feedback_Entries {

	/**
	 * Nazwa niestandardowej tabeli.
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
	 * Obsługuje żądanie AJAX i zwraca listę wpisów feedback (z cache).
	 *
	 * @return void
	 */
	public function ajax_get_feedback_entries() {
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

		$page     = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$per_page = 10;
		$offset   = ( $page - 1 ) * $per_page;

		global $wpdb;

		$cache_key = 'cfr_entries_page_' . $page;
		$data      = wp_cache_get( $cache_key, 'custom-form-review' );

		if ( false === $data ) {
			/*
			 * phpcs:disable WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
			 */
			$sql = sprintf(
				'SELECT id, first_name, last_name, email, subject, created_at
				 FROM `%s` ORDER BY created_at DESC LIMIT %%d OFFSET %%d',
				$this->table_name
			);
			$entries = $wpdb->get_results( $wpdb->prepare( $sql, $per_page, $offset ) );

			$total_sql = sprintf( 'SELECT COUNT(*) FROM `%s`', $this->table_name );
			$total     = $wpdb->get_var( $total_sql );
			// phpcs:enable

			$data = array(
				'entries'      => $entries,
				'total'        => (int) $total,
				'per_page'     => $per_page,
				'current_page' => $page,
			);

			wp_cache_set( $cache_key, $data, 'custom-form-review', 300 );
		}

		wp_send_json_success( $data );
		wp_die();
	}
}
