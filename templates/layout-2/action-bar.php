<?php
/**
 * Template: Courses - action bar.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_id       = get_query_var( 'section' ) ? (int) get_query_var( 'section' ) : 1;
$curriculums      = $args['curriculums'];
$current_item     = $args['current_item'];
$curriculum_type  = $args['curriculum_type'];
$curriculums_keys = array_keys( $curriculums );
$current_index    = \ST\Lms\find_current_curriculum_index( $current_item, $curriculums, $section_id );
$is_quiz          = \ST\Lms\STLMS_QUIZ_CPT === get_post_type( $current_item );

$next_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $next_key ) {
	++$next_key;
}

$prev_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $prev_key ) {
	--$prev_key;
}
$course_result   = apply_filters( 'stlms_course_result_endpoint', 'course-result' );
$result_page_url = sprintf( '%s/%s/%d/', untrailingslashit( home_url() ), $course_result, get_the_ID() );
$layout          = stlms_addons_template();

?>
<div class="stlms-course-content">
<div class="stlms-lesson-view__header">
	<div class="stlms-lesson-view__breadcrumb">
		<ul>
			<li>
				<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>" aria-label="<?php esc_attr_e( 'Course page', 'skilltriks' ); ?>">
					<svg class="icon" width="16" height="16">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#home"></use>
					</svg>
				</a>
			</li>
			<li><?php echo esc_html( get_the_title( $args['current_item'] ) ); ?></li>
		</ul>
	</div>
	<div class="stlms-lesson-view__pagination">
		<?php if ( $prev_key >= 0 && isset( $curriculums_keys[ $prev_key ] ) ) : ?>
			<a href="<?php echo esc_url( \ST\Lms\get_curriculum_link( $curriculums_keys[ $prev_key ] ) ); ?>" class="stlms-btn stlms-btn-icon stlms-btn-flate stlms-prev-btn">
				<svg class="icon" width="11" height="19">
					<use xlink:href="<?php echo esc_url( STLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#nav-left"></use>
				</svg>
			</a>
		<?php endif; ?>
		<?php if ( $next_key >= 1 && isset( $curriculums_keys[ $next_key ] ) ) : ?>
			<a href="<?php echo esc_url( \ST\Lms\get_curriculum_link( $curriculums_keys[ $next_key ] ) ); ?>" class="stlms-btn stlms-btn-icon stlms-btn-flate stlms-next-btn<?php echo $is_quiz ? ' hidden' : ''; ?>">
				<svg class="icon" width="11" height="19">
					<use xlink:href="<?php echo esc_url( STLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#nav-right"></use>
				</svg>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $result_page_url ); ?>" class="stlms-btn stlms-btn-icon stlms-btn-flate stlms-next-btn<?php echo 'video' === $curriculum_type || $is_quiz ? ' hidden' : ''; ?>">
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( STLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#nav-right"></use>
				</svg>
			</a>
		<?php endif; ?>
	</div>
</div>