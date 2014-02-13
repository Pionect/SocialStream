<?php

Class pnct_socialstream_flickrparser {
    
    private $feed_uri;
    private $user_id;
    
    public function __construct($id,$user_id) {
        $this->feed_uri = 'http://api.flickr.com/services/feeds/photos_public.gne?id='.$id;
        $this->user_id = $user_id;
    }
    
    public function retreive(){
        /* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */  
        ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');

        $updates = simplexml_load_file($this->feed_uri);  //Load feed with simplexml
        
        //add check for $updates
        
        foreach ($updates->entry as $entry){
            $item = new pnct_socialstream_item();
            $external_id = (string)$entry->id;
            $item->external_id = substr($external_id,strrpos($external_id,'/')+1);
            $item->timestamp = (string) $entry->updated;

            // if non existing it fails and if new item isn't newer it also fails.
            if($item->idAndStampUnchanged()){
                continue;
            }
            
            $item->type = 'flickr';
            $item->user_id = $this->user_id;
            $item->content = (string)$entry->title;
            $item->published = strtotime((string) $entry->published);
            
            foreach($entry->link as $link){
                $attr = $link->attributes();
                if($attr->rel=='alternate'){
                    $item->url = (string) $attr->href;
                } elseif($attr->rel=='enclosure'){
                    $item->thumbnail = str_replace('_b.','.',(string) $attr->href);
                }
            }
            
            $item->save();
        }
    }
    
}