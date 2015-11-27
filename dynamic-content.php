<?php
/**
 * @package Dynamic Content
 * @version 1.0
 */
/*
Plugin Name: Dynamic Content
Plugin URI: http://1000camels.com/plugins/dynamic-content/
Description: This plugin dynamically loads WordPress content
Author: Darcy Christ
Version: 1.0
Author URI: http://1000camels.com/
*/

if(!class_exists('Dynamic_Content'))
{
	class Dynamic_Content {

		private $posttype = 'glossary';

		public function __construct() 
		{
			session_start();
			session_unset();

			add_action( 'wp_enqueue_scripts', array( &$this, 'dc_enqueue_assets' ) );

			add_action( 'wp_ajax_nopriv_dc_get_content', array( &$this, 'dc_get_content' ) );
			add_action( 'wp_ajax_dc_get_content', array( &$this, 'dc_get_content' ) );

			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'add_menu'));

			add_filter( 'the_content', array( &$this, 'scan_posts' ) );
		}
		
		/**
                 *
                 */
		public function scan_posts($content)
		{
			$this->load_items();	

			foreach( $_SESSION['items'] as $key => $item ) {
				error_log('scanning for '.$item->post_title);
				$content = preg_replace('/'.$item->post_title.'/', '<span class="dc-item">'.$item->post_title.'</span>', $content); 
			}

			return $content;	
		}

		/**
		 * Get all the items based upon the $this->posttype;
 		 */
		public function load_items()
		{
			if( isset($_SESSION['items']) && count($_SESSION['items']) > 0 ) return true; 

			//error_log(array_keys($_SESSION['items']);
			error_log( 'loading items: '.join( ', ',array_keys($_SESSION['items']) ) );

			$query_vars = array();
			$query_vars['post_type'] = $this->posttype;
			$query_vars['posts_per_page'] = -1;
                        
			$posts = new WP_Query( $query_vars );

                        if( $posts->have_posts() ) {
				$this->items = array();	
                                while ( $posts->have_posts() ) {
                                        $posts->the_post();
					$key = get_the_title();
					 $simplekey = preg_replace('/\s+/', '', $key);
                                        $_SESSION['items'][$simplekey] = $posts->post;
                                }
	              	}
			//error_log(print_r($_SESSION['items'], TRUE));
		}

		/**
         	 *
		 */
		public function dc_get_content() 
		{	
			session_start();
                        $this->load_items();  

			$key = $_POST['item'];
			$simplekey = preg_replace('/\s+/', '', $key);
        		if ( isset($_SESSION['items'][$simplekey]) ) { 
				print json_encode($_SESSION['items'][$simplekey]);
    			}			
			
			die();
		}

		/**
                 *
                 */
		public function dc_enqueue_assets()
		{ 
			global $wp_query;

        		wp_enqueue_style( 'dc-style', plugin_dir_url( __FILE__ ).'css/style.css' );
        		wp_enqueue_script( 'dc-script',  plugin_dir_url( __FILE__ ) . 'js/script.js', array( 'jquery' ), '1.0', true );

        		wp_localize_script( 'dc-script', 'dcvariables', array(
        	        	'ajaxurl' => admin_url( 'admin-ajax.php' ),
       		         	'query_vars' => json_encode( $wp_query->query ),
       		 	));
		}

		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{
			// Set up the settings for this plugin
			$this->init_settings();
		}

		/**
		 * Initialize some custom settings
		 */     
		public function init_settings()
		{
			// register the settings for this plugin
			register_setting('wp_dc_plugin-group', 'setting_a');
			register_setting('wp_dc_plugin-group', 'setting_b');
		}

		/**
		 * add a menu
		 */     
		public function add_menu()
		{
			add_options_page('Dynamic Content Settings', 'Dynamic Content', 'manage_options', 'wp_dc_plugin', array(&$this, 'plugin_settings_page'));
		}

		/**
		 * Menu Callback
		 */     
		public function plugin_settings_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}

		/**
                 * Activate the plugin
                 */
                public static function activate()
                {
                        // Do nothing
                }
   
                /**
                 * Deactivate the plugin
                 */    
                public static function deactivate()
                {
                        // Do nothing
                }	

	}
}

if(class_exists('Dynamic_Content'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('Dynamic_Content', 'activate'));
    register_deactivation_hook(__FILE__, array('Dynamic_Content', 'deactivate'));

    // instantiate the plugin class
    $wp_dc_plugin = new Dynamic_Content();
}

// Add a link to the settings page onto the plugin page
if(isset($wp_dc_plugin))
{
    // Add the settings link to the plugins page
    function plugin_settings_link($links)
    { 
        $settings_link = '<a href="options-general.php?page=dynamic-content">Settings</a>'; 
        array_unshift($links, $settings_link); 
        return $links; 
    }

    $plugin = plugin_basename(__FILE__); 
    add_filter("plugin_action_links_$plugin", 'plugin_settings_link');
}