<?php
/**
 * Helpers shared by dynamic block render templates.
 *
 * @package edu-craft-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves the Case Study post ID for template-only dynamic blocks.
 *
 * Resolution order matches how blocks render in the Site Editor (block context),
 * the main query loop, and singular views.
 *
 * @param WP_Block|null $block Block instance passed to the render template.
 * @return int Post ID, or 0 when not a valid Case Study context.
 */
function edu_craft_get_case_study_block_post_id( $block = null ) {
	$is_case_study = static function ( $post_id ) {
		$post_id = (int) $post_id;

		return ( $post_id > 0 && 'case_study' === get_post_type( $post_id ) ) ? $post_id : 0;
	};

	if ( $block instanceof WP_Block && ! empty( $block->context['postId'] ) ) {
		$resolved = $is_case_study( $block->context['postId'] );
		if ( $resolved ) {
			return $resolved;
		}
	}

	$resolved = $is_case_study( get_the_ID() );
	if ( $resolved ) {
		return $resolved;
	}

	if ( is_singular( 'case_study' ) ) {
		return $is_case_study( get_queried_object_id() );
	}

	return 0;
}

/**
 * Loads Case Study archive data once and prints Interactivity state.
 *
 * @return array{items: array<int, array>, invalid_industry: bool, terms: array<int, array{slug: string, name: string}>}
 */
function edu_craft_case_study_archive_prepare() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	if ( ! is_post_type_archive( 'case_study' ) ) {
		return array(
			'items'             => array(),
			'invalid_industry'  => false,
			'terms'             => array(),
		);
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only view state for archive.
	$industry_raw = isset( $_GET['industry'] ) ? sanitize_title( wp_unslash( $_GET['industry'] ) ) : '';

	$items_data = array(
		'items'             => array(),
		'invalid_industry'  => false,
	);

	if ( function_exists( 'edu_craft_domain_get_case_study_items_data' ) ) {
		$items_data = edu_craft_domain_get_case_study_items_data( $industry_raw, 20 );
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'industry',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	$terms_out = array();
	foreach ( $terms as $t ) {
		$terms_out[] = array(
			'slug' => $t->slug,
			'name' => $t->name,
		);
	}

	$archive_url = get_post_type_archive_link( 'case_study' );
	if ( ! $archive_url ) {
		$archive_url = home_url( '/' );
	}

	wp_interactivity_state(
		'eduCraftCaseStudyArchive',
		array(
			'items'             => array_values( $items_data['items'] ),
			'activeIndustry'    => $industry_raw,
			'invalidIndustry'   => ! empty( $items_data['invalid_industry'] ),
			'isLoading'         => false,
			'restUrl'           => rest_url( 'edu-craft-domain/v1/case-studies' ),
			'nonce'             => wp_create_nonce( 'wp_rest' ),
			'archiveUrl'        => $archive_url,
			'terms'             => $terms_out,
			'queryParam'        => 'industry',
		)
	);

	$cache = array(
		'items'             => array_values( $items_data['items'] ),
		'invalid_industry'  => ! empty( $items_data['invalid_industry'] ),
		'terms'             => $terms_out,
	);
	return $cache;
}

/**
 * Prints Interactivity state once (DRY entry point).
 *
 * @return void
 */
function edu_craft_case_study_archive_bootstrap_store() {
	edu_craft_case_study_archive_prepare();
}

/**
 * Renders a Case Study card for archive lists (SSR or hydrated).
 *
 * @param array<string,mixed> $item Item data (REST/plugin shape).
 * @return void
 */
function edu_craft_render_case_study_archive_card( array $item ) {
	$title     = isset( $item['title'] ) ? (string) $item['title'] : '';
	$permalink = isset( $item['permalink'] ) ? (string) $item['permalink'] : '#';
	$excerpt   = isset( $item['excerpt'] ) ? (string) $item['excerpt'] : '';
	$thumb     = isset( $item['thumbnail'] ) && is_array( $item['thumbnail'] ) ? $item['thumbnail'] : null;
	$inds      = isset( $item['industries'] ) && is_array( $item['industries'] ) ? $item['industries'] : array();
	?>
	<div class="col-12 col-md-6 col-lg-4">
		<article class="card h-100 shadow-sm border-0 edu-craft-csa-card">
			<?php if ( $thumb && ! empty( $thumb['src'] ) ) : ?>
				<a href="<?php echo esc_url( $permalink ); ?>" class="ratio ratio-16x9 edu-craft-csa-card__image-link bg-light">
					<img
						src="<?php echo esc_url( $thumb['src'] ); ?>"
						alt="<?php echo esc_attr( isset( $thumb['alt'] ) ? (string) $thumb['alt'] : $title ); ?>"
						class="card-img-top object-fit-cover h-100 rounded-top"
						loading="lazy"
					/>
				</a>
			<?php endif; ?>
			<div class="card-body d-flex flex-column">
				<h3 class="h5 card-title">
					<a href="<?php echo esc_url( $permalink ); ?>" class="stretched-link text-decoration-none text-dark"><?php echo esc_html( $title ); ?></a>
				</h3>
				<?php if ( $excerpt !== '' ) : ?>
					<p class="card-text text-secondary small"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $inds ) ) : ?>
					<div class="d-flex flex-wrap gap-2 mt-auto pt-2">
						<?php foreach ( $inds as $ind ) : ?>
							<?php
							if ( ! is_array( $ind ) ) {
								continue;
							}
							$name = isset( $ind['name'] ) ? (string) $ind['name'] : '';
							$link = isset( $ind['link'] ) ? (string) $ind['link'] : '';
							if ( $name === '' ) {
								continue;
							}
							if ( $link !== '' ) :
								?>
								<a href="<?php echo esc_url( $link ); ?>" class="cs-industry-badge" tabindex="-1"><?php echo esc_html( $name ); ?></a>
							<?php else : ?>
								<span class="cs-industry-badge"><?php echo esc_html( $name ); ?></span>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	</div>
	<?php
}
