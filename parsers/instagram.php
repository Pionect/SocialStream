<?php

Class pnct_socialstream_instagramparser {
    
    private $feed_uri;
    private $user_id;
    private $instagram_id;
    
    public function __construct($id,$user_id) {
        $clientid = get_option('socialstream_instagram_clientid');
        $this->feed_uri = 'https://api.instagram.com/v1/users/'.$id.'/media/recent/?client_id='.$clientid ;
        $this->user_id = $user_id;
        $this->instagram_id = $id;
    }
    
    public function retreive(){
        /* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */  
        ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');

        $options = array(
            'http' => array(
                'method'  => 'GET'
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($this->feed_uri, false, $context);
        
        $updates = json_decode($result);
        
        foreach ($updates->data as $photo){
            dump($photo);die;
            $item = new pnct_socialstream_item();
            $item->external_id = (string)$photo->id;
            $item->timestamp = $photo->created_time;
            if($item->idAndStampUnchanged()){
                continue;
            }
            
            $item->type      = 'instagram';
            $item->user_id   = $this->instagram_id;
            $item->url       = $photo->link;
            $item->published = $item->timestamp;
            $item->thumbnail = $photo->images->thumbnail->url;
            
            $desc = array(
                'image' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $photo->images->standard_resolution->url),
                'caption' => iconv("UTF-8", "ISO-8859-1//TRANSLIT", $photo->caption->text )
            );

            $item->content = serialize($desc);
            
            $item->save();
        }
    }
    
}