<?php
/**
 * Template: Course detail page content.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$curriculums_list   = ! empty( $args['course_data']['curriculums'] ) ? $args['course_data']['curriculums'] : array();
$current_curriculum = ! empty( $args['course_data']['current_curriculum'] ) ? $args['course_data']['current_curriculum'] : array();
$content_type       = isset( $current_curriculum['media']['media_type'] ) ? $current_curriculum['media']['media_type'] : 'quiz';

$section_id      = get_query_var( 'section' ) ? (int) get_query_var( 'section' ) : 1;
$current_item_id = get_query_var( 'item_id' ) ? (int) get_query_var( 'item_id' ) : 0;

load_template(
	\BlueDolphin\Lms\locate_template( "course-content-$content_type.php" ),
	true,
	array(
		'course_id'  => $args['course_id'],
		'curriculum' => $current_curriculum,
	)
);
?>
</div>
<?php if ( ! empty( $curriculums_list ) ) : ?>
<div class="bdlms-lesson-sidebar">
	<div class="bdlms-sidebar-toggle bdlms-btn outline-small">
		<svg class="icon" width="20" height="20">
			<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#menu-burger"></use>
		</svg>
		<span><?php esc_html_e( 'Lesson List', 'bluedolphin-lms' ); ?></span>
		<svg class="icon-cross" width="20" height="20">
			<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
		</svg>
	</div>
	<div class="bdlms-lesson-accordion">
		<div class="bdlms-accordion">
			<?php
			$completed = true;
			foreach ( $curriculums_list as $item_key => $curriculums ) :
				$items          = ! empty( $curriculums['items'] ) ? $curriculums['items'] : array();
				$total_duration = \BlueDolphin\Lms\count_duration( $items );
				$duration_str   = \BlueDolphin\Lms\seconds_to_hours_str( $total_duration );
				?>
				<div class="bdlms-accordion-item" data-expanded="true">
					<div class="bdlms-accordion-header active">
						<div class="bdlms-lesson-title bdlms-h5">
							<div class="no"><?php echo esc_html( ++$item_key ); ?>.</div>
							<div class="bdlms-lesson-name">
								<div class="name"><?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
								<div class="info bdlms-tag secondary">
									<span><?php printf( '%d/%d', 1, count( $curriculums['items'] ) ); ?></span>
									<?php if ( ! empty( $duration_str ) ) : ?>
										<span><?php echo esc_html( $duration_str ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="bdlms-accordion-collapse" style="display: block;">
						<div class="bdlms-lesson-list">
							<ul>
								<?php
								foreach ( $items as $key => $item ) :
									++$key;
									$media_type      = 'quiz-2';
									$item_id         = isset( $item['item_id'] ) ? $item['item_id'] : 0;
									$curriculum_type = 'quiz_id';
									$inactive        = false;
									if ( \BlueDolphin\Lms\BDLMS_LESSON_CPT === get_post_type( $item_id ) ) {
										$media           = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_LESSON_MEDIA, true );
										$media_type      = ! empty( $media['media_type'] ) ? $media['media_type'] : '';
										$media_type      = 'text' === $media_type ? 'file-text' : $media_type;
										$settings        = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS, true );
										$curriculum_type = 'lesson_id';
									} else {
										$settings = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_QUIZ_SETTINGS, true );
									}
									$duration      = isset( $settings['duration'] ) ? (int) $settings['duration'] : '';
									$duration_type = isset( $settings['duration_type'] ) ? $settings['duration_type'] : '';
									if ( empty( $current_item_id ) && $key > 1 ) {
										$inactive = true;
									}
									if ( $section_id === $item_key && $current_item_id === $item_id ) {
										$inactive  = true;
										$completed = false;
									}

									$meta_key = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, $args['course_id'] );
									$needle   = $item_key . '_' . $item_id;
									$haystack = get_user_meta( get_current_user_id(), $meta_key, true );
									$haystack = is_array( $haystack ) ? $haystack : array();
									?>
								<li class="<?php echo $inactive ? esc_attr( 'active' ) : ''; ?>">
									<div class="<?php echo esc_attr( $inactive ? esc_attr( 'in-progress ' ) : '' ) . esc_attr( $completed ? 'completed ' : '' ); ?>course-progress">
										<?php if ( $inactive ) : ?>
											<input type="checkbox" name="<?php echo esc_attr( $curriculum_type ); ?>[]" class="bdlms-check curriculum-progress-box" value="<?php echo esc_attr( $item_id ); ?>" aria-label="Course progress" checked='checked' disabled>
										<?php else : ?>
											<input type="checkbox" name="<?php echo esc_attr( $curriculum_type ); ?>[]" value="<?php echo esc_attr( $item_id ); ?>" class="bdlms-check curriculum-progress-box"<?php echo $inactive ? ' readonly' : ''; ?><?php checked( true, in_array( $needle, $haystack, true ) ); ?> disabled aria-label="Course progress">
										<?php endif; ?>

										<a href="<?php echo in_array( $needle, $haystack, true ) ? esc_url( get_permalink() . $section_id . '/' . rtrim( $curriculum_type, '_id' ) . '/' . $item_id ) : 'javascript:;'; ?>" class="bdlms-lesson-class">
											<span class="class-name"><span><?php printf( '%d.%d.', (int) $item_key, (int) $key ); ?></span> <?php echo esc_html( get_the_title( $item_id ) ); ?></span>
											<span class="class-time">
												<svg class="icon" width="16" height="16">
													<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#<?php echo esc_html( $media_type ); ?>">
													</use>
												</svg>
												<?php
												if ( ! empty( $duration ) ) {
													$duration_type .= $duration > 1 ? 's' : '';
													printf( '%d %s', (int) $duration, esc_html( ucfirst( $duration_type ) ) );
												} else {
													echo esc_html__( 'No duration', 'bluedolphin-lms' );
												}
												?>
											</span>
										</a>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="bdlms-accordion-item" data-expanded="true">
				<div class="bdlms-accordion-header no-accordion">
					<div class="bdlms-lesson-title">
						<div class="no"><?php echo esc_html( ++$item_key ); ?>.</div>
						<div class="bdlms-lesson-name">
							<div class="name"><?php esc_html_e( 'Conclusion', 'bluedolphin-lms' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<?php
endif;
