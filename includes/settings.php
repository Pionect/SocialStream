<?php

class pnct_socialstream_settings {
	function __construct() {
        //voor het knopje in het menu
		add_action('admin_menu', array(&$this, 'admin_menu'));
        //voor de knop 'manual import' op de settings page
        add_action('admin_post_pnct_socialstream_startcron', array(&$this, 'startImportManual'));
        add_action('admin_post_pnct_socialstream_truncate', array(&$this, 'truncate'));
        add_action('admin_post_pnct_socialstream_saveSettings',array(&$this, 'saveSettings'));
        add_action('admin_post_pnct_socialstream_getTwitterBearer',array(&$this, 'getTwitterBearer'));
	}
    
	function admin_menu () {
        add_options_page('Social Stream','Social Stream','manage_options','pnct-socialstream',array($this,'settings_page'));
        //voor de settings
        add_action('admin_init', array(&$this,'init_settings'));
	}
    
    function init_settings(){
        register_setting('socialstream', 'socialstream_color',array(&$this,'checkColor'));
        register_setting('socialstream', 'socialstream_useraccounts',array(&$this,'checkAccounts'));
        register_setting('socialstream', 'socialstream_platforms');
        register_setting('socialstream', 'socialstream_usertype');
        register_setting('socialstream', 'socialstream_user_posttype');
        register_setting('socialstream', 'socialstream_instagram_clientid');
        /*add_settings_field('socialstream_color', 'Color', array($this, 'create_an_id_field'), 'pnct-socialstream');*/
    }
    
    function checkAccounts($input){
        $oldaccounts = get_option('socialstream_useraccounts');
        
        //in future loop over $input
        //find facebook id, flickr_id 
        if($input['instagram']!=""){
            if($input['instagram'] != $oldaccounts['instagram']){
                $clientid = get_option('socialstream_instagram_clientid');
                $url = 'https://api.instagram.com/v1/users/search?q='.$input['instagram'].'&client_id='.$clientid;

                $options = array(
                    'http' => array(
                        'method'  => 'GET',
                    ),
                );

                $context  = stream_context_create($options);
                $result = json_decode(file_get_contents($url, false, $context));
                if($result){  
                    if($result->meta->code==200 && count($result->data)>0){
                        $input['instagram_id'] = $result->data[0]->id;
                    }
                }
            } else {
                $input['instagram_id'] = $oldaccounts['instagram_id'];
            }
        }
        
        return $input;
    }
    
    function checkColor($input){
        if(strlen($input)==7 && substr($input,0,1)=='#'){
            return $input;
        }else{
            //add_settings_error( $setting, $code, $message, $type );
            return '#333333';
        }
    }
    
	function  settings_page () {
        $accounts = get_option('socialstream_useraccounts');
		include_once SOCIALSTREAM_DIR.'/assets/settings.php';
	}
    
    function startImportManual(){
        wp_clear_scheduled_hook('pnct_socialstream_import');
        wp_schedule_event( time()+(60*60*4), 'daily', 'pnct_socialstream_import');
        do_action('pnct_socialstream_import');
        wp_redirect('/wp-admin/options-general.php?page=pnct-socialstream');
    }
    
    function getTwitterBearer($key,$secret){
        $key = $_POST['twitter_key'];
        $secret = $_POST['twitter_secret'];
        $url = 'https://api.twitter.com/oauth2/token';

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded;charset=UTF-8\r\n".
                             "Authorization: Basic ".base64_encode($key.':'.$secret)."\r\n",
                'method'  => 'POST',
                'content' => 'grant_type=client_credentials',
            ),
        );
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));
        
        if($result){
            
            update_option('socialstream_twitterbearer', $result->access_token);
            wp_redirect('/wp-admin/options-general.php?page=pnct-socialstream');
        } else {
            wp_redirect('/wp-admin/options-general.php?page=pnct-socialstream&twitter=fail');
        }
    }
    
    function truncate(){
        global $wpdb;
        $sql = $wpdb->prepare('TRUNCATE TABLE `'.$wpdb->prefix.'socialstream`');
        $wpdb->query($sql);
        wp_redirect('/wp-admin/options-general.php?page=pnct-socialstream');
    }
}
