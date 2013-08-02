<?php				
/**
 * TSP_Easy_Dev_Options_Featured_Categories - Extends the TSP_Easy_Dev_Pro_Options Class
 * @package TSP_Easy_Dev
 * @author sharrondenice, thesoftwarepeople
 * @author Sharron Denice, The Software People
 * @copyright 2013 The Software People
 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
 */

/**
 * @method void display_parent_page()
 * @method void display_plugin_options_page()
 */
class TSP_Easy_Dev_Options_Featured_Categories extends TSP_Easy_Dev_Pro_Options
{
	/**
	 * Display all the plugins that The Software People has released
	 *
	 * @since 1.1.0
	 *
	 * @param none
	 *
	 * @return output to stdout
	 */
	public function display_parent_page()
	{
		$active_plugins			= get_option('active_plugins');
		$all_plugins 			= get_plugins();
	
		$free_active_plugins 	= array();
		$free_installed_plugins = array();
		$free_recommend_plugins = array();
		
		$pro_active_plugins 	= array();
		$pro_installed_plugins 	= array();
		$pro_recommend_plugins 	= array();
		
		$json 					= file_get_contents( $this->get_value('plugin_list') );
		$tsp_plugins 			= json_decode($json);
		
		foreach ( $tsp_plugins->{'plugins'} as $plugin_data )
		{
			if ( $plugin_data->{'type'} == 'FREE' )
			{
				if ( in_array($plugin_data->{'name'}, $active_plugins ) )
				{
					$free_active_plugins[] = (array)$plugin_data;
				}//endif
				elseif ( array_key_exists($plugin_data->{'name'}, $all_plugins ) )
				{
					$free_installed_plugins[] = (array)$plugin_data;
				}//end elseif
				else
				{
					$free_recommend_plugins[] = (array)$plugin_data;
				}//endelse
			}//endif
			elseif ( $plugin_data->{'type'} == 'PRO' )
			{
				if ( in_array($plugin_data->{'name'}, $active_plugins ) )
				{
					$pro_active_plugins[] = (array)$plugin_data;
				}//endif
				elseif ( array_key_exists($plugin_data->{'name'}, $all_plugins ) )
				{
					$pro_installed_plugins[] = (array)$plugin_data;
				}//endelseif
				else
				{
					$pro_recommend_plugins[] = (array)$plugin_data;
				}//endelse
			}//endelseif
		}//endforeach
		
		$free_active_count									= count($free_active_plugins);
		$free_installed_count 								= count($free_installed_plugins);
		$free_recommend_count 								= count($free_recommend_plugins);

		$free_total											= $free_active_count + $free_installed_count + $free_recommend_count;

		$pro_active_count									= count($pro_active_plugins);
		$pro_installed_count 								= count($pro_installed_plugins);
		$pro_recommend_count 								= count($pro_recommend_plugins);
		
		$pro_total											= $pro_active_count + $pro_installed_count + $pro_recommend_count;
				
		// Display settings to screen
		$smarty = new TSP_Easy_Dev_Smarty( $this->get_value('smarty_template_dirs'), 
			$this->get_value('smarty_cache_dir'), 
			$this->get_value('smarty_compiled_dir'), true );
			
		$smarty->assign( 'free_active_count',		$free_active_count);
		$smarty->assign( 'free_installed_count',	$free_installed_count);
		$smarty->assign( 'free_recommend_count',	$free_recommend_count);

		$smarty->assign( 'pro_active_count',		$pro_active_count);
		$smarty->assign( 'pro_installed_count',		$pro_installed_count);
		$smarty->assign( 'pro_recommend_count',		$pro_recommend_count);
		
		$smarty->assign( 'free_active_plugins',		$free_active_plugins);
		$smarty->assign( 'free_installed_plugins',	$free_installed_plugins);
		$smarty->assign( 'free_recommend_plugins',	$free_recommend_plugins);

		$smarty->assign( 'pro_active_plugins',		$pro_active_plugins);
		$smarty->assign( 'pro_installed_plugins',	$pro_installed_plugins);
		$smarty->assign( 'pro_recommend_plugins',	$pro_recommend_plugins);

		$smarty->assign( 'free_total',				$free_total);
		$smarty->assign( 'pro_total',				$pro_total);

		$smarty->assign( 'title',					"WordPress Plugins by The Software People");
		$smarty->assign( 'contact_url',				$this->get_value('contact_url'));

		$smarty->display( 'easy-dev-parent-page.tpl');
	}//end ad_menu
	
