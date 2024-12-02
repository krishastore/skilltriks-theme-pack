<?php
/**
 * Template: Courses
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
$category       = array_map( 'intval', $category );
$levels         = ! empty( $_GET['levels'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['levels'] ) ) ) : array();
$levels         = array_map( 'intval', $levels );
$_orderby       = ! empty( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : 'menu_order';

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

$course_args = apply_filters( 'bdlms_course_list_page_query', $course_args );
$courses     = new \WP_Query( $course_args );
$layout      = bdlms_addons_template();

?>

<div class="bdlms-wrap alignfull">
	<div class="bdlms-inner-banner">
		<div class="bdlms-container">
			<div class="bdlms-banner-content">
				<div class="bdlms-banner-info">
					<h1 class="bdlms-h1">
						<?php esc_html_e( 'All Courses', 'bluedolphin-lms' ); ?>
					</h1>
				</div>
				<div class="bdlms-banner-media">
					<img fetchpriority="high" decoding="async" src="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/banner-image.webp" alt="" width="585" height="355">
				</div>
			</div>
		</div>
	</div>
	<div class="bdlms-container">
		<div class="bdlms-course-view__header">
			<div class="bdlms-filtered-item">
				<?php
				echo wp_kses(
					sprintf(
						// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
						__( 'Showing <span>%1$d</span> of <span>%2$d</span> Courses', 'bluedolphin-lms' ),
						esc_html( $courses->post_count ),
						esc_html( $courses->found_posts )
					),
					array(
						'span' => array(),
					)
				);
				?>
			</div>
			<div class="bdlms-list-grid-toggle">
				<button class="bdlms-grid-view active" aria-label="Grid view">
					<svg width="30" height="30">
						<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#grid"></use>
					</svg>
				</button>
				<button class="bdlms-list-view" aria-label="List view">
					<svg width="30" height="30">
						<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#list"></use>
					</svg>
				</button>
			</div>
			<div class="bdlms-sort-by">
				<form onsubmit="return false;">
					<select aria-label="Sort by">
						<option value=""><?php esc_html_e( 'Sort By', 'bluedolphin-lms' ); ?></option>
						<option value="asc"<?php selected( $_orderby, 'asc' ); ?>><?php esc_html_e( 'Alphabetically (A To Z)', 'bluedolphin-lms' ); ?></option>
						<option value="desc"<?php selected( $_orderby, 'desc' ); ?>><?php esc_html_e( 'Alphabetically (Z To A)', 'bluedolphin-lms' ); ?></option>
						<option value="newest"<?php selected( $_orderby, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bluedolphin-lms' ); ?></option>
					</select>
				</form>
			</div>
			<button class="bdlms-filter-toggle" aria-label="Filter course">
				<svg width="24" height="24">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
				</svg>
			</button>
		</div>
	</div>

	<div class="bdlms-course-list-wrap">
		<div class="bdlms-container">
			<?php if ( $courses->have_posts() && ( isset( $args['filter'] ) && 'yes' === $args['filter'] ) ) : ?>
				<div class="bdlms-course-filter">
					<button class="bdlms-filter-toggle" aria-label="Close sidebar">
						<svg width="24" height="24">
							<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
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
									<button type="submit" aria-label="Search Course">
										<svg width="30" height="30">
											<use xlink:href="<?php echo esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#search-icon"></use>
										</svg>
									</button>
								</div>
							</form>
						</div>
					</div>
					<form method="get" onsubmit="return false;" class="bdlms-filter-form">
						<div class="bdlms-filter-item">
							<div class="bdlms-filter-title bdlms-h4">
								<?php esc_html_e( 'Categories', 'bluedolphin-lms' ); ?>
							</div>
							<?php
							$terms_list  = \BlueDolphin\Lms\course_taxonomies( \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
							$total_count = $courses->found_posts;
							?>
							<div class="bdlms-filter-list">
								<ul>
									<li>
										<div class="bdlms-check-wrap">
											<input type="checkbox" class="bdlms-check" id="bdlms_category_all">
											<label for="bdlms_category_all" class="bdlms-check-label"><?php esc_html_e( 'All', 'bluedolphin-lms' ); ?><span><?php echo esc_html( (string) $total_count ); ?></span>
											</label>
										</div>
									</li>
									<?php foreach ( $terms_list as $key => $course_term ) : ?>
										<li>
											<div class="bdlms-check-wrap">
												<input type="checkbox" name="category[]" class="bdlms-check" id="bd_course_term_<?php echo (int) $key; ?>" value="<?php echo esc_attr( $course_term['id'] ); ?>"<?php echo in_array( $course_term['id'], $category, true ) ? ' checked' : ''; ?>>
												<label for="bd_course_term_<?php echo (int) $key; ?>" class="bdlms-check-label">
													<?php echo esc_html( $course_term['name'] ); ?>
													<span><?php echo esc_html( $course_term['count'] ); ?></span>
												</label>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<?php
						$levels_list = \BlueDolphin\Lms\course_taxonomies( \BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG );
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
												<?php esc_html_e( 'All', 'bluedolphin-lms' ); ?><span><?php echo esc_html( (string) $total_count ); ?></span>
											</label>
										</div>
									</li>
									<?php foreach ( $levels_list as $key => $get_level ) : ?>
										<li>
											<div class="bdlms-check-wrap">
												<input type="checkbox" name="levels[]" class="bdlms-check" id="bd_course_level_<?php echo (int) $key; ?>" value="<?php echo esc_attr( $get_level['id'] ); ?>"<?php echo in_array( $get_level['id'], $levels, true ) ? ' checked' : ''; ?>>
												<label for="bd_course_level_<?php echo (int) $key; ?>" class="bdlms-check-label">
												<?php echo esc_html( $get_level['name'] ); ?>
													<span><?php echo esc_html( $get_level['count'] ); ?></span>
												</label>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<input type="hidden" name="order_by" value="<?php echo esc_attr( $_orderby ); ?>">
						<input type="hidden" name="_s" value="<?php echo esc_attr( $search_keyword ); ?>">
					</form>
				</div>
			<?php endif; ?>
			<div class="bdlms-course-view" id="bdlms_course_view">
				<div class="bdlms-course-view__body">
					<?php if ( $courses->have_posts() ) : ?>
						<div class="bdlms-course-list">
							<ul>
							<?php
							while ( $courses->have_posts() ) :
								$courses->the_post();
								$get_terms        = get_the_terms( get_the_ID(), \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
								$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
								$curriculums      = get_post_meta( get_the_ID(), \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
								$total_lessons    = 0;
								$total_quizzes    = 0;
								$course_view_link = get_the_permalink();
								$course_link      = $course_view_link;
								$button_text      = esc_html__( 'Enrol Now', 'bluedolphin-lms' );
								$extra_class      = '';
								$is_enrol         = false;
								$total_duration   = 0;
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
									$first_curriculum = array_map( 'intval', $first_curriculum );
									$section_id       = reset( $first_curriculum );
									$item_id          = end( $first_curriculum );
									if ( is_user_logged_in() ) {
										$meta_key       = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, get_the_ID() );
										$user_id        = get_current_user_id();
										$enrol_courses  = get_user_meta( $user_id, \BlueDolphin\Lms\BDLMS_ENROL_COURSES, true );
										$is_enrol       = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
										$button_text    = $is_enrol ? esc_html__( 'Start Learning', 'bluedolphin-lms' ) : $button_text;
										$current_status = get_user_meta( $user_id, $meta_key, true );
										if ( ! empty( $current_status ) ) {
											$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
											$current_status  = explode( '_', $current_status );
											$section_id      = (int) reset( $current_status );
											$item_id         = (int) end( $current_status );
											$button_text     = esc_html__( 'Continue Learning', 'bluedolphin-lms' );
											$extra_class     = ' secondary';
											$last_curriculum = end( $curriculums );
											$last_curriculum = explode( '_', $last_curriculum );
											$last_curriculum = array_map( 'intval', $last_curriculum );
											if ( reset( $last_curriculum ) === $section_id && end( $last_curriculum ) === $item_id ) {
												$restart_course = \BlueDolphin\Lms\restart_course( get_the_ID() );
												if ( $restart_course ) {
													$first_curriculum = reset( $curriculums );
													$first_curriculum = explode( '_', $first_curriculum );
													$first_curriculum = array_map( 'intval', $first_curriculum );
													$section_id       = reset( $first_curriculum );
													$item_id          = end( $first_curriculum );
													$button_text      = esc_html__( 'Restart Course', 'bluedolphin-lms' );
													$extra_class      = ' ';
												}
											}
										}
									}
									$curriculum_type = get_post_type( $item_id );
									$curriculum_type = str_replace( 'bdlms_', '', $curriculum_type );
									$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_view_link ), $section_id, $curriculum_type, $item_id );
									$button_text     = apply_filters( 'bdlms_course_view_button_text', $button_text );
									$course_link     = apply_filters( 'bdlms_course_view_button_link', $course_link );
								}
								?>
								<li>
									<div class="bdlms-course-item">
										<div class="bdlms-course-item__img">
											<?php if ( ! empty( $terms_name ) ) : ?>
												<div class="bdlms-course-item__tag">
													<span class="bdlms-tag primary-light"><?php echo esc_html( $terms_name ); ?></span>
												</div>
											<?php endif; ?>
											<a href="<?php echo esc_url( $course_view_link ); ?>" aria-label="<?php the_title(); ?>">
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
														<svg width="20" height="20">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
															</use>
														</svg>
														<?php
														if ( $total_lessons > 1 ) {
															// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
															printf( esc_html__( '%d Lessons', 'bluedolphin-lms' ), (int) $total_lessons );
														} else {
															// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
															printf( esc_html__( '%d Lesson', 'bluedolphin-lms' ), (int) $total_lessons );
														}
														?>
													</li>
													<li>
														<svg width="20" height="20">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#quiz">
															</use>
														</svg>
														<?php
														if ( $total_quizzes > 1 ) {
															// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
															printf( esc_html__( '%d Quizzes', 'bluedolphin-lms' ), (int) $total_quizzes );
														} else {
															// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
															printf( esc_html__( '%d Quiz', 'bluedolphin-lms' ), (int) $total_quizzes );
														}
														?>
													</li>
													<li>
														<svg width="20" height="20">
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
												</ul>
											</div>
											<h2 class="bdlms-course-item__title bdlms-h5">
												<a href="<?php echo esc_url( $course_view_link ); ?>"><?php the_title(); ?></a>
											</h2>
											<?php
											$author_url    = add_query_arg( array( 'filter_author' => get_the_author_meta( 'ID' ) ) );
											$author        = get_the_author_meta( 'display_name' );
											$author_avatar = get_avatar_url( get_the_author_meta( 'ID' ) );
											?>
											<div class="bdlms-course-item__action">
												<div class="bdlms-course-item__by">
													<img src="<?php echo esc_url( $author_avatar ); ?>" alt="<?php echo esc_html( $author ); ?>">
													<a href="<?php echo esc_url( $author_url ); ?>" class="bdlms-link-text"><?php echo esc_html( $author ); ?></a>
												</div>
												<div class="bdlms-btn-wrap">
													<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="bdlms-btn bdlms-btn-block<?php echo esc_attr( $extra_class ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_html( (string) get_the_ID() ); ?>"><?php echo esc_html( $button_text ); ?><i class="bdlms-loader"></i></a>
												</div>
											</div>
										</div>
									</div>
								</li>
								<?php endwhile; ?>
							</ul>
						</div>
					<?php elseif ( ! empty( $search_keyword ) ) : ?>
						<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bluedolphin-lms' ); ?> <a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) ); ?>"><?php esc_html_e( 'Back to courses', 'bluedolphin-lms' ); ?>.</a></div>
					<?php else : ?>
						<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark"><?php esc_html_e( 'No courses were found.', 'bluedolphin-lms' ); ?></div>
					<?php endif; ?>
				</div>
				<?php if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) : ?>
					<div class="bdlms-course-view__footer">
						<div class="bdlms-pagination">
						<?php
						$big            = 999999999;
						$next           = '<svg width="16" height="16" style="display:block;"><use xlink:href="' . esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout . '/images/sprite-front.svg#page-next' ) . '"></use></svg>';
						$prev           = '<svg width="16" height="16" style="display:block;"><use xlink:href="' . esc_url( BDLMS_ADDONS_ASSETS . '/' . $layout . '/images/sprite-front.svg#page-prev' ) . '"></use></svg>';
						$paginate_links = paginate_links(
							array(
								'base'      => str_replace( (string) $big, '%#%', get_pagenum_link( $big ) ),
								'format'    => '?paged=%#%',
								'current'   => max( 1, $_paged ),
								'total'     => $courses->max_num_pages,
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
