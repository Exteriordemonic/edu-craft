<?php
/**
 * WP-CLI demo content seeder (Task 07).
 *
 * @package EduCraftDomain
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns or creates a taxonomy term by slug.
 *
 * @param string $taxonomy Taxonomy slug.
 * @param string $name     Human-readable name.
 * @param string $slug     Term slug.
 * @return int Term ID or 0 on failure.
 */
function edu_craft_domain_cli_ensure_term( $taxonomy, $name, $slug ) {
	$existing = get_term_by( 'slug', $slug, $taxonomy );
	if ( $existing && ! is_wp_error( $existing ) ) {
		return (int) $existing->term_id;
	}

	$result = wp_insert_term(
		$name,
		$taxonomy,
		array(
			'slug' => $slug,
		)
	);

	if ( is_wp_error( $result ) ) {
		if ( 'term_exists' === $result->get_error_code() ) {
			$data = $result->get_error_data();
			if ( is_array( $data ) && isset( $data['term_id'] ) ) {
				return (int) $data['term_id'];
			}
			if ( is_numeric( $data ) ) {
				return (int) $data;
			}
		}
		WP_CLI::warning( sprintf( 'Term %s / %s: %s', $taxonomy, $slug, $result->get_error_message() ) );
		return 0;
	}

	return (int) $result['term_id'];
}

/**
 * Resolves product category term IDs from slugs.
 *
 * @param string[] $slugs Product category slugs.
 * @return int[]
 */
function edu_craft_domain_cli_product_cat_ids_from_slugs( array $slugs ) {
	$ids = array();
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			$ids[] = (int) $term->term_id;
		}
	}
	return $ids;
}

/**
 * Resolves industry term IDs from slugs.
 *
 * @param string[] $slugs Industry slugs.
 * @return int[]
 */
function edu_craft_domain_cli_industry_ids_from_slugs( array $slugs ) {
	$ids = array();
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'industry' );
		if ( $term && ! is_wp_error( $term ) ) {
			$ids[] = (int) $term->term_id;
		}
	}
	return $ids;
}

/**
 * Creates or updates a simple WooCommerce product by SKU.
 *
 * @param string   $sku            Product SKU.
 * @param string   $name           Product name.
 * @param string   $regular_price  Price string.
 * @param string[] $product_cat_slugs Category slugs.
 * @return int Product ID.
 */
function edu_craft_domain_cli_upsert_simple_product( $sku, $name, $regular_price, array $product_cat_slugs ) {
	$product_id = function_exists( 'wc_get_product_id_by_sku' ) ? (int) wc_get_product_id_by_sku( $sku ) : 0;

	if ( $product_id ) {
		$product = wc_get_product( $product_id );
	} else {
		$product = new WC_Product_Simple();
		$product->set_sku( $sku );
	}

	if ( ! $product instanceof WC_Product_Simple ) {
		WP_CLI::warning( sprintf( 'SKU %s is not a simple product; skipping update.', $sku ) );
		return 0;
	}

	$product->set_name( $name );
	$product->set_regular_price( $regular_price );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );

	$saved_id = $product->save();

	$cat_ids = edu_craft_domain_cli_product_cat_ids_from_slugs( $product_cat_slugs );
	if ( ! empty( $cat_ids ) ) {
		wp_set_object_terms( $saved_id, $cat_ids, 'product_cat' );
	}

	return (int) $saved_id;
}

/**
 * Creates or updates a published case study with ACF and industry terms.
 *
 * @param string   $slug            Post name slug.
 * @param string   $title           Title.
 * @param string   $content         Post content HTML.
 * @param string[] $industry_slugs  Industry term slugs.
 * @param array<string,string> $acf String fields: client, short_description, client_url.
 * @return int Post ID.
 */
