<?php
/**
 * Template: Userinfo shortcode.
 *
 * @package BD\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) :
	$userinfo   = wp_get_current_user();
	$logout_url = wp_logout_url( \BD\Lms\get_page_url( 'login' ) );
	?>
	<div class="bdlms-filter-item">
		<div class="bdlms-user">
			<div class="bdlms-user-photo">
				<div class="bdlms-photo">
					<?php echo get_avatar( $userinfo->ID ); ?>
					<span class="notification-badge"></span>
				</div>
			</div>
			<div class="bdlms-user-info">
				<span class="bdlms-user-name"><?php echo esc_html( $userinfo->display_name ); ?></span>
				<a href="<?php echo esc_url( $logout_url ); ?>" class="bdlms-logout bdlms-link-text"><?php esc_html_e( 'Logout', 'bluedolphin-lms' ); ?></a>
			</div>	
		</div>
		<div class="bdlms-user-dd__menu" style="display: block;">
			<a href="<?php echo esc_url( \BD\Lms\get_page_url( 'my_learning' ) ); ?>" class="bdlms-user-dd__link bdlms-p-small active"><?php esc_html_e( 'My Learnings', 'bluedolphin-lms' ); ?></a>
			<a href="#" class="bdlms-user-dd__link bdlms-p-small"><?php esc_html_e( 'Notifications', 'bluedolphin-lms' ); ?> <span class="bdlms-noti-count">12</span></a>
			<a href="#" class="bdlms-user-dd__link bdlms-p-small"><?php esc_html_e( 'Account Settings', 'bluedolphin-lms' ); ?></a>
		</div>
	</div>
<?php else : ?>
	<a href="<?php echo esc_url( \BD\Lms\get_page_url( 'login' ) ); ?>" class="bdlms-btn bdlms-btn-block"><?php esc_html_e( 'Login', 'bluedolphin-lms' ); ?></a>
<?php endif; ?>