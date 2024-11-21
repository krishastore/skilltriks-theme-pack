<?php
/**
 * Plugin Name:     BlueDolphin LMS Add on
 * Plugin URI:
 * Description:     A Comprehensive Solution For Training Management. Contact Us For More Details On Training Management System.
 * Author:          KrishaWeb
 * Author URI:      https://getbluedolphin.com
 * Text Domain:     bluedolphin-lms
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BDLMS_ADDONS_BASEFILE', __FILE__ );
define( 'BDLMS_ADDONS_VERSION', '1.0.0' );
define( 'BDLMS_ADDONS_ABSURL', plugins_url( '/', BDLMS_ADDONS_BASEFILE ) );
define( 'BDLMS_ADDONS_ABSPATH', dirname( BDLMS_ADDONS_BASEFILE ) );
define( 'BDLMS_ADDONS_TEMPLATEPATH', BDLMS_ADDONS_ABSPATH . '/templates' );
define( 'BDLMS_ADDONS_ASSETS', BDLMS_ADDONS_ABSURL . 'assets' );

/**
 * Plugin activation.
 */
function bdlms_layout_activation() {
	// Activation code here.
}
register_activation_hook( __FILE__, 'bdlms_layout_activation' );

/**
 * Plugin deactivation.
 */
function bdlms_layout_deactivation() {
	// Deactivation code here.
}
register_deactivation_hook( __FILE__, 'bdlms_layout_deactivation' );

/**
 * Add on template path.
 */
function bdlms_addons_template() {
	$layout = 'default';
	$option = get_option( 'bdlms_settings', array() );
	$layout = isset( $option['theme'] ) ? $option['theme'] : $layout;
	return $layout;
}

/**
 * Enqueue scripts
 */
function bdlms_addons_styles() {
	$layout = bdlms_addons_template();
	if ( 'layout-default' === $layout ) {
		wp_register_style( 'bdlms-frontend', BDLMS_ASSETS . '/css/frontend.css', array(), BDLMS_ADDONS_VERSION );
		wp_register_script( 'bdlms-frontend', BDLMS_ASSETS . '/js/build/frontend.js', array( 'jquery' ), BDLMS_ADDONS_VERSION, true );
	} else {
		wp_register_style( 'bdlms-frontend', BDLMS_ADDONS_ASSETS . '/' . $layout . '/css/bdlms-style.css', array(), BDLMS_ADDONS_VERSION );
		wp_register_script( 'bdlms-addons-frontend', BDLMS_ADDONS_ASSETS . '/' . $layout . '/js/bdlms-setting.js', array( 'jquery' ), BDLMS_ADDONS_VERSION, true );
		wp_enqueue_script( 'bdlms-addons-frontend' );
	}
}
add_action( 'wp_enqueue_scripts', 'bdlms_addons_styles' );

/**
 * Customise css.
 */
function customise_css() {
	$option               = get_option( 'bdlms_settings', array() );
	$layout               = isset( $option['theme'] ) ? $option['theme'] : 'layout-default';
	$customise_css        = isset( $option[ $layout ] ) ? $option[ $layout ] : array();
	$customise_color      = isset( $customise_css['colors'] ) ? $customise_css['colors'] : array();
	$customise_typography = isset( $customise_css['typography'] ) ? $customise_css['typography'] : array();
	?>
		<style>
			:root {
				<?php
				foreach ( $customise_color as $color => $value ) {
					echo '--bdlms-' . str_replace( '_', '-', $color ) . ': ' . $value . ';'; //phpcs:ignore.
				}
				foreach ( $customise_typography as $typography => $value ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $key => $v ) {
							echo '--bdlms-' . str_replace( '_', '-', $typography ) . '-' . str_replace( '_', '-', $key ) . ': ' . $v . ';'; //phpcs:ignore.
						}
					}
				}
				?>
			}
		</style>
	<?php
}
add_action( 'wp_head', 'customise_css' );