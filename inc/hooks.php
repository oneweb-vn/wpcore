<?php
/**
 * Custom hooks
 *
 * @package wpcore
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpcore_site_info' ) ) {
	/**
	 * Add site info hook to WP hook library.
	 */
	function wpcore_site_info() {
		do_action( 'wpcore_site_info' );
	}
}

add_action( 'wpcore_site_info', 'wpcore_add_site_info' );
if ( ! function_exists( 'wpcore_add_site_info' ) ) {
	/**
	 * Add site info content.
	 */
	function wpcore_add_site_info() {
		$the_theme = wp_get_theme();

		$site_info = sprintf(
			'<a href="%1$s">%2$s</a><span class="sep"> | </span>%3$s(%4$s)',
			esc_url( __( 'https://wordpress.org/', 'wpcore' ) ),
			sprintf(
				/* translators: WordPress */
				esc_html__( 'Proudly powered by %s', 'wpcore' ),
				'WordPress'
			),
			sprintf(
				/* translators: 1: Theme name, 2: Theme author */
				esc_html__( 'Theme: %1$s by %2$s.', 'wpcore' ),
				$the_theme->get( 'Name' ),
				'<a href="' . esc_url( __( 'https://wpcore.com', 'wpcore' ) ) . '">wpcore.com</a>'
			),
			sprintf(
				/* translators: Theme version */
				esc_html__( 'Version: %s', 'wpcore' ),
				$the_theme->get( 'Version' )
			)
		);

		// Check if customizer site info has value.
		if ( get_theme_mod( 'wpcore_site_info_override' ) ) {
			$site_info = get_theme_mod( 'wpcore_site_info_override' );
		}

		echo apply_filters( 'wpcore_site_info_content', $site_info ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
}
