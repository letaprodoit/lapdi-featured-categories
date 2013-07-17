<?php
/*
Plugin Name: 	TSP Featured Categories
Plugin URI: 	http://www.thesoftwarepeople.com/software/plugins/wordpress/featured-categories-for-wordpress.html
Description: 	Featured Categories allows you to add featured categories with images to your blog's website. Featured categories have three (3) layouts and include thumbnails.
Author: 		The Software People
Author URI: 	http://www.thesoftwarepeople.com/
Version: 		1.0.3
Copyright: 		Copyright © 2013 The Software People, LLC (www.thesoftwarepeople.com). All rights reserved
License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
*/

define( 'TSPFC_REQUIRED_WP_VERSION', '3.5.1' );
define( 'TSPFC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once(ABSPATH . 'wp-admin/includes/plugin.php' );

// Get the plugin path
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('DIRECTORY_SEPARATOR')) {
    if (strpos(php_uname('s') , 'Win') !== false) define('DIRECTORY_SEPARATOR', '\\');
    else define('DIRECTORY_SEPARATOR', '/');
}

// Set the abs plugin path
define('PLUGIN_ABS_PATH', ABSPATH . PLUGINDIR );
$plugin_abs_path = PLUGIN_ABS_PATH . DIRECTORY_SEPARATOR . "tsp-featured-categories";
define('TSPFC_ABS_PATH', $plugin_abs_path);
$plugin_url = WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/';
define('TSPFC_URL_PATH', $plugin_url);

$upload_dir = wp_upload_dir();

define('TSPFC_TEMPLATE_PATH', TSPFC_ABS_PATH . '/templates');
define('TSPFC_TEMPLATE_CACHE_PATH', $upload_dir['basedir'] . '/tsp-plugins/tsp-featured-categories/cache');
define('TSPFC_TEMPLATE_COMPILE_PATH', $upload_dir['basedir'] . '/tsp-plugins/tsp-featured-categories/compiled');

// Set the file path
$file_path    = $plugin_abs_path . DIRECTORY_SEPARATOR . basename(__FILE__);

// Set the absolute path
$asolute_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
define('TSPFC_ABSPATH', $asolute_path);


include_once(TSPFC_ABS_PATH . '/includes/settings.inc.php');


if (!class_exists('Smarty'))
{
    if (file_exists(TSPFC_ABS_PATH . '/libs/Smarty.class.php'))
        require_once TSPFC_ABS_PATH . '/libs/Smarty.class.php';
}

// Initialization and Hooks
global $wpdb;
global $wp_version;

define('TSPFC_VERSION', '1.0.0');
define('TSPFC_DB_VERSION', '0.0.2');
define('TSPFC_TABLE_NAME', $wpdb->prefix . 'tspfc_termsmeta');
define('TSPFC_OLD_TABLE_NAME', $wpdb->prefix . 'termsmeta');

register_activation_hook( __FILE__, 'fn_tspfc_install') ;
register_uninstall_hook( __FILE__, 'fn_tspfc_uninstall' );