	/**
	 * Implements the settings_page to display settings specific to this plugin
	 *
	 * @since 1.1.0
	 *
	 * @param none
	 *
	 * @return output to screen
	 */
	function display_plugin_options_page() 
	{
		$message = "";
		
		$error = "";
		
		// get settings from database
		$shortcode_fields = get_option( $this->get_value('shortcode-fields-option-name') );
		
		$defaults = new TSP_Easy_Dev_Data ( $shortcode_fields );

		$form = null;
		if ( array_key_exists( $this->get_value('name') . '_form_submit', $_REQUEST ))
		{
			$form = $_REQUEST[ $this->get_value('name') . '_form_submit'];
		}//endif
				
		// Save data for settings page
		if( isset( $form ) && check_admin_referer( $this->get_value('name'), $this->get_value('name') . '_nonce_name' ) ) 
		{
			$defaults->set_values( $_POST );
			$shortcode_fields = $defaults->get();
			
			update_option( $this->get_value('shortcode-fields-option-name'), $shortcode_fields );
			
			$message = __( "Options saved.", $this->get_value('name') );
		}
		
		$form_fields = $defaults->get_values( true );

		// Display settings to screen
		$smarty = new TSP_Easy_Dev_Smarty( $this->get_value('smarty_template_dirs'), 
			$this->get_value('smarty_cache_dir'), 
			$this->get_value('smarty_compiled_dir'), true );

		$smarty->assign( 'form_fields',				$form_fields);
		$smarty->assign( 'message',					$message);
		$smarty->assign( 'error',					$error);
		$smarty->assign( 'form',					$form);
		$smarty->assign( 'plugin_name',				$this->get_value('name'));
		$smarty->assign( 'nonce_name',				wp_nonce_field( $this->get_value('name'), $this->get_value('name').'_nonce_name' ));
		
		$smarty->display( $this->get_value('name') . '_shortcode_settings.tpl');
				
	}//end settings_page
	
}//end TSP_Easy_Dev_Options_Featured_Categories


/**
 * TSP_Easy_Dev_Widget_Featured_Categories - Extends the TSP_Easy_Dev_Widget Class
 * @package TSPEasyPlugin
 * @author sharrondenice, thesoftwarepeople
 * @author Sharron Denice, The Software People
 * @copyright 2013 The Software People
 * @license APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version $Id: [FILE] [] [DATE] [TIME] [USER] $
 */

/**
 * Extends the TSP_Easy_Dev_Widget_Facepile Class
 *
 * original author: Sharron Denice
 */
class TSP_Easy_Dev_Widget_Featured_Categories extends TSP_Easy_Dev_Widget
{
	/**
	 * Constructor
	 */	
	public function __construct() 
	{
		add_filter( get_class()  .'-init', 	array( $this, 'init'), 10, 1 );
		add_action( 'admin_init', 			array( $this, 'copy_term_metadata' ));
	}//end __construct

	
	/**
	 * Function added to filter to allow initialization of widget
	 *
	 * @since 1.1.0
	 *
	 * @param object $options Required - pass in reference to options class
	 *
	 * @return none
	 */
	public function init( $options )
	{
        // Create the widget
		parent::__construct( $options );
	}//end init

	/**
	 * Override required of form function to display widget information
	 *
	 * @since 1.1.0
	 *
	 * @param array $instance Required - array of current values
	 *
	 * @return display to widget box
	 */
	public function display_form( $fields )
	{
		$smarty = new TSP_Easy_Dev_Smarty( $this->options->get_value('smarty_template_dirs'), 
			$this->options->get_value('smarty_cache_dir'), 
			$this->options->get_value('smarty_compiled_dir'), true );

    	$smarty->assign( 'form_fields', $fields );
    	$smarty->assign( 'class', 'widefat' );
		$smarty->display( 'default_form.tpl' );
	}//end form
	
