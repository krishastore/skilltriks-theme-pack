<?php
/**
 * Template: Course Detail Page
 *
 * @package ST\Lms
 *
 * phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id        = ! empty( $args['course_id'] ) ? $args['course_id'] : 0;
$curriculums_list = ! empty( $args['course_data']['curriculums'] ) ? $args['course_data']['curriculums'] : array();

global $current_user;
$current_user_id    = $current_user->ID;
$current_user_name  = $current_user->display_name;
$current_user_email = $current_user->user_email;
$layout             = stlmstp_addons_template();
?>

<div class="stlms-wrap">
	<?php
	$course_category    = get_the_terms( get_the_ID(), \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
	$category_id        = wp_list_pluck( $course_category, 'term_id' );
	$course_category    = wp_list_pluck( $course_category, 'name' );
	$level              = get_the_terms( get_the_ID(), \ST\Lms\STLMS_COURSE_TAXONOMY_TAG );
	$level              = wp_list_pluck( $level, 'name' );
	$level              = end( $level );
	$assessment         = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_ASSESSMENT, true );
	$passing_grade      = isset( $assessment['passing_grade'] ) ? $assessment['passing_grade'] . '%' : '0%';
	$curriculums        = \ST\Lms\merge_curriculum_items( $curriculums_list );
	$curriculums        = array_keys( $curriculums );
	$course_progress    = \ST\Lms\calculate_course_progress( $course_id, $curriculums ) . '%';
	$lessons            = \ST\Lms\get_curriculums( $curriculums_list, \ST\Lms\STLMS_LESSON_CPT );
	$total_lessons      = count( $lessons );
	$quizzes            = \ST\Lms\get_curriculums( $curriculums_list, \ST\Lms\STLMS_QUIZ_CPT );
	$last_quiz          = end( $quizzes );
	$total_quizzes      = count( $quizzes );
	$total_duration     = \ST\Lms\count_duration( array_merge( $lessons, $quizzes ) );
	$total_duration_str = \ST\Lms\seconds_to_decimal_hours( $total_duration );
	$enrol_courses      = get_user_meta( $current_user_id, \ST\Lms\STLMS_ENROL_COURSES, true );
	$is_enrol           = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
	$course_certificate = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_SIGNATURE, true );
	$has_certificate    = isset( $course_certificate['certificate'] ) ? $course_certificate['certificate'] : 0;
	if ( isset( $assessment['evaluation'] ) && 2 === $assessment['evaluation'] ) {
		$passing_grade = isset( $last_quiz['settings']['passing_marks'] ) ? $last_quiz['settings']['passing_marks'] : '0';
	}
	?>
	<?php require_once STLMSTP_ADDONS_TEMPLATEPATH . '/layout-2/sub-header.php'; ?>
	<div class="stlms-course-detail-banner">
		<div class="stlms-container">
			<div class="stlms-banner-info">
				<div class="stlms-tag-wrap">
					<?php foreach ( $course_category as $category ) : ?>
						<span class="stlms-tag primary"><?php echo esc_html( $category ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php the_title( '<h1 class="stlms-h1">', '</h1>' ); ?>
				<div class="stlms-course-item__meta">
					<ul>
						<li>
							<svg width="20" height="20">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
								</use>
							</svg>
							<?php
							if ( $total_lessons > 1 ) {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Lessons', 'skilltriks-theme-pack' ), $total_lessons ) );
							} else {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Lesson', 'skilltriks-theme-pack' ), $total_lessons ) );
							}
							?>
						</li>
						<li>
							<svg width="20" height="20">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#quiz"">
								</use>
							</svg>
							<?php
							if ( $total_quizzes > 1 ) {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Quizzes', 'skilltriks-theme-pack' ), $total_quizzes ) );
							} else {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Quiz', 'skilltriks-theme-pack' ), $total_quizzes ) );
							}
							?>
						</li>
						<li>
							<svg width="20" height="20">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#clock">
								</use>
							</svg>
							<?php
							if ( ! empty( $total_duration_str ) ) {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%s Hours', 'skilltriks-theme-pack' ), $total_duration_str ) );
							} else {
								esc_html_e( 'Lifetime', 'skilltriks-theme-pack' );
							}
							?>
						</li>
					</ul>
				</div>
			</div>
			<div class="stlms-banner-media">
			<?php
			if ( has_post_thumbnail() ) :
				the_post_thumbnail();
			else :
				?>
				<img fetchpriority="high" decoding="async" src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
			<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="stlms-course-detail-wrap">
		<div class="stlms-container">
			<div class="stlms-course-detail-column">
				<div class="stlms-course-left">
					<?php
					$content            = get_the_content();
					$course_information = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_INFORMATION, true );
					$requirements       = isset( $course_information['requirement'] ) ? $course_information['requirement'] : '';
					$what_you_learn     = isset( $course_information['what_you_learn'] ) ? $course_information['what_you_learn'] : '';
					$skills_gain        = isset( $course_information['skills_you_gain'] ) ? $course_information['skills_you_gain'] : '';
					$course_includes    = isset( $course_information['course_includes'] ) ? $course_information['course_includes'] : '';
					$faq_questions      = isset( $course_information['faq_question'] ) ? $course_information['faq_question'] : '';
					$faq_answers        = isset( $course_information['faq_answer'] ) ? $course_information['faq_answer'] : '';
					$first_curriculum   = reset( $curriculums_list );
					$has_curriculum     = isset( $first_curriculum['items'] ) && count( $first_curriculum['items'] );
					?>
					<div class="stlms-course-detail-nav">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<?php if ( $content || $requirements || $what_you_learn || $skills_gain ) : ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link stlms-p-base" id="about-course-tab" onclick="openTab(event, 'about-course')" type="button" role="tab" aria-controls="about-course" aria-selected="true">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#course-book">
											</use>
										</svg>
										<?php esc_html_e( 'About Course', 'skilltriks-theme-pack' ); ?>
									</button>
								</li>
							<?php endif; ?>
							<?php if ( $has_curriculum ) : ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link stlms-p-base" id="curriculum-tab" onclick="openTab(event, 'curriculum')" type="button" role="tab" aria-controls="curriculum" aria-selected="false">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#curriculum">
											</use>
										</svg>
										<?php esc_html_e( 'Course Curriculum', 'skilltriks-theme-pack' ); ?>
									</button>
								</li>
							<?php endif; ?>
							<?php if ( $faq_questions && $faq_answers ) : ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link stlms-p-base" id="faq-tab" onclick="openTab(event, 'faq')" type="button" role="tab" aria-controls="faq" aria-selected="false">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#faq">
											</use>
										</svg>
										<?php esc_html_e( 'FAQ', 'skilltriks-theme-pack' ); ?>
									</button>
								</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane" id="about-course" role="tabpanel" aria-labelledby="<?php esc_attr_e( 'about-course-tab', 'skilltriks-theme-pack' ); ?>" tabindex="0">
							<div class="tab-content-wrap">
								<?php if ( $content ) : ?>
									<div class="stlms-course-requirement-box">
										<h3 class="stlms-h4"><?php esc_html_e( 'About Course', 'skilltriks-theme-pack' ); ?></h3>
										<div class="stlms-quiz-content">
											<?php echo wp_kses_post( wpautop( $content ) ); ?>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( $requirements ) : ?>
									<div class="stlms-course-requirement-box">
										<h3 class="stlms-h4"><?php esc_html_e( 'Course Requirement', 'skilltriks-theme-pack' ); ?></h3>
										<ul class="stlms-course-requirement-check">
											<?php foreach ( $requirements as $requirement ) : ?>
												<li><?php echo esc_html( $requirement ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<?php if ( $what_you_learn ) : ?>
									<div class="stlms-course-requirement-box">
										<h3 class="stlms-h4"><?php esc_html_e( 'What We Learn', 'skilltriks-theme-pack' ); ?></h3>
										<ul class="stlms-course-requirement-check">
											<?php foreach ( $what_you_learn as $learn ) : ?>
												<li><?php echo esc_html( $learn ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<?php if ( $skills_gain ) : ?>
									<div class="stlms-course-requirement-box">
										<h3 class="stlms-h4"><?php esc_html_e( 'Skills you Gain', 'skilltriks-theme-pack' ); ?></h3>
										<ul class="stlms-course-requirement-check">
											<?php foreach ( $skills_gain as $skill ) : ?>
												<li><?php echo esc_html( $skill ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<?php if ( $course_includes ) : ?>
									<div class="stlms-course-requirement-box">
										<h3 class="stlms-h4"><?php esc_html_e( 'Course Includes', 'skilltriks-theme-pack' ); ?></h3>
										<ul class="stlms-course-requirement-check">
											<?php foreach ( $course_includes as $include ) : ?>
												<li><?php echo esc_html( $include ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php
						$course_completed = false;
						$first_curriculum = reset( $curriculums_list );
						$items            = isset( $first_curriculum['items'] ) ? $first_curriculum['items'] : array();
						$first_item       = reset( $items );
						$section_id       = 1;
						$item_id          = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
						$course_link      = get_the_permalink();
						$button_text      = esc_html__( 'Enrol Now', 'skilltriks-theme-pack' );
						$extra_class      = '';
						$meta_key         = sprintf( \ST\Lms\STLMS_COURSE_STATUS, $course_id );
						$button_text      = $is_enrol ? esc_html__( 'Start Learning', 'skilltriks-theme-pack' ) : $button_text;
						$current_status   = get_user_meta( $current_user_id, $meta_key, true );
						if ( ! empty( $current_status ) ) {
							$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
							$current_status  = explode( '_', $current_status );
							$section_id      = (int) reset( $current_status );
							$item_id         = (int) end( $current_status );
							$button_text     = esc_html__( 'Continue Learning', 'skilltriks-theme-pack' );
							$extra_class     = ' secondary';
							$last_curriculum = end( $curriculums_list );
							$items           = isset( $last_curriculum['items'] ) ? $last_curriculum['items'] : array();
							$last_item       = end( $items );
							$last_item_id    = isset( $last_item['item_id'] ) ? $last_item['item_id'] : 0;
							$last_section_id = count( $curriculums_list );
							if ( $last_section_id === $section_id && $last_item_id === $item_id ) {
								$restart_course = \ST\Lms\restart_course( $course_id );
								if ( $restart_course ) {
									$section_id       = 1;
									$item_id          = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
									$button_text      = esc_html__( 'Restart Course', 'skilltriks-theme-pack' );
									$extra_class      = '';
									$course_completed = true;
								}
							}
						}
						$curriculum_type = get_post_type( $item_id );
						$curriculum_type = str_replace( 'stlms_', '', $curriculum_type );
						$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_link ), $section_id, $curriculum_type, $item_id );

						if ( $has_curriculum ) :
							?>
							<div class="tab-pane" id="curriculum" role="tabpanel" aria-labelledby="<?php esc_attr_e( 'curriculum-tab', 'skilltriks-theme-pack' ); ?>" tabindex="0">
								<div class="stlms-accordion-course-content">
									<div class="stlms-accordion">
									<?php
									$current_section_id = ! empty( $current_status ) ? (int) reset( $current_status ) : 0;
									$current_item_id    = ! empty( $current_status ) ? (int) end( $current_status ) : 0;
									$completed          = true;
									$inactive           = false;

									foreach ( $curriculums_list as $item_key => $curriculums ) :
										$current_curriculum = false;
										$items              = ! empty( $curriculums['items'] ) ? $curriculums['items'] : array();
										$total_duration     = \ST\Lms\count_duration( $items );
										$duration_str       = \ST\Lms\seconds_to_hours_str( $total_duration );
										$section_desc       = ! empty( $curriculums['section_desc'] ) ? $curriculums['section_desc'] : '';
										if ( ++$item_key === $current_section_id ) {
											$current_curriculum = true;
										}
										if ( empty( $current_section_id ) && 1 === $item_key ) {
											$current_curriculum = true;
										}
										?>
										<div class="stlms-accordion-item" <?php echo $current_curriculum && ! $course_completed ? esc_attr( 'data-expanded=true' ) : ''; ?>>
											<div class="stlms-accordion-header">
												<div class="stlms-lesson-title">
													<div class="stlms-lesson-name">
														<div class="name"><?php echo (int) $item_key; ?>. <?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
														<?php if ( ! empty( $duration_str ) ) : ?>
															<div class="info">
																<span><?php echo esc_html( $duration_str ); ?></span>
															</div>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="stlms-accordion-collapse">
												<?php if ( $section_desc ) : ?>
													<div class="stlms-accordion-note">
														<?php echo esc_html( $section_desc ); ?>
													</div>
												<?php endif; ?>
												<div class="stlms-lesson-list">
													<ul>
													<?php
													foreach ( $items as $key => $item ) :
														++$key;
														$media_type  = 'quiz-2';
														$item_id     = isset( $item['item_id'] ) ? $item['item_id'] : 0;
														$in_progress = false;
														if ( \ST\Lms\STLMS_LESSON_CPT === get_post_type( $item_id ) ) {
															$media      = get_post_meta( $item_id, \ST\Lms\META_KEY_LESSON_MEDIA, true );
															$media_type = ! empty( $media['media_type'] ) ? $media['media_type'] : '';
															$media_type = 'text' === $media_type ? 'file-text' : $media_type;
															$settings   = get_post_meta( $item_id, \ST\Lms\META_KEY_LESSON_SETTINGS, true );
														} else {
															$settings = get_post_meta( $item_id, \ST\Lms\META_KEY_QUIZ_SETTINGS, true );
														}
														$duration      = isset( $settings['duration'] ) ? (int) $settings['duration'] : '';
														$duration_type = isset( $settings['duration_type'] ) ? $settings['duration_type'] : '';

														if ( empty( $current_item_id ) ) {
															$inactive = true;
														}

														if ( $course_completed ) {
															$completed = true;
														} else {
															$in_progress = $current_section_id === $item_key && $current_item_id === $item_id;
															if ( $in_progress ) {
																$completed = false;
																$inactive  = true;
															}
														}
														?>
														<li>
															<label class="<?php echo esc_attr( $in_progress ? 'in-progress' : '' ) . esc_attr( $completed ? ' completed' : '' ); ?>">
																<input type="checkbox" class="stlms-check" <?php echo $in_progress || $completed ? esc_attr( 'checked' ) : ''; ?>>
																<span class="stlms-lesson-class">
																	<span class="class-type">
																		<svg class="icon" width="16" height="16">
																			<use
																				xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#<?php echo esc_html( $media_type ); ?>">
																			</use>
																		</svg>
																	</span>
																	<span class="class-name"><span><?php echo esc_html( sprintf( '%d.%d.', $item_key, $key ) ); ?></span> <?php echo esc_html( get_the_title( $item_id ) ); ?></span>
																	<span class="class-time-info">
																		<span class="class-time">
																		<?php
																		if ( ! empty( $duration ) ) {
																			$duration_type .= $duration > 1 ? 's' : '';
																			echo esc_html( sprintf( '%d %s', (int) $duration, ucfirst( $duration_type ) ) );
																		} else {
																			esc_html_e( 'No duration', 'skilltriks-theme-pack' );
																		}
																		?>
																		</span>
																		<?php if ( ( $in_progress && ! $course_completed ) || ( 0 === $current_section_id && 1 === $item_key && 1 === $key && $is_enrol ) ) : ?>
																			<a href="<?php echo esc_url( $course_link ); ?>" class="stlms-btn small"><?php esc_html_e( 'Continue', 'skilltriks-theme-pack' ); ?></a>
																		<?php elseif ( $inactive && ! $course_completed ) : ?>
																			<svg class="lock-icon" width="16" height="16">
																				<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#lock">
																				</use>
																			</svg>
																		<?php endif; ?>
																	</span>
																</span>
															</label>
														</li>
													<?php endforeach; ?>
													</ul>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<?php if ( $faq_questions && $faq_answers ) : ?>
							<div class="tab-pane" id="faq" role="tabpanel" aria-labelledby="<?php esc_attr_e( 'faq-tab', 'skilltriks-theme-pack' ); ?>" tabindex="0">
								<div class="stlms-accordion-faq">
									<div class="stlms-accordion">
									<?php
									foreach ( $faq_questions as $key => $faq_question ) :
										if ( ! empty( $faq_answers[ $key ] ) ) :
											?>
											<div class="stlms-accordion-item" <?php echo 0 === $key ? 'data-expanded="true"' : ''; ?>>
												<div class="stlms-accordion-header">
													<?php echo esc_html( $faq_question ); ?>
												</div>
												<div class="stlms-accordion-collapse">
													<div class="stlms-quiz-content">
														<p><?php echo esc_html( $faq_answers[ $key ] ); ?></p>
													</div>
												</div>
											</div>
											<?php
										endif;
									endforeach;
									?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="stlms-course-right">
					<div class="stlms-course-info-box">
						<h3 class="title stlms-h4"><?php esc_html_e( 'Course Includes', 'skilltriks-theme-pack' ); ?></h3>
						<ul class="stlms-course-include">
							<li>
								<?php esc_html_e( 'Video Total Duration', 'skilltriks-theme-pack' ); ?>
								<span class="stlms-tag secondary">
									<?php
									if ( ! empty( $total_duration_str ) ) {
                                        // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
										echo esc_html( sprintf( __( '%s Hours', 'skilltriks-theme-pack' ), $total_duration_str ) );
									} else {
										esc_html_e( 'Lifetime', 'skilltriks-theme-pack' );
									}
									?>
								</span>
							</li>
							<li>
							<?php
							echo wp_kses(
								sprintf(
									// Translators: %d total number of lesson.
									__( 'Lesson <span class="stlms-tag secondary">%d</span>', 'skilltriks-theme-pack' ),
									$total_lessons
								),
								array(
									'span' => array(
										'class' => true,
									),
								)
							);
							?>
							</li>
							<li>
							<?php
							echo wp_kses(
								sprintf(
									// Translators: %d total number of quiz.
									__( 'Quiz<span class="stlms-tag secondary">%d</span>', 'skilltriks-theme-pack' ),
									$total_quizzes
								),
								array(
									'span' => array(
										'class' => true,
									),
								)
							);
							?>
							</li>
							<?php
							if ( $level ) {
								echo wp_kses(
									sprintf(
										// Translators: %s skill level.
										'<li>' . __( 'Skill Level<span class="stlms-tag secondary">%s</span>', 'skilltriks-theme-pack' ) . '</li>',
										$level
									),
									array(
										'li'   => array(),
										'span' => array(
											'class' => true,
										),
									)
								);
							}
							?>
							<li>
								<?php esc_html_e( 'Certificate Of Completion', 'skilltriks-theme-pack' ); ?>
								<span class="stlms-tag secondary"><?php $has_certificate ? esc_html_e( 'Yes', 'skilltriks-theme-pack' ) : esc_html_e( 'No', 'skilltriks-theme-pack' ); ?></span>
							</li>
							<li>
							<?php
							$passing_text = isset( $assessment['evaluation'] ) && 2 === $assessment['evaluation'] ? 'Marks' : 'Grade';
							echo wp_kses(
								sprintf(
									// Translators: %s passing grade.
									__( 'Passing %1$s<span class="stlms-tag secondary">%2$s</span>', 'skilltriks-theme-pack' ),
									esc_html( $passing_text ),
									esc_html( $passing_grade )
								),
								array(
									'span' => array(
										'class' => true,
									),
								)
							);
							?>
							</li>
						</ul>
						<div class="stlms-btn-wrap">
							<?php if ( $is_enrol ) : ?>
								<div class="stlms-progress__bar">
									<div class="stlms-progress__bar-inner" style="width: <?php echo esc_attr( $course_progress ); ?>"></div>
								</div>
							<?php endif; ?>
							<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" class="stlms-btn stlms-btn-block<?php echo esc_attr( $extra_class ); ?>" data-course="<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="stlms-loader"></i></a>
						</div>
						<?php if ( $has_certificate && '100%' === $course_progress ) : ?>
							<div class="stlms-btn-wrap">
								<a href="javascript:;" id="download-certificate" class="stlms-btn stlms-btn-block secondary" data-course="<?php echo esc_attr( $course_id ); ?>"><?php esc_html_e( 'Download Certificate', 'skilltriks-theme-pack' ); ?></a>
							</div>
						<?php endif; ?>
						<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
							<a href="javascript:void(0);" data-fancybox data-src="#assign-course" class="stlms-btn outline stlms-btn-block"><?php esc_html_e( 'Assign Course', 'skilltriks-theme-pack' ); ?></a>
						<?php endif; ?>
					</div>
					<?php
					$parent_terms_id = array();
					foreach ( $category_id as $cid ) {
						$parent_id = wp_get_term_taxonomy_parent_id( $cid, \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
						if ( $parent_id ) {
							$parent_terms_id[] = $parent_id;
						}
					}
					$terms_id       = array_merge( $parent_terms_id, $category_id );
					$tax_query_args = array(
						'taxonomy' => \ST\Lms\STLMS_COURSE_CATEGORY_TAX,
						'field'    => 'term_id',
						'terms'    => $terms_id,
					);

					$courses_arg = array(
						'post_type'    => \ST\Lms\STLMS_COURSE_CPT,
						'post_status'  => 'publish',
						// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
						'post__not_in' => array( $course_id ),
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						'tax_query'    => array(
							$tax_query_args,
						),
					);
					$courses = new WP_Query( $courses_arg );
					if ( $courses->have_posts() ) :
						?>
						<div class="stlms-similar-course">
							<h3 class="title stlms-h4"><?php esc_html_e( 'Similar Courses', 'skilltriks-theme-pack' ); ?></h3>
							<ul>
							<?php
							while ( $courses->have_posts() ) :
								$courses->the_post();
								$get_terms        = get_the_terms( get_the_ID(), \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
								$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
								$curriculums      = get_post_meta( get_the_ID(), \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
								$total_lessons    = 0;
								$total_quizzes    = 0;
								$course_view_link = get_the_permalink();
								?>
								<li>
									<a href="<?php echo esc_url( $course_view_link ); ?>" class="stlms-similar-course-item">
										<div class="stlms-similar-course-media">
											<?php
											if ( has_post_thumbnail() ) :
												the_post_thumbnail();
											else :
												?>
												<img fetchpriority="high" decoding="async" src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
											<?php endif; ?>
										</div>
										<div class="stlms-similar-course-info">
											<?php the_title( ' <div class="title stlms-h6">', '</div>' ); ?>
											<span class="stlms-tag primary-light">
												<?php echo esc_html( $terms_name ); ?>
											</span>
										</div>
									</a>
								</li>
							<?php endwhile; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- assign popup -->
<?php
$stlms_users    = get_users(
	array(
		'fields'       => array( 'ID', 'display_name' ),
		'role__not_in' => array( 'Administrator' ),
		'exclude'      => get_current_user_id(),
	)
);
$assigned_users = get_post_meta( $course_id, ST\LMS\META_KEY_COURSE_ASSIGNED, true ) ? get_post_meta( $course_id, ST\LMS\META_KEY_COURSE_ASSIGNED, true ) : array();
?>
<div id="assign-course" class="stlms-dialog" data-course="<?php echo esc_attr( $course_id ); ?>" style="display: none;">
	<form class="stlms-assign-course__box">
		<div class="stlms-dialog__header">
			<div class="stlms-dialog__title">
				<?php esc_html_e( 'Assign This Course', 'skilltriks-theme-pack' ); ?>
			</div>
			<button class="stlms-dialog__close" data-fancybox-close>
				<svg width="30" height="30">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
				</svg>
			</button>
		</div>
		<div class="stlms-dialog__content-box">
			<div class="stlms-dialog__content">
				<div class="stlms-dialog__content-title">
					<p>
						<span>
							<?php esc_html_e( 'Choose people whom you wish to assign this course', 'skilltriks-theme-pack' ); ?>
						</span>
					</p>
				</div>
			</div>
			<div class="stlms-dialog__content">
				<div class="stlms-form-group">
					<label class="stlms-select-search" for="employee-list">
						<?php esc_html_e( 'Choose Employee(s)', 'skilltriks-theme-pack' ); ?>
						<select multiple data-placeholder="John Doe" class="stlms-select2-multi js-states form-control" id="employee-list">
							<?php foreach ( $stlms_users as $users ) : ?>
								<option value="<?php echo esc_attr( base64_encode( $users->ID ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode ?>" <?php echo in_array( (int) $users->ID, $assigned_users, true ) ? 'disabled' : ''; ?>><?php echo esc_html( $users->display_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
			</div>
			<div class="stlms-dialog__content">
				<div class="stlms-switch-wrap">
					<label>
						<input type="checkbox" class="stlms-check"><?php esc_html_e( 'Common completion date for all?', 'skilltriks-theme-pack' ); ?>
					</label>
				</div>
			</div>
			<div class="stlms-dialog__content" id="common-date">
				<div class="stlms-form-group">
					<label for="completion-date"><?php esc_html_e( 'Common completion date for all', 'skilltriks-theme-pack' ); ?></label>
					<input type="date" id="completion-date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" />
				</div>
			</div>
			<div class="stlms-dialog__content" id="unique-date">
				<div class="stlms-form-col">
					<div class="stlms-form-group">
						<label for="completion-date"><?php esc_html_e( 'Common completion date for John Doe', 'skilltriks-theme-pack' ); ?></label>
						<input type="date" id="completion-date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="stlms-dialog__footer">
			<div class="stlms-dialog__cta center">
				<button class="stlms-btn" data-fancybox-close id="showSnackbar"><?php esc_html_e( 'Assign Course', 'skilltriks-theme-pack' ); ?></button>
			</div>
		</div>
	</form>
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