function edu_craft_domain_cli_upsert_case_study( $slug, $title, $content, array $industry_slugs, array $acf ) {
	$existing_ids = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'case_study',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	$existing = ! empty( $existing_ids ) ? get_post( (int) $existing_ids[0] ) : null;

	$postarr = array(
		'post_type'    => 'case_study',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => $content,
		'post_name'    => $slug,
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = (int) $existing->ID;
		$post_id       = wp_update_post( wp_slash( $postarr ), true );
	} else {
		$post_id = wp_insert_post( wp_slash( $postarr ), true );
	}

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( $post_id->get_error_message() );
		return 0;
	}

	$post_id = (int) $post_id;

	$industry_ids = edu_craft_domain_cli_industry_ids_from_slugs( $industry_slugs );
	if ( ! empty( $industry_ids ) ) {
		wp_set_object_terms( $post_id, $industry_ids, 'industry' );
	}

	if ( function_exists( 'update_field' ) ) {
		if ( isset( $acf['client'] ) ) {
			update_field( 'client', $acf['client'], $post_id );
		}
		if ( isset( $acf['short_description'] ) ) {
			update_field( 'short_description', $acf['short_description'], $post_id );
		}
		if ( array_key_exists( 'client_url', $acf ) && '' !== $acf['client_url'] ) {
			update_field( 'client_url', $acf['client_url'], $post_id );
		}
	}

	return $post_id;
}

/**
 * Runs the full demo seed (idempotent by SKU / post slug / term slug).
 *
 * @return void
 */
