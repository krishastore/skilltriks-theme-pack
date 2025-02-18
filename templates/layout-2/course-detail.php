<?php
/**
 * Template: Course Detail Page
 *
 * @package BD\Lms
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
$layout             = bdlms_addons_template();
?>

<div class="bdlms-wrap">
	<?php
	$course_category    = get_the_terms( get_the_ID(), \BD\Lms\BDLMS_COURSE_CATEGORY_TAX );
	$category_id        = wp_list_pluck( $course_category, 'term_id' );
	$course_category    = wp_list_pluck( $course_category, 'name' );
	$level              = get_the_terms( get_the_ID(), \BD\Lms\BDLMS_COURSE_TAXONOMY_TAG );
	$level              = wp_list_pluck( $level, 'name' );
	$level              = end( $level );
	$assessment         = get_post_meta( $course_id, \BD\Lms\META_KEY_COURSE_ASSESSMENT, true );
	$passing_grade      = isset( $assessment['passing_grade'] ) ? $assessment['passing_grade'] . '%' : '0%';
	$curriculums        = \BD\Lms\merge_curriculum_items( $curriculums_list );
	$curriculums        = array_keys( $curriculums );
	$course_progress    = \BD\Lms\calculate_course_progress( $course_id, $curriculums ) . '%';
	$lessons            = \BD\Lms\get_curriculums( $curriculums_list, \BD\Lms\BDLMS_LESSON_CPT );
	$total_lessons      = count( $lessons );
	$quizzes            = \BD\Lms\get_curriculums( $curriculums_list, \BD\Lms\BDLMS_QUIZ_CPT );
	$total_quizzes      = count( $quizzes );
	$total_duration     = \BD\Lms\count_duration( array_merge( $lessons, $quizzes ) );
	$total_duration_str = \BD\Lms\seconds_to_decimal_hours( $total_duration );
	$enrol_courses      = get_user_meta( $current_user_id, \BD\Lms\BDLMS_ENROL_COURSES, true );
	$is_enrol           = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
	$course_certificate = get_post_meta( $course_id, \BD\Lms\META_KEY_COURSE_SIGNATURE, true );
	$has_certificate    = isset( $course_certificate['certificate'] ) ? $course_certificate['certificate'] : 0;
	?>
	<div class="bdlms-course-detail-banner">
		<div class="bdlms-container">
			<div class="bdlms-banner-info">
				<div class="bdlms-tag-wrap">
					<?php foreach ( $course_category as $category ) : ?>
						<span class="bdlms-tag primary"><?php echo esc_html( $category ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php the_title( '<h1 class="bdlms-h1">', '</h1>' ); ?>
				<div class="bdlms-course-item__meta">
					<ul>
						<li>
							<svg width="20" height="20">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
								</use>
							</svg>
							<?php
							if ( $total_lessons > 1 ) {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Lessons', 'bluedolphin-lms' ), $total_lessons ) );
							} else {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Lesson', 'bluedolphin-lms' ), $total_lessons ) );
							}
							?>
						</li>
						<li>
							<svg width="20" height="20">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#quiz"">
								</use>
							</svg>
							<?php
							if ( $total_quizzes > 1 ) {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Quizzes', 'bluedolphin-lms' ), $total_quizzes ) );
							} else {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%d Quiz', 'bluedolphin-lms' ), $total_quizzes ) );
							}
							?>
						</li>
						<li>
							<svg width="20" height="20">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#clock">
								</use>
							</svg>
							<?php
							if ( ! empty( $total_duration_str ) ) {
                                // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( '%s Hours', 'bluedolphin-lms' ), $total_duration_str ) );
							} else {
								esc_html_e( 'Lifetime', 'bluedolphin-lms' );
							}
							?>
						</li>
					</ul>
				</div>
			</div>
			<div class="bdlms-banner-media">
			<?php
			if ( has_post_thumbnail() ) :
				the_post_thumbnail();
			else :
				?>
				<img fetchpriority="high" decoding="async" src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
			<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="bdlms-course-detail-wrap">
		<div class="bdlms-container">
			<div class="bdlms-course-detail-column">
				<div class="bdlms-course-left">
					<?php
					$content            = get_the_content();
					$course_information = get_post_meta( $course_id, \BD\Lms\META_KEY_COURSE_INFORMATION, true );
					$requirements       = isset( $course_information['requirement'] ) ? $course_information['requirement'] : '';
					$what_you_learn     = isset( $course_information['what_you_learn'] ) ? $course_information['what_you_learn'] : '';
					$skills_gain        = isset( $course_information['skills_you_gain'] ) ? $course_information['skills_you_gain'] : '';
					$course_includes    = isset( $course_information['course_includes'] ) ? $course_information['course_includes'] : '';
					$faq_questions      = isset( $course_information['faq_question'] ) ? $course_information['faq_question'] : '';
					$faq_answers        = isset( $course_information['faq_answer'] ) ? $course_information['faq_answer'] : '';
					$first_curriculum   = reset( $curriculums_list );
					$has_curriculum     = isset( $first_curriculum['items'] ) && count( $first_curriculum['items'] );
					?>
					<div class="bdlms-course-detail-nav">
						<ul class="nav nav-tabs" id="myTab" role="tablist">
							<?php if ( $content || $requirements || $what_you_learn || $skills_gain ) : ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link goto-section bdlms-p-base" id="about-course-tab" onclick="openTab(event, 'about-course')" type="button" role="tab" aria-controls="about-course" aria-selected="true">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#course-book">
											</use>
										</svg>
										<?php esc_html_e( 'About Course', 'bluedolphin-lms' ); ?>
									</button>
								</li>
							<?php endif; ?>
							<?php if ( $has_curriculum ) : ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link goto-section bdlms-p-base" id="curriculum-tab" onclick="openTab(event, 'curriculum')" type="button" role="tab" aria-controls="curriculum" aria-selected="false">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#curriculum">
											</use>
										</svg>
										<?php esc_html_e( 'Course Curriculum', 'bluedolphin-lms' ); ?>
									</button>
								</li>
							<?php endif; ?>
							<?php if ( $faq_questions && $faq_answers ) : ?>
								<li class="nav-item" role="presentation">
									<button class="nav-link goto-section bdlms-p-base" id="faq-tab" onclick="openTab(event, 'faq')" type="button" role="tab" aria-controls="faq" aria-selected="false">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#faq">
											</use>
										</svg>
										<?php esc_html_e( 'FAQ', 'bluedolphin-lms' ); ?>
									</button>
								</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="tab-content">
						<div class="tab-pane" id="about-course" role="tabpanel" aria-labelledby="<?php esc_attr_e( 'about-course-tab', 'bluedolphin-lms' ); ?>" tabindex="0">
							<div class="tab-content-wrap">
								<?php if ( $content ) : ?>
									<div class="bdlms-course-requirement-box">
										<h3 class="bdlms-h4"><?php esc_html_e( 'About Course', 'bluedolphin-lms' ); ?></h3>
										<div class="bdlms-quiz-content">
											<?php echo wp_kses_post( wpautop( $content ) ); ?>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( $requirements ) : ?>
									<div class="bdlms-course-requirement-box">
										<h3 class="bdlms-h4"><?php esc_html_e( 'Course Requirement', 'bluedolphin-lms' ); ?></h3>
										<ul class="bdlms-course-requirement-check">
											<?php foreach ( $requirements as $requirement ) : ?>
												<li><?php echo esc_html( $requirement ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<?php if ( $what_you_learn ) : ?>
									<div class="bdlms-course-requirement-box">
										<h3 class="bdlms-h4"><?php esc_html_e( 'What We Learn', 'bluedolphin-lms' ); ?></h3>
										<ul class="bdlms-course-requirement-check">
											<?php foreach ( $what_you_learn as $learn ) : ?>
												<li><?php echo esc_html( $learn ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<?php if ( $skills_gain ) : ?>
									<div class="bdlms-course-requirement-box">
										<h3 class="bdlms-h4"><?php esc_html_e( 'Skills you Gain', 'bluedolphin-lms' ); ?></h3>
										<ul class="bdlms-course-requirement-check">
											<?php foreach ( $skills_gain as $skill ) : ?>
												<li><?php echo esc_html( $skill ); ?></li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
								<?php if ( $course_includes ) : ?>
									<div class="bdlms-course-requirement-box">
										<h3 class="bdlms-h4"><?php esc_html_e( 'Course Includes', 'bluedolphin-lms' ); ?></h3>
										<ul class="bdlms-course-requirement-check">
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
						$button_text      = esc_html__( 'Enrol Now', 'bluedolphin-lms' );
						$extra_class      = '';
						$meta_key         = sprintf( \BD\Lms\BDLMS_COURSE_STATUS, $course_id );
						$button_text      = $is_enrol ? esc_html__( 'Start Learning', 'bluedolphin-lms' ) : $button_text;
						$current_status   = get_user_meta( $current_user_id, $meta_key, true );
						if ( ! empty( $current_status ) ) {
							$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
							$current_status  = explode( '_', $current_status );
							$section_id      = (int) reset( $current_status );
							$item_id         = (int) end( $current_status );
							$button_text     = esc_html__( 'Continue Learning', 'bluedolphin-lms' );
							$extra_class     = ' secondary';
							$last_curriculum = end( $curriculums_list );
							$items           = isset( $last_curriculum['items'] ) ? $last_curriculum['items'] : array();
							$last_item       = end( $items );
							$last_item_id    = isset( $last_item['item_id'] ) ? $last_item['item_id'] : 0;
							$last_section_id = count( $curriculums_list );
							if ( $last_section_id === $section_id && $last_item_id === $item_id ) {
								$restart_course = \BD\Lms\restart_course( $course_id );
								if ( $restart_course ) {
									$section_id       = 1;
									$item_id          = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
									$button_text      = esc_html__( 'Restart Course', 'bluedolphin-lms' );
									$extra_class      = '';
									$course_completed = true;
								}
							}
						}
						$curriculum_type = get_post_type( $item_id );
						$curriculum_type = str_replace( 'bdlms_', '', $curriculum_type );
						$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_link ), $section_id, $curriculum_type, $item_id );

						if ( $has_curriculum ) :
							?>
							<div class="tab-pane" id="curriculum" role="tabpanel" aria-labelledby="<?php esc_attr_e( 'curriculum-tab', 'bluedolphin-lms' ); ?>" tabindex="0">
								<div class="bdlms-accordion-course-content">
									<div class="bdlms-accordion">
									<?php
									$current_section_id = ! empty( $current_status ) ? (int) reset( $current_status ) : 0;
									$current_item_id    = ! empty( $current_status ) ? (int) end( $current_status ) : 0;
									$completed          = true;
									$inactive           = false;

									foreach ( $curriculums_list as $item_key => $curriculums ) :
										$current_curriculum = false;
										$items              = ! empty( $curriculums['items'] ) ? $curriculums['items'] : array();
										$total_duration     = \BD\Lms\count_duration( $items );
										$duration_str       = \BD\Lms\seconds_to_hours_str( $total_duration );
										$section_desc       = ! empty( $curriculums['section_desc'] ) ? $curriculums['section_desc'] : '';
										if ( ++$item_key === $current_section_id ) {
											$current_curriculum = true;
										}
										if ( empty( $current_section_id ) && 1 === $item_key ) {
											$current_curriculum = true;
										}
										?>
										<div class="bdlms-accordion-item" <?php echo $current_curriculum && ! $course_completed ? esc_attr( 'data-expanded=true' ) : ''; ?>>
											<div class="bdlms-accordion-header">
												<div class="bdlms-lesson-title">
													<div class="bdlms-lesson-name">
														<div class="name"><?php echo (int) $item_key; ?>. <?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
														<?php if ( ! empty( $duration_str ) ) : ?>
															<div class="info">
																<span><?php echo esc_html( $duration_str ); ?></span>
															</div>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="bdlms-accordion-collapse">
												<?php if ( $section_desc ) : ?>
													<div class="bdlms-accordion-note">
														<?php echo esc_html( $section_desc ); ?>
													</div>
												<?php endif; ?>
												<div class="bdlms-lesson-list">
													<ul>
													<?php
													foreach ( $items as $key => $item ) :
														++$key;
														$media_type  = 'quiz-2';
														$item_id     = isset( $item['item_id'] ) ? $item['item_id'] : 0;
														$in_progress = false;
														if ( \BD\Lms\BDLMS_LESSON_CPT === get_post_type( $item_id ) ) {
															$media      = get_post_meta( $item_id, \BD\Lms\META_KEY_LESSON_MEDIA, true );
															$media_type = ! empty( $media['media_type'] ) ? $media['media_type'] : '';
															$media_type = 'text' === $media_type ? 'file-text' : $media_type;
															$settings   = get_post_meta( $item_id, \BD\Lms\META_KEY_LESSON_SETTINGS, true );
														} else {
															$settings = get_post_meta( $item_id, \BD\Lms\META_KEY_QUIZ_SETTINGS, true );
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
																<input type="checkbox" class="bdlms-check" <?php echo $in_progress || $completed ? esc_attr( 'checked' ) : ''; ?>>
																<span class="bdlms-lesson-class">
																	<span class="class-type">
																		<svg class="icon" width="16" height="16">
																			<use
																				xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#<?php echo esc_html( $media_type ); ?>">
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
																			esc_html_e( 'No duration', 'bluedolphin-lms' );
																		}
																		?>
																		</span>
																		<?php if ( ( $in_progress && ! $course_completed ) || ( 0 === $current_section_id && 1 === $item_key && 1 === $key ) ) : ?>
																			<a href="<?php echo esc_url( $course_link ); ?>" class="bdlms-btn small"><?php esc_html_e( 'Continue', 'bluedolphin-lms' ); ?></a>
																		<?php elseif ( $inactive && ! $course_completed ) : ?>
																			<svg class="lock-icon" width="16" height="16">
																				<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#lock">
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
							<div class="tab-pane" id="faq" role="tabpanel" aria-labelledby="<?php esc_attr_e( 'faq-tab', 'bluedolphin-lms' ); ?>" tabindex="0">
								<div class="bdlms-accordion-faq">
									<div class="bdlms-accordion">
									<?php
									foreach ( $faq_questions as $key => $faq_question ) :
										if ( ! empty( $faq_answers[ $key ] ) ) :
											?>
											<div class="bdlms-accordion-item" data-expanded="true">
												<div class="bdlms-accordion-header">
													<?php echo esc_html( $faq_question ); ?>
												</div>
												<div class="bdlms-accordion-collapse">
													<div class="bdlms-quiz-content">
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
				<div class="bdlms-course-right">
					<div class="bdlms-course-info-box">
						<h3 class="title bdlms-h4"><?php esc_html_e( 'Course Includes', 'bluedolphin-lms' ); ?></h3>
						<ul class="bdlms-course-include">
							<li>
								<?php esc_html_e( 'Video Total Duration', 'bluedolphin-lms' ); ?>
								<span class="bdlms-tag secondary">
									<?php
									if ( ! empty( $total_duration_str ) ) {
                                        // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
										echo esc_html( sprintf( __( '%s Hours', 'bluedolphin-lms' ), $total_duration_str ) );
									} else {
										esc_html_e( 'Lifetime', 'bluedolphin-lms' );
									}
									?>
								</span>
							</li>
							<li>
							<?php
							echo wp_kses(
								sprintf(
									// Translators: %d total number of lesson.
									__( 'Lesson <span class="bdlms-tag secondary">%d</span>', 'bluedolphin-lms' ),
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
									__( 'Quiz<span class="bdlms-tag secondary">%d</span>', 'bluedolphin-lms' ),
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
										'<li>' . __( 'Skill Level<span class="bdlms-tag secondary">%s</span>', 'bluedolphin-lms' ) . '</li>',
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
								<?php esc_html_e( 'Certificate Of Completion', 'bluedolphin-lms' ); ?>
								<span class="bdlms-tag secondary"><?php esc_html_e( 'Yes', 'bluedolphin-lms' ); ?></span>
							</li>
							<li>
							<?php
							echo wp_kses(
								sprintf(
									// Translators: %s passing grade.
									__( 'Passing Grade<span class="bdlms-tag secondary">%s</span>', 'bluedolphin-lms' ),
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
						<div class="bdlms-btn-wrap">
							<?php if ( $is_enrol ) : ?>
								<div class="bdlms-progress__bar">
									<div class="bdlms-progress__bar-inner" style="width: <?php echo esc_attr( $course_progress ); ?>"></div>
								</div>
							<?php endif; ?>
							<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" class="bdlms-btn bdlms-btn-block<?php echo esc_attr( $extra_class ); ?>" data-course="<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="bdlms-loader"></i></a>
						</div>
						<?php if ( $has_certificate && '100%' === $course_progress ) : ?>
							<div class="bdlms-btn-wrap">
								<a href="javascript:;" id="download-certificate" class="bdlms-btn bdlms-btn-block secondary" data-course="<?php echo esc_attr( $course_id ); ?>"><?php esc_html_e( 'Download Certificate', 'bluedolphin-lms' ); ?></a>
							</div>
						<?php endif; ?>
					</div>
					<?php
					$parent_terms_id = array();
					foreach ( $category_id as $cid ) {
						$parent_id = wp_get_term_taxonomy_parent_id( $cid, \BD\Lms\BDLMS_COURSE_CATEGORY_TAX );
						if ( $parent_id ) {
							$parent_terms_id[] = $parent_id;
						}
					}
					$terms_id       = array_merge( $parent_terms_id, $category_id );
					$tax_query_args = array(
						'taxonomy' => \BD\Lms\BDLMS_COURSE_CATEGORY_TAX,
						'field'    => 'term_id',
						'terms'    => $terms_id,
					);

					$courses_arg = array(
						'post_type'    => \BD\Lms\BDLMS_COURSE_CPT,
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
						<div class="bdlms-similar-course">
							<h3 class="title bdlms-h4"><?php esc_html_e( 'Similar Courses', 'bluedolphin-lms' ); ?></h3>
							<ul>
							<?php
							while ( $courses->have_posts() ) :
								$courses->the_post();
								$get_terms        = get_the_terms( get_the_ID(), \BD\Lms\BDLMS_COURSE_CATEGORY_TAX );
								$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
								$curriculums      = get_post_meta( get_the_ID(), \BD\Lms\META_KEY_COURSE_CURRICULUM, true );
								$total_lessons    = 0;
								$total_quizzes    = 0;
								$course_view_link = get_the_permalink();
								?>
								<li>
									<a href="<?php echo esc_url( $course_view_link ); ?>" class="bdlms-similar-course-item">
										<div class="bdlms-similar-course-media">
											<?php
											if ( has_post_thumbnail() ) :
												the_post_thumbnail();
											else :
												?>
												<img fetchpriority="high" decoding="async" src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
											<?php endif; ?>
										</div>
										<div class="bdlms-similar-course-info">
											<?php the_title( ' <div class="title bdlms-h6">', '</div>' ); ?>
											<span class="bdlms-tag primary-light">
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