<?php
/*
Plugin Name: Bean Social
Plugin URI: http://themebeans.com/plugin/bean-social/?ref=plugin_bean_social
Description: Create and add social media icons with our widget and associated shortcodes.
Version: 1.0
Author: ThemeBeans
Author URI: http://themebeans.com/?ref=plugin_bean_social
*/

// DON'T CALL ANYTHING
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if ( ! class_exists( 'Bean_Social' ) ) :

class Bean_Social {

    public static $social_services = array(
                                    "twitter" => "Twitter",
                                    "facebook" => "Facebook",
                                    "dribbble" => "Dribbble",
                                    "mail" => "Mail",
                                    "instagram" => "Instagram",
                                    "pinterest" => "Pinterest",
                                    "vimeo" => "Vimeo",
                                    "evernote" => "Evernote",
                                    "myspace" => "MySpace",
                                    "linkedin" => "LinkedIn",
                                    "forrest" => "Forrest",
                                    "paypal" => "PayPal",
                                    "googleplus" => "Google Plus",
                                    "spotify" => "Spotify",
                                    "behance" => "Behance",
                                    "rss" => "RSS",
                                    "dropbox" => "Dropbox",
                                    "soundcloud" => "Soundcloud",
                                    "rdio" => "Rdio",
                                    "deviantart" => "deviantART",
                                    "skype" => "Skype",
                                    "soundhound" => "SoundHound",
                                    "zerply" => "Zerply",
                                    "picasa" => "Picasa",
                                    "500px" => "500px",
                                    "youtube" => "YouTube",
                                    "steam" => "Steam",
                                    "reddit" => "Reddit"
                                    );

    private $screen_id = null;

    /**
     * Class Constructor
     * Hooks a function to init action that initializes the plugin
    */
    function __construct() {
        add_action('init', array( &$this, 'social_init') , 0);

        add_filter('the_posts', array( &$this, 'add_style_if_shortcode_being_used' ) );
    }

    /**
     * Initializes the plugin
     * Hooks a function to admin_menu to initialize the plugin
    */
    function social_init() {
        if (is_admin()) {
            add_action( 'admin_init', array( &$this, 'register_settings' ) );
            add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
        } else {

        }
    }

    /**
     * Whitelist a group of options to be used by the plugin
     */
    function register_settings() {
        foreach(self::$social_services as $social_service_slug => $social_service) {
            register_setting( 'bean-social-options', 'bean_social-' . $social_service_slug );
        }

    }

    /**
     * Add a page under Appearance menu in the admin dashboard
     * Hooks a few necessary functions
     */
    function admin_menu() {	
    	add_options_page(
    		__('Bean Social', 'bean'), __('Bean Social', 'bean'), 'manage_options', 'bean_social', array(&$this, 'admin_page')
    	);
    }

    /**
     * Add action for enqueuing stylesheet
     * This function is hooked to the_posts which is triggered before wp_head
     * It checks all the posts to see if a shortcode is being used and adds a shortcode only then
     */

    function add_style_if_shortcode_being_used($posts) {
        if (empty($posts)) return $posts;

        $shortcode_found = false;
        foreach ($posts as $post) {
            if (stripos($post->post_content, '[bean_social') !== false) {
                $shortcode_found = true;
                break;
            }
        }

        if ($shortcode_found) {
            wp_enqueue_style( 'bean-social-style', plugins_url( 'css/bean-social.css', __FILE__ ) );
        }

        return $posts;
    }

    /**
     * Render the plugin page
     */
    function admin_page() {
        ?>

        <div class="wrap">
            <?php screen_icon('options-general'); ?>
            <h2><?php echo esc_html__('Bean Social Plugin', 'bean'); ?></h2>
			
			<div class="content-wrap" style="margin: 20px 5px; width: 70%;">
				<h4>Initial Setup.</h4>
	            <p>Create and add social media icons throughout your WordPress install using our Bean Social widget and associated shortcodes. Note that only the URLs you enter will display their relative icons. If you like this plugin, consider checking out our other <a href="http://themebeans.com/plugins/?ref=bean_social" target="blank"><b>Free Plugins</b></a>, as well as our <a href="http://themebeans.com/themes/?ref=bean_social" target="blank"><b>Premium WordPress Themes</b></a>. Cheers!</p><br />
	            
	            <h4>Social Shortcodes.</h4>
	            <p>Use the following shortcodes to implement the social icons throughout your install. The first pulls all the available icons, in which you have entered links. The second is an example of a shortcode that filters specific icons to display.</p>
	           
	            <p><code>[bean_social]</code><br /><br />
	            <code>[bean_social icons= "Twitter, Facebook, Dribbble"]</code></p><br />
	            
	            <h4>Profile URLs.</h4>
	            <p>Fill in the fields below with your associated URLs:</p>
			</div>
			
			
            <form method="post" action="options.php">

                <?php settings_fields( 'bean-social-options' ); ?>

                <table class="form-table">
                    <?php
                    foreach(self::$social_services as $social_service_slug => $social_service) {
                    ?>
                        <tr valign="top">
                            <th scope="row"><?php echo $social_service; ?></th>
                            <td><input type="text" class="regular-text ltr" name="<?php echo 'bean_social-' . $social_service_slug; ?>" value="<?php echo get_option('bean_social-' . $social_service_slug); ?>" /></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
				<div style="margin-left:10px; margin-top: 20px;">
                <?php submit_button(); ?>
				</div>
            </form>
        </div>

        <?php
    }

    /**
     * This function just reads the attributes and passes them to render_social_icons() for the actual rendering
     */
    function render_shortcodes( $atts ) {
        return self::draw_social_icons( $atts );
    }

    /**
     * Output the social icons
     * This function is responsible for rendering the output for both the widget and shortcodes
     */
    function draw_social_icons( $filter = null ) {
        $return_html_string = "<ul class='bean_social_icons'>";

        if ( !empty($filter) ) {
            $filter_arr = explode( ',', str_replace(' ', '', $filter["icons"] ) );
        }

        foreach( self::$social_services as $social_service_slug => $social_service ) {
            $social_link = get_option( 'bean_social-' . $social_service_slug );
            if (empty($social_link)) continue;

            if ( isset($filter_arr) ) {
                if ( ! ( in_array( $social_service_slug, $filter_arr ) ) && ! ( in_array( $social_service, $filter_arr ) ) ) {
                    continue;
                }
            }

            $return_html_string .= "<a title='$social_service' href='" . $social_link . "'>" .
                                        "<li class='bean_social_icon bean_social-$social_service_slug'></li>" .
                                    "</a>";
        }

        $return_html_string .= '</ul>';

        return $return_html_string;
    }
}

new Bean_Social;

endif;

// Include widget
require_once('bean-social-widget.php');

// Add shortcode
add_shortcode( 'bean_social', array( 'Bean_Social', 'render_shortcodes' ) );

?>