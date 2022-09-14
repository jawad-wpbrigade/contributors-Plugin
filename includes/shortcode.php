<?php
/**
 * Use shortcode to show data from plugin
 *
 * @since   1.0.0
 * @package post_contributors
 * @author Jawad Malik
 */
function contributors_shortcode_func( $atts ) {

	$contributors = get_post_meta( get_the_ID(), 'contributors-list', true );

	if ( empty( $contributors ) || is_page() ) {
		return 'this is not what it was meant to be used for.';
	}

	$shortcode_content = '<p>Contributors: ';
	foreach ( $contributors  as $contributor ) {
		$user = get_user_by( 'ID', $contributor );

		if ( false === $user ) {
			continue;
		}

		$link = get_author_posts_url( $user->ID, $user->user_nicename );

		$shortcode_content .= "<a href = '$link'>$user->display_name</a>, ";
	}

	$shortcode_content .= '.</p>';

	return $shortcode_content;
}
add_shortcode( 'contributors_shortcode', 'contributors_shortcode_func' );
