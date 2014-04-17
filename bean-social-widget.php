<?php
/**
 * Widget Name: Bean Social Widget
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

// REGISTER WIDGET
function reg_bean_social()
{
	register_widget('Bean_Social_Widget');
}
add_action('init', 'reg_bean_social', 1);

// WIDGET CLASS
class Bean_Social_Widget extends WP_Widget
{



	/*===================================================================*/
	/*	WIDGET SETUP
	/*===================================================================*/
	public function __construct()
	{
		parent::__construct(
	 		'bean_social', // BASE ID
			'Bean Social', // NAME
			array( 'description' => __( 'Add social media icons.', 'bean' ), )
		);


	    if ( is_active_widget(false, false, $this->id_base) )
	        add_action( 'wp_head', array(&$this, 'load_widget_style') );
	}




	/*===================================================================*/
	/*	ENQUEUE WIDGET STYLE
	/*===================================================================*/
	public function load_widget_style()
	{
	    wp_enqueue_style( 'bean-social-style', plugin_dir_url(__FILE__) . 'css/bean-social.css', false, '1.0', 'all' );
	}




	/*===================================================================*/
	/*	DISPLAY WIDGET
	/*===================================================================*/
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




	/*===================================================================*/
	/*	UPDATE WIDGET
	/*===================================================================*/
	public function update($new_instance, $old_instance)
	{
	    $instance = array();

        // STRIP TAGS TO REMOVE HTML - IMPORTANT FOR TEXT IMPUTS
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['desc'] = stripslashes( $new_instance['desc'] );

	    return $instance;
	}




	/*===================================================================*/
	/*	WIDGET SETTINGS (FRONT END PANEL)
	/*===================================================================*/
	public function form($instance)
	{
        // WIDGET DEFAULTS
        $defaults = array(
            'title' => 'We\'re Social',
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

} //END class Bean_Social_Widget
?>