function edu_craft_domain_cli_seed_demo_run() {
	if ( ! function_exists( 'WC' ) ) {
		WP_CLI::error( 'WooCommerce is not available. Activate WooCommerce and try again.' );
	}

	if ( ! function_exists( 'update_field' ) ) {
		WP_CLI::error( 'ACF is not available (update_field missing). Activate ACF and try again.' );
	}

	if ( ! post_type_exists( 'case_study' ) ) {
		WP_CLI::error( 'The case_study post type is not registered yet.' );
	}

	WP_CLI::log( 'Creating product categories…' );

	$product_categories = array(
		array( 'name' => 'Programowanie', 'slug' => 'programowanie' ),
		array( 'name' => 'Marketing', 'slug' => 'marketing' ),
		array( 'name' => 'Design', 'slug' => 'design' ),
		array( 'name' => 'Zarządzanie', 'slug' => 'zarzadzanie' ),
		array( 'name' => 'B2B', 'slug' => EDU_CRAFT_DOMAIN_B2B_PRODUCT_CAT_SLUG ),
		array( 'name' => 'Indywidualne', 'slug' => 'indywidualne' ),
		array( 'name' => 'Promocja', 'slug' => 'promocja' ),
	);

	foreach ( $product_categories as $row ) {
		edu_craft_domain_cli_ensure_term( 'product_cat', $row['name'], $row['slug'] );
	}

	WP_CLI::log( 'Creating industry terms…' );

	$industries = array(
		array( 'name' => 'IT', 'slug' => 'it' ),
		array( 'name' => 'Finanse', 'slug' => 'finanse' ),
		array( 'name' => 'E-commerce', 'slug' => 'e-commerce' ),
		array( 'name' => 'Edukacja', 'slug' => 'edukacja' ),
		array( 'name' => 'Healthcare', 'slug' => 'healthcare' ),
	);

	foreach ( $industries as $row ) {
		edu_craft_domain_cli_ensure_term( 'industry', $row['name'], $row['slug'] );
	}

	WP_CLI::log( 'Creating products…' );

	$catalog = array(
		array(
			'sku'             => 'edu-craft-kurs-javascript',
			'name'            => 'Kurs JavaScript od podstaw',
			'price'           => '599.00',
			'product_cat'     => array( 'programowanie', 'indywidualne' ),
		),
		array(
			'sku'             => 'edu-craft-warsztat-react',
			'name'            => 'Warsztat React dla zespołów',
			'price'           => '3499.00',
			'product_cat'     => array( 'programowanie', 'b2b' ),
		),
		array(
			'sku'             => 'edu-craft-szkolenie-ai-marketing',
			'name'            => 'Szkolenie AI w marketingu',
			'price'           => '1299.00',
			'product_cat'     => array( 'marketing', 'b2b' ),
		),
		array(
			'sku'             => 'edu-craft-bootcamp-ux-ui',
			'name'            => 'Bootcamp UX/UI',
			'price'           => '2499.00',
			'product_cat'     => array( 'design', 'indywidualne' ),
		),
		array(
			'sku'             => 'edu-craft-akademia-pm',
			'name'            => 'Akademia Project Managera',
			'price'           => '1899.00',
			'product_cat'     => array( 'zarzadzanie', 'b2b' ),
		),
	);

	foreach ( $catalog as $item ) {
		$pid = edu_craft_domain_cli_upsert_simple_product(
			$item['sku'],
			$item['name'],
			$item['price'],
			$item['product_cat']
		);
		if ( $pid ) {
			WP_CLI::log( sprintf( '  Product #%d — %s', $pid, $item['name'] ) );
		}
	}

	WP_CLI::log( 'Creating case studies…' );

	$studies = array(
		array(
			'slug'            => 'jak-firma-x-time-to-market',
			'title'           => 'Jak firma X zredukowała time-to-market o 40%',
			'content'         => '<p>Program szkoleniowy dla zespołu produktowego: standaryzacja pracy z design systemem i skrócenie cyklu wydawniczego o około 40% w pierwszym kwartale.</p>',
			'industry_slugs'  => array( 'it' ),
			'acf'             => array(
				'client'             => 'Firma X',
				'short_description'  => 'Standaryzacja procesu dostarczania zmian i szybsze wdrożenia dzięki pracy warsztatowej z zespołem developerskim.',
				'client_url'         => 'https://example.org/firma-x',
			),
		),
		array(
			'slug'            => 'warsztat-react-zespol-12',
			'title'           => 'Warsztat React dla zespołu 12 osób',
			'content'         => '<p>Intensywny warsztat on-site dla 12 osób: komponenty, stan lokalny, testowanie podstawowych interakcji oraz dobre praktyki w istniejącym kodzie produkcyjnym.</p>',
			'industry_slugs'  => array( 'it', 'edukacja' ),
			'acf'             => array(
				'client'             => 'Zespół produktowy Klienta',
				'short_description'  => 'Dwudniowy warsztat React z naciskiem na wspólne ćwiczenia i adaptację wzorców do realnego repozytorium zespołu.',
				'client_url'         => '',
			),
		),
		array(
			'slug'            => 'ai-black-friday',
			'title'           => 'AI w kampanii Black Friday',
			'content'         => '<p>Wsparcie zespołu marketingowego przy kampanii sezonowej: segmentacja treści, szablony kreacji i iteracja komunikatów przy ograniczonym czasie wejścia na rynek.</p>',
			'industry_slugs'  => array( 'e-commerce', 'it' ),
			'acf'             => array(
				'client'             => 'Sklep e-commerce',
				'short_description'  => 'Kampania Black Friday z wykorzystaniem narzędzi AI do przyspieszenia produkcji wariantów kreacji i testów A/B.',
				'client_url'         => 'https://example.org/sklep-demo',
			),
		),
	);

	foreach ( $studies as $study ) {
		$sid = edu_craft_domain_cli_upsert_case_study(
			$study['slug'],
			$study['title'],
			$study['content'],
			$study['industry_slugs'],
			$study['acf']
		);
		if ( $sid ) {
			WP_CLI::log( sprintf( '  Case study #%d — %s', $sid, $study['title'] ) );
		}
	}

	WP_CLI::success( 'Demo content seed finished. Run from project: ddev wp edu-craft seed-demo' );
}

/**
 * WP-CLI command: wp edu-craft seed-demo
 */
final class Edu_Craft_Domain_CLI_Seed_Demo_Command {

	/**
	 * Seed demo products, categories, industry terms, and case studies (idempotent).
	 *
	 * ## EXAMPLES
	 *
	 *     wp edu-craft seed-demo
	 *     ddev wp edu-craft seed-demo
	 *
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		edu_craft_domain_cli_seed_demo_run();
	}
}