//--------------------------------------------------------
// install plugin
//--------------------------------------------------------
function fn_tspfc_install()
{
    global $wpdb;
    
    $new_install = false;
    
    // create table on first install
    if ($wpdb->get_var("show tables like '" . TSPFC_TABLE_NAME . "'") != TSPFC_TABLE_NAME) {
        fn_tspfc_create_table($wpdb);
        add_option("tspfc_db_version", TSPFC_DB_VERSION);
        add_option("tspfc_configuration", '');
        $new_install = true;
    }
    
    // On plugin update only the version number is updated.
    $installed_ver = get_option("tspfc_db_version");
    if ($installed_ver != TSPFC_DB_VERSION) {
        update_option("tspfc_db_version", TSPFC_DB_VERSION);
    }
    
	// if old table exists and this is a new install, copy its contents into the new table
    if ($new_install && $wpdb->get_var("show tables like '" . TSPFC_OLD_TABLE_NAME ."'") == TSPFC_OLD_TABLE_NAME) {
        fn_tspfc_copy_table($wpdb);
    }
    
	$message = "";
	
	if (!wp_mkdir_p( TSPFC_TEMPLATE_CACHE_PATH ))
		$message .= "<br>Unable to create " . TSPFC_TEMPLATE_CACHE_PATH . " directory. Please create this directory manually via FTP or cPanel.";
	else
		@chmod( TSPFC_TEMPLATE_CACHE_PATH, 0777 );
	
	
	if (!wp_mkdir_p( TSPFC_TEMPLATE_COMPILE_PATH ))
		$message .= "<br>Unable to create " . TSPFC_TEMPLATE_COMPILE_PATH . " directory. Please create this directory manually via FTP or cPanel.";
	else
		@chmod( TSPFC_TEMPLATE_COMPILE_PATH, 0777 );
	
	return $message;
}
//--------------------------------------------------------
// uninstall plugin
//--------------------------------------------------------
function fn_tspfc_uninstall()
{
    global $wpdb;
    
    // delete table
    if ($wpdb->get_var("show tables like '" . TSPFC_TABLE_NAME . "'") == TSPFC_TABLE_NAME) {
        fn_tspfc_drop_table($wpdb);
    }
    delete_option( "tspfc_db_version" );
    delete_option( "tspfc_configuration" );
	delete_option( 'tspfc_options' );
}
//--------------------------------------------------------
// Process shortcodes
//--------------------------------------------------------
function fn_tspfc_process_shortcodes($att)
{
	global $TSPFC_OPTIONS;
	
	if ( is_feed() )
		return '[tsp-featured-categories]';

	$options = $TSPFC_OPTIONS;
	
	if (!empty($att))
		$options = array_merge( $TSPFC_OPTIONS, $att );
		     	
	$output = fn_tspfc_display($options,false);
	
	return $output;
}

