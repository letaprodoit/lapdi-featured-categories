<?php	
if ( !class_exists( 'TSP_Easy_Dev_Pro_Smarty' ) )
{
	/**
	 * Wrapper for the Smarty Pro class
	 * @package 	TSP_Easy_Dev_Pro
	 * @author 		sharrondenice, thesoftwarepeople
	 * @author 		Sharron Denice, The Software People
	 * @copyright 	2013 The Software People
	 * @license 	APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version 	1.0
	 */
	final class TSP_Easy_Dev_Pro_Smarty extends TSP_Easy_Dev_Smarty
	{
		/**
		 * A boolean to turn debugging on for this class - used in Smarty so must be public
		 *
		 * @ignore
		 *
		 * @var boolean
		 */
		public $debugging = false;

		/**
		 * Constructor
		 *
		 * @param array $template_dirs Optional array of template directories
		 * @param string $cache_dir Optional directory for cache
		 * @param string $compiled_dir Optional directory for cache
		 * @param boolean $form Optional are we displaying a form or not
		 *
		 */
		public function __construct( $template_dirs = null, $cache_dir = null, $compiled_dir = null, $form = false ) 
		{
			parent::__construct( $template_dirs, $cache_dir, $compiled_dir, $form );
						
			if ( $form )
			{
				$this->assign( 'EASY_DEV_FORM_FIELDS',	'pro-easy-dev-form-fields.tpl' );
			}//end if
		}//end __construct
	}//end TSP_Easy_Dev_Smarty
}//end if