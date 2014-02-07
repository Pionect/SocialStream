<?php

Class pnct_socialstream_facebookparser {
    
    private $feed_uri;
    private $user_id;
    
    public function __construct($id,$user_id) {
        $this->feed_uri = 'https://www.facebook.com/feeds/page.php?format=atom10&id='.$id;
        $this->user_id = $user_id;
    }
    
    public function retreive(){
        /* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */  
        ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');
        
        $updates = simplexml_load_file($this->feed_uri);  //Load feed with simplexml
        
        foreach ($updates->entry as $fb_update){
            $item = new pnct_socialstream_item();
            $item->external_id  = (string)$fb_update->id;
            $item->timestamp = (string)$fb_update->updated;
            if($item->idAndStampUnchanged()){
                continue;
            }
            
            $item->type = 'facebook';
            $item->user_id = $this->user_id;
            $item->published = strtotime((string) $fb_update->published);
            $desc = (string)$fb_update->content;
            
            
            //Converts UTF-8 into ISO-8859-1 to solve special symbols issues
            $content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);
            //$item->content = preg_replace("/<br/>/i", "\n", $content); //remove img only
            $item->content = trim(strip_tags($content,'<br>'));
            
            //$item->makeLink();
            
            //find images in the content
            $doc = new DOMDocument();
            $doc->loadHTML($desc);
            $imageTags = $doc->getElementsByTagName('img');
            foreach($imageTags as $tag){
                if($item->thumbnail) continue;
                $item->thumbnail = $tag->getAttribute('src');
            }
            
            //Get link to update
            $link = $fb_update->link->attributes();
            $item->url = (string)$link->href;
            
            $item->save();
        }
    }
    
}