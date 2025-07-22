<?php
/**
 * Template: Assign Course By Me.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) ) { //phpcs:ignore WordPress.WP.Capabilities.Unknown 
	exit;
}
$course_args = array(
	'post_type'      => \ST\Lms\STLMS_COURSE_CPT,
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
);
$courses     = get_posts( $course_args );
$stlms_users = get_users(
	array(
		'fields'       => array( 'ID', 'display_name' ),
		'role__not_in' => array( 'Administrator' ),
		'exclude'      => get_current_user_id(),
	)
);

?>
<div class="stlms-wrap alignfull">
	<?php require_once STLMSTP_ADDONS_TEMPLATEPATH . '/layout-2/sub-header.php'; ?>
	<div class="stlms-title-banner">
		<div class="stlms-container">
			<div class="stlms-page-title">
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
	</div>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-view">
				<div class="stlms-course-view__body">
					<div class="stlms-assign-course__form">
						<form>
							<div class="stlms-assign-course__box">
								<div class="stlms-course-view__title">
									<h4><?php esc_html_e( 'Step 1: Choose Course', 'skilltriks-theme-pack' ); ?></h4>
								</div>
								<div class="stlms-form-group">
									<label for="search-course"><?php esc_html_e( 'Search Course', 'skilltriks-theme-pack' ); ?></label>
									<input type="text" id="search-course" data-list="#course-list" placeholder="Type here to search course" />
								</div>
								<div class="stlms-search-list">
									<ul id="course-list">
										<?php foreach ( $courses as $course_id ) : ?>
										<li>
											<label>
												<input type="radio" class="stlms-check" name="course" value="<?php echo esc_html( $course_id ); ?>"> <?php echo esc_html( get_the_title( $course_id ) ); ?>
											</label>
										</li>
										<?php endforeach; ?>
									</ul>
									<div class="no-results" id="course-no-results" style="display: none;"><?php esc_html_e( 'No results found', 'skilltriks-theme-pack' ); ?></div>
								</div>
							</div>
							<div class="stlms-assign-course__box">
								<div class="stlms-course-view__title">
									<h4>
										<?php esc_html_e( 'Step 2: Choose Employee(s)', 'skilltriks-theme-pack' ); ?>
									</h4>
									<span id="employee_cnt"></span>
								</div>
								<div class="stlms-form-group">
									<label for="search-employee"><?php esc_html_e( 'Search Employee', 'skilltriks-theme-pack' ); ?></label>
									<input type="text" id="search-employee" data-list="#employee-list" placeholder="Type here to search employee" />
								</div>
								<div class="stlms-search-list">
									<ul id="employee-list">
										<?php
										foreach ( $stlms_users as $users ) :
											?>
										<li>
											<label>
												<input type="checkbox" class="stlms-check stlms-employee" value="<?php echo esc_html( base64_encode( $users->ID ) ); ?>"> <?php echo esc_html( $users->display_name ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode ?> 
											</label>
										</li>
										<?php endforeach; ?>
									</ul>
									<div class="no-results" id="employee-no-results" style="display: none;"><?php esc_html_e( 'No results found', 'skilltriks-theme-pack' ); ?></div>
								</div>
							</div>
							<div class="stlms-assign-course__box">
								<div class="stlms-course-view__title">
									<h4>
										<?php esc_html_e( 'Step 3: Choose Completion Date', 'skilltriks-theme-pack' ); ?>
									</h4>
								</div>
								<p class="stlms-assign-course__box-text"><?php esc_html_e( 'Keep field blank for no completion date', 'skilltriks-theme-pack' ); ?></p>
								<div class="stlms-switch-wrap">
									<?php esc_html_e( 'Common completion date for all?', 'skilltriks-theme-pack' ); ?>
									<label class="switch">
										<input type="checkbox" class="stlms-check">
										<span class="slider round"></span>
									</label>
								</div>
								<div class="stlms-form-group" id="common-date">
									<label for="completion-date"><?php esc_html_e( 'Completion Date', 'skilltriks-theme-pack' ); ?></label>
									<input type="date" id="completion-date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" />
								</div>
								<div class="stlms-form-row" id="unique-date">
									<div class="stlms-form-col">
										<div class="stlms-form-group">
											<label for="completion-date"></label>
											<input type="date" id="completion-date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" />
										</div>
									</div>
								</div>
							</div>
							<div class="stlms-assign-course__submit">
								<button class="stlms-btn" id="showSnackbar"><?php esc_html_e( 'Assign Course', 'skilltriks-theme-pack' ); ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="stlms-snackbar-wrap">
		<div class="stlms-container">
			<div id="snackbar-error" class="stlms-snackbar error-snackbar">
				<svg width="30" height="30">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross-error"></use>
				</svg>
				<?php esc_html_e( 'Oops, something went wrong. Please try again later.', 'skilltriks-theme-pack' ); ?>
				<button id="hideSnackbar" class="hideSnackbar">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
			</div>
		</div>
	</div>
</div>