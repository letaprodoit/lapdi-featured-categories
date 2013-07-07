<?php
/*
Plugin Name: 	TSP Featured Categories
Plugin URI: 	http://www.thesoftwarepeople.com/software/plugins/wordpress/featured-categories-for-wordpress.html
Description: 	Featured Categories allows you to add featured categories with images to your blog's website. Featured categories have three (3) layouts and include thumbnails.
Author: 		The Software People
Author URI: 	http://www.thesoftwarepeople.com/
Version: 		1.0
Copyright: 		Copyright © 2013 The Software People, LLC (www.thesoftwarepeople.com). All rights reserved
License: 		APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
*/

// Get the plugin path
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('DIRECTORY_SEPARATOR')) {
    if (strpos(php_uname('s') , 'Win') !== false) define('DIRECTORY_SEPARATOR', '\\');
    else define('DIRECTORY_SEPARATOR', '/');
}

// Set the abs plugin path
define('PLUGIN_ABS_PATH', ABSPATH . PLUGINDIR );
$plugin_abs_path = ABSPATH . PLUGINDIR . DIRECTORY_SEPARATOR . "tsp_featured_categories";
define('TSPFC_ABS_PATH', $plugin_abs_path);
$plugin_url = WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)) . '/';
define('TSPFC_URL_PATH', $plugin_url);

define('TSPFC_TEMPLATE_PATH', TSPFC_ABS_PATH . '/templates');

// Set the file path
$file_path    = $plugin_abs_path . DIRECTORY_SEPARATOR . basename(__FILE__);

// Set the absolute path
$asolute_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
define('TSPFC_ABSPATH', $asolute_path);


include_once(TSPFC_ABS_PATH . '/includes/settings.inc.php');

// Initialization and Hooks
global $wpdb;
global $wp_version;
global $tspfc_version;
global $tspfc_db_version;
global $tspfc_table_name;

$tspfc_version    = '1.0.0';
$tspfc_db_version = '0.0.1';
$tspfc_table_name = $wpdb->prefix . 'termsmeta';

register_activation_hook($file_path, 'fn_tsp_featured_categories_install');
register_uninstall_hook($file_path, 'fn_tsp_featured_categories_uninstall');

//--------------------------------------------------------
// Process shortcodes
//--------------------------------------------------------
function fn_tsp_featured_categories_process_shortcodes($att)
{
	global $TSPFC_OPTIONS;
	
	if ( is_feed() )
		return '[tsp_featured_categories]';

	$options = $TSPFC_OPTIONS;
	
	if (!empty($att))
		$options = array_merge( $TSPFC_OPTIONS, $att );
		     	
	$output = fn_tsp_featured_categories_display($options,false);
	
	return $output;
}

