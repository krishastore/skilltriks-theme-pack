<?php
/**
 * Template: Assign Course To Me.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_assigned_to_me = get_user_meta( get_current_user_id(), \ST\Lms\STLMS_COURSE_ASSIGN_TO_ME, true ) ? get_user_meta( get_current_user_id(), \ST\Lms\STLMS_COURSE_ASSIGN_TO_ME, true ) : array();
$due_soon              = get_option( 'stlms_settings' );
$due_soon              = ! empty( $due_soon['due_soon'] ) ? $due_soon['due_soon'] : '';
$stlms_users           = array();

foreach ( $course_assigned_to_me as $key => $completion_date ) :
	list( $course_id, $_user_id ) = explode( '_', $key, 2 );
	$stlms_users[]                = get_userdata( $_user_id )->display_name;
endforeach;

$stlms_users = array_unique( $stlms_users );
$layout      = stlmstp_addons_template();
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
	<div class="stlms-filter-toggle-wrap">
		<div class="stlms-container">
			<button class="stlms-filter-toggle">
				<svg width="24" height="24">
					<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#filters"></use>
				</svg>
			</button>
		</div>
	</div>
	<?php if ( ! empty( $_GET['status'] ) && 'success' === wp_unslash( sanitize_key( $_GET['status'] ) ) ) : ?>
	<div class="stlms-snackbar-wrap">
		<div class="stlms-container">
			<div id="snackbar-success" class="stlms-snackbar">
				<svg width="30" height="30">
					<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#tick"></use>
				</svg>
				<?php esc_html_e( 'Course Assigned Successfully!', 'skilltriks-theme-pack' ); ?>
				<button id="hideSnackbar" class="hideSnackbar">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-filter">
				<button class="stlms-filter-toggle stlms-filter-close">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
					<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_new_course' ) ); ?>" class="stlms-btn stlms-btn-block">
						<?php esc_html_e( 'Assign New Course', 'skilltriks-theme-pack' ); ?>
					</a>
				<?php endif; ?>
				<div class="stlms-filter-item">
					<div class="stlms-filter-title stlms-h4">
						<?php esc_html_e( 'Course Progress', 'skilltriks-theme-pack' ); ?>
					</div>
					<label class="stlms-select-search" for="select-progress">
						<select data-placeholder="Choose" class="stlms-select2 js-states form-control" data-minimum-results-for-search="Infinity" id="select-progress">
							<option value=""><?php esc_html_e( 'Choose', 'skilltriks-theme-pack' ); ?></option>
							<option value="not-started"><?php esc_html_e( 'Not Started', 'skilltriks-theme-pack' ); ?></option>
							<option value="in-progress"><?php esc_html_e( 'In Progress', 'skilltriks-theme-pack' ); ?></option>
							<option value="completed"><?php esc_html_e( 'Completed', 'skilltriks-theme-pack' ); ?></option>	
						</select>
					</label>
				</div>
				<div class="stlms-filter-item">
					<div class="stlms-filter-title stlms-h4">
						<?php esc_html_e( 'Employees', 'skilltriks-theme-pack' ); ?>
					</div>
					<label class="stlms-select-search" for="select-employee">
						<?php esc_html_e( 'Select Employee Name', 'skilltriks-theme-pack' ); ?>
						<select data-placeholder="Choose" class="stlms-select2 js-states form-control" id="select-employee">
							<option value=""><?php esc_html_e( 'Choose', 'skilltriks-theme-pack' ); ?></option>
							<?php foreach ( $stlms_users as $users ) : ?>
							<option value="<?php echo esc_html( $users ); ?>"><?php echo esc_html( $users ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
			</div>

			<div class="stlms-course-view">
				<div class="stlms-course-view__body">
					<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
					<div class="stlms-assigned-course__header">
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_by_me' ) ); ?>" class="stlms-assigned-course__btn">
							<span>
								<?php esc_html_e( 'Assigned By Me', 'skilltriks-theme-pack' ); ?>
							</span>
						</a>
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_to_me' ) ); ?>" class="stlms-assigned-course__btn active">
							<span>
								<?php esc_html_e( 'Assigned To Me', 'skilltriks-theme-pack' ); ?>
							</span>
						</a>
					</div>
					<?php endif; ?>
					<div class="stlms-datatable">
						<table id="myTable" class="stripe row-border" style="width:100%">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Course Assigned', 'skilltriks-theme-pack' ); ?></th>
									<th><?php esc_html_e( 'Assigned By', 'skilltriks-theme-pack' ); ?></th>
									<th><?php esc_html_e( 'Completion Date', 'skilltriks-theme-pack' ); ?></th>
									<th><?php esc_html_e( 'Progress Status', 'skilltriks-theme-pack' ); ?></th>
									<th><?php esc_html_e( 'Actions', 'skilltriks-theme-pack' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $course_assigned_to_me as $key => $completion_date ) :

									list( $course_id, $_user_id ) = explode( '_', $key, 2 );

									$user_info        = get_userdata( $_user_id );
									$date_format      = get_option( 'date_format' );
									$completion_date  = strtotime( $completion_date );
									$due_soon_day     = '-' . $due_soon . ' day';
									$due_date         = strtotime( $due_soon_day, $completion_date );
									$formatted_date   = wp_date( $date_format, $completion_date );
									$current_status   = get_user_meta( get_current_user_id(), sprintf( \ST\Lms\STLMS_COURSE_STATUS, $course_id ), true );
									$curriculums      = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
									$course_progress  = '0%';
									$course_view_link = get_the_permalink( $course_id );
									$course_link      = $course_view_link;
									$button_text      = esc_html__( 'Enrol Now', 'skilltriks-theme-pack' );
									if ( ! empty( $curriculums ) ) {
										$curriculums      = \ST\Lms\merge_curriculum_items( $curriculums );
										$curriculums      = array_keys( $curriculums );
										$course_progress  = \ST\Lms\calculate_course_progress( $course_id, $curriculums, $current_status ) . '%';
										$first_curriculum = reset( $curriculums );
										$first_curriculum = explode( '_', $first_curriculum );
										$first_curriculum = array_map( 'absint', $first_curriculum );
										$section_id       = reset( $first_curriculum );
										$item_id          = end( $first_curriculum );
										if ( is_user_logged_in() ) {
											$meta_key      = sprintf( \ST\Lms\STLMS_COURSE_STATUS, $course_id );
											$enrol_courses = get_user_meta( get_current_user_id(), \ST\Lms\STLMS_ENROL_COURSES, true );
											$is_enrol      = ! empty( $enrol_courses ) && in_array( (int) $course_id, $enrol_courses, true );
											$button_text   = $is_enrol ? esc_html__( 'Start', 'skilltriks-theme-pack' ) : $button_text;
											if ( ! empty( $current_status ) ) {
												$course_progress = \ST\Lms\calculate_course_progress( $course_id, $curriculums, $current_status ) . '%';
												$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
												$current_status  = explode( '_', $current_status );
												$section_id      = (int) reset( $current_status );
												$item_id         = (int) end( $current_status );
												$button_text     = esc_html__( 'Continue', 'skilltriks-theme-pack' );
												$last_curriculum = end( $curriculums );
												$last_curriculum = explode( '_', $last_curriculum );
												$last_curriculum = array_map( 'absint', $last_curriculum );
												if ( reset( $last_curriculum ) === $section_id && end( $last_curriculum ) === $item_id ) {
													$restart_course = \ST\Lms\restart_course( $course_id );
													if ( $restart_course ) {
														$first_curriculum = reset( $curriculums );
														$first_curriculum = explode( '_', $first_curriculum );
														$first_curriculum = array_map( 'absint', $first_curriculum );
														$section_id       = reset( $first_curriculum );
														$item_id          = end( $first_curriculum );
														$button_text      = esc_html__( 'Restart', 'skilltriks-theme-pack' );
													}
												}
											}
										}
										$curriculum_type = get_post_type( $item_id );
										$curriculum_type = str_replace( 'stlms_', '', $curriculum_type );
										$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_view_link ), $section_id, $curriculum_type, $item_id );
										$button_text     = apply_filters( 'stlms_course_view_button_text', $button_text );
										$course_link     = apply_filters( 'stlms_course_view_button_link', $course_link );
									}

									if ( '100%' === $course_progress ) {
										$course_status = 'Completed';
									} elseif ( '0%' === $course_progress ) {
										$course_status = 'Not Started';
									} else {
										$course_status = 'In Progress';
									}

									if ( 'publish' === get_post_status( $course_id ) ) :
										?>
								<tr>
									<td>
										<a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>" class="stlms-datatable__course-link">
											<?php echo esc_html( get_the_title( $course_id ) ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $user_info->display_name ); ?></td>
									<td>
										<div class="due-date">
											<?php echo ! empty( $formatted_date ) ? esc_html( $formatted_date ) : '-'; ?>
											<?php
											if ( ! empty( $completion_date ) && '100%' !== $course_progress ) :
													$today_timestamp     = (int) current_datetime()->format( 'U' );
													$formatted_timestamp = strtotime( $formatted_date );
												?>
												<?php if ( $today_timestamp >= $due_date && $today_timestamp <= $formatted_timestamp ) : ?>	
													<span class="stlms-tag due-soon-tag">
														<?php esc_html_e( 'Due Soon', 'skilltriks-theme-pack' ); ?>
													</span>
												<?php endif; ?>
												<?php if ( $today_timestamp > $formatted_timestamp ) : ?>	
													<span class="stlms-tag due-tag">
														<?php esc_html_e( 'Due', 'skilltriks-theme-pack' ); ?>
													</span>
													<?php
												endif;
											endif;
											?>
										</div>
									</td>
									<td>
										<div class="stlms-progress">
											<?php echo esc_html( $course_status . '(' . $course_progress . ')' ); ?>
											<div class="stlms-progress__bar">
												<div class="stlms-progress__bar-inner" style="width: <?php echo esc_html( $course_progress ); ?>"></div>
											</div>
										</div>
									</td>
									<td><a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="stlms-btn outline small" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_html( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="stlms-loader"></i></a></td>
								</tr>
										<?php
									endif;
								endforeach;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>