<?php	
if ( !class_exists( 'TSP_Easy_Dev_Pro_Terms' ) )
{
	/**
	 * API implementations for TSP Easy Dev Pro's Terms class - Manages term fields and data
	 * @package 	TSP_Easy_Dev_Pro
	 * @author 		sharrondenice, thesoftwarepeople
	 * @author 		Sharron Denice, The Software People
	 * @copyright 	2013 The Software People
	 * @license 	APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version 	1.0
	 */
	final class TSP_Easy_Dev_Pro_Terms
	{
		/**
		 * A reference to the TSP_Easy_Dev_Pro_Options object
		 *
		 * @var object
		 */
		private $options	= null;
		/**
		 * A boolean to turn debugging on for this class
		 *
		 * @ignore
		 *
		 * @var boolean
		 */
		private $debugging 	= false;
		
		/**
		 * Constructor
		 *
		 * @since 1.0
		 *
		 * @param object $options Required - reference to the TSP_Easy_Dev_Pro_Options class
		 *
		 * @return none
		 */
		public function __construct( $options )
		{
			$this->options = $options;
			
			add_action( 'created_term', 			array( $this, 'update_term_metadata'));
			add_action( 'edit_term', 				array( $this, 'update_term_metadata'));
			add_action( 'edit_category_form', 		array( $this, 'load_term_metadata_box'));
		}//end __construct
	
		/**
		 *  Display the form fields on the category page
		 *
		 * @ignore - Must be public, used by WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param object $term Required - Passed in from hook the term object;
		 *
		 * @return output to screen
		 */
		public function load_term_metadata_box( $term )
		{
			$term_ID = null;
			
			if (isset ( $term->term_id ))
			{
				$term_ID = $term->term_id;
			}//end if
				
			$term_fields = get_option( $this->options->get_value('term-fields-option-name') );
			$defaults = new TSP_Easy_Dev_Data ( $term_fields );

		    $default_fields = $defaults->get_values();
		    
		    $category_data = array();
		    
		    // loop through the default fields in order to get all of the post
		    // data in the database
		    if ( ! empty ( $default_fields ))
		    {
			    // if this is NOT a new category entry then retrieve the associated data
			    // for this category
			    if (!empty ( $term_ID ))
			    {
				    $all_term_data = get_option( $this->options->get_value('term-data-option-name') );
				    
			        if (!empty ( $all_term_data ))
			        {
					    foreach ( $default_fields as $key => $value )
					    {
							if ( array_key_exists( $term_ID, $all_term_data ) )
							{
								if ( array_key_exists( $key, $all_term_data[$term_ID] ) )
								{
									$category_data[$key]    = $all_term_data[$term_ID][$key];
								}//end if
							}//end if
					    }//end foreach
			        }//end if
			    }//end if
			    
			    $defaults->set_values( $category_data );
			    $form_fields = $defaults->get_values( true );
		
				$smarty = new TSP_Easy_Dev_Pro_Smarty( $this->options->get_value('smarty_template_dirs'), 
					$this->options->get_value('smarty_cache_dir'), 
					$this->options->get_value('smarty_compiled_dir'), true );
					
		    	$smarty->assign( 'form_fields', 	$form_fields );
		    	$smarty->assign( 'ID', 				$term_ID );
		    	$smarty->assign( 'name', 			$this->options->get_value('name') );
		    	$smarty->assign( 'title', 			$this->options->get_value('title') );
				$smarty->display( 'pro-default-form-with-wrapper.tpl' );
		    }//end if
		}//end add_term_metadata_fields
	
		/**
		 *  Modify category data
		 *
		 * @ignore - Must be public, used by WordPress hooks
		 *
		 * @since 1.0
		 *
		 * @param integer $term_ID Required the id of the term/category to update
		 *
		 * @return none
		 */
		public function update_term_metadata ( $term_ID )
		{
		    if (!empty ( $term_ID ))
		    {
				$term_fields = get_option( $this->options->get_value('term-fields-option-name') );
				$defaults = new TSP_Easy_Dev_Data ( $term_fields );
	
			   	$defaults->set_values( $_POST );
			    
			    $form_fields = $defaults->get_values();
			    
			    // store the saved data for this term
			    if (!empty ( $form_fields ))
			    {
				    $term_data = array();
				    
				    foreach ( $form_fields as $key => $value )
				    {
					    $terms_data[$term_ID][$key] = $value;
				    }//end foreach
			    }//end if
			    
			    // get the stored term meta data
				$all_term_data = get_option( $this->options->get_value('term-data-option-name') );
				
				$all_term_data[$term_ID] = $terms_data[$term_ID];
				
				update_option( $this->options->get_value('term-data-option-name'), $all_term_data );
		    }//end if
		}//end modify_term_data
	
		/**
		 * Return an array of the term fields
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param int $ID  - the post's ID
		 *
		 * @return array $post_fields return an array of fiels stored in the post
		 */
		public function get_term_fields( $ID )
		{
			$new_term_fields = array();
			       
			$term_fields = get_option( $this->options->get_value('term-fields-option-name') );
			$defaults = new TSP_Easy_Dev_Data ( $term_fields );
			
			$fields = $defaults->get_values();
	
	        if (!empty ( $fields ))
	        {
		        $all_term_data =  $this->get_term_metadata();
		        
		        if (!empty ( $all_term_data ))
		        {
			        foreach ( $fields as $key => $default_value )
			        {
				        $value = null;
				        
						if ( array_key_exists( $ID, $all_term_data ) )
						{
							if ( array_key_exists( $key, $all_term_data[$ID] ) )
							{
								$value = $all_term_data[$ID][$key];
							}//end if
						}//end if
	
				        if (!empty( $value ))
				        	$new_term_fields[$key] = $value;
				        else
				        	$new_term_fields[$key] = "";
			        }//end foreach
		        }//end if
		        
	        }//endif
	        		
			return $new_term_fields;
		}//end get_post_fields

		/**
		 * Return the term metadata
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return array - Data stored for the all terms
		 */
		public function get_term_metadata()
		{
			return get_option( $this->options->get_value('term-data-option-name') );
		}//end get_term_post
	}//end TSP_Easy_Dev_Pro_Terms
}//end if
