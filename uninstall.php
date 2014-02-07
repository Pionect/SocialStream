<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$option_name = 'plugin_option_name';

// For Single site
delete_option( $option_name );

require_once 'includes/model.php';
$model = new pnct_socialstream_item();
$model->cleanupDB();