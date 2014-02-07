<?php
//should be instantiated for the cron job.
Class pnct_socialstream_importer {
    
    public function importAll(){
        $parser_dir = SOCIALSTREAM_DIR.'/parsers/';
        require_once $parser_dir.'facebook.php';
        require_once $parser_dir.'twitter.php';
        require_once $parser_dir.'flickr.php';
        require_once $parser_dir.'vimeo.php';
        require_once $parser_dir.'instagram.php';
        
        global $wpdb;
        
        $usertype = SOCIALSTREAM_USERTYPE;
        
        switch($usertype){
            case 'wp_post':
            // get all streams
            $streams = $wpdb->get_results('SELECT post_id as user_id,meta_key as platform,meta_value as user
                        FROM wp_postmeta pm
                        INNER JOIN wp_posts p ON p.ID = pm.post_id
                        AND pm.meta_key IN ("facebook_id","twitter_username","flickr_id","vimeo_username")
                        AND meta_value != ""');
            break;
            case 'single':
                $accounts = get_option('socialstream_useraccounts');
                
                $stream = new stdClass();
                $stream->user       = $accounts['instagram_id'];
                $stream->platform   = 'instagram_id';
                $stream->user_id    = 0;
                
                //$stream->vimeo_username   = $accounts['vimeo'];                
                //$stream->instagram_id     = $accounts['instagram_id'];
                //$stream->facebook_id    = $accounts['facebook_id'];
                //$stream->flickr_id      = $accounts['flickr_id'];
                $streams = array($stream);
                break;
        }
        
        // loop over streams URIs        
        foreach($streams as $stream){
            // instantiate custom parser
            switch($stream->platform){
                case 'twitter_username':
                    $parser = new pnct_socialstream_twitterparser($stream->user,$stream->user_id);
                    $parser->retreive();
                    break;
                case 'facebook_id':
                    $parser = new pnct_socialstream_facebookparser($stream->user,$stream->user_id);
                    $parser->retreive();
                    break;
                case 'vimeo_username':
                    $parser = new pnct_socialstream_vimeoparser($stream->user,$stream->user_id);
                    $parser->retreive();
                    break;
                case 'flickr_id':
                    $parser = new pnct_socialstream_flickrparser($stream->user,$stream->user_id);
                    $parser->retreive();
                    break;
                case 'instagram_id':
                    $parser = new pnct_socialstream_instagramparser($stream->user,$stream->user_id);
                    $parser->retreive();
                    break;
            }
        }
        
        date_default_timezone_set('Europe/Amsterdam');
        update_option('socialstream_lastdate', date('d-m-Y H:i'));
    }
}

function pnct_startImport(){
    $importer = new pnct_socialstream_importer();
    $importer->importAll();
}

add_action('pnct_socialstream_import', 'pnct_startImport');