add_shortcode('tsp_featured_categories', 'fn_tsp_featured_categories_process_shortcodes');
//--------------------------------------------------------
// install plugin
//--------------------------------------------------------
function fn_tsp_featured_categories_install()
{
    global $wpdb;
    global $tspfc_table_name;
    global $tspfc_db_version;
    
    // create table on first install
    if ($wpdb->get_var("show tables like '$tspfc_table_name'") != $tspfc_table_name) {
        tsp_featured_categories_create_table($wpdb, $tspfc_table_name);
        add_option("tspfc_db_version", $tspfc_db_version);
        add_option("tspfc_configuration", '');
    }
    
    // On plugin update only the version number is updated.
    $installed_ver = get_option("tspfc_db_version");
    if ($installed_ver != $tspfc_db_version) {
        update_option("tspfc_db_version", $tspfc_db_version);
    }
}
//--------------------------------------------------------
// uninstall plugin
//--------------------------------------------------------
function fn_tsp_featured_categories_uninstall()
{
    global $wpdb;
    global $tspfc_table_name;
    // delete table
    if ($wpdb->get_var("show tables like '$tspfc_table_name'") == $tspfc_table_name) {
        fn_tsp_featured_categories_drop_table($wpdb, $tspfc_table_name);
    }
    delete_option("tspfc_db_version");
    delete_option("tspfc_configuration");
}
//--------------------------------------------------------
// create table to store metadata
//--------------------------------------------------------
function fn_tsp_featured_categories_create_table($wpdb, $table_name)
{
    $sql     = "CREATE TABLE  " . $table_name . " (
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
function fn_tsp_featured_categories_drop_table($wpdb, $table_name)
{
    $sql     = "DROP TABLE  " . $table_name . " ;";
    $results = $wpdb->query($sql);
}
//--------------------------------------------------------
// Get admin scripts
//--------------------------------------------------------
function fn_tsp_featured_categories_get_admin_scripts()
{
    if (is_admin()) {
    
        // queue the styles
        wp_register_style('thickbox-css', '/wp-includes/js/thickbox/thickbox.css');
        wp_enqueue_style('thickbox-css');

        //queue the javascripts
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('quicktags');
        wp_enqueue_script('tsp_featured_categories-scripts.js', TSPFC_URL_PATH.'/js/tsp_featured_categories-scripts.js');
    }
}
// Actions
add_filter('admin_enqueue_scripts', 'fn_tsp_featured_categories_get_admin_scripts');
//--------------------------------------------------------
// Get category metadata
//--------------------------------------------------------
function fn_tsp_featured_categories_get_category_metadata($terms_id, $key, $single = false)
{
    $terms_id   = (int)$terms_id;
    $meta_cache = wp_cache_get($terms_id, 'terms_meta');
    
    if (!$meta_cache) {
        fn_tsp_featured_categories_update_category_meta_cache($terms_id);
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
function fn_tsp_featured_categories_add_category_metadata($terms_id, $meta_key, $meta_value, $unique = false)
{
    global $wpdb;
    global $tspfc_table_name;
    
    // expected_slashed ($meta_key)
    $meta_key   = stripslashes($meta_key);
    $meta_value = stripslashes($meta_value);
    
    if ($unique && $wpdb->get_var($wpdb->prepare("SELECT meta_key FROM $tspfc_table_name WHERE meta_key = %s AND terms_id = %d", $meta_key, $terms_id))) return false;
    $meta_value = maybe_serialize($meta_value);
    $wpdb->insert($tspfc_table_name, compact('terms_id', 'meta_key', 'meta_value'));
    
    wp_cache_delete($terms_id, 'terms_meta');
    
    return true;
}
//--------------------------------------------------------
// Delete metadata to category
//--------------------------------------------------------
function fn_tsp_featured_categories_delete_category_metadata($terms_id, $key, $value = '')
{
    global $wpdb;
    global $tspfc_table_name;
    
    // expected_slashed ($key, $value)
    $key     = stripslashes($key);
    $value   = stripslashes($value);
    
    if (empty($value)) {
        $sql1    = $wpdb->prepare("SELECT meta_id FROM $tspfc_table_name WHERE terms_id = %d AND meta_key = %s", $terms_id, $key);
        $meta_id = $wpdb->get_var($sql1);
    } else {
        $sql2    = $wpdb->prepare("SELECT meta_id FROM $tspfc_table_name WHERE terms_id = %d AND meta_key = %s AND meta_value = %s", $terms_id, $key, $value);
        $meta_id = $wpdb->get_var($sql2);
    }
    
    if (!$meta_id) return false;
    if (empty($value)) $wpdb->query($wpdb->prepare("DELETE FROM $tspfc_table_name WHERE terms_id = %d AND meta_key = %s", $terms_id, $key));
    else $wpdb->query($wpdb->prepare("DELETE FROM $tspfc_table_name WHERE terms_id = %d AND meta_key = %s AND meta_value = %s", $terms_id, $key, $value));
    
    wp_cache_delete($terms_id, 'terms_meta');
    
    return true;
}
//--------------------------------------------------------
// Update the category metadata cache
//--------------------------------------------------------
function fn_tsp_featured_categories_update_category_metadata($terms_id, $meta_key, $meta_value, $prev_value = '')
{
    global $wpdb;
    global $tspfc_table_name;
    
    // expected_slashed ($meta_key)
    $meta_key   = stripslashes($meta_key);
    $meta_value = stripslashes($meta_value);
    
    if (!$wpdb->get_var($wpdb->prepare("SELECT meta_key FROM $tspfc_table_name WHERE meta_key = %s AND terms_id = %d", $meta_key, $terms_id))) {
        return fn_tsp_featured_categories_add_category_metadata($terms_id, $meta_key, $meta_value);
    }
    
    $meta_value = maybe_serialize($meta_value);
    $data       = compact('meta_value');
    $where      = compact('meta_key', 'terms_id');
    
    if (!empty($prev_value)) {
        $prev_value = maybe_serialize($prev_value);
        $where['meta_value']            = $prev_value;
    }
    
    $wpdb->update($tspfc_table_name, $data, $where);
    
    wp_cache_delete($terms_id, 'terms_meta');
    
    return true;
}
//--------------------------------------------------------
// Update the category metadata cache
//--------------------------------------------------------
function fn_tsp_featured_categories_update_category_meta_cache($terms_ids)
{
    global $wpdb;
    global $tspfc_table_name;
    
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
    if ($meta_list = $wpdb->get_results("SELECT terms_id, meta_key, meta_value FROM $tspfc_table_name WHERE terms_id IN ($id_list) ORDER BY terms_id, meta_key", ARRAY_A)) {
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
function fn_tsp_featured_categories_enqueue_styles()
{
    wp_enqueue_style('tsp_featured_categories.css', TSPFC_URL_PATH . 'tsp_featured_categories.css');
}

add_action('wp_print_styles', 'fn_tsp_featured_categories_enqueue_styles');
//--------------------------------------------------------

//--------------------------------------------------------
// Queue the javascripts
//--------------------------------------------------------
function fn_tsp_featured_categories_enqueue_scripts()
{
    wp_enqueue_script( 'jquery' );
    
    wp_register_script('tsp_plugin_jquery.ui.widget.js', plugins_url('js/jquery.ui.widget.js', __FILE__ ), array('jquery'));
    wp_enqueue_script('tsp_plugin_jquery.ui.widget.js');
    
    wp_register_script('tsp_plugin_jquery.smoothDivScroll-1.1.js', plugins_url('js/jquery.smoothDivScroll-1.1.js', __FILE__ ), array('jquery','tsp_plugin_jquery.ui.widget.js'));
    wp_enqueue_script('tsp_plugin_jquery.smoothDivScroll-1.1.js');
    
	wp_register_script( 'tsp_plugin_skel.min.js', plugins_url( 'includes/js/skel.min.js', __FILE__ ) );
	wp_enqueue_script( 'tsp_plugin_skel.min.js' );

    wp_enqueue_script('tsp_featured_categories-gallery-scripts.js', 
    	plugins_url( 'js/tsp_featured_categories-gallery-scripts.js', __FILE__ ), 
    	array('jquery','tsp_plugin_jquery.ui.widget.js','tsp_plugin_jquery.smoothDivScroll-1.1.js','tsp_plugin_skel.min.js'));
}
add_action('wp_enqueue_scripts', 'fn_tsp_featured_categories_enqueue_scripts');
//--------------------------------------------------------
//--------------------------------------------------------
// Get category thumbnail
//--------------------------------------------------------
function fn_tsp_featured_categories_get_category_thumbnail($category)
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
function fn_tsp_featured_categories_display($args = null, $echo = true)
{
    global $TSPFC_OPTIONS;
	    
	$smarty = new Smarty;
	$smarty->setTemplateDir(TSPFC_TEMPLATE_PATH);
	$smarty->setCompileDir(TSPFC_TEMPLATE_PATH.'/compiled/');
	$smarty->setCacheDir(TSPFC_TEMPLATE_PATH.'/cache/');
	
	$return_HTML = "";
	
	$fp = $TSPFC_OPTIONS;
	
	if (!empty($args))
		$fp = array_merge( $TSPFC_OPTIONS, $args );
    
    // User settings
    $title           = $fp['title'];
    $numbercats 	 = $fp['numbercats'];
    $cattype         = $fp['cattype'];
    $parentcat          = $fp['parentcat'];
    $hideempty       = $fp['hideempty'];
    $hidedesc        = $fp['hidedesc'];
    $maxdesc       	 = $fp['maxdesc'];
    $layout          = $fp['layout'];
    $orderby         = $fp['orderby'];
    $widththumb      = $fp['widththumb'];
    $heightthumb     = $fp['heightthumb'];
    $before_title    = $fp['beforetitle'];
    $after_title     = $fp['aftertitle'];
    
    // If there is a title insert before/after title tags
    if (!empty($title)) {
        $return_HTML .= $before_title . $title . $after_title;
    }
    	
	$queried_categories = array();
		
	if ($cattype == 'featured')
	{
		// Return all categories with a limit of $numbercats categories
		$cat_args = array('orderby' => $orderby, 'parent' => $parentcat, 'hide_empty' => $hideempty);
		$all_categories = get_terms('category',$cat_args);

		// Add only featured categories
		foreach ($all_categories as $category)
		{
			// Determine if the category is featured
			$featured   = fn_tsp_featured_categories_get_category_metadata($category->term_id, 'featured', 1);
			
			if ($featured)
				$queried_categories[] = $category;
		}//endforeach
	}//endif
	else
	{
		// Return all categories with a limit of $numbercats categories
		$cat_args = array('orderby' => $orderby, 'number' => $numbercats, 'parent' => $parentcat, 'hide_empty' => $hideempty);
		$queried_categories = get_terms('category',$cat_args);
	}//endelse
	
	$layout2_before_html = '';
	$layout2_html = '';
	$layout2_after_html = '';
	$layout2_img_cnt = 0;
	
	// gallery before & after code
	if ($layout == 2)
	{
		$layout2_before_html = '
		<div id="makeMeScrollable">
			<div class="scrollingHotSpotLeft"></div>
			<div class="scrollingHotSpotRight"></div>
			<div class="scrollWrapper">
				<div class="scrollableArea">
		';
					
		$layout2_after_html = '
			    </div>																		
			</div>
		</div>';
	}
	
	$cat_cnt = 0;
	$num_cats = sizeof($queried_categories);
	
	$cat_width = round(100 / $num_cats).'%';
   
    foreach ($queried_categories as $category)
    {   
	    $image   = fn_tsp_featured_categories_get_category_metadata($category->term_id, 'image', 1);
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
		$smarty->assign("img_count", $layout2_img_cnt++, true);
		$smarty->assign("cat_width", $cat_width, true);
		
		if ($cat_cnt == $num_cats)
			$smarty->assign("last_cat", true, true);
		else
			$smarty->assign("last_cat", null, true);

        if ($layout == 2)
        	$layout2_html .= $smarty->fetch('layout'.$layout.'.tpl');
        else
        	$return_HTML .= $smarty->fetch('layout'.$layout.'.tpl');
        
    }//endforeach
    
    if ($layout == 2)
    {
    	$return_HTML .=  $layout2_before_html;
    	$return_HTML .=  $layout2_html;
    	$return_HTML .=  $layout2_after_html;
    }
    
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
function tsp_featured_categories_widget_init()
{
    register_widget('TSP_Featured_Categories_Widget');
}
// Add functions to init
add_action('widgets_init', 'tsp_featured_categories_widget_init');
//--------------------------------------------------------
class TSP_Featured_Categories_Widget extends WP_Widget
{
    //--------------------------------------------------------
    // Constructor
    //--------------------------------------------------------
    function __construct()
    {
        // Get widget options
        $widget_options  = array(
            'classname'                 => 'widget_tsp_featured_categories',
            'description'               => __('This widget allows you to add in your sites themes a list of featured categories.', 'tsp_featured_categories')
        );
        // Get control options
        $control_options = array(
            'width' => 300,
            'height' => 350,
            'id_base' => 'widget_tsp_featured_categories'
        );
        // Create the widget
        parent::__construct('widget_tsp_featured_categories', __('TSP Featured Categories', 'tsp_featured_categories') , $widget_options, $control_options);
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
            'parentcat' 		=> $instance['parentcat'],
            'hideempty' 	=> $instance['hideempty'],
            'hidedesc'	 	=> $instance['hidedesc'],
            'maxdesc'	 	=> $instance['maxdesc'],
            'orderby' 		=> $instance['orderby'],
            'widththumb' 	=> $instance['widththumb'],
            'heightthumb' 	=> $instance['heightthumb'],
            'beforetitle' 	=> $before_title,
            'aftertitle' 	=> $after_title
        );
        // Display the widget
        echo $before_widget;
        fn_tsp_featured_categories_display($arguments);
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
        $instance['parentcat']       = $new_instance['parentcat'];
        $instance['hideempty']   = $new_instance['hideempty'];
        $instance['hidedesc']     = $new_instance['hidedesc'];
        $instance['maxdesc']      = $new_instance['maxdesc'];
        $instance['numbercats']   = $new_instance['numbercats'];
        $instance['orderby']      = $new_instance['orderby'];
        $instance['widththumb']   = $new_instance['widththumb'];
        $instance['heightthumb']  = $new_instance['heightthumb'];
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
        _e('Title:', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('title'); ?>" name="<?php
        echo $this->get_field_name('title'); ?>" value="<?php
        echo $instance['title']; ?>" style="width:100%;" />
</p>

<!-- Display the number of categories -->
<p>
   <label for="<?php
        echo $this->get_field_id('numbercats'); ?>"><?php
        _e('How many categories do you want to display?', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('numbercats'); ?>" name="<?php
        echo $this->get_field_name('numbercats'); ?>" value="<?php
        echo $instance['numbercats']; ?>" style="width:100%;" />
</p>
<!-- Choose if only featured categories will displayed -->
<p>
   <label for="<?php
        echo $this->get_field_id('cattype'); ?>"><?php
        _e('Category Type.', 'tsp_featured_categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('cattype'); ?>" id="<?php
        echo $this->get_field_id('cattype'); ?>" >
      <option class="level-0" value="all" <?php
        if ($instance['cattype'] == "all") echo " selected='selected'" ?>><?php
        _e('All', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="featured" <?php
        if ($instance['cattype'] == "featured") echo " selected='selected'" ?>><?php
        _e('Featured Only', 'tsp_featured_categories') ?></option>
   </select>
</p>

<!-- Choose show all categories or hide empty ones -->
<p>
   <label for="<?php
        echo $this->get_field_id('hideempty'); ?>"><?php
        _e('Hide Empty Categories?', 'tsp_featured_categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('hideempty'); ?>" id="<?php
        echo $this->get_field_id('hideempty'); ?>" >
      <option class="level-0" value="1" <?php
        if ($instance['hideempty'] == 1) echo " selected='selected'" ?>><?php
        _e('Yes', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="0" <?php
        if ($instance['hideempty'] == 0) echo " selected='selected'" ?>><?php
        _e('No', 'tsp_featured_categories') ?></option>
   </select>
</p>

<!-- Choose to show or hide category descriptions -->
<p>
   <label for="<?php
        echo $this->get_field_id('hidedesc'); ?>"><?php
        _e('Hide Category Descriptions?', 'tsp_featured_categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('hidedesc'); ?>" id="<?php
        echo $this->get_field_id('hidedesc'); ?>" >
      <option class="level-0" value="Y" <?php
        if ($instance['hidedesc'] == "Y") echo " selected='selected'" ?>><?php
        _e('Yes', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="N" <?php
        if ($instance['hidedesc'] == "N") echo " selected='selected'" ?>><?php
        _e('No', 'tsp_featured_categories') ?></option>
   </select>
</p>

<!-- Max number of description chars -->
<p>
   <label for="<?php
        echo $this->get_field_id('maxdesc'); ?>"><?php
        _e('Max chars to display for description', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('maxdesc'); ?>" name="<?php
        echo $this->get_field_name('maxdesc'); ?>" value="<?php
        echo $instance['maxdesc']; ?>" style="width:100%;" />
</p>

<!-- Choose the category's layout -->
<p>
   <label for="<?php
        echo $this->get_field_id('layout'); ?>"><?php
        _e('Choose layout of the category preview:', 'tsp_featured_categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('layout'); ?>" id="<?php
        echo $this->get_field_id('layout'); ?>" >
      <option class="level-0" value="0" <?php
        if ($instance['layout'] == "0") echo " selected='selected'" ?>><?php
        _e('Image (left), Title, Text (right) [Horizontal]', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="1" <?php
        if ($instance['layout'] == "1") echo " selected='selected'" ?>><?php
        _e('Image (left), Title, Text (right) [Vertical]', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="2" <?php
        if ($instance['layout'] == "2") echo " selected='selected'" ?>><?php
        _e('Scrolling Gallery [Horizontal]', 'tsp_featured_categories') ?></option>
   </select>
</p>

<!-- Choose a parent category -->
<p>
   <label for="<?php
        echo $this->get_field_id('parentcat'); ?>"><?php
        _e('Parent categoery', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('parentcat'); ?>" name="<?php
        echo $this->get_field_name('parentcat'); ?>" value="<?php
        echo $instance['parentcat']; ?>" style="width:20%;" />
</p>

<!-- Choose how the categories will be ordered -->
<p>
   <label for="<?php
        echo $this->get_field_id('orderby'); ?>"><?php
        _e('Choose type of order:', 'tsp_featured_categories') ?></label>
   <select name="<?php
        echo $this->get_field_name('orderby'); ?>" id="<?php
        echo $this->get_field_id('orderby'); ?>" >
      <option class="level-0" value="none" <?php
        if ($instance['orderby'] == "none") echo " selected='selected'" ?>><?php
        _e('Random', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="name" <?php
        if ($instance['orderby'] == "name") echo " selected='selected'" ?>><?php
        _e('Name', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="count" <?php
        if ($instance['orderby'] == "count") echo " selected='selected'" ?>><?php
        _e('Count', 'tsp_featured_categories') ?></option>
      <option class="level-0" value="id" <?php
        if ($instance['orderby'] == "id") echo " selected='selected'" ?>><?php
        _e('ID', 'tsp_featured_categories') ?></option>
   </select>
</p>

<!-- Choose the thumbnail width -->
<p>
   <label for="<?php
        echo $this->get_field_id('widththumb'); ?>"><?php
        _e('Thumbnail Width', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('widththumb'); ?>" name="<?php
        echo $this->get_field_name('widththumb'); ?>" value="<?php
        echo $instance['widththumb']; ?>" style="width:20%;" />
</p>

<!-- Choose the thumbnail height -->
<p>
   <label for="<?php
        echo $this->get_field_id('heightthumb'); ?>"><?php
        _e('Thumbnail Height', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('heightthumb'); ?>" name="<?php
        echo $this->get_field_name('heightthumb'); ?>" value="<?php
        echo $instance['heightthumb']; ?>" style="width:20%;" />
</p>

<!-- Before title -->
<p>
   <label for="<?php
        echo $this->get_field_id('beforetitle'); ?>"><?php
        _e('HTML Before Title', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('beforetitle'); ?>" name="<?php
        echo $this->get_field_name('beforetitle'); ?>" value="<?php
        echo $instance['beforetitle']; ?>" style="width:20%;" />
</p>

<!-- After title -->
<p>
   <label for="<?php
        echo $this->get_field_id('aftertitle'); ?>"><?php
        _e('HTML After Title', 'tsp_featured_categories') ?></label>
   <input id="<?php
        echo $this->get_field_id('aftertitle'); ?>" name="<?php
        echo $this->get_field_name('aftertitle'); ?>" value="<?php
        echo $instance['aftertitle']; ?>" style="width:20%;" />
</p>
   <?php
    }
} //end class TSP_Featured_Categories_Widget
//---------------------------------------------------
// Category MetaData Section
//---------------------------------------------------
//--------------------------------------------------------
// save the metadata
//--------------------------------------------------------
function fn_tsp_featured_categories_modify_data($category_ID)
{
    // Check that the meta form is posted
    $tspfc_edit = $_POST["tspfc_edit"];
    
    if (isset($tspfc_edit) && !empty($tspfc_edit)) {
        // featured
        if ((int)$_POST['tspfc_image_category'] == 1) {
            fn_tsp_featured_categories_add_category_metadata($category_ID, 'featured', 1, TRUE) or fn_tsp_featured_categories_update_category_metadata($category_ID, 'featured', 1);
        } elseif ((int)$_POST['tspfc_image_category'] == 0) {
            fn_tsp_featured_categories_delete_category_metadata($category_ID, 'featured');
        }
        
        // image
        if ($_POST['tspfc_image']) {
            fn_tsp_featured_categories_add_category_metadata($category_ID, 'image', "{$_POST['tspfc_image']}", TRUE) or fn_tsp_featured_categories_update_category_metadata($category_ID, 'image', "{$_POST['tspfc_image']}");
        } else {
            fn_tsp_featured_categories_delete_category_metadata($category_ID, 'image');
        }
    }
}
add_action('created_term', 'fn_tsp_featured_categories_modify_data');
add_action('edit_term', 'fn_tsp_featured_categories_modify_data');
//--------------------------------------------------------
//--------------------------------------------------------
// Funciton to display form fields to update/save meta data
//--------------------------------------------------------
function fn_tsp_featured_categories_box($tag)
{
    global $wp_version, $taxonomy;
    $category_ID = $tag->term_id;

    $featured    = fn_tsp_featured_categories_get_category_metadata($category_ID, 'featured', 1);
    $cur_image   = fn_tsp_featured_categories_get_category_metadata($category_ID, 'image', 1);

	$smarty = new Smarty;
	$smarty->setTemplateDir(TSPFC_TEMPLATE_PATH);
	$smarty->setCompileDir(TSPFC_TEMPLATE_PATH.'/compiled/');
	$smarty->setCacheDir(TSPFC_TEMPLATE_PATH.'/cache/');

	$smarty->assign("stylesheet", TSPFC_URL_PATH."/tsp_featured_categories.css", true);
	$smarty->assign("cat_ID", $category_ID, true);
	$smarty->assign("title", __('TSP Featured Categories', 'tsp_featured_categories'), true);
	$smarty->assign("subtitle", __('Featured category?', 'tsp_featured_categories'), true);
	$smarty->assign("featured", $featured, true);
	$smarty->assign("cur_image", $cur_image, true);
	$smarty->assign("field_prefix", "tspfc_image", true);
	
	
    $return_HTML = $smarty->fetch('category_settings.tpl');
    
    echo $return_HTML;
}

add_action('edit_category_form', 'fn_tsp_featured_categories_box');
//--------------------------------------------------------

?>
