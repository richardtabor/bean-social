<?php
/**
 * Widget Name: Bean Social
 * Widget URI: http://themebeans.com
 * Description:  Display social media icons based on the Bean Social settings in your dashboard.
 * Author: ThemeBeans
 * Author URI: http://themebeans.com
 *
 *
 * @package Bean Plugins
 * @subpackage BeanSocial
 * @author ThemeBeans
 * @since BeanSocial 1.0
 */

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("Bean_Social_Widget");'));

class Bean_Social_Widget extends WP_Widget 
{
	// Constructor
	function __construct() {
		parent::__construct(
			'bean_social', // Base ID
			__( 'Bean Social', 'bean' ), // Name
			array( 'description' => __( 'Display social media icons.', 'bean' ), ) // Args
		);

		if ( is_active_widget(false, false, $this->id_base) ) {
	        add_action( 'wp_head', array(&$this, 'load_widget_style') );
	     } 
	}

	// Enqueue
	public function load_widget_style()
	{
	    wp_enqueue_style( 'bean-social-style', plugin_dir_url(__FILE__) . 'css/bean-social.css', false, '1.0', 'all' );
	}

	// Display widget
	public function widget($args, $instance)
	{
	    $title = apply_filters( 'widget_title', $instance['title'] );
        $desc = $instance['desc'];

	    echo $args['before_widget'];
	        if ( ! empty( $title ) )
	            echo $args['before_title'] . $title . $args['after_title'];

        if($desc != '') : ?><p><?php echo $desc; ?></p><?php endif;

	    echo Bean_Social::draw_social_icons(); 

	    echo $args['after_widget'];
	}

	// Update widget
	public function update($new_instance, $old_instance)
	{
	    $instance = array();

        // STRIP TAGS TO REMOVE HTML - IMPORTANT FOR TEXT IMPUTS
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['desc'] = stripslashes( $new_instance['desc'] );

	    return $instance;
	}


	// Widget settings
	public function form($instance)
	{
        $defaults = array(
            'title' => '',
            'desc'  => '',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );

	    ?>
		<p>
		<?php _e('Set your social icon links within the "Bean Social" menu item in your WordPress settings menu.', 'bean'); ?>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

        <p style="margin-top: -8px;">
        <textarea class="widefat" rows="5" cols="15" id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>"><?php echo $instance['desc']; ?></textarea>
        </p>

	    <?php
	}
}