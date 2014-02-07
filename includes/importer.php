<?php
//should be instantiated for the cron job.
Class pnct_socialstream_importer {
    
    public function importAll(){
        $parser_dir = SOCIALSTREAM_DIR.'/parsers/';
        require_once $parser_dir.'facebook.php';
        require_once $parser_dir.'twitter.php';
        require_once $parser_dir.'flickr.php';
        require_once $parser_dir.'vimeo.php';
        
        global $wpdb;
        
        // get all streams
        $streams = $wpdb->get_results('SELECT post_id,meta_key,meta_value 
                    FROM wp_postmeta pm
                    INNER JOIN wp_posts p ON p.ID = pm.post_id
                    AND pm.meta_key IN ("facebook_id","twitter_username","flickr_id","vimeo_username")
                    AND meta_value != ""');
        
        // loop over streams URIs        
        foreach($streams as $stream){
            // instantiate custom parser
            switch($stream->meta_key){
                case 'twitter_username':
                    $parser = new pnct_socialstream_twitterparser($stream->meta_value,$stream->post_id);
                    $parser->retreive();
                    break;
                case 'facebook_id':
                    $parser = new pnct_socialstream_facebookparser($stream->meta_value,$stream->post_id);
                    $parser->retreive();
                    break;
                case 'vimeo_username':
                    $parser = new pnct_socialstream_vimeoparser($stream->meta_value,$stream->post_id);
                    $parser->retreive();
                    break;
                case 'flickr_id':
                    $parser = new pnct_socialstream_flickrparser($stream->meta_value,$stream->post_id);
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