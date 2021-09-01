<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists('WOW_Ajax')){

	class WOW_Ajax{

		protected $switchAPI;
		protected $api_base_url_switch = '/';
		protected $api_base_url = 'https://builder.themeum.com/wp-json/restapi/v2/';
		protected $wow_api_request_body;
		protected $wow_api_request_body_default;

		/**
		 * WOW_Ajax constructor.
		 */
		public function __construct() {
			$this->switchAPI = false;
			$this->wow_api_request_body_default = array(
				'request_from'  => 'wppb',
				'request_wow_version'  => WOW_VERSION,
			);
			$this->wow_api_request_body = apply_filters('wow_api_request_body', array());

			//add_action( 'wp_ajax_nopriv_addonsdata', array($this, 'wow_addons_single_data') );
			add_action( 'wp_ajax_addonsdata', array($this, 'wow_addons_single_data') );
			add_action( 'wp_ajax_wow_page_save', array($this, 'wow_page_save') );
			add_action( 'wp_ajax_wow_pagebuilder_section_action', array($this, 'wow_pagebuilder_section_action') );

			//Widget
			add_action('wp_ajax_render_widget_form_data', array($this, 'show_panel_widget_form'));
			add_action('wp_ajax_wow_render_widget', array($this, 'wow_render_widget'));

			//Render Addon for no template
			add_action('wp_ajax_wow_render_addon', array($this, 'wow_render_addon'));

			//Delete Css with Post
			add_action( 'before_delete_post', array($this, 'delete_css_file_with_post') );

			// Get The Url Of the Custom Size Image
			add_action('wp_ajax_wow_image_size_url', array($this, 'wow_image_size_url'));

			//Get Template from WOW Page Builder Server
			add_action('wp_ajax_wow_load_page_template', array($this, 'wow_load_page_template'));
			add_action('wp_ajax_wow_import_single_page_template', array($this, 'wow_import_single_page_template'));

			//Get Blocks
			add_action('wp_ajax_wow_get_blocks', array($this, 'wow_get_blocks') );

			//Clear cache action
			add_action('wp_ajax_wow_clear_cache', array($this, 'wow_clear_cache') );

			// Section And Template Add
			add_filter('wow_add_block', array( $this , 'wow_add_block_callback' ) );
			add_filter('wow_add_layout', array( $this , 'wow_add_layout_callback' ) );
		}

		public function wow_add_block_callback( $sections = array() ) {
			return $sections;
		}

		public function wow_add_layout_callback( $templates = array() ) {
			return $templates;
		}

		public function wow_image_size_url(){
			if ( empty($_POST['id']) || empty($_POST['size']) ){
				wp_send_json_error();
				return;
			}
			$image_data = wp_get_attachment_image_src(  $_POST['id'], $_POST['size'] );
			if( isset($image_data[0]) ){
				wp_send_json_success( $image_data[0] );
			}else{
				wp_send_json_error();
			}
		}

		public function wow_addons_single_data(){

			if(!$_POST['id']) return;
			if(!$_POST['name']) return;

			$addons_data = array();
			$addons_data['assets'] = array();
			$addons_data['html'] = '<div id="wow-addon-'.$_POST["id"].'" class="clearfix" ></div>';
			$addons_data['status'] = true;

			$available_addons = wow_helper()->get_addon_classes();
			if( !empty($available_addons) ) {
				foreach ( $available_addons as $class ) {
					$data = new $class();
					if( $data->get_name() == $_POST['name'] ){
						$total_data = $data->get_settings();
						if( is_array($total_data) ){
							if( !empty($total_data) ){
								$total = array();
								foreach ($total_data as $key => $value) {
									if( isset($value['std']) ){
										$total[$key] = $value['std'];
									}else{
										$total[$key] = '';
									}
								}
								$addons_data['formData'] = json_encode( $total );
							}
						}
					}
				}
			}

			echo json_encode($addons_data);
			die();
		}

		/**
		 * Return Common CSS from the Path
		 */
		public static function get_content_common_css(){
			$css = '';

			$get_content_width  = (int)wow_get_option('wow_container_width');
			$wow_col_spacing   = (int)wow_get_option('wow_col_spacing');

			if ( ! $get_content_width ){ $get_content_width = '1320'; }
			if ( ! $wow_col_spacing = ( $wow_col_spacing / 2 ) ){
				$wow_col_spacing = 0.75;
			}
			$css .= ".wow-builder-container#wow-builder-container .wow-row-parent > .wow-container, .wow-carousel-content-wrap .wow-container{ max-width: {$get_content_width}px }";
			$css .= ".wow-builder-container#wow-builder-container > .wow-row-parent > .wow-container,.wow-builder-container#wow-builder-container  .wow-row-placeholder > .wow-row-parent > .wow-container,.wow-carousel-content-wrap .wow-container, .wow-builder-container .wow-column-parent-view { padding-left: {$wow_col_spacing}rem; }.wow-builder-container#wow-builder-container > .wow-row-parent > .wow-container, .wow-builder-container#wow-builder-container .wow-row-placeholder > .wow-row-parent > .wow-container,.wow-carousel-content-wrap .wow-container, .wow-builder-container .wow-column-parent-view{ padding-right: {$wow_col_spacing}rem; }  .wow-container > .wow-row { margin-left: -{$wow_col_spacing}rem; }.wow-container > .wow-row { margin-right: -{$wow_col_spacing}rem; } .wow-builder-container .wow-column-parent-editor .wow-column { margin-left: {$wow_col_spacing}rem; } .wow-builder-container .wow-column-parent-editor .wow-column { margin-right: {$wow_col_spacing}rem; }";
			return $css;
		}

		/**
		 * Validate CSS.
		 *
		 * Checks for imbalanced braces, brackets, and comments.
		 * Notifications are rendered when the customizer state is saved.
		 *
		 * @since 4.7.0
		 * @since 4.9.0 Checking for balanced characters has been moved client-side via linting in code editor.
		 *
		 * @param string $css The input string.
		 * @return true|WP_Error True if the input was validated, otherwise WP_Error.
		 */
		public function validate( $css ) {
			$validity = new WP_Error();

			if ( preg_match( '#</?\w+#', $css ) ) {
				$validity->add( 'illegal_markup', __( 'Markup is not allowed in CSS.' ) );
			}

			if ( ! $validity->has_errors() ) {
				$validity = $this->validate( $css );
			}
			return $validity;
		}


		/**
		 * Save Page Builder data wp post meta
		 */
		public function  wow_page_save(){
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			$page_id = (int) sanitize_text_field($_POST['page_id']);
			$page_builder_data = $_POST['page_builder_data'];
			$wow_page_css = strip_tags( stripslashes( $_POST['wow_page_css'] ), '<style>' );
			
			$wow_page_css = $wow_page_css . $this->get_content_common_css();
			$wow_page_css = $this->move_import_url_to_top_css($wow_page_css);

			update_post_meta($page_id, '_wow_content', $page_builder_data);
			update_post_meta($page_id, '_wow_page_css', $wow_page_css);
			update_post_meta($page_id, '_wow_current_post_editor', 'wow_builder_activated');

			$success_array = array( 'msg' => __('WOW Page Builder content has been updated.', 'wow-pagebuilder') );
			$error_array = array();

			$filename = "wow-page-css-{$page_id}.css";

			$upload_dir = wp_upload_dir();
			$dir = trailingslashit($upload_dir['basedir']) . 'wp-wow-website-builder/';

			WP_Filesystem( false, $upload_dir['basedir'], true );

			if( ! $wp_filesystem->is_dir( $dir ) ) {
				$wp_filesystem->mkdir( $dir );
			}
			//If fail to save css in directory, then it will show a message to user
			if ( ! $wp_filesystem->put_contents( $dir . $filename, $wow_page_css ) ) {
				$error_array['msg_error'] = __('CSS can not be saved due to permission!!!', 'wow-pagebuilder');
			}

			$msg_array = array_merge($success_array, $error_array);
			wp_send_json_success($msg_array);
		}

		public function wow_pagebuilder_section_action(){
			$action_type 	= sanitize_text_field($_POST['actionType']);
			$section_id 	= sanitize_text_field($_POST['id']);
			$db_section 	= get_option( 'wow_section', false );
			if( $action_type == 'save' ){
				$id 			= sanitize_text_field($_POST['id']);
				$title 			= sanitize_text_field($_POST['title']);
				$section_data 	= json_decode( stripslashes_deep( $_POST['section_data'] ) );
				if( $db_section ){
					$db_section = json_decode( $db_section );
					$db_section->$id = array( 'id' => $id, 'title' => $title, 'block' => $section_data );
					update_option( 'wow_section', json_encode($db_section) );
				} else {
					add_option( 'wow_section', json_encode( array( $id => array( 'id' => $id, 'title' => $title, 'block' => $section_data ) ) ) );
				}
			} elseif(  $action_type == 'get' ){
				echo get_option( 'wow_section' );
				die();
			} else {
				if( $db_section ){
					$db_section = json_decode( $db_section );
					unset( $db_section->$section_id );
					update_option( 'wow_section', json_encode($db_section) );
				}
			}
		}

		function show_panel_widget_form(){
			$widget_settings = array();
			if ( ! empty($_POST['widget']['settings'])){
				$widget_settings = $_POST['widget']['settings'];
			}
			$widget_instance = WPPB::$instance->wow_widget;
			$widget_base_id = $widget_instance->get_widget_instance()->id;
			$addons_data = array();
			$addons_data['assets'] = array();
			$addons_data['html'] = '<div id="wow-addon-'.$widget_instance->get_id().'" class="clearfix" > '.$widget_instance->render().' </div>';
			$addons_data['formData'] = '<div id="widget-'.$widget_base_id.'" class="widget open widget-form">'.$widget_instance->get_form($widget_settings).'</div>';
			$addons_data['status'] = true;
			echo json_encode($addons_data);
			die();
		}

		function wow_render_widget(){
			if ( ! empty($_POST['widget']['settings'])){
				$widget_settings = $_POST['widget']['settings'];
				try{
					$widget_instance = WPPB::$instance->wow_widget;
					$render_widget = $widget_instance->render($widget_settings);
					$render_widget = '<div id="wow-addon-'.$widget_instance->get_id().'" class="clearfix" > ' .$render_widget.' </div>';
					wp_send_json_success(array('html' => $render_widget, 'assets' => array() ));
				}catch (\Exception $exception){
					wp_send_json_error(array('msg' => $exception->getMessage()));
				}
			}
			wp_send_json_error();
		}


		public function wow_render_addon(){
			$addon_name = null;
			if (empty($_POST['addon'])){
				wp_send_json_error();
			}
			$addon = $_POST['addon'];
			$addon_name = $addon['name'];
			try{
				$addon_instantce = addon_instance($addon_name);
				if ( method_exists($addon_instantce, 'render')){
					$rendered_addon =  $addon_instantce->render($addon);
					wp_send_json_success(array('html' => $rendered_addon, 'assets' => array() ));
				}
			}catch (\Exception $e){
				wp_send_json_error(array('msg' => $e->getMessage()));
			}
		}


		/**
		 * @since 1.0.0
		 * Move @import google css to top
		 */
		public function move_import_url_to_top_css($get_css = ''){
			$css_url = "@import url('https://fonts.googleapis.com/css?family=";
			$google_font_exists = substr_count($get_css, $css_url);

			if ($google_font_exists){
				$pattern = sprintf(
					'/%s(.+?)%s/ims',
					preg_quote($css_url, '/'), preg_quote("');", '/')
				);

				if (preg_match_all($pattern, $get_css, $matches)) {
					$fonts = $matches[0];

					$get_css = str_replace($fonts, '', $get_css);
					if( preg_match_all( '/font-weight[ ]?:[ ]?[\d]{3}[ ]?;/' , $get_css, $matche_weight ) ){ // short out font weight
						$weight = array_map( function($val){
							$process = trim( str_replace( array( 'font-weight',':',';' ) , '', $val ) );
							if( is_numeric( $process ) ){
								return $process;
							}
						}, $matche_weight[0] );
						foreach ( $fonts as $key => $val ) {
							$fonts[$key] = str_replace( "');",'', $val ).':ital,wght@'.implode( ',',$weight )."');";
						}
					}

					//Multiple same fonts to single font
					$fonts = array_unique($fonts);
					$get_css = implode('', $fonts).$get_css;
				}
			}
			return $get_css;
		}


		/**
		 * @since 1.0.0
		 * Delete css file generated by WOW Page Builder
		 */
		function delete_css_file_with_post( $postid ){
			$is_wow_content = wow_helper()->is_wow_content($postid);
			if ( ! $is_wow_content){
				return;
			}

			$filename = "wow-page-css-{$postid}.css";

			$upload_dir = wp_upload_dir();
			$dir = trailingslashit($upload_dir['basedir']) . 'wp-wow-website-builder/';
			if (file_exists($dir.$filename)){
				unlink($dir.$filename);
			}
		}

		/**
		 * @since 1.0.0-BETA
		 * Get Template
		 */
		// LOAD ALL TEMPLATE 
		public function wow_load_page_template(){
			if($this->switchAPI){
				$cachedTemplateFile = "wow-templates.json";
				$cache_time = (60*60*24*7); //cached for 7 days
	
				$templateData = array();
	
				$upload_dir = wp_upload_dir();
				$dir = trailingslashit($upload_dir['basedir']) . 'wp-wow-website-builder/cache/templates/';
				$file_path_name = $dir . $cachedTemplateFile;
	
				if (file_exists($file_path_name) && (filemtime($file_path_name) + $cache_time) > time()){
					$getTemplatesFromCached = file_get_contents($file_path_name);
					$templateData = json_decode($getTemplatesFromCached, true);
					$cached_at = 'Last cached: '.human_time_diff( filemtime($file_path_name), current_time('timestamp')). ' ago';
					$thirparty_template = $this->wow_get_thirdparty_template();
	
					if( !empty( $thirparty_template ) ){
						$templateData = array_merge( $templateData, $thirparty_template );
					}
	
					wp_send_json(array('success' => true, 'cached_at' => $cached_at,  'data' => $templateData));
				}else{
					$templateData = $this->load_templates_from_remote();
					$thirparty_template = $this->wow_get_thirdparty_template();
					if( !empty( $thirparty_template ) ){
						$templateData = array_merge( $templateData, $thirparty_template );
					}
				}
			} else {
				// ==============
				$file_path = plugin_dir_path( __DIR__ ) . 'jsondata/layout.json';
				$templateData = json_decode(file_get_contents( $file_path), true);
				wp_send_json($templateData);
				// ==============
			}

			wp_send_json_success($templateData);
		}

		/**
		 * @since 1.0.0
		 * Get Thirdparty Templates
		 */
		public function wow_get_thirdparty_template(){
			$templateData = array();

			$templates = apply_filters( 'wow_add_layout', array() );
			if( !empty( $templates ) ){
				foreach( $templates as $value ){
					if( isset( $value['name'], $value['category'], $value['preview2x'], $value['preview'], $value['single'] ) ){
						if( is_array( $value['single'] ) && !empty( $value['single'] ) ){
							$category = array();
							foreach( $value['category'] as $val ){ $category[] = array( 'name' => $val, 'slug' => $val ); }
							$p_id = time().rand(1,1000);
							$templateData[] = array(
								'name' => $value['name'],
								'category' => $category,
								'ID' => $p_id,
								'parentID' => 0,
								'preview' => $value['preview'],
								'preview_m' => $value['preview2x'],
								'thirdparty' => true,
							);
							foreach( $value['single'] as $v ){
								if( $v['file'] ){
									$templateData[] = array(
										'name' => $value["name"].'&#8211;'.$v["name"],
										'category' => $category,
										'ID' => time().rand(1,1000),
										'parentID' => $p_id,
										'preview' => $v['preview'],
										'preview_m' => $v['preview2x'],
										'file' => $v['file'],
										'liveurl' => isset( $v['liveurl'] ) ? $v['liveurl'] : '',
										'thirdparty' => true,
									);
								}
							}
						}
					}
				}
			}
			return $templateData;
		}

		/**
		 * @since 1.0.0-BETA
		 * Load Template from Server
		 */
		public function load_templates_from_remote(){

			$apiUrl = $this->api_base_url.'templates';
			$post_args = array( 'timeout' => 120);
			$body_param = array_merge($this->wow_api_request_body_default, array( 'request_for' => 'get_all_template'));
			$post_args['body'] = array_merge($body_param, $this->wow_api_request_body);
			$tempalteRequest = wp_remote_post($apiUrl, $post_args);
			if (is_wp_error($tempalteRequest)){
				wp_send_json_error(array('messages' => $tempalteRequest->get_error_messages()));
			}
			$templateData = json_decode($tempalteRequest['body'], true);

			$cachedTemplateFile = "wow-templates.json";
			$upload_dir = wp_upload_dir();
			$dir = trailingslashit($upload_dir['basedir']) . 'wp-wow-website-builder/cache/templates/';
			$file_path_name = $dir . $cachedTemplateFile;
			if ( ! file_exists($dir)) {
				mkdir( $dir, 0777, true );
			}
			file_put_contents($file_path_name,  json_encode( $templateData )); // Put template content to cached directory
			
			return $templateData;
		}

		/**
		 * @since 1.0.0
		 * Import single template
		 */
		public function wow_import_single_page_template(){
			$template_id = (int) sanitize_text_field($_POST['template_id']);
			$fileUrl = $_POST['fileUrl'];
			$cache_time = (60*60*24*7); //cached for 7 days
			$cachedTemplateFile = "wow-single-template-{$template_id}.json";
			$upload_dir = wp_upload_dir();
			$dir = trailingslashit($upload_dir['basedir']) . 'wp-wow-website-builder/cache/templates/';
			$file_path_name = $dir . $cachedTemplateFile;

			if($this->switchAPI){
				if( !$fileUrl ){
					// Checking if exists file and cache validity true
					if (file_exists($file_path_name) && (filemtime($file_path_name) + $cache_time) > time()){
						$getTemplatesFromCached = file_get_contents($file_path_name);
						$templateData = json_decode($getTemplatesFromCached, true);
					}else{
						$templateData = $this->load_and_cache_single_template($template_id);
					}
				} else {
					if (file_exists($fileUrl)){
						$getContent = file_get_contents($fileUrl);
						$templateData = json_decode($getContent, true);
					}else{
						$templateData = $this->load_and_cache_single_template($template_id);
					}
				}
			} else {
				// ==============
				$file_path = plugin_dir_path( __DIR__ ) . 'jsondata/pages/page-'.$template_id.'.json';
				if (file_exists($file_path_name)){
					$templateData = $this->load_and_cache_single_template($template_id);
				}else{
					$templateData = json_decode(file_get_contents( $file_path), true);
				}
				wp_send_json($templateData);
				// ==============
			}


			wp_send_json_success($templateData);
		}


		/**
		 * @since 1.0.0-BETA
		 * Load single template and cache it
		 */
		public function load_and_cache_single_template($template_id = 0){
			if ( ! $template_id){
				return false;
			}
			$apiUrl = $this->api_base_url.'single-template';

			$post_args = array( 'timeout' => 120);

			$body_param = array_merge($this->wow_api_request_body_default,
				array( 'request_for' => 'get_single_template', 'template_id' => $template_id)
			);
			$post_args['body'] = array_merge($body_param, $this->wow_api_request_body);
			$tempalteRequest = wp_remote_post($apiUrl, $post_args);

			if (is_wp_error($tempalteRequest)){
				wp_send_json_error(array('messages' => $tempalteRequest->get_error_messages()));
			}

			$templateData = json_decode($tempalteRequest['body'], true);
			if (!empty($templateData['success']) && $templateData['success']) {
				$templateData = json_decode( $templateData['rawData'], true );

				//If data not null, or not empty
				if (! empty($templateData)){
					$cachedTemplateFile = "wow-single-template-{$template_id}.json";
					$upload_dir         = wp_upload_dir();
					$dir                = trailingslashit($upload_dir['basedir']).'wp-wow-website-builder/cache/templates/';

					$file_path_name = $dir.$cachedTemplateFile;

					if ( ! file_exists($dir)) {
						mkdir( $dir, 0777, true );
					}
					//Put template content to cached directory
					file_put_contents($file_path_name,  json_encode( $templateData ));
				}
			}
			return $templateData;
		}

		/**
		 * @since 1.0.0
		 * Get all blocks from cache or request
		 */
		// GET ALL BLOCKS
		public function wow_get_blocks(){
			$cache_time = (60*60*24*7); //cached for 7 days

			$cachedTemplateFile = "wow-blocks.json";
			$upload_dir = wp_upload_dir();
			$dir = trailingslashit($upload_dir['basedir']) . 'wp-wow-website-builder/cache/blocks/';
			$file_path_name = $dir . $cachedTemplateFile;

			//Checking if exists file and cache validity true
			if (file_exists($file_path_name) && (filemtime($file_path_name) + $cache_time) > time()){
				$getTemplatesFromCached = file_get_contents($file_path_name);
				$blocksData = json_decode($getTemplatesFromCached, true);
			}else{
				$blocksData = $this->cacheBlocks();
			}

			// Thirdparty Blocks
			$sections = apply_filters( 'wow_add_block', array() );
			if( !empty( $sections ) ){
				foreach( $sections as $value ){
					if( isset( $value['name'], $value['file'], $value['banner'], $value['section'] ) ){
						$blocksData[] = array(
							'name' => $value['name'],
							'ID' => time().rand(1,1000),
							'rawData' => json_decode( file_get_contents( $value['file'] ), true ),
							'banner' => $value['banner'],
							'thirdparty' => true,
							'liveurl' => ( isset($value['liveurl']) ? $value['liveurl'] : '' ),
							'type' => array( array( 'label'=> $value["section"] , 'value' => $value["section"] ) )
						);
					}
				}
			}
			wp_send_json_success($blocksData);
		}

		public function cacheBlocks(){
			$cachedTemplateFile = "wow-blocks.json";
			$upload_dir         = wp_upload_dir();
			$dir                = trailingslashit($upload_dir['basedir']).'wp-wow-website-builder/cache/blocks/';
			$apiUrl = $this->api_base_url.'sections';
			$post_args = array( 'timeout' => 120);
			$body_param = array_merge($this->wow_api_request_body_default, array( 'request_for' => 'wow_get_blocks'));
			$post_args['body'] = array_merge($body_param, $this->wow_api_request_body);

			if($this->switchAPI) {
				$tempalteRequest = wp_remote_post($apiUrl, $post_args);
	
				if (is_wp_error($tempalteRequest)){
					wp_send_json_error(array('messages' => $tempalteRequest->get_error_messages()));
				}
				$blocksRemoteData = json_decode(trim($tempalteRequest['body']), true);
				$newBlockdagta = array();
				foreach ($blocksRemoteData as $block){
					$rowData = $block['rawData'];
					$block['rawData'] = json_decode($rowData, true);
					$newBlockdagta[] = $block;
				}
			} else {
				// TODO: CHECK THIS ONE 
				// ==============
				$file_path = plugin_dir_path( __DIR__ ) . 'jsondata/block.json';
				$blocksRemoteData = json_decode(trim(file_get_contents( $file_path)), true);
				$newBlockdagta = $blocksRemoteData['data'];
				// ==============
			}


			$file_path_name = $dir.$cachedTemplateFile;
			if ( ! file_exists($dir)) {
				mkdir( $dir, 0777, true );
			}
			//Put template content to cached directory
			file_put_contents($file_path_name,  json_encode( $newBlockdagta ));

			return $newBlockdagta;
		}

		/**
		 * @since 1.0.0-BETA
		 * Action for cache delete directory and files
		 */
		public function wow_clear_cache(){
			check_ajax_referer( 'wow-nonce-string', 'wow_nonce');

			$upload_dir     = wp_upload_dir();
			$dirPattern     = trailingslashit($upload_dir['basedir']).'wp-wow-website-builder/cache/';

			$isDone = $this->deleteRecursiveDirFile($dirPattern);
			if ($isDone){
				wp_send_json_success();
			}
			wp_send_json_error();
		}

		/**
		 * @since 1.0.0-BETA
		 * Delete Recursively directory and files
		 */
		public function deleteRecursiveDirFile($path = null){
			if (!$path){
				return false;
			}

			$path = trailingslashit($path);
			$files = glob($path.'*');

			foreach ($files as $file) {
				is_dir($file) ? $this->deleteRecursiveDirFile($file) : unlink($file);
			}
			rmdir($path);
			return true;
		}



	}
}