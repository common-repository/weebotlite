<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.weedesign.de
 * @since      1.0.0
 *
 * @package    weebotLite
 * @subpackage weebotLite/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, hooks and functions for the backend
 *
 * @link       http://www.weedesign.de
 * @since      1.0.0
 * @package    weebotLite
 * @subpackage weebotLite/admin
 * @author     Wolfgang Ertl <wolfgang.ertl@weedesign.de>
 */
 
class weebotLite_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name		The name of this plugin.
	 * @param      string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function provides stylesheets for admin pages
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in weebotLite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The weebotLite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		/*
		*	nanoscroller (vendor)
		*/
		wp_enqueue_style( $this->plugin_name."-nanoscroller", str_replace("/admin/","/public/",plugin_dir_url( __FILE__ )) . 'assets/css/nanoscroller.css', array(), $this->version, 'all' );

		/*
		*	select2 (vendor)
		*/
		wp_enqueue_style( $this->plugin_name."-select2", plugin_dir_url( __FILE__ ) . 'assets/css/select2.min.css', array(), $this->version, 'all' );
		
		/*
		*	admin styles
		*/
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function provides javascript for the admin pages
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in weebotLite_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The weebotLite_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/*
		*	nanoscroller (vendor)
		*/
		wp_register_script( 
			$this->plugin_name.'-nanoscroller', 
			str_replace("/admin/","/public/",plugin_dir_url( __FILE__ )) . 'assets/js/jquery.nanoscroller.min.js', 
			array( 'jquery' )
		);
		wp_enqueue_script( $this->plugin_name.'-nanoscroller' );
		
		/*
		*	select2 (vendor)
		*/
		wp_register_script( 
			$this->plugin_name.'-select2', 
			plugin_dir_url( __FILE__ ) . 'assets/js/select2.min.js', 
			array( 'jquery' )
		);
		wp_enqueue_script( $this->plugin_name.'-select2' );
		
		/*
		*	list (vendor)
		*/
		wp_register_script( 
			$this->plugin_name.'-list', 
			plugin_dir_url( __FILE__ ) . 'assets/js/list.min.js', 
			array( 'jquery' )
		);
		wp_enqueue_script( $this->plugin_name.'-list' );
		
		/*
		*	admin functions
		*/
		wp_register_script( 
			$this->plugin_name."-admin", 
			plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', 
			array( 'jquery' )
		);
		wp_enqueue_script( $this->plugin_name."-admin" );
		wp_localize_script( $this->plugin_name."-admin", 'weebotLiteAJAXadmin', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	}
	
	/**
	 * Register a new column for the admin users page
	 *
	 * @since    1.0.0
	 */
	function register_admin_user_column( $columns ) {
		$columns['weebotLite'] = 'weebotLite';
		return $columns;
	}

	/**
	 * Display the column for the admin users page
	 *
	 * @since    1.0.0
	 */
	function display_admin_user_column( $empty, $column_name, $user_id ) {
		if ( 'weebotLite' != $column_name )
			return $empty;
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_user";
		$question = $wpdb->get_results("SELECT * FROM $table_name WHERE user=".$user_id." and type='support'");
		
		$yes_selected = (count($question)==1) ? ' selected="selected"' : '';
		$no_selected = (count($question)==0) ? ' selected="selected"' : '';
		return '
			<select class="weebotLite_USER_select" data-success="'.esc_attr__( 'Saved', $this->plugin_name ).'" data-user="'.$user_id.'">
				<option value="0"'.$no_selected.'>'.esc_attr__( 'No', $this->plugin_name ).'</option>
				<option value="1"'.$yes_selected.'>'.esc_attr__( 'Yes', $this->plugin_name ).'</option>
			</select>';
	}

	/**
	 * Get an user 
	 *
	 * @since		1.0.0
	 * @param		int    	$id			The User-ID
	 */
	function get_user($id=false) {
		global $wpdb;
		$table_name = $wpdb->prefix . "weebotLite_user";
		$sql = ($id===false) ? "user='".get_current_user_id()."' and type='support'" : "id='".$id."'";
		$user = $wpdb->get_results("SELECT * FROM $table_name WHERE ".$sql);
		if(count($user)==0) {
			return false;
		} else {
			return $user[0];
		}
	}
	
	/**
	 * Get the amount of unanswered questions
	 *
	 * @since		1.0.0
	 */
	function get_unanswered_questions_count() {
		global $wpdb;
		$table_name_questions = $wpdb->prefix . "weebotLite_questions";
		return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name_questions where answered!=1 and language='".get_locale()."'");
	}
	
	/**
	 * Add new questions count to the admin bar
	 *
	 * @since		1.0.0
	 * @param		int    	$wp_admin_bar			Admin Bar
	 */
	function custom_toolbar_link($wp_admin_bar) {
	
		$options = get_option($this->plugin_name,$this->default_options());
		if($options["support"]==1) {
			if($this->get_user()!==false) {
				$args = array(
					'id' => 'weebotLite',
					'title' => '<span class="ab-icon"></span><span class="count">'.$this->get_unanswered_questions_count()."</span>",
					'href' => admin_url( 'admin.php?page=' . $this->plugin_name."-questions&method=unanswered" ),
					'meta' => array(
						'class' => 'weebotLite',
						'title' => 'weebotLite'
					)
				);
				$wp_admin_bar->add_node($args);
			}
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		add_options_page( 'weebotLite Settings', 'weebotLite', 'manage_options', $this->plugin_name."-options", array($this, 'display_plugin_setup_page') );
		
		/**
		 * Add Backend Menu for Editors
		 */
		if($this->get_user()!==false||current_user_can('administrator')) {
			add_menu_page('weebotLite', 'weebotLite', 'manage_categories', $this->plugin_name, array($this, 'display_plugin_page'), 'dashicons-format-chat');
			add_submenu_page( $this->plugin_name, esc_attr__( 'Questions', $this->plugin_name ), esc_attr__( 'Questions', $this->plugin_name ), 'manage_categories', $this->plugin_name."-questions", array($this, 'display_plugin_questions_page'));
			add_submenu_page( $this->plugin_name, esc_attr__( 'Answers', $this->plugin_name ), esc_attr__( 'Answers', $this->plugin_name ), 'manage_categories', $this->plugin_name."-answers", array($this, 'display_plugin_answers_page'));
			add_submenu_page( $this->plugin_name, esc_attr__( 'User', $this->plugin_name ), esc_attr__( 'User', $this->plugin_name ), 'manage_categories', $this->plugin_name."-user", array($this, 'display_plugin_user_page'));
			add_submenu_page( $this->plugin_name, esc_attr__( 'Settings', $this->plugin_name ), esc_attr__( 'Settings', $this->plugin_name ), 'manage_options', $this->plugin_name."-options", array($this, 'display_plugin_setup_page'));
		}
		
	}
	
	/**
	 * Get a page by id or url
	 *
	 * @since		1.0.0
	 * @param		text    	$type			Column-name (id or page)
	 * @param		int/text    $value			[id] = int, [page] = text
	 */
	public function get_page_by($type,$value) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->plugin_name . "_pages";
		$page = $wpdb->get_results("SELECT * FROM $table_name WHERE $type='$value'");
		if(count($page)==0) {
			if($type=="page") {
				$insert_page = $this->insert_page($value);
				$page = $wpdb->get_results("SELECT * FROM $table_name WHERE $type='$value'");
			} else {
				return false;
			}
		}
		return $page[0];
	}

	 /**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ."-options") . '">' . __('Settings', $this->plugin_name) . '</a>',
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ."-options&amp;action=delete" ) . '">' . __('Delete Database', $this->plugin_name) . '</a>',
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ."-options&amp;action=empty" ) . '">' . __('Empty Database', $this->plugin_name) . '</a>',
		);
		return array_merge(  $settings_link, $links );

	}
	
	/**
	 * Get page data for admin pages (questions, answers, user)
	 *
	 * @since		1.0.0
	 * @param		text    	$table				table-name
	 * @param		text    	$special_method		SQL for filtering special entries (unanswered, supporter)
	 * @param		text   		$search_column		column to search in
	 * @param		text    	$type				page-type (question, event, user)
	 */
	public function get_page_data($table,$special_method,$search_column,$type) {
	
		global $wpdb;
	
		$method = (isset($_GET["method"])) ? " where ".$special_method : "";
				
		$search = false;
		
		$s = false;
		
		if(isset($_POST)) {
		
			if(isset($_POST["s"])) {
				if(!empty($_POST["s"])) {
				
					$s = $_POST["s"];
					if(!empty($method)) {
						$search = " and ";
					} else {
						$search = " where ";
					}
					$searchAdd = $search_column." like '%".str_replace("'","",$_POST["s"])."%'";
					
				}
			}
			
			if(isset($_POST["{$type}"])) {
				foreach($_POST["{$type}"] as $event) {
					$thisDeleted = false;
					foreach($_POST["action"] as $action) {
						if($thisDeleted===false) {
							if($action=="trash") {
								$delete = $this->delete($table,$event);
							}
						}
					}
				}
			}
			
		}

		if(isset($_GET["s"])) {
			if(!empty($_GET["s"])) {
			
				$s = $_GET["s"];
				if(!empty($method)) {
					$search = " and ";
				} else {
					$search = " where ";
				}
				$searchAdd = $search_column." like '%".str_replace("'","",$_GET["s"])."%'";
				
			}
		}
		
		$table_name = $wpdb->prefix . "weebotLite_".$table;
		
		$all_count_real = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name where language='".get_locale()."'");
		
		$all_count_special = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name where ".$special_method." and language='".get_locale()."'");
		
		$all_count = (isset($_GET["method"])) ? $all_count_special : $all_count_real;
		
		if($search!==false) {
			$all_count_search = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $search $searchAdd and language='".get_locale()."'");
			$all_count = $all_count_search;
			$method.= $search.$searchAdd;
		} else {
			$all_count_search = 0;
		}
		
		if(empty($method)) {
			$method = " where language='".get_locale()."'";
		} else {
			$method.= " and language='".get_locale()."'";
		}
		
		$start = (isset($_GET["start"])) ? $_GET["start"]*1 : 0;
		$limit = 10;
		
		$page = (isset($_GET["start"])) ? ceil($start/$limit)+1 : 1;
			
		$orderby = (isset($_GET["orderby"])) ? $_GET["orderby"] : "id";
		$orderbySQL = strtr($orderby, array('date' => 'id', 'title' => 'action'));
		
		$order = (isset($_GET["order"])) ? $_GET["order"] : "desc";
		$orderAnti = ($order=="asc") ? "desc" : "asc";
		
		$table_name = $wpdb->prefix . "weebotLite_".$table;
		
		$events = $wpdb->get_results("SELECT * FROM $table_name".$method." order by ".$orderbySQL." ".$order." LIMIT ".$start.",".($limit));
		
		return array(
			"s" => $s,
			"search" => $search,
			"all_count_real" => $all_count_real,
			"all_count_special" => $all_count_special,
			"all_count_search" => $all_count_search,
			"all_count" => $all_count,
			"start" => $start,
			"limit" => $limit,
			"page" => $page,
			"orderby" => $orderby,
			"orderbySQL" => $orderbySQL,
			"order" => $order,
			"orderAnti" => $orderAnti,
			$table => $events
		);
	}
	
	/**
	 * Get answer-files
	 *
	 * @since		1.0.0
	 */
	public function get_answer_files() {
		$options = get_option($this->plugin_name,$this->default_options());
		$files = array();
		if ($handle = opendir(plugin_dir_path( __DIR__ )."public/template/".$options["theme"]."/chat/answer/file/")) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$files[] = $file;
				}
			}
			closedir($handle);
		}
		if(count($files)>0) {
			return $files;
		} else {
			return false;
		}
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		if(isset($_GET["action"])) {
			if($_GET["action"]=="delete") {
				uninstall_weebotLite();
			}
		}
		if(isset($_GET["action"])) {
			if($_GET["action"]=="empty") {
				uninstall_weebotLite(true);
			}
		}
		$this->template("settings");
	}
	
	/**
	 * Render the user page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_user_page() {
		$this->template("user");
	}
	
	/**
	 * Render the dashboard page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_page() {
		$this->template("index");
	}
	
	/**
	 * Update last online information of user
	 *
	 * @since    1.0.0
	 */
	public function online() {
		global $wpdb;
		$table_name_user = $wpdb->prefix . "weebotLite_user";
		$wpdb->update( $table_name_user, array( 'online' => time(), 'language' => get_locale()), array('user'=>get_current_user_id(),'type'=>'support') );
	}
	
	/**
	 * Render the questions page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_questions_page() {
		$this->template("questions");
	}
	
	/**
	 * Render the answers page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_answers_page() {
		$this->template("answers");
	}

	/**
	*  Save the plugin options
	*
	* @since    1.0.0
	*/
	public function options_update() {
		register_setting( $this->plugin_name, $this->plugin_name, array($this, 'validate') );
	}


	/**
	 * Validate all options fields
	 *
	 * @since    1.0.0
	 */
	public function validate($input) {
	
		$valid = array();

		$valid['chat'] = (isset($input['chat']) && !empty($input['chat'])) ? 1: 0;
		$valid['chat:width'] = $input['chat:width'];
		$valid['chat:welcome'] = $input['chat:welcome'];
		$valid['chat:supporter'] = (isset($input['chat:supporter']) && !empty($input['chat:supporter'])) ? 1: 0;
		$valid['footer'] = (isset($input['footer']) && !empty($input['footer'])) ? 1: 0;
		$valid['theme'] = $input['theme'];
		$valid['chat:seconds'] = $input['chat:seconds'];
		$valid['chat:hide'] = (isset($input['chat:hide']) && !empty($input['chat:hide'])) ? 1: 0;
		$valid['chat:questions'] = (isset($input['chat:questions']) && !empty($input['chat:questions'])) ? 1: 0;
		$valid['chat:messages:limit'] = $input['chat:messages:limit'];
		$valid['chat:logged'] = (isset($input['chat:logged']) && !empty($input['chat:logged'])) ? 1: 0;
		$valid['support'] = (isset($input['support']) && !empty($input['support'])) ? 1 : 0;
		$valid['online'] = (isset($input['online']) && !empty($input['online'])) ? 1 : 0;
		$valid['page:parameter'] = (isset($input['page:parameter']) && !empty($input['page:parameter'])) ? 1 : 0;
		$valid['page:hash'] = (isset($input['page:hash']) && !empty($input['page:hash'])) ? 1 : 0;
		$valid['spam'] = (isset($input['spam']) && !empty($input['spam'])) ? 1 : 0;
		$valid['spam:chars'] = $input['spam:chars'];
		$valid['spam:words'] = (isset($input['spam:words']) && !empty($input['spam:words'])) ? 1 : 0;
		$valid['spam:words:include'] = $input['spam:words:include'];
		$valid['spam:words:exclude'] = $input['spam:words:exclude'];
		$valid['spam:strings:exclude'] = $input['spam:strings:exclude'];

		return $valid;
		
	}
	
	/**
	 * Get the chat-themes
	 *
	 * @since    	1.0.0
	 * @param      	string 		$theme			Theme-Path
	 */
	public function get_themes() {
		$themes = array();
		$template_dir = plugin_dir_path( dirname( __FILE__ ) )."public/template/";
		if ($handle = opendir($template_dir)) {
			while (false !== ($theme = readdir($handle))) {
				if(file_exists($template_dir.$theme."/info.yml")) {
					$theme_info = array();
					$theme_info_content = file_get_contents($template_dir.$theme."/info.yml");
					$theme_info_lines = explode("\n",$theme_info_content);
					foreach($theme_info_lines as $line) {
						$line_data = explode("\t:\t",$line);
						if(isset($line_data[1])) {
							$theme_info["{$line_data[0]}"] = $line_data[1];
						}
					}
					$theme_info["css"] = (isset($theme_info["css"])) ? $theme_info["css"] : $theme;
					$theme_info["js"] = (isset($theme_info["js"])) ? $theme_info["js"] : "public";
					$theme_info["template"] = (isset($theme_info["template"])) ? $theme_info["template"] : $theme;
					$themes["{$theme}"] = $theme_info;
				}
			}
			return $themes;
		}
		return false;
	}

	/**
	 * AJAX functions
	 *
	 * @since    1.0.0
	 */
	public function AJAX() {
		$plugin = new weebotLite();
		$options = get_option($this->plugin_name,$this->default_options());
		
		global $wpdb;
		
		switch($_POST["method"]) {
		
			/**
			 * Initialize weebotLite
			 */
			case "init":
				if($options["support"]==1) {
					$user = $this->get_user();
					if($user!==false) {
						print_r($user);
					}
				}
			break;
			
			/**
			 * Add/Remove support user
			 */
			case "user":
				switch($_POST["type"]) {
					case "1":
						$table_name = $wpdb->prefix . "weebotLite_user";
						$rows_affected = $wpdb->insert( $table_name, array( 
							'id' => null,
							'user' => $_POST["user"],
							'language' => get_locale(),
							'type' => 'support'
						));
					break;
					case "0":
						$table_name = $wpdb->prefix . "weebotLite_user";
						$delete = $wpdb->get_results("DELETE FROM $table_name where user='".$_POST["user"]."' and type='support'");
					break;
				}
			break;
			
			/**
			 * Delete questions/answers/users
			 */
			case "delete":
				$delete = $this->delete($_POST["table"],$_POST["id"]);
			break;
			
			/**
			 * Check for new questions
			 */
			case "check":
				echo $this->get_unanswered_questions_count()."<!weebotLite!>";
				$table_name_user = $wpdb->prefix . "weebotLite_user";
				$wpdb->update( $table_name_user, array( 'online' => time(), 'language' => get_locale()), array('user'=>get_current_user_id(),'type'=>'support') );
				$chats = $this->get_supporter_chat();
				if($chats!==false) {
					$table_name_user = $wpdb->prefix . "weebotLite_chats";
					foreach($chats as $chat) {
						$wpdb->update( $table_name_user, array( 'status' => 1 ), array( 'id'=> $chat->id ) );
						$this->template( "chat/question/new" , $chat );
					}
				}
			break;
			
			/**
			 * Lookup existing answers
			 */
			case "lookup":
				switch($_POST["data"]["type"]) {
					case "answers":
						$table_name = $wpdb->prefix . "weebotLite_events";
						$answers = $wpdb->get_results("SELECT * FROM $table_name WHERE action LIKE '%".$_POST["data"]["query"]."%' or id='".$_POST["data"]["query"]."' LIMIT 0,100");
						$this->template( "chat/answers/create/existing" , array("answers"=>$answers,"question"=>$_POST["data"]["id"]) );
					break;
				}
			break;
			
			/**
			 * Answer a question
			 */
			case "answer":
				$question = $this->get_question_by_id($_POST["data"]["question"]);
				if($question->answered!=1) {
					$answer = $this->create_answer();
				}
			break;
		}
		exit;
	}
	
	/**
	 * Delete information (answer, question, user)
	 *
	 * @since		1.0.0
	 * @param		text    	$table			Database-table
	 * @param		int    		$id				Row-ID
	 */
	public function delete($table,$id) {
		global $wpdb;
		$table_name = $wpdb->prefix . "weebotLite_".$table;
		$delete = $wpdb->delete($table_name,array( 'id' => $id ), array( '%d' ) );
		switch($table) {
		
			/**
			 * Delete questions
			 */
			case "questions":
				$table_name = $wpdb->prefix . "weebotLite_bridge";
				$bridges = $wpdb->get_results("SELECT * FROM $table_name WHERE question='".$id."'");
				if(count($bridges)>0) {
					foreach($bridges as $bridge) {
						$table_name = $wpdb->prefix . "weebotLite_events";
						#$delete = $wpdb->delete($table_name,array( 'id' => $bridge->event ), array( '%d' ) );
						$delete = $wpdb->get_results("DELETE FROM $table_name where id='".$bridge->event."'");
						$table_name = $wpdb->prefix . "weebotLite_bridge";
						$delete = $wpdb->delete($table_name,array( 'id' => $bridge->id ), array( '%d' ) );
					}
				}
				$table_name = $wpdb->prefix . "weebotLite_chats";
				$delete = $wpdb->delete($table_name,array( 'question' => $id ), array( '%d' ) );
			break;
			
			/**
			 * Delete answers
			 */
			case "events":
				$table_name = $wpdb->prefix . "weebotLite_bridge";
				$bridges = $wpdb->get_results("SELECT * FROM $table_name WHERE event='".$id."'");
				if(count($bridges)>0) {
					foreach($bridges as $bridge) {
						$table_name = $wpdb->prefix . "weebotLite_questions";
						$delete = $wpdb->delete($table_name,array( 'id' => $bridge->question ), array( '%d' ) );
						$table_name = $wpdb->prefix . "weebotLite_chats";
						$delete = $wpdb->delete($table_name,array( 'question' => $bridge->question ), array( '%d' ) );
						$table_name = $wpdb->prefix . "weebotLite_bridge";
						$delete = $wpdb->delete($table_name,array( 'id' => $bridge->id ), array( '%d' ) );
					}
				}
			break;
			
			/**
			 * Delete user
			 */
			case "user":
				$table_name = $wpdb->prefix . "weebotLite_bridge";
				$bridges = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$id."'");
				if(count($bridges)>0) {
					foreach($bridges as $bridge) {
						$this->delete("questions",$bridge->question);
						$this->delete("events",$bridge->event);
					}
				}
			break;
		}
	}
	
	/**
	 * Edit answers
	 *
	 * @since		1.0.0
	 */
	public function edit_answer() {
		global $wpdb;

			$page = (isset($_POST["{$this->plugin_name}"]["page"])) ? $_POST["{$this->plugin_name}"]["page"] : 0;
			$page_related = (isset($_POST["{$this->plugin_name}"]["page"])) ? 1 : 0;
			$action = ($_POST["{$this->plugin_name}"]["type"]==3) ? $_POST["{$this->plugin_name}"]["file"] : $_POST["{$this->plugin_name}"]["action"];
			$wpdb->update( $wpdb->prefix . "weebotLite_events", array( 'page' => $page, 'type' => $_POST["{$this->plugin_name}"]["type"], 'action' => $action ), array('id'=>$_GET["post"]) );
		
		$wpdb->update( $wpdb->prefix . "weebotLite_bridge", array( 'type' => $_POST["{$this->plugin_name}"]["type"] ), array('event'=>$_GET["post"]) );
		$questions = $this->get_questions_by_answer($_GET["post"]);
		if(count($questions)>0) {
			foreach($questions as $question) {
				$wpdb->update( $wpdb->prefix . "weebotLite_questions", array( 'type' => $_POST["{$this->plugin_name}"]["type"], 'page_related' => $page_related ), array('id'=>$question->id) );
			}
		}
	}
	
	/**
	 * Create an answer
	 *
	 * @since		1.0.0
	 */
	public function create_answer() {
		
		global $wpdb;
		
		$postPluginName = $this->plugin_name;
		
		$post = isset($_POST["$postPluginName"]) ? $_POST["$postPluginName"] : $_POST["data"];
		
		$table_name = $wpdb->prefix . "weebotLite_events";
		$time = date("Y-m-d H:i:s");
		$action = ($post["type"]==3) ? $post["file"] : stripslashes($post["action"]);
		if(isset($post["page"])) {
			if($post["page"]!=0) {
				$page = $post["page"];
				if(isset($post["question"])) {
					$table_name_questions = $wpdb->prefix . "weebotLite_questions";
					$wpdb->update( $table_name_questions, array( 'page_related' => 1), array('id'=>$post["question"]) );
				}
			} else {
				$page = 0;
			}
		} else {
			$page = 0;
		}

			$question = $this->get_question_by_id($post["question"]);
			$locale = $question->language;
		
		$existing = false;
		if(isset($post["existing"])) {
			if($post["existing"]!=-1) {
				$existing = true;
			}
		}
		if($existing===false) {
			$rows_affected = $wpdb->insert( $table_name, array( 
				'id' => null,
				'time' => $time,
				'type' => $post["type"],
				'action' => $action,
				'language' => $locale,
				'page' => $page,
				'user' => get_current_user_id()
			));
		}
		if(isset($post["question"])) {
			if($existing===false) {
				$new_event = $wpdb->get_results("SELECT * FROM $table_name where time='$time' and type='".$post["type"]."' and user='".get_current_user_id()."'");
			} else {
				$new_event = $wpdb->get_results("SELECT * FROM $table_name where id='".$post["existing"]."'");
			}
			if(count($new_event)==1) {
				$table_name_bridge = $wpdb->prefix . "weebotLite_bridge";
				$rows_affected = $wpdb->insert( $table_name_bridge, array( 
					'id' => null,
					'time' => $time,
					'event' => $new_event[0]->id,
					'question' => $post["question"]
				));
				$table_name_questions = $wpdb->prefix . "weebotLite_questions";
				$wpdb->update( $table_name_questions, array( 'answered' => 1, 'type' => $new_event[0]->type), array('id'=>$post["question"]) );
				$table_name_chats = $wpdb->prefix . "weebotLite_chats";
				$wpdb->update( $table_name_chats, array( 'status' => 2), array('question'=>$post["question"]) );
				$table_name_bridge = $wpdb->prefix . "weebotLite_bridge";
				$thisQuestion = $this->get_question_by_id($post["question"]);
				if($new_event[0]->type=="-1") {
					$wpdb->update( $table_name_bridge, array( 'type' => $new_event[0]->type,'event' => $new_event[0]->id,), array('question'=>$post["question"],'user'=>$post["user"],'type'=>'-2') );
				} else {
					if($page==0) {
						$questions = $wpdb->get_results("SELECT * FROM $table_name_questions where words='".$thisQuestion->words."' and answered=0 and page_related=0 and id!=".$thisQuestion->id);
					} else {
						$questions = $wpdb->get_results("SELECT * FROM $table_name_questions where words='".$thisQuestion->words."' and answered=0 and page=".$page." and id!=".$thisQuestion->id);
					}
					if(count($questions)>0) {
						foreach($questions as $question) {
							$delete_question = $wpdb->get_results("DELETE FROM $table_name_questions where id='".$question->id."'");
							$bridges = $wpdb->get_results("SELECT * FROM $table_name_bridge where question='".$question->id."' and user!=0");
							foreach($bridges as $bridge) {
								$wpdb->update( $table_name_bridge, array( 'type' => $new_event[0]->type,'event' => $new_event[0]->id,'question'=>$post["question"]), array('id'=>$bridge->id) );
								$rows_affected = $wpdb->insert( $table_name_bridge, array( 
									'id' => null,
									'time' => $time,
									'type' => $new_event[0]->type,
									'event' => $new_event[0]->id,
									'question' => $post["question"],
									'question_old' => $bridge->question
								));
							}
						}
					}
					$wpdb->update( $table_name_bridge, array( 'type' => $new_event[0]->type,'event' => $new_event[0]->id), array('question'=>$post["question"],'type'=>'-2') );
				}
				if($new_event[0]->type!=3) {
					$emails = $wpdb->get_results("SELECT * FROM $table_name_bridge where question='".$post["question"]."' and type=".$new_event[0]->type." and event=".$new_event[0]->id." and email=1");
					foreach($emails as $email) {
						$user_data = $this->user_data($email->user);
						$email_address = (isset($user_data->data)) ? $user_data->data->user_email : $user_data->email;
						$action = ($new_event[0]->type==2) ? '<a href="'.$new_event[0]->action.'">'.$new_event[0]->action.'</a>': $new_event[0]->action;
						wp_mail( 
							$email_address, 
							$post["subject"], 
							'<em>'.$thisQuestion->question."</em><br /><strong>".$action."</strong>", 
							array('Content-Type: text/html; charset=UTF-8') 
						);
						$wpdb->update( $table_name_bridge, array( 'email' => 2), array('id'=>$email->id) );
					}
				}
			}
		}
	}
	
	/**
	 * Get file info
	 *
	 * @since    	1.0.0
	 * @param      	int    		$id			File-ID
	 */
	public function get_file_info($id) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_files";
		$file = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$id."'");
		
		if(count($file)>0) {
			return $file[0];
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get answer by question ID
	 *
	 * @since		1.0.0
	 * @param		int    		$question			Question-ID
	 */
	public function get_answer_by_question($question) {
	
		global $wpdb;

		$table_name = $wpdb->prefix . "weebotLite_bridge";
		$bridge = $wpdb->get_results("SELECT * FROM $table_name WHERE question='".$question."' and event!=0");
		
		if(count($bridge)>0) {
			$table_name = $wpdb->prefix . "weebotLite_events";
			$results = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$bridge[0]->event."'");
			if(count($results)>0) {
				return $results;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get questions by answer ID
	 *
	 * @since		1.0.0
	 * @param		int    		$event			Answer-ID
	 */
	public function get_questions_by_answer($event) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_bridge";
		$bridge = $wpdb->get_results("SELECT * FROM $table_name WHERE event='".$event."' and user=0");
		
		if(count($bridge)>0) {
			$table_name = $wpdb->prefix . "weebotLite_questions";
			$results = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$bridge[0]->question."'");
			if(count($results)>0) {
				return $results;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get all indexed pages
	 *
	 * @since		1.0.0
	 */
	public function get_pages() {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_pages";
		$pages = $wpdb->get_results("SELECT * FROM $table_name");
		
		if(count($pages)>0) {
			return $pages;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get active supporter-chat
	 *
	 * @since		1.0.0
	 */
	public function get_supporter_chat() {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_chats";
		$chats = $wpdb->get_results("SELECT * FROM $table_name WHERE support='".get_current_user_id()."' and status=0");
		
		if(count($chats)>0) {
			return $chats;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get all online user
	 *
	 * @since		1.0.0
	 */
	public function get_online_user() {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->plugin_name . "_user";
		$user = $wpdb->get_results("SELECT * FROM $table_name WHERE type!='support' and language='".get_locale()."' and `online` > ".(time()-6)." ORDER BY online ASC");
		if(count($user)>0) {
			return $user;
		} else {
			return array();
		}
	}
	
	/**
	 * Get chat-log of user
	 *
	 * @since		1.0.0
	 * @param		int    		$start			Chat-log (start)
	 * @param		int    		$limit			Chat-log (limit)
	 * @param		int    		$user			User-ID
	 */
	public function user_log($start=1,$limit=10,$user) {

		global $wpdb;
		
		$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
		$log = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' ORDER BY id DESC LIMIT {$start},{$limit}");
		
		if(count($log)>0) {
			return $log;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get user data
	 *
	 * @since		1.0.0
	 * @param		int    		$user			User-ID
	 */
	public function user_data($user=false) {
		if ( is_user_logged_in() && $user===false ) {
			return get_userdata(get_current_user_id());
		} else {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->plugin_name . "_user";
			$result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$user."'");
			if(count($result)>0) {
				return $result[0];
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Get answer data
	 *
	 * @since		1.0.0
	 * @param		int    		$id			Answer-ID
	 */
	public function get_answer_by_id($id) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_events";
		$event = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$id."'");
		
		if(count($event)>0) {
			return $event[0];
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get words
	 *
	 * @since		1.0.0
	 * @param		text    		$ids		Word-IDs - seperated by :
	 */
	public function get_words_by_ids($ids) {
	
		global $wpdb;
		
		$idsArr = explode(":",$ids);
		
		$table_name = $wpdb->prefix . "weebotLite_words";
		
		$words = array();
		
		foreach($idsArr as $id) {
			
			$word = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$id."'");
			
			if(count($word)>0) {
				$words[] = $word[0];
			}
		}
		
		return $words;
		
	}
	
	/**
	 * Get "old" question
	 * Needed if a question was asked a few times and answered by one answer (no gossip)
	 *
	 * @since		1.0.0
	 * @param		int    		$id		Question-ID
	 */
	public function get_question_by_old_question($id) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_bridge";
		$question = $wpdb->get_results("SELECT * FROM $table_name WHERE question_old='".$id."'");
		
		if(count($question)>0) {
			return $question[0];
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get question data
	 *
	 * @since		1.0.0
	 * @param		int    		$id		Question-ID
	 */
	public function get_question_by_id($id) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_questions";
		$question = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$id."'");
		
		if(count($question)>0) {
			return $question[0];
		} else {
			return false;
		}
		
	}
	
	/**
	 * Include template-files
	 *
	 * @since		1.0.0
	 * @param		text    	$file			Path to file
	 * @param		array    	$parameter		Variables/Data
	 */
	public function template($file,$parameter=false) {
		if(file_exists(plugin_dir_path( dirname( __FILE__ ) )."admin/template/".$file.".phtml")) {
			include plugin_dir_path( dirname( __FILE__ ) )."admin/template/".$file.".phtml";
		}
	}
	
	/**
	 * Get default plugin-options
	 *
	 * @since		1.0.0
	 */
	public function default_options() {
		$weebotLite = new weebotLite( $this->plugin_name, $this->version );
		return $weebotLite->default_options();
	}

}
