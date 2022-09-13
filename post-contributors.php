<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    http://example.org
 * @since   1.0.0
 * @package Meta_Box
 *
 * @wordpress-plugin
 * Plugin Name:       Post Contributors
 * Plugin URI:        http://example.org
 * Description:       Add Contributor meta box in editor plus shows selected contributors on frontend
 * Version:           1.0.0
 * Author:            jawad
 * Author URI:        http://example.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'POST_CONTRIBUTORS_VERSION', '1.0.0' );

/**
 * Class for custom meta box
 *
 * For post contributors
 */
class Class_Post_Contributors {


	/**
	 * Hooking into add_meta_boxes
	 * to add our contributors meta box
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'contributor_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_editor' ) );
		add_filter( 'the_content', array( $this, 'contributors_post_content' ), 99 );
	}

	/**
	 * Outputing contributors on frontend
	 * at the end of content
	 *
	 * @param object $content object contains the content of current post.
	 */
	public function contributors_post_content( $content ) {

		if ( get_post_type() === 'page' || get_post_type() === 'attachment') {
			return;
		}

		$contributors_id = get_post_meta( get_the_ID(), 'contributors-list', true );

		if ( empty( $contributors_id ) ) {
				return $content;
		}

		$contributors = '';

		foreach ( $contributors_id as $contributor_id ) {
			$user          = get_user_by( 'ID', $contributor_id );

			if ( false === $user ) {
					continue;
			}

			$link          = get_author_posts_url( $user->ID, $user->user_nicename );

			$contributors .= "<div><a href = '$link'><p>$user->display_name</p></a></div>";
		}

		$heading  = '<h3>Contributors: </h3>';
		$content .= $heading;
		$content .= $contributors;

		return $content;

	}



	/**
	 * Saving checkboxes on post update/publish
	 *
	 * @param post_id $post_id get the current post id.
	 */
	public function save_editor( $post_id ) {

		if ( isset( $_POST['contributors-array'] ) && ! empty( $_POST['contributors-array'] ) ) {

			$sanitized_contributors_list = wp_unslash( $_POST['contributors-array'] );

			// $sanitized_contributors_list = maybe_serialize( $contributors_list );

			foreach ( $sanitized_contributors_list as &$list_item ) {
				$list_item = sanitize_text_field( $list_item );
			}

			update_post_meta( $post_id, 'contributors-list', $sanitized_contributors_list );

		} else {

			delete_post_meta( $post_id, 'contributors-list' );

		}

	}

	/**
	 * Contributor meta box
	 * On post type and custom post types
	 * adding id, label, screen here
	 */
	public function contributor_meta_box() {
		$post_types = get_post_types( array( 'public' => true ) );
		unset( $post_types['page'], $post_types['attachment'] );
		add_meta_box( 'meta-id', 'Contributors', array( $this, 'contributor_meta_box_html' ), $post_types );
	}

	/**
	 * Contributor_meta_box callback function to
	 * output backend html inside post editor
	 */
	public function contributor_meta_box_html() {

		$contributors_list = get_post_meta( get_the_ID(), 'contributors-list', true ) ? get_post_meta( get_the_ID(), 'contributors-list', true ) : array();

		// querying all the users!
		$user_query = new WP_User_Query(
			array(
				'number' => '-1',
				'role__in' => array( 'author','Administrator' ),
				'fields' => array(
					'user_nicename',
					'display_name',
					'ID',
				),
			)
		);

		// looping through all the results in $contributors.
		if ( ! empty( $user_query->get_results() ) ) {
			foreach ( $user_query->get_results() as $contributor ) {
				$checked_value = in_array( $contributor->ID, $contributors_list, true ) ? 'checked' : '';

				echo '<input type="checkbox" id="' . esc_attr( $contributor->ID ) . '" name="contributors-array[]" value= "' . esc_attr( $contributor->ID ) . '" ' . esc_attr( $checked_value ) . '>
				<label for="' . esc_attr( $contributor->ID ) . '">' . esc_html( $contributor->display_name ) . '</label><br>';

			}
		} else {
			echo 'no users found.';
		}
	}
}

new class_post_contributors();

