<?php
/**
 * Template: Course Curriculum - Quiz.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$curriculum     = isset( $args['curriculum'] ) ? $args['curriculum'] : array();
$item_id        = isset( $curriculum['item_id'] ) ? $curriculum['item_id'] : 0;
$questions      = ! empty( $curriculum['questions'] ) ? $curriculum['questions'] : array();
$total_duration = \ST\Lms\count_duration( $curriculum );
$duration_str   = \ST\Lms\seconds_to_hours_str( $total_duration );
$duration_str   = ! empty( $duration_str ) ? trim( $duration_str ) : '';
shuffle( $questions );
$total_questions = count( $questions );
$layout          = stlmstp_addons_template();
?>

<div class="stlms-lesson-view__body">
	<div class="stlms-quiz-header">
		<div class="stlms-quiz-timer">
			<div class="base-timer">
				<svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
				<g class="base-timer__circle">
					<circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
					<path id="base-timer-path-remaining" stroke-dasharray="-14 283" class="base-timer__path-remaining" d="
						M 50, 50
						m -45, 0
						a 45,45 0 1,0 90,0
						a 45,45 0 1,0 -90,0
					"></path>
				</g>
				</svg><span class="stlms-quiz-countdown base-timer__label" id="stlms_quiz_countdown" data-total_questions="<?php echo esc_attr( (string) $total_questions ); ?>" data-timestamp="<?php echo esc_attr( (string) $total_duration ); ?>"></span>
			</div>	
		</div>
	</div>
	<div class="stlms-quiz-view">
		<div id="smartwizard" dir="" class="sw sw-theme-basic sw-justified">
			<ul class="nav" style="display:none;">
				<li class="nav-item">
					<a class="nav-link" href="#step-1">
						<div class="num">1</div>
						<?php
							// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
							echo esc_html( sprintf( __( 'Step %d', 'skilltriks-theme-pack' ), 1 ) );
						?>
					</a>
				</li>
				<?php
				$question_index = 1;
				if ( ! empty( $questions ) ) :
					foreach ( $questions as $question ) :
						++$question_index;
						?>
						<li class="nav-item">
							<a class="nav-link" href="#step-<?php echo esc_attr( (string) $question_index ); ?>">
								<div class="num"><?php echo esc_html( (string) $question_index ); ?></div>
								<?php
								// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( 'Step %s', 'skilltriks-theme-pack' ), $question_index ) );
								?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
				<li class="nav-item">
					<a class="nav-link" href="#step-<?php echo esc_attr( (string) ( $question_index + 1 ) ); ?>">
						<div class="num"><?php echo esc_html( (string) ( $question_index + 1 ) ); ?></div>
						<?php
							// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
							echo esc_html( sprintf( __( 'Step %s', 'skilltriks-theme-pack' ), $question_index + 1 ) );
						?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
					<div class="stlms-quiz-view-content">
						<div class="stlms-quiz-start">
							<h3 class="stlms-h4"><?php echo esc_html( get_the_title( $item_id ) ); ?></h3>
							<div class="info">
								<span>
									<?php
										echo esc_html(
											sprintf(
												// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
												_n( ' %s Question', ' %s Questions', (int) $total_questions, 'skilltriks-theme-pack' ),
												number_format_i18n( $total_questions ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											)
										);
										?>
								</span>
								<span><?php echo esc_html( $duration_str ); ?></span>
							</div>
							<button class="stlms-btn stlms-next-wizard"<?php disabled( true, empty( $questions ) ); ?>><?php esc_html_e( 'Let’s Start', 'skilltriks-theme-pack' ); ?></button>
						</div>
					</div>
				</div>
				<?php
				$question_index = 1;
				if ( ! empty( $questions ) ) :
					foreach ( $questions as $current_index => $question ) :
						++$question_index;
						$question_type  = get_post_meta( $question, \ST\Lms\META_KEY_QUESTION_TYPE, true );
						$questions_list = \ST\Lms\get_question_by_type( $question, $question_type );
						?>
				<div id="step-<?php echo esc_attr( (string) $question_index ); ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo esc_attr( (string) $question_index ); ?>">
					<div class="stlms-quiz-view-content">
						<div class="stlms-quiz-question">
							<div class="qus-no"><?php echo esc_html( sprintf( __( 'Question %1$s/%2$s', 'skilltriks-theme-pack' ), $current_index + 1, $total_questions ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></div>
							<h3 class="stlms-h4"><?php echo esc_html( get_the_title( $question ) ); ?></h3>
							<?php
							if ( ! empty( $questions_list[ $question_type ] ) && is_array( $questions_list[ $question_type ] ) ) :
								$answers = $questions_list[ $question_type ];
								shuffle( $answers );
								?>
								<div class="stlms-quiz-option-list">
									<ul>
										<?php foreach ( $answers as $answer ) : ?>
											<li>
												<label class="<?php echo in_array( $question_type, array( 'true_or_false' ), true ) ? 'boolean' : ''; ?>">
													<?php if ( in_array( $question_type, array( 'true_or_false', 'single_choice' ), true ) ) : ?>
														<input type="radio" name="stlms_answers[<?php echo esc_attr( (string) $question ); ?>]" class="stlms-check" value="<?php echo esc_attr( wp_hash( trim( $answer ) ) ); ?>">
													<?php else : ?>
														<input type="checkbox" name="stlms_answers[<?php echo esc_attr( (string) $question ); ?>][]" class="stlms-check"  value="<?php echo esc_attr( wp_hash( trim( $answer ) ) ); ?>">
													<?php endif; ?>
													<?php echo esc_html( trim( $answer ) ); ?>
												</label>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php elseif ( 'fill_blank' === $question_type ) : ?>
								<div class="stlms-quiz-input-ans">
									<div class="stlms-form-group">
										<label class="stlms-form-label"><?php esc_html_e( 'Your Answer', 'skilltriks-theme-pack' ); ?></label>
										<input type="text" name="stlms_written_answer[<?php echo esc_attr( (string) $question ); ?>]" class="stlms-form-control" placeholder="<?php esc_attr_e( 'Enter Your thoughts here...', 'skilltriks-theme-pack' ); ?>">
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
				<div id="step-<?php echo esc_attr( (string) ( $question_index + 1 ) ); ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo esc_attr( (string) ( $question_index + 1 ) ); ?>">
					<div class="stlms-quiz-complete">
						<div class="quiz-passed-text" style="display: none;">
							<div class="quiz-complete-icon">
								<svg width="150" height="150" class="quiz-complete">
									<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#quiz-complete"></use>
								</svg>
							</div>
							<h3><?php esc_html_e( 'You have passed the quiz!', 'skilltriks-theme-pack' ); ?></h3>
							<p><?php esc_html_e( 'Great Job reaching your goal!', 'skilltriks-theme-pack' ); ?></p>
						</div>
						<div class="quiz-failed-text" style="display: none;">
							<div class="quiz-fail-icon">
								<svg width="150" height="150" class="quiz-complete">
									<use xlink:href="<?php echo esc_url( STLMSTP_ADDONS_ASSETS . '/' . $layout ); ?>/images/sprite-front.svg#quiz-fail"></use>
								</svg>
							</div>
							<h3><?php esc_html_e( 'Unfortunately, you didn\'t pass the quiz.', 'skilltriks-theme-pack' ); ?></h3>
							<p><?php esc_html_e( 'Better luck next time.', 'skilltriks-theme-pack' ); ?></p>
						</div>
						<div class="stlms-quiz-result-list">
							<div class="stlms-quiz-result-item">
								<p class="stlms-p-large"><?php esc_html_e( 'Correct answers', 'skilltriks-theme-pack' ); ?></p>
								<span id="grade"></span>
							</div>
							<div class="stlms-quiz-result-item">
								<p class="stlms-p-large"><?php esc_html_e( 'Attempted Questions', 'skilltriks-theme-pack' ); ?></p>
								<span id="accuracy"></span>
							</div>
							<div class="stlms-quiz-result-item">
								<p class="stlms-p-large"><?php esc_html_e( 'Time taken', 'skilltriks-theme-pack' ); ?></p>
								<span id="time"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="stlms-lesson-view__footer">
		<?php if ( ! empty( $curriculum['settings']['show_correct_review'] ) ) : ?>
			<button class="stlms-btn outline-small stlms-check-answer" disabled><?php esc_html_e( 'Check Answer', 'skilltriks-theme-pack' ); ?></button>
		<?php endif; ?>
		<button class="stlms-btn small stlms-next-wizard"><?php esc_html_e( 'Continue', 'skilltriks-theme-pack' ); ?></button>
</div>