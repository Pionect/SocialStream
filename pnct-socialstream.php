<?php
/*
Plugin Name: Social Stream
Description: This plugin imports social media messages for multiple users.
Author: Pionect - Tomas van Rijsse
Version: 1.0
Author URI: http://www.pionect.nl
Plugin URI: http://www.pionect.nl
*/

define('SOCIALSTREAM_DIR', plugin_dir_path(__FILE__));
define('SOCIALSTREAM_URL', plugin_dir_url(__FILE__));

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
    wp_schedule_event( time()+(10), 'daily', 'pnct_socialstream_import');
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
    wp_register_style( 'socialstream', plugins_url('css/index.css',__FILE__) );
    wp_enqueue_style( 'socialstream' );
}