add_shortcode('tsp-featured-categories', 'fn_tspfc_process_shortcodes');
add_shortcode('tsp_featured_categories', 'fn_tspfc_process_shortcodes');
//--------------------------------------------------------
// create table to store metadata
//--------------------------------------------------------
function fn_tspfc_create_table($wpdb)
{
    $sql     = "CREATE TABLE  " . TSPFC_TABLE_NAME . " (
          meta_id bigint(20) NOT NULL auto_increment,
          terms_id bigint(20) NOT NULL default '0',
          meta_key varchar(255) default NULL,
          meta_value longtext,
          PRIMARY KEY  (`meta_id`),
          KEY `terms_id` (`terms_id`),
          KEY `meta_key` (`meta_key`)
        ) ENGINE=MyISAM AUTO_INCREMENT=6887 DEFAULT CHARSET=utf8;";
    $results = $wpdb->query($sql);
}
//--------------------------------------------------------
// delete table to store metadata
//--------------------------------------------------------
function fn_tspfc_drop_table($wpdb)
{
    $sql     = "DROP TABLE  " . TSPFC_TABLE_NAME . " ;";
    $results = $wpdb->query($sql);
}
//--------------------------------------------------------
// copy old table data to new one
//--------------------------------------------------------
function fn_tspfc_copy_table($wpdb)
{
    $sql     = "SELECT * FROM `" . TSPFC_OLD_TABLE_NAME . "` WHERE `meta_key` = 'featured' OR `meta_key` = 'image';";
    $entries = $wpdb->get_results($sql, ARRAY_A);
    
    foreach ( $entries as $e ) 
    {
    	$sql = "REPLACE INTO `" . TSPFC_TABLE_NAME . "` (`terms_id`,`meta_key`,`meta_value`) VALUES ('{$e['terms_id']}','{$e['meta_key']}', '{$e['meta_value']}');";
    	$results = $wpdb->query($sql);
    	
    }//endforeach
}
//--------------------------------------------------------
// Get admin scripts
//--------------------------------------------------------
function fn_tspfc_get_admin_scripts()
{
    if (is_admin()) {
    
        // queue the styles
        wp_register_style('thickbox-css', '/wp-includes/js/thickbox/thickbox.css');
        wp_enqueue_style('thickbox-css');

        //queue the javascripts
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('quicktags');

	    wp_register_script('tspfc-scripts.js', plugins_url('js/scripts.js', __FILE__ ), array('jquery'));
        wp_enqueue_script('tspfc-scripts.js');
    }
}
// Actions
add_filter('admin_enqueue_scripts', 'fn_tspfc_get_admin_scripts');
//--------------------------------------------------------
// Get category metadata
//--------------------------------------------------------
function fn_tspfc_get_category_metadata($terms_id, $key, $single = false)
{
    $terms_id   = (int)$terms_id;
    $meta_cache = wp_cache_get($terms_id, 'terms_meta');
    
    if (!$meta_cache) {
        fn_tspfc_update_category_meta_cache($terms_id);
        $meta_cache = wp_cache_get($terms_id, 'terms_meta');
    }
    
    if (isset($meta_cache[$key])) {
        if ($single) {
            return maybe_unserialize($meta_cache[$key][0]);
        } else {
            return array_map('maybe_unserialize', $meta_cache[$key]);
        }
    }
    return '';
}
//--------------------------------------------------------
// Add metadata to category
//--------------------------------------------------------
function fn_tspfc_add_category_metadata($terms_id, $meta_key, $meta_value, $unique = false)
{
    global $wpdb;
    
    // expected_slashed ($meta_key)
    $meta_key   = stripslashes($meta_key);
    $meta_value = stripslashes($meta_value);
    
    if ($unique && $wpdb->get_var($wpdb->prepare("SELECT meta_key FROM " . TSPFC_TABLE_NAME . " WHERE meta_key = %s AND terms_id = %d", $meta_key, $terms_id))) return false;
    $meta_value = maybe_serialize($meta_value);
    $wpdb->insert(TSPFC_TABLE_NAME, compact('terms_id', 'meta_key', 'meta_value'));
    
    wp_cache_delete($terms_id, 'terms_meta');
    
    return true;
}
//--------------------------------------------------------
// Delete metadata to category
//--------------------------------------------------------
function fn_tspfc_delete_category_metadata($terms_id, $key, $value = '')
{
    global $wpdb;
    
    // expected_slashed ($key, $value)
    $key     = stripslashes($key);
    $value   = stripslashes($value);
    
    if (empty($value)) {
        $sql1    = $wpdb->prepare("SELECT meta_id FROM " . TSPFC_TABLE_NAME . " WHERE terms_id = %d AND meta_key = %s", $terms_id, $key);
        $meta_id = $wpdb->get_var($sql1);
    } else {
        $sql2    = $wpdb->prepare("SELECT meta_id FROM " . TSPFC_TABLE_NAME . " WHERE terms_id = %d AND meta_key = %s AND meta_value = %s", $terms_id, $key, $value);
        $meta_id = $wpdb->get_var($sql2);
    }
    
    if (!$meta_id) return false;
    if (empty($value)) $wpdb->query($wpdb->prepare("DELETE FROM " . TSPFC_TABLE_NAME . " WHERE terms_id = %d AND meta_key = %s", $terms_id, $key));
    else $wpdb->query($wpdb->prepare("DELETE FROM " . TSPFC_TABLE_NAME . " WHERE terms_id = %d AND meta_key = %s AND meta_value = %s", $terms_id, $key, $value));
    
    wp_cache_delete($terms_id, 'terms_meta');
    
    return true;
}
//--------------------------------------------------------
// Update the category metadata cache
//--------------------------------------------------------
function fn_tspfc_update_category_metadata($terms_id, $meta_key, $meta_value, $prev_value = '')
{
    global $wpdb;
    
    // expected_slashed ($meta_key)
    $meta_key   = stripslashes($meta_key);
    $meta_value = stripslashes($meta_value);
    
    if (!$wpdb->get_var($wpdb->prepare("SELECT meta_key FROM " . TSPFC_TABLE_NAME . " WHERE meta_key = %s AND terms_id = %d", $meta_key, $terms_id))) {
        return fn_tspfc_add_category_metadata($terms_id, $meta_key, $meta_value);
    }
    
    $meta_value = maybe_serialize($meta_value);
    $data       = compact('meta_value');
    $where      = compact('meta_key', 'terms_id');
    
    if (!empty($prev_value)) {
        $prev_value = maybe_serialize($prev_value);
        $where['meta_value']            = $prev_value;
    }
    
    $wpdb->update(TSPFC_TABLE_NAME, $data, $where);
    
    wp_cache_delete($terms_id, 'terms_meta');
    
    return true;
}
//--------------------------------------------------------
// Update the category metadata cache
//--------------------------------------------------------
function fn_tspfc_update_category_meta_cache($terms_ids)
{
    global $wpdb;
    
    if (empty($terms_ids)) return false;
    if (!is_array($terms_ids)) {
        $terms_ids = preg_replace('|[^0-9,]|', '', $terms_ids);
        $terms_ids = explode(',', $terms_ids);
    }
    
    $terms_ids = array_map('intval', $terms_ids);
    $ids       = array();
    
    foreach ((array)$terms_ids as $id) {
        if (false === wp_cache_get($id, 'terms_meta')) $ids[]           = $id;
    }
    
    if (empty($ids)) return false;
    
    // Get terms-meta info
    $id_list   = join(',', $ids);
    $cache     = array();
    if ($meta_list = $wpdb->get_results("SELECT terms_id, meta_key, meta_value FROM " . TSPFC_TABLE_NAME . " WHERE terms_id IN ($id_list) ORDER BY terms_id, meta_key", ARRAY_A)) {
        foreach ((array)$meta_list as $metarow) {
            $mpid      = (int)$metarow['terms_id'];
            $mkey      = $metarow['meta_key'];
            $mval      = $metarow['meta_value'];
            // Force sub keys to be array type:
            if (!isset($cache[$mpid]) || !is_array($cache[$mpid])) $cache[$mpid]           = array();
            if (!isset($cache[$mpid][$mkey]) || !is_array($cache[$mpid][$mkey])) $cache[$mpid][$mkey]           = array();
            // Add a value to the current pid/key:
            $cache[$mpid][$mkey][]           = $mval;
        }
    }
    
    foreach ((array)$ids as $id) {
        if (!isset($cache[$id])) $cache[$id]           = array();
    }
    
    foreach (array_keys($cache) as $terms) wp_cache_set($terms, $cache[$terms], 'terms_meta');
    
    return $cache;
}
//--------------------------------------------------------
// Queue the stylesheet
//--------------------------------------------------------
function fn_tspfc_enqueue_styles()
{
	wp_register_style( 'tspfc-stylesheet', plugins_url( 'tsp-featured-categories.css', __FILE__ ) );
	wp_enqueue_style( 'tspfc-stylesheet' );
}

