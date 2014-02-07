<?php
/*
Plugin Name: Social Stream
Description: This plugin imports social media messages.
Author: Pionect - Tomas van Rijsse
Version: 1.0
Author URI: http://www.pionect.nl
Plugin URI: http://www.pionect.nl
*/

define('SOCIALSTREAM_DIR', plugin_dir_path(__FILE__));
define('SOCIALSTREAM_URL', plugin_dir_url(__FILE__));
define('SOCIALSTREAM_USERTYPE',get_option('socialstream_usertype'));

require_once 'includes/model.php';
require_once 'includes/importer.php';
require_once 'includes/settings.php';
require_once 'includes/show.php';

new pnct_socialstream_settings;

/*
function pnct_socialstream_load(){
    if(is_admin()) //load admin files only in admin
        require_once(SOCIALSTREAM_DIR.'includes/admin.php');
        
    require_once(SOCIALSTREAM_DIR.'includes/core.php');
}
pnct_socialstream_load();*/

register_activation_hook(__FILE__, 'pnct_socialstream_activation');
register_deactivation_hook(__FILE__, 'pnct_socialstream_deactivation');

function pnct_socialstream_activation() {
	// opt-in for cron jobs
    wp_schedule_event( time()+(10), 'hourly', 'pnct_socialstream_import');
    $model = new pnct_socialstream_item();
    $model->initDB();
}

function pnct_socialstream_deactivation() {    
	// opt-out on cron jobs
    wp_clear_scheduled_hook('pnct_socialstream_import');
}

/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'pnct_socialstream_styles' );

/**
 * Enqueue plugin style-file
 */
function pnct_socialstream_styles() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_script( 'pnctslider', plugins_url('js/pnctslider.js',__FILE__) );
    wp_register_style( 'socialstream', plugins_url('css/index.css',__FILE__) );
    wp_enqueue_script('pnctslider');
    wp_enqueue_style( 'socialstream' );
}

// Add settings link on plugin page
function pnct_socialstream_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=pnct-socialstream">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'pnct_socialstream_settings_link' );

function dump($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}