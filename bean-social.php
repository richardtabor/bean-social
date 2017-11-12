<?php
/**
 * Plugin Name: Bean Social
 * Plugin URI: http://themebeans.com/plugin/bean-social
 * Description: Create and add social media icons with our widget and associated shortcodes.
 * Version: 1.6.1
 * Author: ThemeBeans
 * Author URI: http://themebeans.com
 *
 *
 * @package Bean Plugins
 * @subpackage BeanSocial
 * @author ThemeBeans
 * @since BeanSocial 1.0
 */


/*===================================================================*/
/* MAKE SURE WE DO NOT EXPOSE ANY INFO IF CALLED DIRECTLY
/*===================================================================*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/*===================================================================*/
/*
/* PLUGIN FEATURES SETUP
/*
/*===================================================================*/

/*===================================================================*/
/* ADD SETTINGS LINK TO PLUGINS PAGE
/*===================================================================*/
define( 'BEANSOCIAL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

add_filter( 'plugin_action_links', 'beansocial_plugin_action_links', 10, 2 );

function beansocial_plugin_action_links( $links, $file ) {
	if ( $file != BEANSOCIAL_PLUGIN_BASENAME )
		return $links;

	$settings_link = '<a href="' . menu_page_url( 'bean-social', false ) . '">'
		. esc_html( __( 'Settings', 'bean-social' ) ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}


/*===================================================================*/
/* DEACTIVATION HOOK - REMOVE OPTION
/*===================================================================*/
function beansocial_deactivate()
{
	delete_option( 'edd_beansocial_activate_license' );
	delete_option( 'edd_beansocial_license_status' );
}
register_deactivation_hook( __FILE__, 'beansocial_deactivate' );








/*===================================================================*/
/*
/* BEGIN BEAN SOCIAL PLUGIN
/*
/*===================================================================*/
/*===================================================================*/
/* PLUGIN CLASS
/*===================================================================*/
if ( ! class_exists( 'Bean_Social' ) ) :
	class Bean_Social {

		//ICON ARRAY
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
		    "deviantart" => "DeviantART",
		    "skype" => "Skype",
		    "soundhound" => "SoundHound",
		    "zerply" => "Zerply",
		    "picasa" => "Picasa",
		    "500px" => "500px",
		    "youtube" => "YouTube",
		    "steam" => "Steam",
		    "reddit" => "Reddit",
		    "foodspotting" => "Foodspotting",
		    "wordpress" => "WordPress",
		    "medium" => "Medium",
		    "vine" => "Vine",
		    "github" => "Github",
		    "envato" => "Envato",
		    "tumblr" => "Tumblr",
		    "imdb" => "IMDB",
		    "flickr" => "Flickr",
		    );

	    private $screen_id = null;




		/*===================================================================*/
		/* CONSTRUCT
		/*===================================================================*/
	    function __construct()
	    {
	        add_action('init', array( &$this, 'social_init') , 0);
	        add_filter('the_posts', array( &$this, 'add_style_if_shortcode_being_used' ) );
	    }




	    /*===================================================================*/
	    /* PLUGIN INIT
	    /*===================================================================*/
	    function social_init()
	    {
	        if (is_admin()) {
	            add_action( 'admin_init', array( &$this, 'register_settings' ) );
	            add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	        } else {

	        }
	    }




		/*===================================================================*/
		/* WHITELIST OPTIONS BY THE PLUGIN
		/*===================================================================*/
	    function register_settings()
	    {
	        foreach(self::$social_services as $social_service_slug => $social_service) {
	            register_setting( 'bean-social-options', 'bean_social-' . $social_service_slug );
	        }

	    }




	    /*===================================================================*/
	    /* ADD THE BEAN SOCIAL MENU LINK
	    /*===================================================================*/
	    function admin_menu()
	    {
	    	add_options_page(
	    		__('Bean Social', 'bean'), __('Bean Social', 'bean'), 'manage_options', 'bean-social', array(&$this, 'bean_social_admin_page')
	    	);
	    }




		/*===================================================================*/
		/* ADD ACTION FOR ENQUEUING STYLESHEET IF THE SHORTCODE IS FOUND
		/*===================================================================*/
		function add_style_if_shortcode_being_used($posts)
		{
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




		/*===================================================================*/
	    /*	READ ATTRIBUTES AND RENDER ICONS
	    /*===================================================================*/
	    function render_shortcodes( $atts )
	    {
	        return self::draw_social_icons( $atts );
	    }




	    /*===================================================================*/
	    /*	OUTPUT SOCIAL ICONS
	    /*===================================================================*/
	   function draw_social_icons( $filter = null )
	    {
	        $return_html_string = "<ul class='bean_social_icons'>";

	        if ( !empty($filter) )
	        {
	            $filter_arr = explode( ',', str_replace(' ', '', $filter["icons"] ) );
	        }

	        foreach( self::$social_services as $social_service_slug => $social_service )
	        {
	            $social_link = get_option( 'bean_social-' . $social_service_slug );
	            if (empty($social_link)) continue;

	            if ( isset($filter_arr) ) {
	                if ( ! ( in_array( $social_service_slug, $filter_arr ) ) && ! ( in_array( $social_service, $filter_arr ) ) ) {
	                    continue;
	                }
	            }

	            $return_html_string .=
				"<li class='bean_social_icon bean_social-$social_service_slug'>" .
	           		"<a target='blank' title='$social_service' href='" . $social_link . "'></a>" .
	            "</li>";
	        }

	        $return_html_string .= '</ul>';
	        return $return_html_string;
	    }




		/*===================================================================*/
		/*	RENDER ADMIN PAGE
		/*===================================================================*/
	    function bean_social_admin_page()
	    {
	    	$license = get_option( 'edd_beansocial_license_key' );
	    	$status = get_option( 'edd_beansocial_license_status' );
	    	?>

	        <div class="wrap">
				<h2><?php echo esc_html__('Bean Social Plugin', 'bean'); ?></h2>
				<p>Create and add social media icons throughout your WordPress install using our Bean Social widget and associated shortcodes. Note that only the URLs you enter will display their relative icons. If you like this plugin, consider checking out our other <a href="http://themebeans.com/plugins/?ref=bean_social" target="blank">Free Plugins</a>, as well as our <a href="http://themebeans.com/themes/?ref=bean_social" target="blank">Premium WordPress Themes</a>. Cheers!</p><br />

		            <h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 10px;">Social Shortcodes</h4>
		            <p>Use the following shortcodes to implement the social icons throughout your install. The first pulls all the available icons, in which you have entered links. The second is an example of a shortcode that filters specific icons to display.</p>

		            <p><code>[bean_social]</code><br /><br />
		            <code>[bean_social icons= "Twitter, Facebook, Dribbble"]</code></p><br />

		            <h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 10px;">Profile URLs</h4>
		            <p>Fill in the fields below with your associated URLs:</p>

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
					<div><?php submit_button(); ?></div>
	            </form>
	        </div>
	        <?php
	    } //END function bean_social_admin_page()

} //END class Bean_Social

new Bean_Social;

endif;

/*===================================================================*/
/* INCLUDES AND ADDS
/*===================================================================*/
//INCLUDE SHORTCODE
require_once('bean-social-widget.php');

//ADD SHORTCODE
add_shortcode( 'bean_social', array( 'Bean_Social', 'render_shortcodes' ) );

?>
