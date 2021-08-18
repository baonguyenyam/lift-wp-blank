<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/wp-best-css-compiler
 * @since      1.0.0
 *
 * @package    Best_Css_Compiler
 * @subpackage Best_Css_Compiler/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Best_Css_Compiler
 * @subpackage Best_Css_Compiler/admin
 * @author     Nguyen Pham <baonguyenyam@gmail.com>
 */

class Best_Css_Compiler_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cssCompiler    The ID of this plugin.
	 */
	private $cssCompiler;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $cssCompiler       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $cssCompiler ) {

		$this->cssCompiler = $cssCompiler;
		add_action( 'carbon_fields_register_fields', array( $this, '___app_option_attach_theme_options' ));
		add_action( 'after_setup_theme', array( $this, '__settingUP' ));

		add_action('admin_menu', array( $this, '___addPluginAdminMenu' ));   
		add_action( 'admin_post_submit_data', array( $this, '__submitData') );
		add_filter( 'plugin_action_links',  array( $this, '__suggestion_boxesadd_setting_link_chat') , 10, 2 );
	}

	// ADD SETTING LINK 

	public function __suggestion_boxesadd_setting_link_chat( $links, $file ) {
		if( $file === 'wp-best-css-compiler/nguyen-app.php' ){
			$link = '<a href="'.admin_url('admin.php?page=crb_carbon_fields_container_settings.php').'">'.esc_html__('Settings', BEST_CSS_COMPILER_DOMAIN ).'</a>';
			array_unshift( $links, $link ); 
		}
		return $links;
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Best_Css_Compiler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Best_Css_Compiler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->cssCompiler['domain'], plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->cssCompiler['version'], 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Best_Css_Compiler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Best_Css_Compiler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->cssCompiler['domain'], plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->cssCompiler['version'], false );

	}

	public function __submitData() {
		global $table_prefix, $wpdb;
		$tblGroup = $table_prefix . BEST_CSS_COMPILER_PREFIX . '_suggest_group';
		$tblSuggest = $table_prefix . BEST_CSS_COMPILER_PREFIX . '_suggest';

		$id = isset($_POST['id']) ? (int)$_POST['id'] : '';
		$type = sanitize_text_field(isset($_POST['type']) ? $_POST['type'] : '');
		$posttype = sanitize_text_field(isset($_POST['posttype']) ? $_POST['posttype'] : '');
		$inputValue = sanitize_text_field(isset($_POST['groupName']) ? $_POST['groupName'] : '');
		$groupTarget = isset($_POST['groupTarget']) ? (int)$_POST['groupTarget'] : '';
		$idTarget = isset($_POST['idTarget']) ? (int)$_POST['idTarget'] : '';

		if($posttype === 'suggest') {
			if(isset($type) && $type != '' && $type != null) {
				if($type === 'edit') {
					$wpdb->update(
						$tblSuggest,
						array(
							'suggest_content'=> $inputValue,
							'group_id' => $groupTarget,
							'target_id' => $idTarget,
						),
						array('suggest_id'=>$id),
					);
				}
				if($type === 'delete') {
					$wpdb->delete(
						$tblSuggest,
						array(
							'suggest_id'=> $id
						),
						array('%d'),
					);
				}
			} else {
				$wpdb->insert(
					$tblSuggest,
					array( 
						'suggest_content' => $inputValue,
						'group_id' => $groupTarget,
						'target_id' => $idTarget,
					),
					array( '%s' ),
				);
			}
		}

		if($posttype === 'screen') {
			if(isset($type) && $type != '' && $type != null) {
				if($type === 'edit') {
					$wpdb->update(
						$tblGroup,
						array(
							'group_content'=> $inputValue
						),
						array('group_id'=>$id),
					);
				}
				if($type === 'delete') {
					$wpdb->delete(
						$tblGroup,
						array(
							'group_id'=> $id
						),
						array('%d'),
					);
				}
			} else {
				$wpdb->insert(
					$tblGroup,
					array( 
						'group_content' => $inputValue
					),
					array( '%s' ),
				);
			}
		}
	
		wp_redirect('admin.php?page=best-css-compiler');
		// wp_redirect($_SERVER["HTTP_REFERER"]);
	}


	public function ___addPluginAdminMenu() {
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(  $this->cssCompiler['nicename'],  esc_html__( 'Suggestions', BEST_CSS_COMPILER_DOMAIN ) , 'administrator', $this->cssCompiler['domain'], array( $this, '___displayPluginAdminDashboard' ), 'dashicons-admin-comments', 30 );
		
		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		add_submenu_page( null, esc_html__('Add New Screen', BEST_CSS_COMPILER_DOMAIN ), esc_html__('Add New Screen', BEST_CSS_COMPILER_DOMAIN ), 'administrator', $this->cssCompiler['domain'].'-screen', array( $this, '___displayPluginAdminAddNewScreen' ));
		add_submenu_page( null, esc_html__('Add New Suggest', BEST_CSS_COMPILER_DOMAIN ), esc_html__('Add New Suggest', BEST_CSS_COMPILER_DOMAIN ), 'administrator', $this->cssCompiler['domain'].'-suggest', array( $this, '___displayPluginAdminAddNewSuggest' ));

	}
	public function ___displayPluginAdminDashboard() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/admin-display.php';
	}
	public function ___displayPluginAdminAddNewScreen() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/screen.php';
	}
	public function ___displayPluginAdminAddNewSuggest() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/suggest.php';
	}

	public function ___app_option_attach_theme_options() {
		$basic_options_container =  Container::make( 'theme_options', esc_html__( 'Settings', BEST_CSS_COMPILER_DOMAIN ) )
		->set_page_parent(  $this->cssCompiler['domain'] )
			// ->set_page_menu_title( 'App Settings' )
			// ->set_page_menu_position(2)
			// ->set_icon( 'dashicons-admin-generic' )
			->add_tab( esc_html__( 'Settings', BEST_CSS_COMPILER_DOMAIN ), self::__chatApp() )
			->add_tab( esc_html__( 'Copyright', BEST_CSS_COMPILER_DOMAIN ), self::__copyright() )
			;
	}
	
	public function __settingUP() {
		require_once plugin_dir_path( __DIR__  ) .'vendor/autoload.php';
		\Carbon_Fields\Carbon_Fields::boot();
	}

	public function __chatApp() {
		$data = array();
		$data = array(
			Field::make(
			'checkbox', 
			'___best_css_compiler_enable',
			esc_html__('Enable', BEST_CSS_COMPILER_DOMAIN)
			)->set_option_value( 'yes' ),
			Field::make( 'text', '__best_css_compiler_title', esc_html__( 'Title', BEST_CSS_COMPILER_DOMAIN ) )
			->set_default_value(esc_html__('Chat with us!', BEST_CSS_COMPILER_DOMAIN ))
			->set_classes( 'lift-cabon-width-class' )
			->set_width(100),
			Field::make( 'image', '__best_css_compiler_logo', esc_html__( 'Logo', BEST_CSS_COMPILER_DOMAIN ) )
			->set_value_type( 'url' )
            ->set_visible_in_rest_api( $visible = true )
			->set_width(100),
			Field::make( 'color', '__best_css_compiler_style', esc_html__( 'Style color', BEST_CSS_COMPILER_DOMAIN ) )
			->set_alpha_enabled( true )
			->set_width(100),	
			Field::make( 'text', '__best_css_compiler_size', esc_html__( 'Icon Size', BEST_CSS_COMPILER_DOMAIN ) )
			->set_default_value('16px')
			->set_width(12.5),
			Field::make( 'text', '__best_css_compiler_title_size', esc_html__( 'Title Size', BEST_CSS_COMPILER_DOMAIN ) )
			->set_default_value('24px')
			->set_width(12.5),
			Field::make( 'text', '__best_css_compiler_content_size', esc_html__( 'Font Size', BEST_CSS_COMPILER_DOMAIN ) )
			->set_default_value('16px')
			->set_width(12.5),
			Field::make( 'select', '__best_css_compiler_position', esc_html__( 'Position', BEST_CSS_COMPILER_DOMAIN ) )
			->add_options( array(
				'bottomright' => esc_html__( 'Bottom Right', BEST_CSS_COMPILER_DOMAIN ),
				'bottomleft' => esc_html__( 'Bottom Left', BEST_CSS_COMPILER_DOMAIN ),
				'topright' => esc_html__( 'Top Right', BEST_CSS_COMPILER_DOMAIN ),
				'topleft' => esc_html__( 'Top Left', BEST_CSS_COMPILER_DOMAIN ),
			) )
			->set_default_value('bottomright')
			->set_width(12.5),
			Field::make( 'text', '__best_css_compiler_padding_x', esc_html__( 'Padding X', BEST_CSS_COMPILER_DOMAIN ) )
			->set_default_value('2em')
			->set_width(12.5),
			Field::make( 'text', '__best_css_compiler_padding_y', esc_html__( 'Padding Y', BEST_CSS_COMPILER_DOMAIN ) )
			->set_default_value('2em')
			->set_width(12.5),
		);
		return $data;
	}

	public function __copyright() {
		$data = array();
		$data = array(
	
			Field::make( 'html', 'crb_html_2', esc_html__( 'Section Description', BEST_CSS_COMPILER_DOMAIN ) )
					->set_html('
					
					<h1>'.esc_html__('Best CSS Compiler', BEST_CSS_COMPILER_DOMAIN ).'</h1>
					<p>'.esc_html__('A Better Way to Connect With Customers. You don\'t have time to talk with some online customers? This plugin will help you connect with them.', BEST_CSS_COMPILER_DOMAIN ).'</p>
					
					'),
					Field::make( 'separator', 'crb_separator_1', esc_html__( 'Copyright', BEST_CSS_COMPILER_DOMAIN ) ),

			Field::make( 'html', 'crb_html_1', esc_html__( 'Section Description', BEST_CSS_COMPILER_DOMAIN ) )
					->set_html('
					
					<p style="margin-top:0;margin-bottom:0"><strong>'.esc_html__( 'Author', BEST_CSS_COMPILER_DOMAIN ).':</strong> <a href="https://baonguyenyam.github.io/" target="_blank">Nguyen Pham</a></p>
					
					'),
	
		);
		return $data;
	}
	

}


