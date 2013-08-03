<?php	
if ( !class_exists( 'TSP_Easy_Dev_Pro_Options' ) )
{
	/**
	 * API implementations for TSP Easy Dev's Options class
	 * @package 	TSP_Easy_Dev_Pro
	 * @author 		sharrondenice, thesoftwarepeople
	 * @author 		Sharron Denice, The Software People
	 * @copyright 	2013 The Software People
	 * @license 	APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version 	1.0
	 */
	abstract class TSP_Easy_Dev_Pro_Options extends TSP_Easy_Dev_Options
	{
		/**
		 * Does the plugin save post options?
		 *
		 * @var boolean
		 */
		public $has_post_options = false;
		/**
		 * Does the plugin save term/category options?
		 *
		 * @var boolean
		 */
		public $has_term_options = false;
		
		/**
		 * A reference to the TSP_Easy_Dev_Pro_Posts object
		 *
		 * @var object
		 */
		private $pro_post 		= null;
		/**
		 * A reference to the TSP_Easy_Dev_Pro_Terms object
		 *
		 * @var object
		 */
		private $pro_term 		= null;
		/**
		 * A boolean to turn debugging on for this class
		 *
		 * @ignore
		 *
		 * @var boolean
		 */
		private $debugging 		= false;
		

		/**
		 * Intialize the options class
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function init ()
		{
			parent::init();

			if ( $this->has_term_options )
			{
				$this->pro_term = new TSP_Easy_Dev_Pro_Terms( $this );
			}//endif
						
			if ( $this->has_post_options )
			{
				$this->pro_post = new TSP_Easy_Dev_Pro_Posts( $this );
			}//endif

			$this->register_options();
		}//end register_options

		/**
		 * Create settings entry in database
		 *
		 * @ignore
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function register_options()
		{
			$prefix = $this->get_value('option_prefix');
			
			$this->set_value('term-fields-option-name', 	$prefix.'-term-fields');
			$this->set_value('term-data-option-name', 		$prefix.'-term-data');

			$this->set_value('post-fields-option-name', 	$prefix.'-post-fields');
						
			$database_post_fields 	= get_option( $this->get_value('post-fields-option-name') );
			$database_term_fields 	= get_option( $this->get_value('term-fields-option-name') );
			
			$default_post_fields 	= $this->get_value('post_fields');
			$default_term_fields 	= $this->get_value('category_fields');

			// if has options and the database options != the current options
			// then if database options are not empty copy them to the default fields and update
			// if the database option does not exist add default
			if( $this->has_post_options &&  $database_post_fields != $default_post_fields ) 
			{
				if (!empty ( $database_post_fields ) )
				{
					$default_post_fields = array_merge( $default_post_fields, $database_post_fields);
					update_option( $this->get_value('post-fields-option-name'), $default_post_fields );
				}//end if
				else
				{
					add_option( $this->get_value('post-fields-option-name'), $default_post_fields );
				}//end else
			}//end if

			// if has options and the database options != the current options
			// then if database options are not empty copy them to the default fields and update
			// if the database option does not exist add default
			if( $this->has_term_options &&  $database_term_fields != $default_term_fields ) 
			{
				if (!empty ( $database_term_fields ) )
				{
					$default_term_fields = array_merge( $default_term_fields, $database_term_fields);
					update_option( $this->get_value('term-fields-option-name'), $default_term_fields );
				}//end if
				else
				{
					add_option( $this->get_value('term-fields-option-name'), $default_term_fields );
				}//end else
			}//end if

			// if option was not found this means the plugin is being installed
			// ONLY overwrite the user data if none is stored
			if( $this->has_term_options && !get_option( $this->get_value('term-data-option-name') ) ) 
			{
				add_option( $this->get_value('term-data-option-name'), null );
			}//end if
		}//end register_options
		
					
		/**
		 * Remove settings entry in database
		 *
		 * @ignore
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function deregister_options()
		{
			parent::deregister_options();
			
			// delete widget fields & data
			if( $this->has_post_options && get_option( $this->get_value('post-fields-option-name') ) ) 
			{
				delete_option( $this->get_value('post-fields-option-name') );
			}//end if

			// delete settings fields & data
			if( $this->has_term_options && get_option( $this->get_value('term-fields-option-name') ) ) 
			{
				delete_option( $this->get_value('term-fields-option-name') );
				delete_option( $this->get_value('term-data-option-name') );
			}//end if
		}//end deregister_options
		

		/**
		 * Get reference to pro post options object
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return TSP_Easy_Dev_Pro_Posts reference to TSP_Easy_Dev_Pro_Posts object
		 */
		public function get_pro_post()
		{
			return $this->pro_post;
		}//end get_pro_post
		

		/**
		 * Get reference to pro terms options object
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return TSP_Easy_Dev_Pro_Terms reference to TSP_Easy_Dev_Pro_Terms object
		 */
		public function get_pro_term()
		{
			return $this->pro_term;
		}//end get_term_post
	}//end TSP_Easy_Dev_Pro_Options
}//endif	