add_action('wp_print_styles', 'fn_tspfc_enqueue_styles');
//--------------------------------------------------------

//--------------------------------------------------------
// Queue the javascripts
//--------------------------------------------------------
function fn_tspfc_enqueue_scripts()
{
    wp_enqueue_script( 'jquery' ); // Queue in wordpress jquery
    
    wp_enqueue_script( 'jquery-ui-widget' ); // Queue in wordpress jquery.ui

    // Smooth Div Scroll is NOT apart of WordPress library so add
    wp_register_script('tspfc-jquery.smoothDivScroll-1.1.js', plugins_url('js/jquery.smoothDivScroll-1.1.js', __FILE__ ), array('jquery','jquery-ui-widget'));
    wp_enqueue_script('tspfc-jquery.smoothDivScroll-1.1.js');
    
 	// Slider initiation for Smooth Div Scroll
	wp_register_script( 'tspfc-gallery-scripts.js', plugins_url( 'js/gallery-scripts.js', __FILE__ ), array('jquery','jquery-ui-widget','tspfc-jquery.smoothDivScroll-1.1.js' ));
    wp_enqueue_script('tspfc-gallery-scripts.js');

	wp_register_script( 'tspp-skel.min.js', plugins_url( 'includes/js/skel.min.js', __FILE__ ) );
	wp_enqueue_script( 'tspp-skel.min.js' );
}
add_action('wp_enqueue_scripts', 'fn_tspfc_enqueue_scripts');
//--------------------------------------------------------
//--------------------------------------------------------
// Get category thumbnail
//--------------------------------------------------------
function fn_tspfc_get_category_thumbnail($category)
{
    $img = '';
    
    ob_start();
    ob_end_clean();
    
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $img    = $matches[1][0];
    
    if (empty($img)) { //Defines a default image
        $img    = TSPFC_URL_PATH . "images/default.gif";
    }
    
    return $img;
}
//--------------------------------------------------------
// Show simple featured categories
//--------------------------------------------------------
function fn_tspfc_display($args = null, $echo = true)
{
    global $TSPFC_OPTIONS;
	    
	$smarty = new Smarty;
	$smarty->setTemplateDir(TSPFC_TEMPLATE_PATH);
	$smarty->setCompileDir(TSPFC_TEMPLATE_COMPILE_PATH);
	$smarty->setCacheDir(TSPFC_TEMPLATE_CACHE_PATH);

	$return_HTML = "";
	
	$fp = $TSPFC_OPTIONS;
	
	if (!empty($args))
		$fp = array_merge( $TSPFC_OPTIONS, $args );
    
    // User settings
    $title           = $fp['title'];
    $numbercats 	 = $fp['numbercats'];
    $cattype         = $fp['cattype'];
    $parentcat       = $fp['parentcat'];
    $hideempty       = $fp['hideempty'];
    $hidedesc        = $fp['hidedesc'];
    $maxdesc       	 = $fp['maxdesc'];
    $layout          = $fp['layout'];
    $widthbox      	 = $fp['widthbox'];
    $heightbox     	 = $fp['heightbox'];
    $orderby         = $fp['orderby'];
    $widththumb      = $fp['widththumb'];
    $heightthumb     = $fp['heightthumb'];
    $beforetitle 	 = html_entity_decode($fp['beforetitle']);
    $aftertitle  	 = html_entity_decode($fp['aftertitle']);
    
    // If there is a title insert before/after title tags
    if (!empty($title)) {
        $return_HTML .= $beforetitle . $title . $aftertitle;
    }
    	
	$queried_categories = array();
		
	if ($cattype == 'featured')
	{
		// Return all categories
		$cat_args = array('orderby' => $orderby, 'parent' => $parentcat, 'hide_empty' => $hideempty);
		$all_categories = get_terms('category',$cat_args);

		$cat_cnt = 1;
		
		// Add only featured categories
		foreach ($all_categories as $category)
		{
			// Determine if the category is featured
			$featured   = fn_tspfc_get_category_metadata($category->term_id, 'featured', 1);
			
			if ($featured && $cat_cnt <= $numbercats)
				$queried_categories[] = $category;
				
			$cat_cnt++;
		}//endforeach
	}//endif
	else
	{
		// Return all categories with a limit of $numbercats categories
		$cat_args = array('orderby' => $orderby, 'number' => $numbercats, 'parent' => $parentcat, 'hide_empty' => $hideempty);
		$queried_categories = get_terms('category',$cat_args);
	}//endelse
	
	$cat_cnt = 0;
	$num_cats = sizeof($queried_categories);
	
	$cat_width = round(100 / $num_cats).'%';
   
    foreach ($queried_categories as $category)
    {   
	    $image   = fn_tspfc_get_category_metadata($category->term_id, 'image', 1);
	    $url = site_url()."/?cat=".$category->term_id;
	    $title = $category->name;
	    
	   	$desc = $category->description;

		if (strlen($category->description) > $maxdesc && $layout != 2)
	    {
	    	$chop_desc = substr($category->description, 0, $maxdesc);
	    	$desc = $chop_desc."...";
	    }
	    		    
	    // Store values into Smarty
	    foreach ($fp as $key => $val)
	    {
	    	$smarty->assign("$key", $val, true);
	    }
		
        $cat_cnt++;

		if ($cat_cnt == 1)
			$smarty->assign("first_cat", true, true);
		else
			$smarty->assign("first_cat", null, true);
			
		$smarty->assign("title", $title, true);
		$smarty->assign("url", $url, true);
		$smarty->assign("image", $image, true);
		$smarty->assign("target", $target, true);
		$smarty->assign("desc", $desc, true);
		$smarty->assign("cat_term", $category->term_id, true);
		$smarty->assign("cat_width", $cat_width, true);
		
		if ($cat_cnt == $num_cats)
			$smarty->assign("last_cat", true, true);
		else
			$smarty->assign("last_cat", null, true);

        $return_HTML .= $smarty->fetch('layout'.$layout.'.tpl');
        
    }//endforeach
    
    if ($echo)
    	echo $return_HTML;
    else
    	return $return_HTML;
}
//--------------------------------------------------------
// Widget Section
//--------------------------------------------------------
//--------------------------------------------------------
// Register widget
//--------------------------------------------------------
function fn_tspfc_widget_init()
{
    register_widget('TSPFC_Widget');
}
// Add functions to init
add_action('widgets_init', 'fn_tspfc_widget_init');
//--------------------------------------------------------
class TSPFC_Widget extends WP_Widget
{
    //--------------------------------------------------------
    // Constructor
    //--------------------------------------------------------
    function __construct()
    {
        // Get widget options
        $widget_options  = array(
            'classname'                 => 'widget-tsp-featured-categories',
            'description'               => __('This widget allows you to add in your sites themes a list of featured categories.', 'tsp-featured-categories')
        );
        // Get control options
        $control_options = array(
            'width' => 300,
            'height' => 350,
            'id_base' => 'widget-tsp-featured-categories'
        );
        // Create the widget
        parent::__construct('widget-tsp-featured-categories', __('TSP Featured Categories', 'tsp-featured-categories') , $widget_options, $control_options);
    }
    //--------------------------------------------------------
    // initialize the widget
    //--------------------------------------------------------
    function widget($args, $instance)
    {
        extract($args);
        
        $arguments = array(
            'title' 		=> $instance['title'],
            'layout' 		=> $instance['layout'],
            'numbercats' 	=> $instance['numbercats'],
            'cattype' 		=> $instance['cattype'],
            'parentcat' 	=> $instance['parentcat'],
            'hideempty' 	=> $instance['hideempty'],
            'hidedesc'	 	=> $instance['hidedesc'],
            'maxdesc'	 	=> $instance['maxdesc'],
            'widthbox' 		=> $instance['widthbox'],
            'heightbox' 	=> $instance['heightbox'],
            'orderby' 		=> $instance['orderby'],
            'widththumb' 	=> $instance['widththumb'],
            'heightthumb' 	=> $instance['heightthumb'],
            'beforetitle' 	=> $instance['beforetitle'],
            'aftertitle' 	=> $instance['aftertitle']
        );
        
        // Display the widget
        echo $before_widget;
        fn_tspfc_display($arguments);
        echo $after_widget;
    }
    //--------------------------------------------------------
    // update the widget
    //--------------------------------------------------------
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        
        // Update the widget data
        $instance['title']        = strip_tags($new_instance['title']);
        $instance['layout']       = $new_instance['layout'];
        $instance['cattype']      = $new_instance['cattype'];
        $instance['parentcat']    = $new_instance['parentcat'];
        $instance['hideempty']    = $new_instance['hideempty'];
        $instance['hidedesc']     = $new_instance['hidedesc'];
        $instance['maxdesc']      = $new_instance['maxdesc'];
        $instance['numbercats']   = $new_instance['numbercats'];
        $instance['widthbox']  	  = $new_instance['widthbox'];
        $instance['heightbox']    = $new_instance['heightbox'];
        $instance['orderby']      = $new_instance['orderby'];
        $instance['widththumb']   = $new_instance['widththumb'];
        $instance['heightthumb']  = $new_instance['heightthumb'];
        $instance['beforetitle']  = htmlentities($new_instance['beforetitle']);
        $instance['aftertitle']   = htmlentities($new_instance['aftertitle']);

