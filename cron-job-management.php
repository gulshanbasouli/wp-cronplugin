<?php
/**
* Plugin Name: Cron Job Management
* Author: Loeion
* Description: Cron Job Management
* Author URI: http://www.loeion.com
* Version: 0.1
*/

/**
 * Prevent Direct Access
 */

defined( 'ABSPATH' ) or die( "Restricted access!" );


function wbc_cron_admin_action() {
  
	add_menu_page('LP Management', 'LP Management', 'manage_options', __FILE__, 'lp_form_management', "dashicons-chart-bar", 7);
}

function lp_form_management() {
	global $wpdb;
	$cron_data = $wpdb->prefix."cron_data";
	//error with the query 
	$sql = "INSERT INTO $cron_data (`working`) VALUES ('Yes')";
	        
	if($wpdb->query($sql)) {
	    echo "Data inserted successfully";   
	}
}

add_action('admin_menu', 'wbc_cron_admin_action');


function wbc_create_cron_data_table() {  
    global $wpdb;
    // Create a table for form management
    $table_name = $wpdb->prefix . 'cron_data';
    if($wpdb->get_var("show tables like $table_name") != $table_name) {
      
      $sqlAF = "CREATE TABLE $table_name (
      `id` mediumint(9) NOT NULL AUTO_INCREMENT,
      `working` VARCHAR(255) NOT NULL,      
      `t_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,     
      UNIQUE KEY id (id)
      );";
      
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sqlAF);    
    }
}

register_activation_hook(__FILE__, 'wbc_create_cron_data_table');



// Cron Job Work starts

    // hourly
    // twicedaily
    // daily

register_activation_hook(__FILE__, 'lp_plugin_schedule_cron');

function lp_plugin_schedule_cron() {
    if (! wp_next_scheduled ( 'my_hourly_event' )) {
	wp_schedule_event(time(), 'hourly', 'my_hourly_event');
    }
}

add_action('my_hourly_event', 'lp_form_management');

register_deactivation_hook(__FILE__, 'lp_plugin_deactivation');

function lp_plugin_deactivation() {
	wp_clear_scheduled_hook('my_hourly_event');
}

// Cron Job Work Ends

add_filter('cron_schedules', 'myplugin_cron_add_intervals');
function myplugin_cron_add_intervals( $schedules ) {
  $schedules['customTime'] = array(
    'interval' => 30,
    'display' => __('Every 30sec')
  );
  return $schedules;
}
