<?php									
/* @group Easy Dev Package settings, all plugins use the same settings, DO NOT EDIT */
if ( !defined( 'TSP_PARENT_NAME' )) define('TSP_PARENT_NAME', 			'tsp_plugins');
if ( !defined( 'TSP_PARENT_TITLE' )) define('TSP_PARENT_TITLE', 		'TSP Plugins');
if ( !defined( 'TSP_PARENT_MENU_POS' )) define('TSP_PARENT_MENU_POS', 	2617638.180);
/* @end */

// Get the plugin path
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');

if (!defined('DS')) {
    if (strpos(php_uname('s') , 'Win') !== false) define('DS', '\\');
    else define('DS', '/');
}//endif

$easy_dev_settings = get_plugin_data( TSPFC_PLUGIN_FILE, false, false );
$easy_dev_settings['parent_name']			= TSP_PARENT_NAME;
$easy_dev_settings['parent_title']			= TSP_PARENT_TITLE;
$easy_dev_settings['menu_pos'] 				= TSP_PARENT_MENU_POS;

$easy_dev_settings['name'] 					= TSPFC_PLUGIN_NAME;
$easy_dev_settings['key'] 					= $easy_dev_settings['TextDomain'];
$easy_dev_settings['title']					= $easy_dev_settings['Name'];
$easy_dev_settings['title_short']			= $easy_dev_settings['Name'];

$easy_dev_settings['option_prefix']			= TSPFC_PLUGIN_NAME . "-option";
$easy_dev_settings['option_prefix_old']		= $easy_dev_settings['TextDomain']."_options";

$easy_dev_settings['file']	 				= TSPFC_PLUGIN_FILE;
$easy_dev_settings['base_name']	 			= TSPFC_PLUGIN_BASE_NAME;

$easy_dev_settings['widget_width']	 		= 300;
$easy_dev_settings['widget_height'] 		= 350;

$easy_dev_settings['smarty_template_dirs']	= array( TSPFC_PLUGIN_PATH . 'templates', TSP_EASY_DEV_ASSETS_TEMPLATES_PATH, TSP_EASY_DEV_PRO_ASSETS_TEMPLATES_PATH );
$easy_dev_settings['smarty_compiled_dir']  	= TSP_EASY_DEV_TMP_PATH . TSPFC_PLUGIN_NAME . DS . 'compiled';
$easy_dev_settings['smarty_cache_dir'] 		= TSP_EASY_DEV_TMP_PATH . TSPFC_PLUGIN_NAME . DS . 'cache';

//* Custom globals *//
$easy_dev_settings['title_short']			= preg_replace("/TSP/","",$easy_dev_settings['Name']);
$easy_dev_settings['store_url']	 			= 'http://www.thesoftwarepeople.com/software/plugins/wordpress';
$easy_dev_settings['wp_query'] 				= '/wp-admin/plugin-install.php?tab=search&type=term&s';
$easy_dev_settings['contact_url'] 			= 'http://www.thesoftwarepeople.com/about-us/contact-us.html';
$easy_dev_settings['plugin_list']			= 'http://www.thesoftwarepeople.com/plugins/wordpress/plugins.json';
//* Custom globals *//

$easy_dev_settings['plugin_options']		= array(
	'category_fields'					=> array(		
		'featured' 	=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Featured category?', 
			'value' 		=> 0,
			'options'		=> array ('Yes' => 1, 'No' => 0),
		),		
		'image' 		=> array( 
			'type' 			=> 'IMAGE', 
			'label' 		=> 'Category Thumbnail', 
			'value' 		=> null,
		),		
	),
	'widget_fields'						=> array(		
		'title' 		=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Title', 
			'value' 		=> 'TSP Featured Categories',
		),		
		'title_pos' 	=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Title is below or above image?:', 
			'value' 		=> 0,
			'options'		=> array( 
									'below'	=> 0,
									'above'	=> 1 ),
		),		
		'cat_type' 		=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Category Type', 
			'value' 		=> 'all',
			'old_labels'	=> array('cattype'),
			'options'		=> array(
									'All'	 	=>	'all',
									'Featured'	=>	'featured'),
		),		
		'number_cats' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'How many categories do you want to display?', 
			'value' 		=> 5,
			'old_labels'	=> array ('numbercats'),
		),		
		'cat_ids' 		=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Category IDs to display', 
			'value' 		=> '',
		),		
		'parent_cat' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Category ID of Parent Category', 
			'value' 		=> '',
			'old_labels'	=> array ('parentcat'),
		),		
		'hide_desc' 	=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Hide Category Description?', 
			'value' 		=> 'N',
			'old_labels'	=> array ('hidedesc'),
			'options'		=> array ('Yes' => 'Y', 'No' => 'N'),
		),		
		'hide_empty' 	=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Hide Empty Categories?', 
			'value' 		=> 'Y',
			'old_labels'	=> array ('hideempty'),
			'options'		=> array ('Yes' => 'Y', 'No' => 'N'),
		),		
		'max_desc' 		=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Max chars to display for description', 
			'value' 		=> 60,
			'old_labels'	=> array ('maxdesc'),
		),		
		'layout' 		=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Choose the category layout:', 
			'value' 		=> 0,
			'options'		=> array( 
									'Image (left), Title, Text (right) [Horizontal]'	=> 0,
									'Image (left), Title, Text (right) [Vertical]'		=> 1,
									'Scrolling Gallery [Horizontal]'					=> 2),
		),		
		'order_by' 		=> array( 
			'type' 			=> 'SELECT', 
			'label' 		=> 'Order by', 
			'value' 		=> 0,
			'old_labels'	=> array('orderby'),
			'options'		=> array(
									'None'	 	=>	'none',
									'Name'		=>	'name',
									'Count'		=>	'count',
									'ID'		=>	'ID'),
		),		
		'box_width' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Box Width (Scrolling Gallery Only)', 
			'value' 		=> 500,
			'old_labels'	=> array ('widthbox'),
		),		
		'box_height' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Box Height (Scrolling Gallery Only)', 
			'value' 		=> 80,
			'old_labels'	=> array ('heightbox'),
		),		
		'thumb_width' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Thumbnail Width', 
			'value' 		=> 80,
			'old_labels'	=> array ('widththumb'),
		),		
		'thumb_height' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'Thumbnail Height', 
			'value' 		=> 80,
			'old_labels'	=> array ('heightthumb'),
		),		
		'before_title' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'HTML Before Title', 
			'value' 		=> '<h3 class="widget-title">',
			'html'			=> true,
			'old_labels'	=> array ('beforetitle'),
		),		
		'after_title' 	=> array( 
			'type' 			=> 'INPUT', 
			'label' 		=> 'HTML After Title', 
			'value' 		=> '</h3>',
			'html'			=> true,
			'old_labels'	=> array ('aftertitle'),
		)
	),
);

$easy_dev_settings['plugin_options']['shortcode_fields'] = $easy_dev_settings['plugin_options']['widget_fields'];		
?>