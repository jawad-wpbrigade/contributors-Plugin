<?php
/**
 * File to add shortcode.
 *
 * @package post_contributors
 * @since 1.0.0
 * @version 1.0.0
 */

/**
 * Callback function for shortcode api
 */
function contributors_shortcode_func() {

   $contributors = get_post_meta( get_the_ID(), 'contributors-list', true );

   if( empty( $contributors ) ) {
      return 'no contributors found';
   }

   if( is_page() ) {
      return 'you cannot use this shortcode on pages.';
   }

   $shortcode_content = '<p>Contributors: ';

   
   $last_contributor_index = count( $contributors );

   $iteration = 1;
	foreach ($contributors  as $contributor) {
      $user          = get_user_by( 'ID', $contributor );

		if ( false === $user ) {
			continue;
		}

		$link          = get_author_posts_url( $user->ID, $user->user_nicename );

      if ( 1 === $last_contributor_index) {
         $shortcode_content .= "<a href = '$link'>$user->display_name</a> ";
         continue;
      }
      if ( $iteration === $last_contributor_index ) {
         $shortcode_content .= "and <a href = '$link'>$user->display_name</a> ";
         continue;
      }
      if ( $iteration === $last_contributor_index - 1 ) {
         $shortcode_content .= "<a href = '$link'>$user->display_name</a> ";
         $iteration++;
         continue;
      }

      $shortcode_content .= "<a href = '$link'>$user->display_name</a>, ";
      $iteration++;
   }
   
   $shortcode_content .= '.</p>';

   return $shortcode_content;
}
add_shortcode( 'contributors_shortcode', 'contributors_shortcode_func' );