	/**
	 * Implementation (required) to print widget & shortcode information to screen
	 *
	 * @since 1.1.0
	 *
	 * @param array $fields  - the settings to display
	 * @param boolean $echo Optional - if false returns output instead of displaying to screen
	 *
	 * @return string $output if echo is true displays to screen else returns string
	 */
	public function display_widget( $fields, $echo = true )
	{
	    extract ( $fields );
	    
		$return_HTML = "";

	    // If there is a title insert before/after title tags
	    if (!empty($title)) {
	        $return_HTML .= $before_title . $title . $after_title;
	    }
	    	    
		$queried_categories = array();
		
		$pro_term = $this->options->get_pro_term();
		
		$all_term_data = $pro_term->get_term_metadata();

		// If the user wants to display only featured categories then add only featured categories to the array
		// else add them all
		if ($cat_type == 'featured')
		{
			// Return all categories
			$cat_args = array( 'orderby' => $order_by, 'child_of' => $parent_cat, 'hide_empty' => ($hide_empty == 'Y') ? 1 : 0 );
			$all_categories = get_terms( 'category', $cat_args );
	
			$cat_cnt = 1;
			
			// Add only featured categories
			foreach ($all_categories as $category)
			{
				$term_ID = $category->term_id;
				$featured = null;
				
				// Determine if the category is featured
				if ( array_key_exists( $term_ID, $all_term_data ) )
				{
					if ( array_key_exists( 'featured', $all_term_data[$term_ID] ) )
					{
						$featured   = $all_term_data[$term_ID]['featured'];
					}//end if
				}//end if
				
				if ($featured && $cat_cnt <= $number_cats)
				{
					$queried_categories[] = $category;
				}//end if
					
				$cat_cnt++;
			}//endforeach
		}//endif
		else
		{
			// Return all categories with a limit of $number_cats categories
			$cat_args = array('orderby' => $order_by, 'number' => $number_cats, 'child_of' => $parent_cat, 'hide_empty' => ($hide_empty == 'Y') ? 1 : 0 );
			$queried_categories = get_terms('category',$cat_args);
		}//endelse
		
		// Now display the category
		$cat_cnt = 0;
		$num_cats = sizeof($queried_categories);
		
		$cat_width = '100%';

		if ( $num_cats > 1 )
		{
			$cat_width = round(95 / $num_cats).'%'; //only take up 95% of the area instead of 100%
		}//end if
	   
	    foreach ($queried_categories as $category)
	    {   
			$term_ID = $category->term_id;

	        // get the fields stored in the database for this post
	        $term_fields = $pro_term->get_term_fields( $term_ID );

		    $url = site_url()."/?cat=".$term_ID;
		    $title = $category->name;
		    
		   	$desc = $category->description;
	
			if (strlen($category->description) > $max_desc && $layout != 2)
		    {
		    	$chop_desc = substr($category->description, 0, $max_desc);
		    	$desc = $chop_desc."...";
		    }//end if
		    		    
	        $cat_cnt++;
	
			$smarty = new TSP_Easy_Dev_Pro_Smarty( $this->options->get_value('smarty_template_dirs'), 
				$this->options->get_value('smarty_cache_dir'), 
				$this->options->get_value('smarty_compiled_dir'), true );
	
		    // Store values into Smarty
		    foreach ($fields as $key => $val)
		    {
		    	$smarty->assign("$key", $val, true);
		    }//end foreach
			
		    // Store values into Smarty
		    foreach ($term_fields as $key => $val)
		    {
		    	$smarty->assign("$key", $val, true);
		    }//end foreach
		    
		    if ( !array_key_exists('image', $term_fields ))
			{
				$smarty->assign("image", 			null);
		    }//end if	
		    
		    
			$smarty->assign("title", 			$title, true);
			$smarty->assign("url", 				$url, true);
			$smarty->assign("desc", 			$desc, true);
			$smarty->assign("cat_term", 		$category->term_id, true);
			$smarty->assign("adj_thumb_height",	round($thumb_height / 2), true);
			$smarty->assign("cat_width",		$cat_width, true);
			$smarty->assign("first_cat",		($cat_cnt == 1) ? true : null, true);
			$smarty->assign("last_cat",			($cat_cnt == $num_cats) ? true : null, true);
				
	        $return_HTML .= $smarty->fetch( $this->options->get_value('name') . '_layout'.$layout.'.tpl' );
	        
	    }//endforeach
	    
	    if ($echo)
	    	echo $return_HTML;
	    
	    return $return_HTML;
	}//end display
			
	/**
	 * Copy the data ffrom the category metadata to the options table
	 *
	 * @ignore - Must be public, used by WordPress hooks
	 *
	 * @since 1.0
	 *
	 * @return none
	 */
	public function copy_term_metadata()
	{
	    global $wpdb;
	    
	    $tables_copied = true;
	    
	    $old_tables = array( $wpdb->prefix . 'termsmeta', $wpdb->prefix . 'tspfc_termsmeta');
	    
	    $all_term_data = $this->options->get_pro_term()->get_term_metadata();
	    		
		foreach ( $old_tables as $table_name )
	    {
		    // if the category table exists then this is the first time we are copying the data
		    if ($wpdb->get_var("show tables like '{$table_name}'") == $table_name ) 
		    {
		        $tables_copied = false;
		    }//end if

			// if old table exists and this is a new install, copy its contents into the new table
		    if ( !$tables_copied ) 
		    {
			    $sql     = "SELECT * FROM `$table_name` WHERE `meta_key` = 'featured' OR `meta_key` = 'image';";
			    $entries = $wpdb->get_results($sql, ARRAY_A);
			    
			    foreach ( $entries as $e ) 
			    {
			    	
			    	$id = $e['terms_id'];
			    	$key = $e['meta_key'];
			    	
			    	$all_term_data[$id][$key] = $e['meta_value'];
			    }//endforeach
			    
			    $this->drop_table( $table_name );
		    }//end if
	    }//end foreach
	    
		if (! $tables_copied )
		{
			update_option( $this->options->get_value('term-data-option-name'), $all_term_data );
		}//end if
	}//end copy_term_metadata_table

	
	/**
	 *  Implementation to remove category metadata tables
	 *
	 * @since 1.0
	 *
	 * @param none
	 *
	 * @return none
	 */
	private function drop_table ( $table_name )
	{
	    global $wpdb;
	    
	    $wpdb->query( 'DROP TABLE IF EXISTS ' . $table_name );
	}//end remove_term_table
}//end TSP_Easy_Dev_Widget_Featured_Categories
?>