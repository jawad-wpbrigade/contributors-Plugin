<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.org
 * @since             1.0.0
 * @package           Meta_Box
 *
 * @wordpress-plugin
 * Plugin Name:       Post Contributors
 * Plugin URI:        http://example.org
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            jawad
 * Author URI:        http://example.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       post-contributors
 * Domain Path:       /languages
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
 * class for custom meta box
 * 
 * for post contributors
 */

class class_post_contributors{

	/**
	 * hooking into add_meta_boxes
	 * to add our contributors meta box
	 */
	public function __construct()
	{
		add_action( 'add_meta_boxes', array($this, 'contributor_meta_box') );
		add_action( 'save_post',array($this, 'save_editor') );
		add_filter( 'the_content', array($this, 'contributors_post_content'), 99 );
	}

	/**
	 * outputing contributors on frontend
	 * at the end of content
	 */
	public function contributors_post_content( $content ){

		if ( get_post_type() === 'post' ) {

			ob_start();

			$contributors_id = get_post_meta( get_the_ID(), 'contributors-list', true );

			$contributors = '';
			
			foreach ( $contributors_id as $contributor_id ) {
				$user = get_user_by( 'ID', $contributor_id );
				$link = get_author_posts_url( $user->ID, $user->user_nicename );
				$contributors .=  "<div><a href = '$link'><p>$user->display_name</p></a></div>";
			}
			$heading = "<h3>Contributors: </h3>";
			$content =  $content . $heading . $contributors ;
			ob_end_clean();
  			return $content;
		}
	}

	/**
	 * saving checkboxes on post update/publish
	 */
	public function save_editor( $post_id ){

			$new_data = $_POST['contributors-array'];

			$old_data = get_post_meta( $post_id, 'contributors-list' );
			// Update post meta
			if( !empty( $old_data ) ){

				update_post_meta( $post_id, 'contributors-list', $new_data );
				
			} else {
				add_post_meta( $post_id, 'contributors-list', $new_data );
		
			}
		
	}

	/**
	 * contributor meta box 
	 * adding id, label, screen here
	 */
	public function contributor_meta_box()
	{
		add_meta_box( 'meta-id', 'Contributors', array($this, 'contributor_meta_box_html'), array('post') );
	}

	/**
	 * contributor_meta_box callback function to 
	 * output backend html inside post editor
	 */
	public function contributor_meta_box_html(){

		$contributors_list = get_post_meta( get_the_ID(), 'contributors-list', true ) ? get_post_meta( get_the_ID(), 'contributors-list', true ) : array();


		//querying all the users
		$user_query = new WP_User_Query(
			array(
			'number' => '-1',
			'fields' => array(
				'user_nicename',
				'display_name',
				'ID',
			),
			)
		);

		//storing query result in $contributors variable
		$contributors = $user_query->get_results();
	


		//looping through all the results in $contributors
		foreach ( $contributors as $contributor ) {
			$checked_value = in_array( $contributor->ID, $contributors_list ) ? "checked" : "";
			
		
			echo '<input type="checkbox" id="'.$contributor->ID.'" name="contributors-array[]" value= "'.$contributor->ID.'" '.$checked_value.'>
			<label for="'.$contributor->ID.'">'.$contributor->display_name.'</label><br>';

		}

	}
}

new class_post_contributors();

