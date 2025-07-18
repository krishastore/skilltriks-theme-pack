<?php
/**
 * Plugin Name:     SkillTriks LMS Add on
 * Plugin URI:
 * Description:     A Comprehensive Solution For Training Management. Contact Us For More Details On Training Management System.
 * Author:          KrishaWeb
 * Author URI:      https://www.skilltriks.com/
 * Text Domain:     skilltriks
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
define( 'STLMS_REQUIRED_PLUGIN_FILE', 'skilltriks/skilltriks.php' );

// Ensure plugin functions are available.
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Plugin activation hook.
 */
function stlms_layout_activation() {
	if ( ! is_plugin_active( STLMS_REQUIRED_PLUGIN_FILE ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
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
 * Show admin notice with banner and plugin thumbnail if Skilltriks is missing or inactive.
 */
function stlms_addons_dependency_notice() {
	if ( is_plugin_active( STLMS_REQUIRED_PLUGIN_FILE ) ) {
		return;
	}

	$plugin_slug       = 'skilltriks';
	$plugin_file       = STLMS_REQUIRED_PLUGIN_FILE;
	$installed_plugins = get_plugins();
	$is_installed      = isset( $installed_plugins[ $plugin_file ] );
	$is_active         = is_plugin_active( $plugin_file );

	$install_url = wp_nonce_url(
		self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ),
		'install-plugin_' . $plugin_slug
	);

	$activate_url = wp_nonce_url(
		self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin_file ),
		'activate-plugin_' . $plugin_file
	);

	$plugin_icon_url   = 'https://ps.w.org/skilltriks/assets/icon-128x128.png';
	$plugin_banner_url = 'https://ps.w.org/skilltriks/assets/banner-1544x500.png';
	?>
	<div class="notice stlms-addon-notice">
		<div class="stlms-addon-notice__banner">
			<img src="<?php echo esc_url( $plugin_banner_url ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="Skilltriks Banner" />
		</div>
		<div class="stlms-addon-notice__body">
			<div class="stlms-addon-notice__icon">
				<img src="<?php echo esc_url( $plugin_icon_url ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="Skilltriks Icon" />
			</div>
			<div class="stlms-addon-notice__content">
				<p>
					<?php
					echo wp_sprintf(
						// translators: %1$s: SkillTriks LMS Theme Add-on, %2$s: SkillTriks plugin.
						esc_html__( '%1$s requires the %2$s plugin to be installed and activated.', 'stlms-addon' ),
						'<strong>' . esc_html__( 'SkillTriks LMS Theme Add-on', 'stlms-addon' ) . '</strong>',
						'<strong>' . esc_html__( 'SkillTriks', 'stlms-addon' ) . '</strong>'
					);
					?>
				</p>
				<p>
					<?php if ( ! $is_installed ) : ?>
						<a href="<?php echo esc_url( $install_url ); ?>" class="button button-primary">
							<?php esc_html_e( 'Install SkillTriks', 'stlms-addon' ); ?>
						</a>
					<?php elseif ( ! $is_active ) : ?>
						<a href="<?php echo esc_url( $activate_url ); ?>" class="button button-primary">
							<?php esc_html_e( 'Activate SkillTriks', 'stlms-addon' ); ?>
						</a>
					<?php endif; ?>
					<a href="https://wordpress.org/plugins/skilltriks/" target="_blank" class="button">
						<?php esc_html_e( 'View Plugin', 'stlms-addon' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'admin_notices', 'stlms_addons_dependency_notice' );

/**
 * Enqueues custom inline styles for the admin notice that displays
 * when the required Skilltriks plugin is not installed or activated.
 */
function stlms_admin_enqueue_notice_styles() {
	if ( ! is_admin() ) {
		return;
	}

	wp_register_style( 'stlms-admin-notice', false, array(), STLMS_ADDONS_VERSION );
	wp_enqueue_style( 'stlms-admin-notice' );

	$custom_css = '
		.stlms-addon-notice {
			border-left: 4px solid #c53030;
			background: #fff;
			margin: 20px 0;
			box-shadow: 0 1px 1px rgba(0,0,0,0.05);
		}
		.stlms-addon-notice__banner img {
			width: 100%;
			height: auto;
			display: block;
			border-bottom: 1px solid #ddd;
			background-size: cover;
		}
		.stlms-addon-notice__body {
			display: flex;
			align-items: flex-start;
			padding: 20px;
		}
		.stlms-addon-notice__icon img {
			width: 64px;
			height: 64px;
			border-radius: 8px;
			margin-right: 20px;
		}
		.stlms-addon-notice__content p {
			margin: 0 0 10px;
			font-size: 14px;
		}
		.stlms-addon-notice__content .button {
			margin-right: 10px;
		}
	';

	wp_add_inline_style( 'stlms-admin-notice', $custom_css );
}
add_action( 'admin_enqueue_scripts', 'stlms_admin_enqueue_notice_styles' );

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
 * Enqueue scripts.
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
		wp_dequeue_style( 'stlms-frontend-assigncourse' );
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