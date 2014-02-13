<?php

Class pnct_socialstream_vimeoparser {
    
    private $feed_uri;
    private $user_id;
    
    public function __construct($username,$user_id) {
        $this->feed_uri = 'http://vimeo.com/api/v2/'.$username.'/videos.xml';
        $this->user_id = $user_id;
    }
    
    public function retreive(){
        /* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */  
        ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');

        $updates = simplexml_load_file($this->feed_uri);  //Load feed with simplexml
        
        foreach ($updates->video as $video){
            $item = new pnct_socialstream_item();
            $item->external_id = (string)$video->id;
            $item->timestamp = (string) $video->upload_date;
            if($item->idAndStampUnchanged()){
                continue;
            }
            
            $item->type = 'vimeo';
            $item->user_id = $this->user_id;
            $item->content = (string)$video->title;
            $item->published = strtotime((string) $video->upload_date);
            $item->url = (string) $video->url;        
            $item->thumbnail = (string) $video->thumbnail_large;
            
            $item->save();
        }
    }
    
}