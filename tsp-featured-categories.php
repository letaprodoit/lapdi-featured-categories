<?php
    /*
    Plugin Name: 	LAPDI Featured Categories
    Plugin URI: 	https://www.letaprodoit.com/apps/plugins/wordpress/featured-categories-for-wordpress
    Description: 	Featured Categories allows you to <strong>add featured categories with images to your blog</strong>'s website. Powered by <strong><a href="http://wordpress.org/plugins/tsp-easy-dev/">LAPDI Easy Dev</a></strong>.
    Author: 		Let A Pro Do IT!
    Author URI: 	http://www.letaprodoit.com/
    Version: 		1.2.9
    Text Domain: 	tspfc
    Copyright: 		Copyright � 2013 Let A Pro Do IT!, LLC (www.letaprodoit.com). All rights reserved
    License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
    */

    require_once(ABSPATH . 'wp-admin/includes/plugin.php' );

    define('TSPFC_PLUGIN_FILE', 				__FILE__ );
    define('TSPFC_PLUGIN_PATH',					plugin_dir_path( __FILE__ ) );
    define('TSPFC_PLUGIN_URL', 					plugin_dir_url( __FILE__ ) );
    define('TSPFC_PLUGIN_BASE_NAME', 			plugin_basename( __FILE__ ) );
    define('TSPFC_PLUGIN_NAME', 				'tsp-featured-categories');
    define('TSPFC_PLUGIN_TITLE', 				'Featured Categories');
    define('TSPFC_PLUGIN_REQ_VERSION', 			"4.5.0");

    if (file_exists( WP_PLUGIN_DIR . "/tsp-easy-dev/tsp-easy-dev.php" ))
    {
        include_once WP_PLUGIN_DIR . "/tsp-easy-dev/tsp-easy-dev.php";
    }//end else
    else
        return;

    global $easy_dev_settings;

    require( TSPFC_PLUGIN_PATH . 'TSP_Easy_Dev.config.php');
    require( TSPFC_PLUGIN_PATH . 'TSP_Easy_Dev.extend.php');
    //--------------------------------------------------------
    // initialize the plugin
    //--------------------------------------------------------
    $featured_categories 						= new TSP_Easy_Dev( TSPFC_PLUGIN_FILE, TSPFC_PLUGIN_REQ_VERSION );

    $featured_categories->set_options_handler( new TSP_Easy_Dev_Options_Featured_Categories( $easy_dev_settings ), false, true );

    $featured_categories->set_widget_handler( 'TSP_Easy_Dev_Widget_Featured_Categories');

    $featured_categories->add_link ( 'FAQ',          preg_replace("/\%PLUGIN\%/", TSPFC_PLUGIN_NAME, TSP_WORDPRESS_FAQ_URL ));
    $featured_categories->add_link ( 'Rate Me',      preg_replace("/\%PLUGIN\%/", TSPFC_PLUGIN_NAME, TSP_WORDPRESS_RATE_URL ) );
    $featured_categories->add_link ( 'Support',      preg_replace("/\%PLUGIN\%/", 'wordpress-fc', TSP_LAB_BUG_URL ));

    $featured_categories->uses_shortcodes 				= true;

    // Queue User styles
    $featured_categories->add_css( TSPFC_PLUGIN_URL . TSPFC_PLUGIN_NAME . '.css' );

    // Queue User Scripts
    $featured_categories->add_script( TSPFC_PLUGIN_URL . 'assets/js' . DS . 'jquery.mousewheel.min.js', array('jquery','jquery-effects-core','jquery-ui-widget') );
    $featured_categories->add_script( TSPFC_PLUGIN_URL . 'assets/js' . DS . 'jquery.kinetic.min.js', array('jquery','jquery-effects-core','jquery-ui-widget') );
    $featured_categories->add_script( TSPFC_PLUGIN_URL . 'assets/js' . DS . 'jquery.smoothdivscroll-1.3-min.js', array('jquery','jquery-effects-core','jquery-ui-widget') );
    $featured_categories->add_script( TSPFC_PLUGIN_URL . 'assets/js' . DS . 'gallery-scripts.js', array('jquery','jquery-effects-core','jquery-ui-widget') );

    $featured_categories->set_plugin_icon( TSP_EASY_DEV_ASSETS_IMAGES_URL . 'icon_16.png' );

    $featured_categories->add_shortcode ( TSPFC_PLUGIN_NAME );
    $featured_categories->add_shortcode ( 'tsp_featured_categories' ); //backwards compatibility

    $featured_categories->run( TSPFC_PLUGIN_FILE );

    // Initialize widget - Required by WordPress
    add_action('widgets_init', function()
    {
        global $featured_categories;

        register_widget ( $featured_categories->get_widget_handler() );
        apply_filters( $featured_categories->get_widget_handler().'-init', $featured_categories->get_options_handler() );
    });