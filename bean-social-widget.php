<?php
/*--------------------------------------------------------------------

 	Widget Name: Bean Social Widget
 	Widget URI: http://themebeans.com
 	Description:  Display social media icons/links based on the Bean Social settings in your dashboard.
 	Author: ThemeBeans
 	Author URI: http://themebeans.com/?ref=plugin_bean_social
 	Version: 1.1

/*--------------------------------------------------------------------*/


// WIDGET CLASS
class widget_bean_social extends WP_Widget {


/*--------------------------------------------------------------------*/
/*	WIDGET SETUP
/*--------------------------------------------------------------------*/
public function __construct() {
	parent::__construct(
 		'bean_social', // BASE ID
		'Bean Social (ThemeBeans)', // NAME
		array( 'description' => __( 'Display social media icons/links based on the Bean Social settings.', 'bean' ), )
	);


    if ( is_active_widget(false, false, $this->id_base) )
        add_action( 'wp_head', array(&$this, 'load_widget_style') );
}

/*--------------------------------------------------------------------*/
/*  LOAD THE WIDGET STYLE IF IT IS BEING USED
/*--------------------------------------------------------------------*/
public function load_widget_style() {
    wp_enqueue_style( 'bean-social-style', plugin_dir_url(__FILE__) . 'css/bean-social.css', false, '1.0', 'all' );
}

/*--------------------------------------------------------------------*/
/*	DISPLAY WIDGET
/*--------------------------------------------------------------------*/
public function widget($args, $instance) {
    $title = apply_filters( 'widget_title', $instance['title'] );

    echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

    echo Bean_Social::draw_social_icons();

    echo $args['after_widget'];
}


/*--------------------------------------------------------------------*/
/*	UPDATE WIDGET
/*--------------------------------------------------------------------*/
public function update($new_instance, $old_instance) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    return $instance;
}


/*--------------------------------------------------------------------*/
/*	WIDGET SETTINGS (FRONT END PANEL)
/*--------------------------------------------------------------------*/
public function form($instance) {
    if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
    }
    else {
        $title = __( 'Bean Social.', 'text_domain' );
    }
    ?>
    <p>
        <?php _e('Set your social profile URLs under the "Bean Social" menu item in your WordPress Dashboard.', 'bean'); ?>
        
        
    </p>

    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
}

}


// REGISTER WIDGET
function register_tb_social_widget(){
	register_widget('widget_bean_social');
}
add_action('init', 'register_tb_social_widget', 1);

?>