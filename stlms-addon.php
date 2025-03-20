<?php
/**
 * Plugin Name:     SkillTriks LMS Add on
 * Plugin URI:
 * Description:     A Comprehensive Solution For Training Management. Contact Us For More Details On Training Management System.
 * Author:          KrishaWeb
 * Author URI:      https://www.skilltriks.com/
 * Text Domain:     skilltriks-lms
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'STLMS_ADDONS_BASEFILE', __FILE__ );
define( 'STLMS_ADDONS_VERSION', '1.0.0' );
define( 'STLMS_ADDONS_ABSURL', plugins_url( '/', STLMS_ADDONS_BASEFILE ) );
define( 'STLMS_ADDONS_ABSPATH', dirname( STLMS_ADDONS_BASEFILE ) );
define( 'STLMS_ADDONS_TEMPLATEPATH', STLMS_ADDONS_ABSPATH . '/templates' );
define( 'STLMS_ADDONS_ASSETS', STLMS_ADDONS_ABSURL . 'assets' );

/**
 * Plugin activation.
 */
function stlms_layout_activation() {
	// Activation code here.
}
register_activation_hook( __FILE__, 'stlms_layout_activation' );

/**
 * Plugin deactivation.
 */
function stlms_layout_deactivation() {
	// Deactivation code here.
}
register_deactivation_hook( __FILE__, 'stlms_layout_deactivation' );

/**
 * Add on template path.
 */
function stlms_addons_template() {
	$layout = 'default';
	$option = get_option( 'stlms_settings', array() );
	$layout = isset( $option['theme'] ) ? $option['theme'] : $layout;
	return $layout;
}

/**
 * Enqueue scripts
 */
function stlms_addons_styles() {
	$layout = stlms_addons_template();
	if ( 'layout-default' === $layout ) {
		wp_register_style( 'stlms-frontend', STLMS_ASSETS . '/css/frontend.css', array(), STLMS_ADDONS_VERSION );
	} else {
		if ( 'layout-2' === $layout ) {
			wp_enqueue_style( 'lato-font-family', 'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap', array(), STLMS_ADDONS_VERSION );
		}
		wp_register_style( 'stlms-frontend', STLMS_ADDONS_ASSETS . '/' . $layout . '/css/stlms-style.css', array(), STLMS_ADDONS_VERSION );
		wp_register_script( 'stlms-addons-frontend', STLMS_ADDONS_ASSETS . '/' . $layout . '/js/stlms-setting.js', array( 'jquery' ), STLMS_ADDONS_VERSION, true );
		wp_enqueue_script( 'stlms-addons-frontend' );
	}
}
add_action( 'wp_enqueue_scripts', 'stlms_addons_styles' );

/**
 * Customise css.
 */
function customise_css() {
	$option               = get_option( 'stlms_settings', array() );
	$layout               = isset( $option['theme'] ) ? $option['theme'] : 'layout-default';
	$customise_css        = isset( $option[ $layout ] ) ? $option[ $layout ] : array();
	$customise_color      = isset( $customise_css['colors'] ) ? $customise_css['colors'] : array();
	$customise_typography = isset( $customise_css['typography'] ) ? $customise_css['typography'] : array();
	?>
		<style>
			:root {
				<?php
				foreach ( $customise_color as $color => $value ) {
					echo '--stlms-' . str_replace( '_', '-', $color ) . ': ' . $value . ';'; //phpcs:ignore.
				}
				foreach ( $customise_typography as $typography => $value ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $key => $v ) {
							echo '--stlms-' . str_replace( '_', '-', $typography ) . '-' . str_replace( '_', '-', $key ) . ': ' . $v . ';'; //phpcs:ignore.
						}
					}
				}
				if ( isset( $customise_typography['global']['font_family'] ) ) {
					echo '--stlms-heading-font-family: ' . $customise_typography['global']['font_family']; //phpcs:ignore.
				}
				if ( isset( $customise_typography['body']['font_family'] ) ) {
					echo '--stlms-body-font-family: ' . $customise_typography['body']['font_family']; //phpcs:ignore.
				}
				?>
			}
		</style>
	<?php
}
add_action( 'wp_head', 'customise_css' );