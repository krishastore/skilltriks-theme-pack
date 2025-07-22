<?php
/**
 * Template: Login
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$error_code = ! empty( $_GET['message'] ) ? (int) $_GET['message'] : 0;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$email   = ! empty( $_GET['email'] ) && is_email( wp_unslash( $_GET['email'] ) ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
$message = '';
if ( 1 === $error_code ) {
	$message = __( 'something went wrong, Please try again', 'skilltriks-theme-pack' );
} elseif ( 2 === $error_code ) {
	$message = __( 'Your account role is different, please contact to administration.', 'skilltriks-theme-pack' );
} elseif ( 3 === $error_code ) {
	// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$message = sprintf( __( 'User %s not registered in system.', 'skilltriks-theme-pack' ), $email );
}
?>
<div class="stlms-wrap alignfull">
	<div class="stlms-login-wrap">
		<div class="stlms-login">
			<div class="stlms-login__header">
				<div class="stlms-login__title stlms-h2"><?php esc_html_e( 'Login to Skilltriks', 'skilltriks-theme-pack' ); ?></div>
				<div class="stlms-login__text"><?php esc_html_e( 'Hey, Welcome back!', 'skilltriks-theme-pack' ); ?><br> <?php esc_html_e( 'Please sign in to grow yourself', 'skilltriks-theme-pack' ); ?></div>
			</div>
			<div class="stlms-login__body">
				<?php if ( is_admin() || ! is_user_logged_in() ) : ?>
					<form action="" method="post">
						<?php wp_nonce_field( \ST\Lms\STLMS_LOGIN_NONCE, '_stlms_nonce' ); ?>
						<input type="hidden" name="action" value="stlms_login">
						<div class="stlms-form-group">
							<label class="stlms-form-label"><?php esc_html_e( 'Username', 'skilltriks-theme-pack' ); ?></label>
							<input type="text" name="username" class="stlms-form-control" placeholder="<?php esc_attr_e( 'Username', 'skilltriks-theme-pack' ); ?>" required>
						</div>
						<div class="stlms-form-group">
							<label class="stlms-form-label"><?php esc_html_e( 'Password', 'skilltriks-theme-pack' ); ?></label>
							<div class="stlms-password-field">
								<input type="password" name="password" class="stlms-form-control" placeholder="********" id="password-field" required>
								<div class="stlms-password-toggle" toggle="#password-field">
									<svg width="30" height="30" class="eye-on">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS . '/images/sprite-front.svg#show-password' ); ?>"></use>
									</svg>
									<svg width="30" height="30" class="eye-off">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS . '/images/sprite-front.svg#hide-password' ); ?>"></use>
									</svg>
								</div>
							</div>
						</div>
						<div class="stlms-keep-login stlms-form-group">
							<div class="stlms-check-wrap">
								<input type="checkbox" name="remember" class="stlms-check" id="remember">
								<label for="remember" class="stlms-check-label text-sm"><?php esc_html_e( 'Keep me logged In', 'skilltriks-theme-pack' ); ?></label>
							</div>
							<div class="stlms-forgot-link">
								<a class="stlms-link-text" href="<?php echo esc_url( wp_lostpassword_url( \ST\Lms\get_page_url( 'login' ) ) ); ?>" target="_blank"><?php esc_html_e( 'Forgot Password?', 'skilltriks-theme-pack' ); ?></a>
							</div>
						</div>
						<div class="stlms-error-message<?php echo empty( $message ) ? ' hidden' : ''; ?>">
							<span class="stlms-form-error"><?php echo esc_html( $message ); ?></span>
						</div>
						<?php
						$auth_url = \ST\Lms\Login\GoogleLogin::instance()->get_auth_url();
						?>
							<div class="stlms-form-footer">
								<button type="submit" class="stlms-btn stlms-btn-block"><?php esc_html_e( 'Log In', 'skilltriks-theme-pack' ); ?><span class="stlms-loader"></span></button>
								<?php if ( $auth_url ) : ?>
								<div class="stlms-login-with">
									<div class="stlms-hr"></div>
										<div class="stlms-or-text"><?php esc_html_e( 'or Log-in with', 'skilltriks-theme-pack' ); ?></div>
									<div class="stlms-hr"></div>
								</div>
								<div class="stlms-login-with-cta">
									<a class='stlms-btn google-sign-in-btn' href="<?php echo esc_url( $auth_url ); ?>">
									<svg width="26" height="28" viewBox="0 0 26 28" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.43653 7.51992C8.17054 7.51992 7.00866 7.83225 5.95089 8.45692C4.89312 9.08159 4.0519 9.9228 3.42724 10.9806C2.80257
										12.0383 2.49023 13.2002 2.49023 14.4662C2.49023 15.7322 2.80257 16.8983 3.42724 17.9644C4.0519 19.0304 4.88063 19.8717 5.91341
										20.488C6.99617 21.121 8.17054 21.4375 9.43653 21.4375C10.7525 21.4375 11.9185 21.1502 12.9347 20.5755C13.9508 20.0008 14.7337 
										19.197 15.2834 18.1642C15.8331 17.1315 16.108 15.9571 16.108 14.6411C16.108 14.0914 16.0747 13.7 16.008 13.4667H9.43653V15.8655H13.3844C13.3178
										16.2819 13.1512 16.69 12.8847 17.0898C12.5682 17.6062 12.1518 18.0143 11.6354 18.3142C11.019 18.6806 10.2861 18.8639 9.43653 18.8639C8.65362 18.8639
										7.93317 18.6681 7.27518 18.2767C6.6172 17.8852 6.09248 17.3522 5.70102 16.6775C5.30957 16.0029 5.11384 15.2658 5.11384 14.4662C5.11384 13.6666 5.30957 
										12.9295 5.70102 12.2549C6.09248 11.5803 6.6172 11.0472 7.27518 10.6557C7.93317 10.2643 8.65362 10.0686 9.43653 10.0686C10.5359 10.0686 11.4605 10.4267 
										12.2101 11.143L14.109 9.34395C12.8097 8.12793 11.2522 7.51992 9.43653 7.51992ZM20.3557 11.4928V13.4667H18.3818V15.4657H20.3557V17.4396H22.3546V15.4657H24.3536V13.4667H22.3546V11.4928H20.3557Z" fill="white"/>
									</svg>
										<?php esc_html_e( 'Google', 'skilltriks-theme-pack' ); ?>
									</a>
								</div>
								<?php endif; ?>
							</div>
					</form>
					<?php
				else :
					wp_safe_redirect( \ST\Lms\get_page_url( 'courses' ) );
					exit;
				endif;
				?>
			</div>
		</div>
	</div>
</div>
