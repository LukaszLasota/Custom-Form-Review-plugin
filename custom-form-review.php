<?php
/**
 * Plugin Name: Custom Form Review
 * Description: Wtyczka umożliwiająca wysyłanie opinii oraz ich przeglądanie przez administratorów za pomocą bloków Gutenberga.
 * Version: 1.0.0
 * Author: Łukasz Lasota
 * License: GPL-2.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-form-review
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CUSTOM_FORM_REVIEW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_FORM_REVIEW_PLUGIN_FILE', __FILE__ );
define( 'CUSTOM_FORM_REVIEW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CUSTOM_FORM_REVIEW_PLUGIN_VERSION', '1.0.0' );

global $wpdb;
define( 'CUSTOM_FORM_REVIEW_TABLE', $wpdb->prefix . 'feedback_entries' );

if ( file_exists( CUSTOM_FORM_REVIEW_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once CUSTOM_FORM_REVIEW_PLUGIN_DIR . 'vendor/autoload.php';
}

if ( ! class_exists( 'Custom_Form_Review' ) ) {

	class Custom_Form_Review {

		private static $instance;
		private $table_name;
		private $feedback_handler;

		private function __construct() {
			$this->table_name = CUSTOM_FORM_REVIEW_TABLE;
		
			register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		
			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_action( 'init', array( $this, 'register_blocks' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'localize_block_editor_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'localize_frontend_assets' ) );
			
			if ( class_exists( '\App\Feedback_Handler' ) ) {
				$this->feedback_handler = new \App\Feedback_Handler( $this->table_name );
			}
		
			add_action( 'wp_ajax_submit_feedback', array( $this, 'submit_feedback' ) );
			add_action( 'wp_ajax_nopriv_submit_feedback', array( $this, 'submit_feedback' ) );
			add_action( 'wp_ajax_get_feedback_entries', array( $this, 'ajax_get_feedback_entries' ) );
			add_action( 'wp_ajax_get_feedback_detail', array( $this, 'ajax_get_feedback_detail' ) );
		}
		
		public static function get_instance() {
			if ( ! isset( self::$instance ) || ! ( self::$instance instanceof Custom_Form_Review ) ) {
				self::$instance = new Custom_Form_Review();
			}
			return self::$instance;
		}
		
		public function load_textdomain() {
			load_plugin_textdomain( 'custom-form-review', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
		
		public function activate_plugin() {
			global $wpdb;
			$table_name      = $this->table_name;
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE {$table_name} (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				first_name varchar(100) NOT NULL,
				last_name varchar(100) NOT NULL,
				email varchar(100) NOT NULL,
				subject varchar(255) NOT NULL,
				message text NOT NULL,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset_collate};";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
		
		public function register_blocks() {
			if ( class_exists( 'App\Blocks_Registration' ) ) {
				\App\Blocks_Registration::register();
			}
		}
		
		public function localize_block_editor_assets() {
			if ( class_exists( '\App\UserLocalizer' ) ) {
				\App\UserLocalizer::localize_for_editor();
			}
			if ( class_exists( '\App\Assets' ) ) {
				\App\Assets::enqueue_editor_assets();
			}
		}
	
		public function localize_frontend_assets() {
			if ( class_exists( '\App\UserLocalizer' ) ) {
				\App\UserLocalizer::localize_for_front();
			}
			if ( class_exists( '\App\Assets' ) ) {
				\App\Assets::enqueue_frontend_assets();
			}
		}
			
		public function submit_feedback() {
			if ( $this->feedback_handler ) {
				$this->feedback_handler->ajax_submit_feedback();
			}
		}
		
		public function ajax_get_feedback_entries() {
			if ( class_exists( '\App\Get_Feedback_Entries' ) ) {
				$getter = new \App\Get_Feedback_Entries( $this->table_name );
				$getter->ajax_get_feedback_entries();
			}
		}
		
		public function ajax_get_feedback_detail() {
			if ( class_exists( '\App\Feedback_Detail_Handler' ) ) {
				$detail_handler = new \App\Feedback_Detail_Handler( $this->table_name );
				$detail_handler->ajax_get_feedback_detail();
			}
		}
	}

	Custom_Form_Review::get_instance();
}
