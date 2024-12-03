<?php
/**
 * Template: Courses - action bar.
 *
 * @package BlueDolphin\Lms
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
$current_index    = \BlueDolphin\Lms\find_current_curriculum_index( $current_item, $curriculums, $section_id );

$next_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $next_key ) {
	++$next_key;
}

$prev_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $prev_key ) {
	--$prev_key;
}
$course_result   = apply_filters( 'bdlms_course_result_endpoint', 'course-result' );
$result_page_url = sprintf( '%s/%s/%d/', untrailingslashit( home_url() ), $course_result, get_the_ID() );

?>
<div class="bdlms-course-content">
<div class="bdlms-lesson-view__header">
	<div class="bdlms-lesson-view__breadcrumb">
		<ul>
			<li>
				<a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) ); ?>" aria-label="<?php esc_attr_e( 'Course page', 'bluedolphin-lms' ); ?>">
					<svg class="icon" width="16" height="16">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#home"></use>
					</svg>
				</a>
			</li>
			<li><?php echo esc_html( get_the_title( $args['current_item'] ) ); ?></li>
		</ul>
	</div>
	<div class="bdlms-lesson-view__pagination" style="display:none;">
		<?php if ( $next_key >= 1 && isset( $curriculums_keys[ $next_key ] ) ) : ?>
			<a href="<?php echo esc_url( \BlueDolphin\Lms\get_curriculum_link( $curriculums_keys[ $next_key ] ) ); ?>" class="bdlms-btn bdlms-btn-icon bdlms-btn-flate bdlms-next-btn">
				<?php esc_html_e( 'Next', 'bluedolphin-lms' ); ?>
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-right"></use>
				</svg>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $result_page_url ); ?>" class="bdlms-btn bdlms-btn-icon bdlms-btn-flate bdlms-next-btn<?php echo 'video' === $curriculum_type ? ' hidden' : ''; ?>">
				<?php esc_html_e( 'Next', 'bluedolphin-lms' ); ?>
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-right"></use>
				</svg>
			</a>
		<?php endif; ?>
	</div>
</div>