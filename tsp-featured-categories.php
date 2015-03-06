<?php
/*
Plugin Name: 	TSP Featured Categories
Plugin URI: 	http://www.thesoftwarepeople.com/software/plugins/wordpress/featured-categories-for-wordpress.html
Description: 	Featured Categories allows you to <strong>add featured categories with images to your blog</strong>'s website. Powered by <strong><a href="http://wordpress.org/plugins/tsp-easy-dev/">TSP Easy Dev</a></strong>.
Author: 		The Software People
Author URI: 	http://www.thesoftwarepeople.com/
Version: 		1.2.2
Text Domain: 	tspfc
Copyright: 		Copyright � 2013 The Software People, LLC (www.thesoftwarepeople.com). All rights reserved
License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
*/

require_once(ABSPATH . 'wp-admin/includes/plugin.php' );

define('TSPFC_PLUGIN_FILE', 				__FILE__ );
define('TSPFC_PLUGIN_PATH',					plugin_dir_path( __FILE__ ) );
define('TSPFC_PLUGIN_URL', 					plugin_dir_url( __FILE__ ) );
define('TSPFC_PLUGIN_BASE_NAME', 			plugin_basename( __FILE__ ) );
define('TSPFC_PLUGIN_NAME', 				'tsp-featured-categories');
define('TSPFC_PLUGIN_TITLE', 				'TSP Featured Categories');
define('TSPFC_PLUGIN_REQ_VERSION', 			"3.5.1");

// The recommended option would be to require the installation of the standard version and
// bundle the Pro classes into your plugin if needed, this plugin requires both the Easy Dev plugin installation
// and looks for the existence of the Easy Dev Pro libraries
if ( !file_exists ( WP_PLUGIN_DIR . "/tsp-easy-dev/TSP_Easy_Dev.register.php" ) || !file_exists( TSPFC_PLUGIN_PATH . "lib/TSP_Easy_Dev_Pro/TSP_Easy_Dev_Pro.register.php" ) )
{
	function display_tspfc_notice()
	{
		$message = TSPFC_PLUGIN_TITLE . ' <strong>was not installed</strong>, plugin requires the installation of <strong><a href="plugin-install.php?tab=search&type=term&s=TSP+Easy+Dev">TSP Easy Dev</a></strong>.';
	    ?>
	    <div class="error">
	        <p><?php echo $message; ?></p>
	    </div>
	    <?php
	}//end display_tspfc_notice

	add_action( 'admin_notices', 'display_tspfc_notice' );
	deactivate_plugins( TSPFC_PLUGIN_BASE_NAME );
	return;
}//endif
else
{
    if (file_exists( WP_PLUGIN_DIR . "/tsp-easy-dev/TSP_Easy_Dev.register.php" ))
    {
    	include_once WP_PLUGIN_DIR . "/tsp-easy-dev/TSP_Easy_Dev.register.php";
    }//end else

    if (file_exists( TSPFC_PLUGIN_PATH . "/lib//TSP_Easy_Dev_Pro/TSP_Easy_Dev_Pro.register.php" ))
    {
    	include_once TSPFC_PLUGIN_PATH . "/lib//TSP_Easy_Dev_Pro/TSP_Easy_Dev_Pro.register.php";
    }//end else
}//end else

global $easy_dev_settings;

require( TSPFC_PLUGIN_PATH . 'TSP_Easy_Dev.config.php');
require( TSPFC_PLUGIN_PATH . 'TSP_Easy_Dev.extend.php');
//--------------------------------------------------------
// initialize the plugin
//--------------------------------------------------------
$featured_categories 						= new TSP_Easy_Dev_Pro( TSPFC_PLUGIN_FILE, TSPFC_PLUGIN_REQ_VERSION );

$featured_categories->set_options_handler( new TSP_Easy_Dev_Options_Featured_Categories( $easy_dev_settings ), false, true );

$featured_categories->set_widget_handler( 'TSP_Easy_Dev_Widget_Featured_Categories');

$featured_categories->add_link ( 'FAQ', 	'http://wordpress.org/extend/plugins/tsp-featured-categories/faq/' );
$featured_categories->add_link ( 'Rate Me', 'http://wordpress.org/support/view/plugin-reviews/tsp-featured-categories' );
$featured_categories->add_link ( 'Support', 'http://lab.thesoftwarepeople.com/tracker/wordpress-fc/issues/new' );

$featured_categories->uses_smarty 					= true;

$featured_categories->uses_shortcodes 				= true;

// Quueue User styles
$featured_categories->add_css( TSPFC_PLUGIN_URL . TSPFC_PLUGIN_NAME . '.css' );

// Quueue User Scripts
$featured_categories->add_script( TSPFC_PLUGIN_URL . 'js' . DS . 'jquery.smoothDivScroll-1.1.js', array('jquery','jquery-ui-widget') );
$featured_categories->add_script( TSPFC_PLUGIN_URL . 'js' . DS . 'gallery-scripts.js', array('jquery','jquery-ui-widget') );
//$featured_categories->add_script( TSP_EASY_DEV_PRO_ASSETS_JS_URL . 'pro-skel.min.js' );

// Queue Admin Styles
$featured_categories->add_css( includes_url() . 'js' . DS . 'thickbox' . DS . 'thickbox.css', true );

// Queue Admin Scripts
$featured_categories->add_script( TSP_EASY_DEV_PRO_ASSETS_JS_URL . 'pro-media-upload.js',  array('jquery','thickbox','media-upload','quicktags'), true );

$featured_categories->set_plugin_icon( TSPFC_PLUGIN_URL . 'images' . DS . 'tsp_icon_16.png' );

$featured_categories->add_shortcode ( TSPFC_PLUGIN_NAME );
$featured_categories->add_shortcode ( 'tsp_featured_categories' ); //backwards compatibility

$featured_categories->run( TSPFC_PLUGIN_FILE );

function tspfc_widgets_init()
{
	global $featured_categories;
	
	register_widget ( $featured_categories->get_widget_handler() );
	apply_filters( $featured_categories->get_widget_handler().'-init', $featured_categories->get_options_handler() );
}// end tspfc_widgets_init

// Initialize widget - Required by WordPress
add_action('widgets_init', 'tspfc_widgets_init');
?>