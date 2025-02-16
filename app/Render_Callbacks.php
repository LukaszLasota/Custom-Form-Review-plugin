<?php
/**
 * Render Callbacks.
 *
 * @package Custom_Form_Review
 */

namespace App;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Render_Callbacks
 *
 * Zawiera funkcje renderujące dla bloków Gutenberg (formularz i lista).
 *
 * @package Custom_Form_Review
 */
class Render_Callbacks {

	/**
	 * Renderuje blok formularza (form-block).
	 *
	 * @param array  $attributes Atrybuty bloku.
	 * @param string $content    Zawartość (bloków wewnętrznych), jeśli występuje.
	 * @return string HTML wygenerowany przez blok.
	 */
	public static function render_custom_form_review_form_block( $attributes, $content ) {
		ob_start();
		?>
		<div class="feedback-form-container">
			<h2><?php echo esc_html__( 'Prześlij swoją opinię', 'custom-form-review' ); ?></h2>
			<form class="feedback-form">
				<input type="text" name="first_name" placeholder="<?php esc_attr_e( 'Imię', 'custom-form-review' ); ?>" />
				<input type="text" name="last_name" placeholder="<?php esc_attr_e( 'Nazwisko', 'custom-form-review' ); ?>" />
				<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email', 'custom-form-review' ); ?>" />
				<input type="text" name="subject" placeholder="<?php esc_attr_e( 'Temat', 'custom-form-review' ); ?>" />
				<textarea name="message" placeholder="<?php esc_attr_e( 'Wiadomość', 'custom-form-review' ); ?>"></textarea>
				<button type="submit"><?php esc_html_e( 'Wyślij', 'custom-form-review' ); ?></button>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renderuje blok listy (list-block).
	 *
	 * Jeśli użytkownik nie ma uprawnień 'manage_options', zwraca komunikat błędu.
	 * W przeciwnym razie zwraca kontener z miejscem na listę, paginację i szczegóły wpisu.
	 *
	 * @param array  $attributes Atrybuty bloku (niewykorzystane).
	 * @param string $content    Zawartość bloku (niewykorzystana).
	 * @return string HTML wygenerowany przez blok.
	 */
	public static function render_custom_form_review_list_block( $attributes = array(), $content = '' ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<div class="feedback-error"><p>' . esc_html__( 'Nie masz uprawnień do przeglądania zawartości tej strony.', 'custom-form-review' ) . '</p></div>';
		}

		ob_start();
		?>
		<div class="feedback-list-container">
			<ul class="feedback-list"></ul>
			<div class="feedback-pagination"></div>
			<div class="feedback-detail"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}