        return $instance;
    }
    //--------------------------------------------------------
    // display the form
    //--------------------------------------------------------
    function form($instance)
    {
	    global $TSPFC_DEFAULTS;
		    
        $instance = wp_parse_args((array)$instance, $TSPFC_DEFAULTS); ?>
      
<!-- Display the title -->
<p>
   <label for="<?php
        echo $this->get_field_id('title'); ?>"><?php
        _e('Title:', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('title'); ?>" name="<?php
        echo $this->get_field_name('title'); ?>" value="<?php
        echo $instance['title']; ?>" style="width:100%;" />
</p>

<!-- Display the number of categories -->
<p>
   <label for="<?php
        echo $this->get_field_id('numbercats'); ?>"><?php
        _e('How many categories do you want to display?', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('numbercats'); ?>" name="<?php
        echo $this->get_field_name('numbercats'); ?>" value="<?php
        echo $instance['numbercats']; ?>" style="width:100%;" />
</p>
<!-- Choose if only featured categories will displayed -->
<p>
   <label for="<?php
        echo $this->get_field_id('cattype'); ?>"><?php
        _e('Category Type.', 'tsp-featured-categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('cattype'); ?>" id="<?php
        echo $this->get_field_id('cattype'); ?>" >
      <option class="level-0" value="all" <?php
        if ($instance['cattype'] == "all") echo " selected='selected'" ?>><?php
        _e('All', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="featured" <?php
        if ($instance['cattype'] == "featured") echo " selected='selected'" ?>><?php
        _e('Featured Only', 'tsp-featured-categories') ?></option>
   </select>
</p>

<!-- Choose show all categories or hide empty ones -->
<p>
   <label for="<?php
        echo $this->get_field_id('hideempty'); ?>"><?php
        _e('Hide Empty Categories?', 'tsp-featured-categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('hideempty'); ?>" id="<?php
        echo $this->get_field_id('hideempty'); ?>" >
      <option class="level-0" value="1" <?php
        if ($instance['hideempty'] == 1) echo " selected='selected'" ?>><?php
        _e('Yes', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="0" <?php
        if ($instance['hideempty'] == 0) echo " selected='selected'" ?>><?php
        _e('No', 'tsp-featured-categories') ?></option>
   </select>
</p>

<!-- Choose to show or hide category descriptions -->
<p>
   <label for="<?php
        echo $this->get_field_id('hidedesc'); ?>"><?php
        _e('Hide Category Descriptions?', 'tsp-featured-categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('hidedesc'); ?>" id="<?php
        echo $this->get_field_id('hidedesc'); ?>" >
      <option class="level-0" value="Y" <?php
        if ($instance['hidedesc'] == "Y") echo " selected='selected'" ?>><?php
        _e('Yes', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="N" <?php
        if ($instance['hidedesc'] == "N") echo " selected='selected'" ?>><?php
        _e('No', 'tsp-featured-categories') ?></option>
   </select>
</p>

<!-- Max number of description chars -->
<p>
   <label for="<?php
        echo $this->get_field_id('maxdesc'); ?>"><?php
        _e('Max chars to display for description', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('maxdesc'); ?>" name="<?php
        echo $this->get_field_name('maxdesc'); ?>" value="<?php
        echo $instance['maxdesc']; ?>" style="width:100%;" />
</p>

<!-- Choose the category's layout -->
<p>
   <label for="<?php
        echo $this->get_field_id('layout'); ?>"><?php
        _e('Choose layout of the category preview:', 'tsp-featured-categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('layout'); ?>" id="<?php
        echo $this->get_field_id('layout'); ?>" >
      <option class="level-0" value="0" <?php
        if ($instance['layout'] == "0") echo " selected='selected'" ?>><?php
        _e('Image (left), Title, Text (right) [Horizontal]', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="1" <?php
        if ($instance['layout'] == "1") echo " selected='selected'" ?>><?php
        _e('Image (left), Title, Text (right) [Vertical]', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="2" <?php
        if ($instance['layout'] == "2") echo " selected='selected'" ?>><?php
        _e('Scrolling Gallery [Horizontal]', 'tsp-featured-categories') ?></option>
   </select>
</p>

<!-- Choose a parent category -->
<p>
   <label for="<?php
        echo $this->get_field_id('parentcat'); ?>"><?php
        _e('Parent category', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('parentcat'); ?>" name="<?php
        echo $this->get_field_name('parentcat'); ?>" value="<?php
        echo $instance['parentcat']; ?>" style="width:20%;" />
</p>


<!-- Choose the Box Width (Scrolling Gallery Only) -->
<p>
   <label for="<?php
        echo $this->get_field_id('widthbox'); ?>"><?php
        _e('Box Width (Scrolling Gallery Only)', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('widthbox'); ?>" name="<?php
        echo $this->get_field_name('widthbox'); ?>" value="<?php
        echo $instance['widthbox']; ?>" style="width:20%;" />
</p>

<!-- Choose the Box Width (Scrolling Gallery Only) -->
<p>
   <label for="<?php
        echo $this->get_field_id('heightbox'); ?>"><?php
        _e('Box Height (Scrolling Gallery Only)', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('heightbox'); ?>" name="<?php
        echo $this->get_field_name('heightbox'); ?>" value="<?php
        echo $instance['heightbox']; ?>" style="width:20%;" />
</p>

<!-- Choose how the categories will be ordered -->
<p>
   <label for="<?php
        echo $this->get_field_id('orderby'); ?>"><?php
        _e('Choose type of order:', 'tsp-featured-categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('orderby'); ?>" id="<?php
        echo $this->get_field_id('orderby'); ?>" >
      <option class="level-0" value="none" <?php
        if ($instance['orderby'] == "none") echo " selected='selected'" ?>><?php
        _e('None', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="name" <?php
        if ($instance['orderby'] == "name") echo " selected='selected'" ?>><?php
        _e('Name', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="count" <?php
        if ($instance['orderby'] == "count") echo " selected='selected'" ?>><?php
        _e('Count', 'tsp-featured-categories') ?></option>
      <option class="level-0" value="id" <?php
        if ($instance['orderby'] == "id") echo " selected='selected'" ?>><?php
        _e('ID', 'tsp-featured-categories') ?></option>
   </select>
</p>

<!-- Choose the thumbnail width -->
<p>
   <label for="<?php
        echo $this->get_field_id('widththumb'); ?>"><?php
        _e('Thumbnail Width', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('widththumb'); ?>" name="<?php
        echo $this->get_field_name('widththumb'); ?>" value="<?php
        echo $instance['widththumb']; ?>" style="width:20%;" />
</p>

<!-- Choose the thumbnail height -->
<p>
   <label for="<?php
        echo $this->get_field_id('heightthumb'); ?>"><?php
        _e('Thumbnail Height', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('heightthumb'); ?>" name="<?php
        echo $this->get_field_name('heightthumb'); ?>" value="<?php
        echo $instance['heightthumb']; ?>" style="width:20%;" />
</p>

<!-- Before title -->
<p>
   <label for="<?php
        echo $this->get_field_id('beforetitle'); ?>"><?php
        _e('HTML Before Title', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('beforetitle'); ?>" name="<?php
        echo $this->get_field_name('beforetitle'); ?>" value="<?php
        echo $instance['beforetitle']; ?>" style="width:20%;" />
</p>

<!-- After title -->
<p>
   <label for="<?php
        echo $this->get_field_id('aftertitle'); ?>"><?php
        _e('HTML After Title', 'tsp-featured-categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('aftertitle'); ?>" name="<?php
        echo $this->get_field_name('aftertitle'); ?>" value="<?php
        echo $instance['aftertitle']; ?>" style="width:20%;" />
</p>
   <?php
    }
} //end class TSPFC_Widget
//---------------------------------------------------
// Category MetaData Section
//---------------------------------------------------
//--------------------------------------------------------
// save the metadata
//--------------------------------------------------------
function fn_tspfc_modify_data($category_ID)
{
    // Check that the meta form is posted
    $tspfc_edit = $_POST["tspfc_edit"];
    
    if (isset($tspfc_edit) && !empty($tspfc_edit)) {
        // featured
        if ((int)$_POST['tspfc_image_category'] == 1) {
            fn_tspfc_add_category_metadata($category_ID, 'featured', 1, TRUE) or fn_tspfc_update_category_metadata($category_ID, 'featured', 1);
        } elseif ((int)$_POST['tspfc_image_category'] == 0) {
            fn_tspfc_delete_category_metadata($category_ID, 'featured');
        }
        
        // image
        if ($_POST['tspfc_image']) {
            fn_tspfc_add_category_metadata($category_ID, 'image', "{$_POST['tspfc_image']}", TRUE) or fn_tspfc_update_category_metadata($category_ID, 'image', "{$_POST['tspfc_image']}");
        } else {
            fn_tspfc_delete_category_metadata($category_ID, 'image');
        }
    }
}
add_action('created_term', 'fn_tspfc_modify_data');
add_action('edit_term', 'fn_tspfc_modify_data');
//--------------------------------------------------------
//--------------------------------------------------------
// Funciton to display form fields to update/save meta data
//--------------------------------------------------------
function fn_tspfc_box($tag)
{
    global $wp_version, $taxonomy;
    $category_ID = $tag->term_id;

    $featured    = fn_tspfc_get_category_metadata($category_ID, 'featured', 1);
    $cur_image   = fn_tspfc_get_category_metadata($category_ID, 'image', 1);

	$smarty = new Smarty;
	$smarty->setTemplateDir(TSPFC_TEMPLATE_PATH);
	$smarty->setCompileDir(TSPFC_TEMPLATE_COMPILE_PATH);
	$smarty->setCacheDir(TSPFC_TEMPLATE_CACHE_PATH);

	$smarty->assign("stylesheet", TSPFC_URL_PATH."/tsp-featured-categories.css", true);
	$smarty->assign("cat_ID", $category_ID, true);
	$smarty->assign("title", __('TSP Featured Categories', 'tsp-featured-categories'), true);
	$smarty->assign("subtitle", __('Featured category?', 'tsp-featured-categories'), true);
	$smarty->assign("featured", $featured, true);
	$smarty->assign("cur_image", $cur_image, true);
	$smarty->assign("field_prefix", "tspfc_image", true);
	
	
    $return_HTML = $smarty->fetch('category_settings.tpl');
    
    echo $return_HTML;
}

add_action('edit_category_form', 'fn_tspfc_box');
//--------------------------------------------------------

?>
