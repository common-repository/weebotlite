<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.weedesign.de
 * @since      1.0.0
 *
 * @package    weebotLite
 * @subpackage weebotLite/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, hooks and functions for the frontend
 *
 * @package    weebotLite
 * @subpackage weebotLite/public
 * @author     Wolfgang Ertl <wolfgang.ertl@weedesign.de>
 */
class weebotLite_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function provides Stylesheets for the frontend
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
		*	Get plugin-options
		*/
		$options = get_option($this->plugin_name,$this->default_options());
		
		/*
		*	Check if user is a supporter
		*/
		$is_supporter = $this->is_supporter();
		
		/*
		*	Check if chat is visible
		*/
		$hide = false;
		
		if($options["chat:hide"]==1&&$is_supporter===true) {
			$hide = true;
		}
		
		if($options["chat:logged"]==1&&!is_user_logged_in()) {
			$hide = true;
		}
		
		if($hide===false) {

			/*
			*	nanoscroller (vendor)
			*/
			wp_enqueue_style( $this->plugin_name."-nanoscroller", plugin_dir_url( __FILE__ ) . 'assets/css/nanoscroller.css', array(), $this->version, 'all' );
			
			/*
			*	Frontend styles
			*/
			$theme = $this->get_theme();
			wp_enqueue_style( $this->plugin_name."-public", plugin_dir_url( __FILE__ ) . 'assets/css/'.$theme["css"].'.css', array(), $this->version, 'all' );
				
		}
		
		/*
		*	If user is supporter and questions should popup in frontend
		*/
		if($options["chat:questions"]==1) {
			if($this->is_supporter()===true) {
				if($hide===true) {
					/*
					*	nanoscroller (vendor)
					*/
					wp_enqueue_style( $this->plugin_name."-nanoscroller", plugin_dir_url( __FILE__ ) . 'assets/css/nanoscroller.css', array(), $this->version, 'all' );
				}
				/*
				*	Backend styles
				*/
				wp_enqueue_style( $this->plugin_name."-admin", str_replace("public/","admin/",plugin_dir_url( __FILE__ )) . 'assets/css/admin.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function provides javascript for the frontend
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
		*	Get plugin-options
		*/
		$options = get_option($this->plugin_name,$this->default_options());
		
		/*
		*	Check if user is a supporter
		*/
		$is_supporter = $this->is_supporter();
		
		/*
		*	Check if chat is visible
		*/
		$hide = false;
		
		if($options["chat:hide"]==1&&$is_supporter===true) {
			$hide = true;
		}
		
		if($options["chat:logged"]==1&&!is_user_logged_in()) {
			$hide = true;
		}
		
		if($hide===false) {
		
			/*
			*	nanoscroller (vendor)
			*/
			wp_register_script( 
				$this->plugin_name.'-nanoscroller', 
				plugin_dir_url( __FILE__ ) . 'assets/js/jquery.nanoscroller.min.js', 
				array( 'jquery' )
			);
			wp_enqueue_script( $this->plugin_name.'-nanoscroller' );

			/*
			*	Frontend functions
			*/
			$theme = $this->get_theme();
			wp_register_script( 
				$this->plugin_name.'-public', 
				plugin_dir_url( __FILE__ ) . 'assets/js/'.$theme["js"].'.js', 
				array( 'jquery' )
			);
			wp_enqueue_script( $this->plugin_name.'-public' );
			wp_localize_script( $this->plugin_name.'-public', $this->plugin_name.'AJAX', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
		}
		
		/*
		*	If user is supporter and questions should popup in frontend
		*/
		if($options["chat:questions"]==1) {
		
			if($is_supporter===true) {
			
				/*
				*	nanoscroller (vendor)
				*/
				if($hide===true) {
					wp_register_script( 
						$this->plugin_name.'-nanoscroller', 
						plugin_dir_url( __FILE__ ) . 'assets/js/jquery.nanoscroller.min.js', 
						array( 'jquery' )
					);
					wp_enqueue_script( $this->plugin_name.'-nanoscroller' );
				}
				
				/*
				*	Admin functions
				*/
				wp_register_script( 
					$this->plugin_name.'-admin', 
					str_replace("public/","admin/",plugin_dir_url( __FILE__ )) . 'assets/js/admin.js', 
					array( 'jquery' )
				);
				wp_enqueue_script( $this->plugin_name.'-admin' );
				wp_localize_script( $this->plugin_name.'-admin', $this->plugin_name.'AJAXadmin', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			}
		
		}

	}
	
	/*
	*	Add Shortcode to Frontend
	*/
	public function add_shortcode() {
		add_shortcode( $this->plugin_name, array($this,'shortcode') );
	}
	
	/*
	*	Display Shortcode
	*/
	public function shortcode($atts,$content=false) {
		$atts = shortcode_atts( array(
			'chat' => 0
		), $atts, 'shortcode' );
		$this->template("chat/shortcode",array("atts"=>$atts,"content"=>$content));
	}
	
	/**
	 * Get answers by question IDs
	 *
	 * @since    	1.0.0
	 * @param      	string    $questions			Question-IDs - seperated by <!weebotLite!>
	 */
	public function get_answers($questions) {
	
		global $wpdb;
		
		$user = $this->user();
		$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
			
		foreach($questions as $question_id) {
			echo $question_id."<!weebotLite!>";
			// get chat-log-message-ids
			$question = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' and question='".$question_id."'");
			if(count($question)>0) {
				if($question[0]->type!=-2) {
					$this->include_answer($question_id,$question[0]->type);
				} else {
					$table_name_chats = $wpdb->prefix . $this->plugin_name . "_chats";
					$question = $wpdb->get_results("SELECT * FROM $table_name_chats WHERE user='".$user."' and question='".$question_id."'");
					if(count($question)>0) {
						echo $question[0]->status;
					}
				}
			} else {
				$question = $wpdb->get_results("SELECT * FROM $table_name WHERE question_old='".$question_id."'");
				if(count($question)>0) {
					$this->include_answer($question[0]->question,$question[0]->type);
				}
			}
			
			echo "<!weebotLite!>";
		}
		
		$table_name_user = $wpdb->prefix . $this->plugin_name . "_user";
		$wpdb->update( $table_name_user, array( 'online' => time()), array('id'=>$user));
		
	}
	
	/**
	 * AJAX functions
	 *
	 * @since    1.0.0
	 */
	public function AJAX () {

		$options = get_option($this->plugin_name,$this->default_options());
		
		switch($_POST["method"]) {
		
			/**
			 * Initialize the plugin
			 */
			case "init":
				echo json_encode($options);
			break;
			
			/**
			 * Add Chat to footer
			 */
			case "init:footer":
				echo $this->template("chat");
			break;
			
			/**
			 * Check for answers
			 */
			case "check":
				$answers = $this->get_answers($_POST["questions"]);
			break;
			
			/**
			 * Track online user
			 */
			case "online":
				global $wpdb;
				$user = $this->user();
				$page = $this->get_page_by("page",$_POST["page"]);
				$table_name_user = $wpdb->prefix . $this->plugin_name . "_user";
				$wpdb->update( $table_name_user, array( 'online' => time(), 'page' => $page->id), array('id'=>$user));
			break;
			
			/**
			 * File Upload
			 */
			case "upload":
				
				global $wpdb;
			
				$error = false;
				
				if($options["chat:message:upload"]==1) {
				
					$user = $this->user();
					
					$time = date("Y-m-d H:i:s");
					
					$fileIDs = "";
					
					for($i=0;$i<count($_FILES["files"]["name"]);$i++) {
						if($_FILES["files"]["size"][$i]<=wp_max_upload_size()) {
							$file_extension = explode(".",$_FILES["files"]["name"][$i]);
							$target_file = plugin_dir_path(dirname( __FILE__ )) ."public/upload/".md5($_FILES["files"]["name"][$i])."-".md5($time).".".$file_extension[count($file_extension)-1];
							if (move_uploaded_file($_FILES["files"]["tmp_name"][$i], $target_file)) {
								$table_name_files = $wpdb->prefix . $this->plugin_name . "_files";
								$insert_file = $wpdb->insert( $table_name_files, array( 
									'id' => null,
									'name' => $_FILES["files"]["name"][$i],
									'type' => $_FILES["files"]["type"][$i],
									'time' => $time,
									'user' => $user,
									'size' => $_FILES["files"]["size"][$i]
								));
								if($insert_file==1){
									$get_file_id = $wpdb->get_results("SELECT * FROM $table_name_files WHERE user='".$user."' and time='".$time."' and size='".$_FILES["files"]["size"][$i]."' and type='".$_FILES["files"]["type"][$i]."' and name='".$_FILES["files"]["name"][$i]."' ORDER BY id DESC LIMIT 0,1");
									if(count($get_file_id)>0) {
										if(!empty($fileIDs)) {
											$fileIDs.= ":";
										}
										$fileIDs.= $get_file_id[0]->id;
									}
								}
							} else {
								$error = true;
							}
						} else {
							$error = true;
						}
					}
					
					$table_name = $wpdb->prefix . $this->plugin_name . "_questions";
					
					$page = $this->get_page_by("page",$_POST["page"]);
					
					$insert_question = $wpdb->insert( $table_name, array( 
						'id' => null,
						'files' => $fileIDs,
						'language' => get_locale(),
						'time' => $time,
						'user' => $user,
						'type' => -2,
						'page' => $page->id,
						'parent' => $_POST["parent"],
						'counter' => 1
					));
					if($insert_question==1){
						$table_name_bridge = $wpdb->prefix . $this->plugin_name . "_bridge";
						$question = $this->new_question_by_user($user);
						$insert_question = $wpdb->insert( $table_name_bridge, array( 
							'id' => null,
							'question' => $question,
							'time' => date("Y-m-d H:i:s"),
							'user' => $user,
							'type' => -2
						));
						echo $question."<!weebotLite!>";
						$supporter = $this->get_supporter();
						if($supporter===false) {
							$this->template("chat/answer/fallback",array("options" => $options, "question" => $question));
						}
						echo "<!weebotLite!>";
						$this->template("chat/question/question/files",array("ids"=>$fileIDs));
					}
					
					$table_name_user = $wpdb->prefix . $this->plugin_name . "_user";
					$wpdb->update( $table_name_user, array( 'online' => time()), array('id'=>$user));
				
				}
			break;
			
			/**
			 * Send a Screenshot
			 */
			case "screenshot":
				
				if($options["chat:message:screenshot"]==1) {
				
					global $wpdb;
					
					$user = $this->user();
					
					$time = date("Y-m-d H:i:s");
					
					$page = $this->get_page_by("page",$_POST["page"]);
					
					$table_name = $wpdb->prefix . $this->plugin_name . "_screenshots";
					$insert_screenshot = $wpdb->insert( $table_name, array( 
						'id' => null,
						'page' => $page->id,
						'time' => $time,
						'width' => $_POST["width"],
						'height' => $_POST["height"],
						'x' => $_POST["x"],
						'y' => $_POST["y"],
						'window' => $_POST["window"],
						'document' => $_POST["document"],
						'user' => $user,
					));
					if($insert_screenshot==1){
						$get_screenshot_id = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' and time='".$time."' and x='".$_POST["x"]."' and y='".$_POST["y"]."' and width='".$_POST["width"]."' and height='".$_POST["height"]."' ORDER BY id DESC LIMIT 0,1");
						$table_name = $wpdb->prefix . $this->plugin_name . "_questions";
						$insert_question = $wpdb->insert( $table_name, array( 
							'id' => null,
							'screenshot' => $get_screenshot_id[0]->id,
							'language' => get_locale(),
							'time' => $time,
							'user' => $user,
							'type' => -2,
							'page' => $page->id,
							'parent' => $_POST["parent"],
							'counter' => 1
						));
						if($insert_question==1){
							$table_name_bridge = $wpdb->prefix . $this->plugin_name . "_bridge";
							$question = $this->new_question_by_user($user);
							$insert_question = $wpdb->insert( $table_name_bridge, array( 
								'id' => null,
								'question' => $question,
								'time' => date("Y-m-d H:i:s"),
								'user' => $user,
								'type' => -2
							));
							echo $question."<!weebotLite!>";
							$supporter = $this->get_supporter();
							if($supporter===false) {
								$this->template("chat/answer/fallback",array("options" => $options, "question" => $question));
							}
							echo "<!weebotLite!>";
							$this->template("chat/question/question/screenshot",array("id"=>$get_screenshot_id[0]->id));
						}
					
					}
					
					$table_name_user = $wpdb->prefix . $this->plugin_name . "_user";
					$wpdb->update( $table_name_user, array( 'online' => time()), array('id'=>$user));
				
				}
			break;
			
			/**
			 * Activate email notification
			 */
			case "email":
				global $wpdb;
				$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
				$table_name_user = $wpdb->prefix . $this->plugin_name . "_user";
				$user = $this->user();
				$result = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' and event=0 and question=".$_POST["question"]." ORDER BY id DESC LIMIT 0,1");
				if(count($result)>0) {
					$wpdb->update( $table_name, array( 'email' => 1, 'online' => time()), array('id'=>$result[0]->id));
					$wpdb->update( $table_name_user, array( 'email' => $_POST["email"], 'name' => $_POST["name"], 'online' => time()), array('id'=>$user));
				}
			break;
			
			/**
			 * Get User-Log
			 */
			case "user:log":
				$logs = $this->user_log($_POST["start"],$options["chat:messages:limit"]);
				if($logs!==false) {
					$limit = ($_POST["start"]*1)+($options["chat:messages:limit"]*1);
					$logs = array_reverse($logs);
					if(count($logs)==$options["chat:messages:limit"]) {
						echo "<div class='more' data-start='".($limit)."'></div>";
					}
					foreach($logs as $log) { 
						$question = $this->get_question_by_id($log->question);
						$this->template("chat/question/question",array("question"=>$question,"log"=>$log));
						if($log->type!=-2) {
							$this->include_answer($log->question,$log->type);
						} else {
							$supporter = $this->get_supporter($question->id);
							if($supporter===false) {
								$this->template("chat/answer/fallback",array("options" => $options, "question" => $question->id));
							}
						}
					}
				}
			break;
			
			/**
			 * Send a new message
			 */
			case "chat:send":
				$question = $_POST["question"];
				$question = strtr(stripslashes($question), array('.' => ' ', ',' => ' ', '?' => ' ', '!' => ' ', ':' => ' ', "'" => '', "\n" => ' '));
				$words_question = explode(" ",strtolower($question));
				
				$send = true;
				if($options["spam"]==1) {
					if(strlen($question)<$options["spam:chars"]) {
						$send = false;
					}
					if($options["spam:words"]==1) {
						if($options["spam:words:include"]!="") {
							$send = false;
							$words_include = explode(",",str_replace(", ",",",$options["spam:words:include"]));
							foreach($words_include as $word_include) {
								foreach($words_question as $word_question) {
									if($word_question==$word_include) {
										$send = true;
									}
								}
							}
						}
						if($options["spam:words:exclude"]!="") {
							$words_exclude = explode(",",str_replace(", ",",",$options["spam:words:exclude"]));
							foreach($words_exclude as $word_exclude) {
								foreach($words_question as $word_question) {
									if($word_question==$word_exclude) {
										$send = false;
									}
								}
							}
						}
						if($options["spam:strings:exclude"]!="") {
							$strings_exclude = explode(",",str_replace(", ",",",$options["spam:strings:exclude"]));
							foreach($strings_exclude as $string_exclude) {
								if(strpos($question,$string_exclude)!==false) {
									$send = false;
								}
							}
						}
					}
				}
				if($send===true) {
					global $wpdb;
					$table_name = $wpdb->prefix . $this->plugin_name . "_words";
					$wordsIDarray = array();
					foreach($words_question as $word_question) {
						if(!empty($word_question)) {
							$word_sql = $wpdb->get_results("SELECT * FROM $table_name WHERE word='".$word_question."'");
							if(count($word_sql)==0) {
								$rows_affected = $wpdb->insert( $table_name, array( 
									'id' => null,
									'counter' => 1,
									'time' => date("Y-m-d H:i:s"),
									'word' => $word_question
								));
								$word_sql = $wpdb->get_results("SELECT * FROM $table_name WHERE word='".$word_question."'");
								$wordsIDarray[] = $word_sql[0]->id;
							} else {
								$wpdb->update( $table_name, array( 'counter' => ($word_sql[0]->counter*1)+1), array('id'=>$word_sql[0]->id));
								$wordsIDarray[] = $word_sql[0]->id;
							}
						}
					}
					asort($wordsIDarray);
					$wordsCounter = 0;
					$wordIDs = "";
					foreach($wordsIDarray as $wordID) {
						if($wordsCounter>0) {
							$wordIDs.= ":";
						}
						$wordIDs.= $wordID;
						$wordsCounter++;
					}
					
					$table_name = $wpdb->prefix . $this->plugin_name . "_questions";

					$page = $this->get_page_by("page",$_POST["page"]);
					
					$user = $this->user();
					
					$time = date("Y-m-d H:i:s");
					$insert_question = $wpdb->insert( $table_name, array( 
						'id' => null,
						'question' => stripslashes($_POST["question"]),
						'words' => $wordIDs,
						'language' => get_locale(),
						'time' => $time,
						'user' => $user,
						'type' => -2,
						'page' => $page->id,
						'parent' => $_POST["parent"],
						'counter' => 1
					));
					if($insert_question==1){
						$table_name_bridge = $wpdb->prefix . $this->plugin_name . "_bridge";
						$question = $this->new_question_by_user($user);
						$insert_question = $wpdb->insert( $table_name_bridge, array( 
							'id' => null,
							'question' => $question,
							'time' => date("Y-m-d H:i:s"),
							'user' => $user,
							'type' => -2
						));
						echo $question."<!weebotLite!>";
						$supporter = $this->get_supporter();
						if($supporter===false) {
							$this->template("chat/answer/fallback",array("options" => $options, "question" => $question));
						}
					}
					
					$table_name_user = $wpdb->prefix . $this->plugin_name . "_user";
					$wpdb->update( $table_name_user, array( 'online' => time()), array('id'=>$user));
					
				} else {
					echo "spam<!weebotLite!>";
					$this->template("chat/answer/spam",array("options" => $options));
				}
			break;

		}

		exit;
	}
	
	/**
	 * Check if email notification is active
	 *
	 * @since    	1.0.0
	 * @param      	int    $question			Question-ID
	 * @param      	int    $user				User-ID
	 */
	public function in_email_list($question,$user=false) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
		if($user===false) {
			$user = $this->user(false);
		}
		$result = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' and event=0 and question=".$question." and email=1 ORDER BY id DESC LIMIT 0,1");
		if(count($result)>0) {
			return $result[0];
		} else {
			return false;
		}
	}
	
	/**
	 * Get user data
	 *
	 * @since    	1.0.0
	 * @param      	int    $user				User-ID
	 */
	public function user_data($user=false) {
		if ( is_user_logged_in() ) {
			return get_userdata(get_current_user_id());
		} else {
			global $wpdb;
			$table_name = $wpdb->prefix . $this->plugin_name . "_user";
			if($user===false) {
				$user = $this->user(false);
			}
			$result = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$user."'");
			if(count($result)>0) {
				return $result[0];
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Display an answer for a question
	 *
	 * @since    	1.0.0
	 * @param      	int    		$id			Question-ID
	 * @param      	int	    	$user		User-ID
	 */
	public function include_answer($id,$type) {
		$answer = $this->get_answer_by_question($id,$type);
		if($answer!==false) {
			if(isset($answer->type)) {
				switch($answer->type) {
					case -1:
						$this->template("chat/answer/gossip",array("question"=>$id,"answer"=>$answer));
					break;
					case 0:
						$this->template("chat/answer/answer",array("question"=>$id,"answer"=>$answer));
					break;
					case 1:
						$this->template("chat/answer/question",array("question"=>$id,"answer"=>$answer));
					break;
					case 2:
						$this->template("chat/answer/url",array("question"=>$id,"answer"=>$answer));
					break;
					case 3:
						$this->template("chat/answer/file",array("question"=>$id,"answer"=>$answer));
					break;
				}
			} else {
				$this->template("chat/answer/gossip",array("question"=>$id,"answer"=>$answer));
			}
		} else {
			$this->template("chat/answer/lost",array("question"=>$id,"answer"=>$answer));
		}
	}
	
	/**
	 * Display time ago
	 *
	 * @since    	1.0.0
	 * @param      	date    		$datetime		Datetime
	 * @param      	boolean	    	$full			Full Output
	 * @timestamp   boolean	    	$timestamp		Is Timestamp
	 */
	function time_elapsed_string($datetime, $full = false, $timestamp = false) {
		$now = new DateTime;
		if($timestamp===true) {
			$date = new DateTime;
			$date->setTimestamp($datetime);
			$datetime = $date->format("Y-m-d H:i:s");
		}
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => esc_attr__( 'year', $this->plugin_name ),
			'm' => esc_attr__( 'month', $this->plugin_name ),
			'w' => esc_attr__( 'week', $this->plugin_name ),
			'd' => esc_attr__( 'day', $this->plugin_name ),
			'h' => esc_attr__( 'hour', $this->plugin_name ),
			'i' => esc_attr__( 'minute', $this->plugin_name ),
			's' => esc_attr__( 'second', $this->plugin_name ),
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? esc_attr__( 's', $this->plugin_name ) : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' '.esc_attr__( 'ago', $this->plugin_name ) : esc_attr__( 'just now', $this->plugin_name );
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
	 * Get Screenshot
	 *
	 * @since    	1.0.0
	 * @param      	int    		$id			Screenshot-ID
	 */
	public function get_screenshot($id) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "weebotLite_screenshots";
		$screenshot = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$id."'");
		
		if(count($screenshot)>0) {
			return $screenshot[0];
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get question data
	 *
	 * @since    	1.0.0
	 * @param      	int    		$id			Question-ID
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
	 * Include a template file
	 *
	 * @since    	1.0.0
	 * @param      	string 		$file			Path to file
	 * @param      	array	    $parameter		Variables/Parameter
	 */
	public function template($file,$parameter=false) {
		$theme = $this->get_theme();
		$filename = plugin_dir_path(dirname( __FILE__ )) ."public/template/".$theme["template"]."/".$file.".phtml";
		$filename_fallback = plugin_dir_path(dirname( __FILE__ )) ."public/template/default/".$file.".phtml";
		if(file_exists($filename)) {
			include $filename;
		} else if(file_exists($filename_fallback)) {
			include $filename_fallback;
		} else {
			echo $filename;
		}
	}
	
	/**
	 * Get the chat-theme
	 *
	 * @since    	1.0.0
	 * @param      	string 		$theme			Theme-Path
	 */
	public function get_theme($theme=false) {
		if($theme===false) {
			$options = get_option($this->plugin_name,$this->default_options());
			$theme = $options["theme"];
		}
		if(file_exists(plugin_dir_path( dirname( __FILE__ ) )."public/template/".$theme."/info.yml")) {
			$theme_info = array();
			$theme_info_content = file_get_contents(plugin_dir_path( dirname( __FILE__ ) )."public/template/".$theme."/info.yml");
			$theme_info_lines = explode("\n",$theme_info_content);
			foreach($theme_info_lines as $line) {
				$line_data = explode("\t:\t",$line);
				if(isset($line_data[1])) {
					$theme_info["{$line_data[0]}"] = str_replace("\r","",$line_data[1]);
				}
			}
			$theme_info["css"] = (isset($theme_info["css"])) ? $theme_info["css"] : $theme;
			$theme_info["js"] = (isset($theme_info["js"])) ? $theme_info["js"] : "public";
			$theme_info["template"] = (isset($theme_info["template"])) ? $theme_info["template"] : $theme;
			return $theme_info;
		} else {
			return false;
		}
	}
	
	/**
	 * Get answer data
	 *
	 * @since    	1.0.0
	 * @param      	int		$question		Question-ID
	 * @param      	int	    $type			Question-Type
	 */
	public function get_answer_by_question($question,$type=false) {
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
		$add = ($type===false) ? "" : " and type='".$type."'";
		$bridge = $wpdb->get_results("SELECT * FROM $table_name WHERE question='".$question."'".$add);
		
		if(count($bridge)>0) {
			$table_name = $wpdb->prefix . $this->plugin_name . "_events";
			$event = $wpdb->get_results("SELECT * FROM $table_name WHERE id='".$bridge[0]->event."'".$add);
			$wpdb->update( $table_name, array( 'counter' => ($event[0]->counter*1)+1), array('id'=>$event[0]->id));
			return $event[0];
		}
		
	}
	
	/**
	 * Get last question (ID) of user
	 *
	 * @since    	1.0.0
	 * @param      	int		$user		User-ID
	 */
	public function last_question_by_user($user) {
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
		$bridge = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' ORDER BY id DESC LIMIT 0,1");
		
		if(count($bridge)>0) {
			return $bridge[0]->question;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Get last question (data) of user
	 *
	 * @since    	1.0.0
	 * @param      	int		$user		User-ID
	 */
	public function new_question_by_user($user) {
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . $this->plugin_name . "_questions";
		$question = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' ORDER BY id DESC LIMIT 0,1");
		
		if(count($question)>0) {
			return $question[0]->id;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Check if User is a supporter
	 *
	 * @since    	1.0.0
	 */
	public function is_supporter() {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->plugin_name . "_user";
		if ( is_user_logged_in() ) {
			$wp_user_id = get_current_user_id();
			$user = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$wp_user_id."' and type='support'");
			
			if(count($user)>0) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get a supporter
	 *
	 * @since    	1.0.0
	 * @param      	int		$id		Chat-ID
	 */
	public function get_supporter($id=false) {
	
		$options = get_option($this->plugin_name,$this->default_options());
		
		if($options["support"]==1) {
		
			global $wpdb;
			$user = $this->user();
		
			if($id===false) {
			
				$table_name = $wpdb->prefix . $this->plugin_name . "_chats";
				$chats = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' ORDER BY id DESC");
				
				if(count($chats)>0) {
					$table_name = $wpdb->prefix . $this->plugin_name . "_user";
					$supporter = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$chats[0]->support."' and `online` > ".(time()-6));
					if(count($supporter)>0) {
						$question = $this->last_question_by_user($user);
						$table_name = $wpdb->prefix . $this->plugin_name . "_chats";
						$rows_affected = $wpdb->insert( $table_name, array( 
							'id' => null,
							'time' => date("Y-m-d H:i:s"),
							'support' => $supporter[0]->user,
							'user' => $user,
							'status' => 0,
							'question' => $question
						));
						return $this->template("chat/answer/support",array("options"=>$options,"support"=>$supporter[0],"question"=>$question));
					}
				}
				
				$table_name = $wpdb->prefix . $this->plugin_name . "_user";
				$supporter = $wpdb->get_results("SELECT * FROM $table_name WHERE type='support' and language='".get_locale()."' and `online` > ".(time()-6)." ORDER BY online ASC");
				
				if(count($supporter)>0) {
					$question = $this->last_question_by_user($user);
					$table_name = $wpdb->prefix . $this->plugin_name . "_chats";
					$rows_affected = $wpdb->insert( $table_name, array( 
						'id' => null,
						'support' => $supporter[0]->user,
						'user' => $user,
						'status' => 0,
						'question' => $question
					));
					return $this->template("chat/answer/support",array("options"=>$options,"support"=>$supporter[0],"question"=>$question));
				} else {
					return false;
				}
				
			} else {
				if($id=="all") {
					$table_name = $wpdb->prefix . $this->plugin_name . "_user";
					$supporter = $wpdb->get_results("SELECT * FROM $table_name WHERE type='support' ORDER BY online DESC");
					if(count($supporter)>0) {
						return $supporter;
					}
				} else {
					$table_name = $wpdb->prefix . $this->plugin_name . "_chats";
					$chats = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' and question='".$id."' ORDER BY id DESC");
					
					if(count($chats)>0) {
						$table_name = $wpdb->prefix . $this->plugin_name . "_user";
						$supporter = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$chats[0]->support."' and type='support'");
						if(count($supporter)>0) {
							return $this->template("chat/answer/support",array("options"=>$options,"support"=>$supporter[0],"question"=>$id));
						}
					}
				}
				return false;
			}
		
		} else {
			return false;
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
	 * Save new page
	 *
	 * @since		1.0.0
	 * @param		text    	$page			Page-URL
	 */
	public function insert_page($page) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->plugin_name . "_pages";
		return $wpdb->insert( $table_name, array( 
			'id' => null,
			'page' => $page
		));
	}
	
	/**
	 * Get user
	 *
	 * @since		1.0.0
	 * @param		boolean    	$create			Create user
	 */
	public function user($create=true) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->plugin_name . "_user";
		if ( is_user_logged_in() ) {
			$wp_user_id = get_current_user_id();
			$user = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$wp_user_id."' and type='user'");
			if(count($user)>0) {
				$user_id = $user[0]->id;
			} else {
				if($create===true) {
					$insert = $wpdb->insert( $table_name, array( 
						'id' => null,
						'type' => "user",
						'online' => time(),
						'user' => $wp_user_id,
						'language' => get_locale()
					));
					$user = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$wp_user_id."' and type='user'");
					$user_id = $user[0]->id;
				} else {
					$user_id = false;
				}
			}
		} else {
			if(!isset($_SESSION['weebotLite_user_id'])) {
				if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
					if (session_status() == PHP_SESSION_NONE) {
						session_start();
					}
				} else {
					if(!session_id()) {
						session_start();
					}
				}
				$_SESSION['weebotLite_user_id'] = (isset($_SESSION['weebotLite_user_id'])) ? $_SESSION['weebotLite_user_id'] : $this->weebotLite_user_id();
			}
			$user = $wpdb->get_results("SELECT * FROM $table_name WHERE session='".$_SESSION['weebotLite_user_id']."'");
			if(count($user)>0) {
				$user_id = $user[0]->id;
			} else {
				if($create===true) {
					$insert = $wpdb->insert( $table_name, array( 
						'id' => null,
						'type' => "session",
						'online' => time(),
						'user' => -1,
						'session' => $_SESSION['weebotLite_user_id'],
						'language' => get_locale()
					));
					$user = $wpdb->get_results("SELECT * FROM $table_name WHERE session='".$_SESSION['weebotLite_user_id']."'");
					$user_id = $user[0]->id;
				} else {
					$user_id = false;
				}
			}
		}
		return $user_id;
	}
	
	/**
	 * Get chat-log of user
	 *
	 * @since		1.0.0
	 * @param		int    		$start			Chat-log (start)
	 * @param		int    		$limit			Chat-log (limit)
	 */
	public function user_log($start=1,$limit=10) {
	
		$user = $this->user(false);
		
		if($user!==false) {
		
			global $wpdb;
			
			$table_name = $wpdb->prefix . $this->plugin_name . "_bridge";
			$log = $wpdb->get_results("SELECT * FROM $table_name WHERE user='".$user."' ORDER BY id DESC LIMIT {$start},{$limit}");
			
			if(count($log)>0) {
				return $log;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
		
	}
	
	/**
	 * Generate a random User-ID
	 *
	 * @since		1.0.0
	 * @param		int    		$length		ID length
	 */
	public function weebotLite_user_id($length=10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
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
