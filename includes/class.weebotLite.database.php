<?php

/**
 * Fired during plugin installation.
 *
 * @link       http://www.weedesign.de
 * @since      1.0.0
 *
 * @package    weebotLite
 * @subpackage weebotLite/includes
 */
 
/**
 * Fired during plugin installation.
 *
 * This class defines all code necessary to run during the plugin's installation.
 *
 * @since      1.0.0
 * @package    weebotLite
 * @subpackage weebotLite/includes
 * @author     Wolfgang Ertl <wolfgang.ertl@weedesign.de>
 */

global $jal_db_version;
$jal_db_version = "1.0";

class weebotLite_Database {


	/**
	 * Fired during plugin installation.
	 *
	 * Installing all tables into the database
	 *
	 * @since    1.0.0
	 */
	public static function install() {

		global $wpdb;
		global $jal_db_version;

		$table_name = $wpdb->prefix . "weebotLite_questions";
		
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$sql = "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				counter int NOT NULL,
				question text NOT NULL,
				parent int NOT NULL,
				words text NOT NULL,
				answered int NOT NULL,
				type int NOT NULL,
				answer int NOT NULL,
				language text NOT NULL,
				user int NOT NULL,
				page int NOT NULL,
				page_related int NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";
		
		$table_name = $wpdb->prefix . "weebotLite_words";

		$sql.= "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				counter int NOT NULL,
				language text NOT NULL,
				word text NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";
		
		$table_name = $wpdb->prefix . "weebotLite_user";

		$sql.= "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				user int NOT NULL,
				session text NOT NULL,
				type text NOT NULL,
				name text NOT NULL,
				email text NOT NULL,
				online int NOT NULL,
				page int NOT NULL,
				language text NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";
		
		$table_name = $wpdb->prefix . "weebotLite_bridge";

		$sql.= "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				question int NOT NULL,
				question_old int NOT NULL,
				event int NOT NULL,
				user int NOT NULL,
				email int NOT NULL,
				type int NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";
		
		$table_name = $wpdb->prefix . "weebotLite_pages";

		$sql.= "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				page text NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";
		
		$table_name = $wpdb->prefix . "weebotLite_chats";

		$sql.= "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				user int NOT NULL,
				support int NOT NULL,
				status int NOT NULL,
				question int NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";

		$table_name = $wpdb->prefix . "weebotLite_events";

		$sql.= "
			CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				counter int NOT NULL,
				type text NOT NULL,
				page int NOT NULL,
				language text NOT NULL,
				user int NOT NULL,
				action text NOT NULL,
				UNIQUE KEY id (id)
			) $collate;
		";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("jal_db_version", $jal_db_version);

	}

	/**
	 * Fired during deleting of database.
	 *
	 * Deletes all plugin-related tables from the database
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {

		global $wpdb;
		global $jal_db_version;

		$tables = array("questions","words","user","events","bridge","chats","pages");
		
		$sql = "";
		
		foreach($tables as $table) {
			$table_name = $wpdb->prefix . "weebotLite_".$table;
			$sql.= "DROP TABLE IF EXISTS $table_name ; ";
			$wpdb->query("DROP TABLE IF EXISTS $table_name");
		}
		
		delete_option($jal_db_version);
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("jal_db_version", $jal_db_version);

	}

}
