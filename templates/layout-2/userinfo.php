<?php
/**
 * Template: Userinfo shortcode.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) :
	$userinfo   = wp_get_current_user();
	$logout_url = wp_logout_url( \ST\Lms\get_page_url( 'login' ) );
	?>
	<div class="stlms-filter-item">
		<div class="stlms-user">
			<div class="stlms-user-photo">
				<div class="stlms-photo">
					<?php echo get_avatar( $userinfo->ID ); ?>
					<span class="notification-badge"></span>
				</div>
			</div>
			<div class="stlms-user-info">
				<span class="stlms-user-name"><?php echo esc_html( $userinfo->display_name ); ?></span>
				<a href="<?php echo esc_url( $logout_url ); ?>" class="stlms-logout stlms-link-text"><?php esc_html_e( 'Logout', 'skilltriks-theme-pack' ); ?></a>
			</div>	
		</div>
		<div class="stlms-user-dd__menu" style="display: block;">
			<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'my_learning' ) ); ?>" class="stlms-user-dd__link stlms-p-small<?php echo \ST\Lms\get_page_url( 'my_learning' ) === get_permalink() ? ' active' : ''; ?>"><?php esc_html_e( 'My Learnings', 'skilltriks-theme-pack' ); ?></a>
			<a href="#" class="stlms-user-dd__link stlms-p-small" style="display: none;"><?php esc_html_e( 'Notifications', 'skilltriks-theme-pack' ); ?> <span class="stlms-noti-count">12</span></a>
			<a href="#" class="stlms-user-dd__link stlms-p-small" style="display: none;"><?php esc_html_e( 'Account Settings', 'skilltriks-theme-pack' ); ?></a>
		</div>
	</div>
<?php else : ?>
	<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'login' ) ); ?>" class="stlms-btn stlms-btn-block"><?php esc_html_e( 'Login', 'skilltriks-theme-pack' ); ?></a>
<?php endif; ?>