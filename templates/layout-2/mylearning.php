<?php
/**
 * Template: My learning
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$search_keyword = ! empty( $_GET['_s'] ) ? sanitize_text_field( wp_unslash( $_GET['_s'] ) ) : '';
$category       = ! empty( $_GET['category'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['category'] ) ) ) : array();
$category       = array_map( 'absint', $category );
$_orderby       = ! empty( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : 'menu_order';
$progress       = ! empty( $_GET['progress'] ) ? sanitize_text_field( wp_unslash( $_GET['progress'] ) ) : '';
$levels         = ! empty( $_GET['levels'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['levels'] ) ) ) : array();
$levels         = array_map( 'intval', $levels );

$course_args = array(
	'post_type'      => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
	'post_status'    => 'publish',
	'posts_per_page' => -1,
);
$_paged      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
if ( get_query_var( 'page' ) ) {
	$_paged = get_query_var( 'page' );
}
if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) {
	$course_args['paged']          = $_paged;
	$course_args['posts_per_page'] = apply_filters( 'bdlms_courses_list_per_page', get_option( 'posts_per_page' ) );
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$author = ! empty( $_GET['filter_author'] ) ? (int) $_GET['filter_author'] : 0;
if ( $author ) {
	$course_args['author__in'] = array( $author );
}
if ( ! empty( $search_keyword ) ) {
	$course_args['s'] = $search_keyword;
}
if ( in_array( $_orderby, array( 'asc', 'desc' ), true ) ) {
	$course_args['orderby'] = 'title';
	$course_args['order']   = strtoupper( $_orderby );
} elseif ( 'newest' === $_orderby ) {
	$course_args['order'] = 'DESC';
} else {
	$course_args['orderby'] = 'menu_order';
}

if ( ! empty( $category ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX,
		'field'    => 'term_id',
		'terms'    => $category,
		'operator' => 'IN',
	);
}
if ( ! empty( $levels ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG,
		'field'    => 'term_id',
		'terms'    => $levels,
		'operator' => 'IN',
	);
}
$enrol_courses = get_user_meta( get_current_user_id(), \BlueDolphin\Lms\BDLMS_ENROL_COURSES, true );
?>

<div class="bdlms-wrap alignfull">
	<div class="bdlms-inner-banner">
		<div class="bdlms-container">
			<div class="bdlms-banner-content">
				<div class="bdlms-banner-info">
					<h1 class="title bdlms-h1">
						<?php esc_html_e( 'My Learnings', 'bluedolphin-lms' ); ?>
					</h1>
				</div>
				<div class="bdlms-banner-media">
					<img src="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . bdlms_addons_template() ); ?>/images/banner-image2.png" alt="">
				</div>
			</div>
		</div>
	</div>

	<div class="bdlms-container">
		<div class="bdlms-course-view__header">
			<?php
			$courses      = new \WP_Query( $course_args );
			$total_course = $courses->found_posts;

			if ( $courses->have_posts() ) :
				?>
			<div class="bdlms-filtered-item">
				<?php
				echo esc_html(
					sprintf(
						// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment 
						__( 'Showing %1$s course of %2$s courses', 'bluedolphin-lms' ),
						$courses->post_count,
						number_format_i18n( $total_course )
					)
				);
				?>
			</div>
			<?php endif; ?>
			<div class="bdlms-sort-by">
				<form onsubmit="return false;">
					<select>
						<option value=""><?php esc_html_e( 'Sort By', 'bluedolphin-lms' ); ?></option>
						<option value="asc"<?php selected( $_orderby, 'asc' ); ?>><?php esc_html_e( 'Alphabetically (A To Z)', 'bluedolphin-lms' ); ?></option>
						<option value="desc"<?php selected( $_orderby, 'desc' ); ?>><?php esc_html_e( 'Alphabetically (Z To A)', 'bluedolphin-lms' ); ?></option>
						<option value="newest"<?php selected( $_orderby, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bluedolphin-lms' ); ?></option>
					</select>
				</form>
			</div>
			<button class="bdlms-filter-toggle">
				<svg width="24" height="24">
					<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . bdlms_addons_template() ); ?>/images/sprite-front.svg#filters"></use>
				</svg>
			</button>
		</div>
	</div>

	<div class="bdlms-course-list-wrap">
		<div class="bdlms-container">
			<div class="bdlms-course-filter">
				<button class="bdlms-filter-toggle">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . bdlms_addons_template() ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<?php do_action( 'bdlms_before_search_bar' ); ?>
				<div class="bdlms-filter-item">
					<div class="bdlms-filter-title bdlms-h4">
					<?php esc_html_e( 'Search', 'bluedolphin-lms' ); ?>
					</div>
					<div class="bdlms-course-search">
						<form onsubmit="return false;">
							<div class="bdlms-search input-group">
								<input type="text" class="bdlms-form-control" placeholder="<?php esc_attr_e( 'Search Course', 'bluedolphin-lms' ); ?>" value="<?php echo esc_attr( $search_keyword ); ?>">
								<button type="submit" class="bdlms-search-submit">
									<span class="bdlms-search-icon">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . bdlms_addons_template() ); ?>/images/sprite-front.svg#search-icon"></use>
										</svg>
									</span>
								</button>
							</div>
						</form>
					</div>
				</div>
				<?php
					$course_status = \BlueDolphin\Lms\course_statistics();
					$total_course  = 0;
					$max_num_page  = 0;
					$has_course    = false;

				if ( ! empty( $enrol_courses ) ) {
					$course_args['post__in'] = $enrol_courses;
					$has_course              = true;
				}
				if ( ! empty( $progress ) ) {
					if ( ! empty( $course_status[ $progress ] ) ) {
						$course_args['post__in'] = $course_status[ $progress ];
					} else {
						$has_course = false;
					}
				}
				if ( $has_course ) {
					$courses      = new \WP_Query( $course_args );
					$total_course = $courses->found_posts;
					$max_num_page = $courses->max_num_pages;
				}

					$statistics = array(
						'total_courses'      => $course_status['total_course'],
						'course_completed'   => count( $course_status['completed'] ),
						'course_in_progress' => count( $course_status['in_progress'] ),
						'course_not_started' => count( $course_status['not_started'] ),
					);
					?>
				<form method="get" onsubmit="return false;" class="bdlms-filter-form">
					<?php
					$terms_list = array();
					foreach ( $enrol_courses as $course_id ) :
						$_terms = get_the_terms( $course_id, \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
						if ( ! empty( $_terms ) && ! is_wp_error( $_terms ) ) {
							foreach ( $_terms as $_term ) {
								if ( isset( $terms_list[ $_term->term_id ] ) ) {
									++$terms_list[ $_term->term_id ]['count'];
								} else {
									$terms_list[ $_term->term_id ] = array(
										'name'  => $_term->name,
										'count' => 1,
									);
								}
							}
						}
					endforeach;

					?>
					<div class="bdlms-filter-item">
						<div class="bdlms-filter-title bdlms-h4">
							<?php esc_html_e( 'Categories', 'bluedolphin-lms' ); ?>
						</div>
						<div class="bdlms-filter-list">
							<ul>
								<li>
									<div class="bdlms-check-wrap">
										<input type="checkbox" class="bdlms-check" id="category-all">
										<label for="category-all" class="bdlms-check-label">
											<?php esc_html_e( 'All', 'bluedolphin-lms' ); ?>
											<span><?php echo absint( $course_status['total_course'] ); ?></span>
										</label>
									</div>
								</li>
								<?php foreach ( $terms_list as $term_id => $term_details ) : ?>
								<li>
									<div class="bdlms-check-wrap">
										<input type="checkbox" name="category[]" class="bdlms-check" id="bd_course_term_<?php echo (int) $term_id; ?>" value="<?php echo esc_attr( $term_id ); ?>" <?php echo in_array( $term_id, $category, true ) ? 'checked' : ''; ?>>
										<label for="bd_course_term_<?php echo (int) $term_id; ?>" class="bdlms-check-label">
											<?php echo esc_html( $term_details['name'] ); ?>
											<?php if ( isset( $term_details['count'] ) ) : ?>
												<span><?php echo esc_html( $term_details['count'] ); ?></span>
											<?php endif; ?>
										</label>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<?php
						$levels_list = array();
					foreach ( $enrol_courses as $course_id ) :
						$_levels = get_the_terms( $course_id, \BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG );
						if ( ! empty( $_levels ) && ! is_wp_error( $_levels ) ) {
							foreach ( $_levels as $_level ) {
								if ( isset( $levels_list[ $_level->term_id ] ) ) {
									++$levels_list[ $_level->term_id ]['count'];
								} else {
									$levels_list[ $_level->term_id ] = array(
										'name'  => $_level->name,
										'count' => 1,
									);
								}
							}
						}
						endforeach;
					?>
					<div class="bdlms-filter-item">
						<div class="bdlms-filter-title bdlms-h4">
							<?php esc_html_e( 'Skill Level', 'bluedolphin-lms' ); ?>
						</div>
						<div class="bdlms-filter-list">
							<ul>
								<li>
									<div class="bdlms-check-wrap">
										<input type="checkbox" class="bdlms-check" id="bdlms_level_all">
										<label for="bdlms_level_all" class="bdlms-check-label">
											<?php esc_html_e( 'All', 'bluedolphin-lms' ); ?><span><?php echo esc_html( (string) $course_status['total_course'] ); ?></span>
										</label>
									</div>
								</li>
								<?php foreach ( $levels_list as $level_id => $level_details ) : ?>
									<li>
										<div class="bdlms-check-wrap">
											<input type="checkbox" name="levels[]" class="bdlms-check" id="bd_course_level_<?php echo (int) $level_id; ?>" value="<?php echo esc_attr( $level_id ); ?>" <?php echo in_array( $level_id, $levels, true ) ? 'checked' : ''; ?>>
											<label for="bd_course_level_<?php echo (int) $level_id; ?>" class="bdlms-check-label">
												<?php echo esc_html( $level_details['name'] ); ?>
												<span><?php echo esc_html( $level_details['count'] ); ?></span>
											</label>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<input type="hidden" name="_s" value="<?php echo esc_attr( $search_keyword ); ?>">
					<input type="hidden" name="order_by" value="<?php echo esc_attr( $_orderby ); ?>">
				</form>
			</div>
			<div class="bdlms-course-view" id="bdlms_course_view">
				<div class="bdlms-course-view__body">
					<div class="bdlms-statistics">
						<ul>
							<?php
							foreach ( $statistics as $key => $value ) :
								$stat_title = ucwords( str_replace( '_', ' ', $key ) );
								?>
								<li>
									<div class="bdlms-statistics__title"><?php echo esc_html( $stat_title ); ?></div>
									<div class="bdlms-statistics__no bdlms-h2"><?php echo esc_html( $value ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php if ( $has_course && $courses->have_posts() ) : ?>
						<div class="bdlms-course-list">
							<ul>
								<?php
								while ( $courses->have_posts() ) :
									$courses->the_post();

									$course_id        = get_the_ID();
									$get_terms        = get_the_terms( $course_id, \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
									$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
									$curriculums      = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
									$total_lessons    = 0;
									$total_quizzes    = 0;
									$course_view_link = get_the_permalink();
									$course_link      = $course_view_link;
									$button_text      = esc_html__( 'Start Learning', 'bluedolphin-lms' );
									$extra_class      = '';
									$course_progress  = '0%';
									if ( ! empty( $curriculums ) ) {
										$lessons          = \BlueDolphin\Lms\get_curriculums( $curriculums, \BlueDolphin\Lms\BDLMS_LESSON_CPT );
										$total_lessons    = count( $lessons );
										$quizzes          = \BlueDolphin\Lms\get_curriculums( $curriculums, \BlueDolphin\Lms\BDLMS_QUIZ_CPT );
										$total_quizzes    = count( $quizzes );
										$total_duration   = \BlueDolphin\Lms\count_duration( array_merge( $lessons, $quizzes ) );
										$curriculums      = \BlueDolphin\Lms\merge_curriculum_items( $curriculums );
										$curriculums      = array_keys( $curriculums );
										$first_curriculum = reset( $curriculums );
										$first_curriculum = explode( '_', $first_curriculum );
										$first_curriculum = array_map( 'absint', $first_curriculum );
										$section_id       = reset( $first_curriculum );
										$item_id          = end( $first_curriculum );
										if ( is_user_logged_in() ) {
											$meta_key       = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, get_the_ID() );
											$user_id        = get_current_user_id();
											$current_status = get_user_meta( $user_id, $meta_key, true );
											$current_status = ! empty( $current_status ) ? explode( '_', $current_status ) : array();
											if ( ! empty( $current_status ) ) {
												$course_progress = \BlueDolphin\Lms\calculate_course_progress( get_the_ID(), $curriculums, $current_status ) . '%';
												$section_id      = (int) reset( $current_status );
												$item_id         = (int) end( $current_status );
												$button_text     = esc_html__( 'Continue Learning', 'bluedolphin-lms' );
												$extra_class     = ' bdlms-btn-light';
												$last_curriculum = end( $curriculums );
												$last_curriculum = explode( '_', $last_curriculum );
												$last_curriculum = array_map( 'absint', $last_curriculum );
												if ( reset( $last_curriculum ) === $section_id && end( $last_curriculum ) === $item_id ) {
													$restart_course = \BlueDolphin\Lms\restart_course( get_the_ID() );
													if ( $restart_course ) {
														$first_curriculum = reset( $curriculums );
														$first_curriculum = explode( '_', $first_curriculum );
														$first_curriculum = array_map( 'absint', $first_curriculum );
														$section_id       = reset( $first_curriculum );
														$item_id          = end( $first_curriculum );
														$button_text      = esc_html__( 'Restart Course', 'bluedolphin-lms' );
														$extra_class      = ' bdlms-btn-dark';
													}
												}
											}
										}
										$curriculum_type = get_post_type( $item_id );
										$curriculum_type = str_replace( 'bdlms_', '', $curriculum_type );
										$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_view_link ), $section_id, $curriculum_type, $item_id );

										$button_text = apply_filters( 'bdlms_course_view_button_text', $button_text );
										$course_link = apply_filters( 'bdlms_course_view_button_link', $course_link );
										?>
										<li>
											<div class="bdlms-course-item">
												<div class="bdlms-course-item__img">
													<?php if ( ! empty( $terms_name ) ) : ?>
														<div class="bdlms-course-item__tag">
															<span class="bdlms-tag primary-light"><?php echo esc_html( $terms_name ); ?></span>
														</div>
													<?php endif; ?>
													<a href="<?php echo esc_url( $course_view_link ); ?>">
														<?php if ( has_post_thumbnail() ) : ?>
															<?php the_post_thumbnail(); ?>
														<?php else : ?>
															<img fetchpriority="high" decoding="async" src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
														<?php endif; ?>
													</a>
												</div>
												<div class="bdlms-course-item__info">
													<div class="bdlms-course-item__meta">
														<ul>
															<li>
																<svg width="16" height="16">
																	<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#clock">
																	</use>
																</svg>
																<?php
																$duration_str = \BlueDolphin\Lms\seconds_to_decimal_hours( $total_duration );
																if ( ! empty( $duration_str ) ) {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	printf( esc_html__( '%s Hours', 'bluedolphin-lms' ), esc_html( (string) $duration_str ) );
																} else {
																	echo esc_html__( 'Lifetime', 'bluedolphin-lms' );
																}
																?>
															</li>
															<li>
																<svg width="16" height="16">
																	<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
																	</use>
																</svg>
																<?php
																if ( $total_lessons > 1 ) {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	printf( esc_html__( '%s Lessons', 'bluedolphin-lms' ), esc_html( (string) $total_lessons ) );
																} else {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	printf( esc_html__( '%s Lesson', 'bluedolphin-lms' ), esc_html( (string) $total_lessons ) );
																}
																?>
															</li>
															<li>
																<svg width="16" height="16">
																	<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#quiz">
																	</use>
																</svg>
																<?php
																if ( $total_quizzes > 1 ) {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	printf( esc_html__( '%s Quizzes', 'bluedolphin-lms' ), esc_html( (string) $total_quizzes ) );
																} else {
                                                                    // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																	printf( esc_html__( '%s Quiz', 'bluedolphin-lms' ), esc_html( (string) $total_quizzes ) );
																}
																?>
															</li>
														</ul>
													</div>
													<h3 class="bdlms-course-item__title bdlms-h5"><a href="<?php echo esc_url( $course_link ); ?>"><?php the_title(); ?></a></h3>
													<div class="bdlms-course-item__action">
														<div class="bdlms-course-item__by">
															<?php echo get_avatar( get_the_author_meta( 'ID' ), 30 ); ?>
															<?php
																echo wp_kses(
																	sprintf(
																		// Translators: %1$s to filter url, %2$s author name.
																		__( '<a href="%1$s">%2$s</a>', 'bluedolphin-lms' ),
																		add_query_arg(
																			array(
																				'filter_author' => get_the_author_meta( 'ID' ),
																			)
																		),
																		get_the_author_meta( 'display_name' )
																	),
																	array(
																		'a' => array(
																			'href' => true,
																			'class' => true,
																			'target' => true,
																		),
																	)
																);
															?>
														</div>
														<div class="bdlms-btn-wrap">
															<?php if ( '100%' === $course_progress ) { ?>
																<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( (string) $course_id ); ?>" class="bdlms-btn bdlms-btn-block secondary download-certificate"><?php esc_html_e( 'Download certificate', 'bluedolphin-lms' ); ?></a>
															<?php } else { ?>
																<div class="bdlms-progress">
																	<div class="bdlms-progress__bar">
																		<div class="bdlms-progress__bar-inner" style="width: <?php echo esc_attr( $course_progress ); ?>"></div>
																	</div>
																</div>
																<a href="<?php echo esc_url( $course_link ); ?>" class="bdlms-btn bdlms-btn-block secondary"><?php echo esc_html( $button_text ); ?></a>
															<?php } ?>
														</div>
													</div>
												</div>
											</div>
										</li>
										<?php
									}
								endwhile;
								?>
							</ul>
						</div>
					<?php elseif ( ! empty( $search_keyword ) ) : ?>
						<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bluedolphin-lms' ); ?> <a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'my_learning' ) ); ?>"><?php esc_html_e( 'Back to my learning', 'bluedolphin-lms' ); ?>.</a></div>
					<?php else : ?>
						<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark"><?php esc_html_e( 'No courses were found.', 'bluedolphin-lms' ); ?></div>
					<?php endif; ?>
				</div>
				<?php if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) : ?>
					<div class="bdlms-course-view__footer">
						<div class="bdlms-pagination">
							<?php
							$big            = 999999999;
							$next           = '<svg width="16" height="16" style="display:block;"><use xlink:href="' . esc_url( BDLMS_ADDONS_ASSETS . '/' . bdlms_addons_template() . '/images/sprite-front.svg#page-next' ) . '"></use></svg>';
							$prev           = '<svg width="16" height="16" style="display:block;"><use xlink:href="' . esc_url( BDLMS_ADDONS_ASSETS . '/' . bdlms_addons_template() . '/images/sprite-front.svg#page-prev' ) . '"></use></svg>';
							$paginate_links = paginate_links(
								array(
									'base'      => str_replace( (string) $big, '%#%', get_pagenum_link( $big ) ),
									'format'    => '?paged=%#%',
									'current'   => max( 1, $_paged ),
									'total'     => $max_num_page,
									'prev_text' => $prev,
									'next_text' => $next,
								)
							);
							if ( $paginate_links ) {
								$allowed_tags = array(
									'svg'  => array(
										'width'  => array(),
										'height' => array(),
										'style'  => array(),
									),
									'use'  => array(
										'xlink:href' => array(),
									),
									'a'    => array(
										'href'  => array(),
										'class' => array(),
									),
									'span' => array(
										'aria'  => array(),
										'class' => array(),
									),
								);
								echo wp_kses( $paginate_links, $allowed_tags );
							}
							?>
						</div>
					</div>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</div>
