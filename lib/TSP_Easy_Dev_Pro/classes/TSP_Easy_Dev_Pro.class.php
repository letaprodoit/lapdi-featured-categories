<?php	
if ( !class_exists( 'TSP_Easy_Dev_Pro' ) )
{
	/**
	 * API implementations for TSP Easy Dev Pro, Use TSP Easy Dev package to easily create, manage and display wordpress plugins
	 * @package 	TSP_Easy_Dev_Pro
	 * @author 		sharrondenice, thesoftwarepeople
	 * @author 		Sharron Denice, The Software People
	 * @copyright 	2013 The Software People
	 * @license 	APACHE v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
	 * @version 	1.0
	 */
	class TSP_Easy_Dev_Pro extends TSP_Easy_Dev
	{
		/**
		 * A boolean to turn debugging on for this class
		 *
		 * @ignore
		 *
		 * @var boolean
		 */
		private $debugging 				= false;
		/**
		 * Array of required plugins
		 *
		 * @ignore
		 *
		 * @var array
		 */
		private $required_plugins 		= false;
		/**
		 * Array of incompatible plugins
		 *
		 * @ignore
		 *
		 * @var array
		 */
		private $incompatiable_plugins 	= false;

		/**
		 * Register Wordpress hooks for this pluginx
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param string - The file name of the plugin
		 *
		 * @return none
		 */
		 public function run( $plugin )
		 {						
			parent::run( $plugin );
			
			add_action('admin_init', 	array( $this, 'process_incompatible_plugins' ));
			add_action('admin_init', 	array( $this, 'process_required_plugins' ));
		 }//end setup
		
		/**
		 *  Implementation to deactivate incompatible plugins
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param array $plugins_list ($plugin_name => $plugin_title) Required array of plugins to deactivate
		 *
		 * @return none
		 */
		public function incompatible_plugins ( $plugins_list )
		{
			$this->incompatiable_plugins = $plugins_list;
		}//end incompatible_plugins

		/**
		 *  Implementation to deactivate self if required plugins not installed
		 *
		 * @api
		 *
		 * @since 1.0
		 *
		 * @param array $plugins_list ($key => $value) Required array of plugins to deactivate
		 *
		 * @return none
		 */
		public function required_plugins ( $plugins_list )
		{
			$this->required_plugins = $plugins_list;
		}//end required_plugins
		
		
		/**
		 * Process the incompatible plugins
		 *
		 * @ignore
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function process_incompatible_plugins ()
		{
			if ( !empty( $this->incompatiable_plugins ) )
			{
				foreach ( $this->incompatiable_plugins as $a_plugin => $a_plugin_title )
				{
					if( is_plugin_active( $a_plugin . DS . $a_plugin . '.php' ) ) 
					{
						$this->message = $a_plugin_title . " <strong>was deactivated</strong>, this plugin is not compatible with {$this->plugin_title}.";
																
						add_action( 'admin_notices', array ( $this, 'display_notice') );

						deactivate_plugins( $a_plugin . DS . $a_plugin . '.php' );
					}//endif
				}//endforeach
			}//end if
		}//end process_incompatible_plugins

		/**
		 * Process the required plugins
		 *
		 * @ignore
		 *
		 * @since 1.0
		 *
		 * @param none
		 *
		 * @return none
		 */
		public function process_required_plugins ()
		{
			if ( !empty( $this->required_plugins ) )
			{
				foreach ( $this->required_plugins as $req_plugin => $req_plugin_title )
				{
					if( !is_plugin_active( $req_plugin . DS . $req_plugin . '.php' ) ) 
					{
						$this->message = $this->plugin_title . " <strong>was not installed</strong>, plugin requires the installation and activation of <a href='plugin-install.php?tab=search&type=term&s={$req_plugin}'>{$req_plugin}</a>.";
																
						add_action( 'admin_notices', array ( $this, 'display_error') );
	
						deactivate_plugins( $this->plugin_name . DS . $this->plugin_name.'.php' );
					}//endif
				}//endforeach
			}//end if
		}//end process_required_plugins
	}//end TSP_Easy_Dev_Pro
}//endif	
?>