<?php
/**
 * File for Post Contributors widget
 *
 * @package post-contributors
 */

/**
 * Class to add widget
 */
class Contributors_Widget extends WP_Widget {


	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'contributors_widget',
			// Widget name will appear in UI
			'Contributors',
			// Widget description
			array( 'description' => 'Show contributors of current post' ),
		);
	}

	// Creating widget front-end.
	// This is where the action happens.
	public function widget( $args, $instance ) {
        $title    = $instance['title'];
		$link_check = $instance['link_check'];
        // before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
        echo $link_check;
        $contributors = get_post_meta( get_the_ID(), 'contributors-list', true );
        var_dump( $contributors );
        echo 'are the people who contributed to this post';
        echo $args['after_widget'];
	}

	// Widget Backend.
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = 'Post Contributors';
		}
		if ( isset( $instance['link_check'] ) ) {
			$link_check = $instance['link_check'];
		} else {
			$link_check = 'checked';
		}
		// Widget admin form.
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="contributors-widget" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
            <input class="contributors-widget" id="<?php echo $this->get_field_id( 'link_check' ); ?>" name="<?php echo $this->get_field_name( 'link_check' ); ?>" type="checkbox" value="<?php echo esc_attr( $link_check ); ?>" <?php echo esc_attr( $link_check ); ?> />

			<label for="<?php echo $this->get_field_id( 'link_check' ); ?>"><?php _e( 'attack link' ); ?></label>
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance             = array();
		$instance['title']    = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['link_check'] = ( ! empty( $new_instance['link_check'] ) ) ? strip_tags( $new_instance['link_check'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here

/**
 * Register and load the widget.
 */
function post_contributors_load_widget() {
	register_widget( 'Contributors_Widget' );
}
add_action( 'widgets_init', 'post_contributors_load_widget' );