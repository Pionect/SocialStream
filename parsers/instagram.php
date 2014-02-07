<?php

Class pnct_socialstream_twitterparser {
    
    private $feed_uri;
    private $user_id;
    private $username;
    
    public function __construct($username,$user_id) {
        $this->feed_uri = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='.$username;
        $this->user_id = $user_id;
        $this->username = $username;
    }
    
    public function retreive(){
        /* The following line is absolutely necessary to read Facebook feeds. Facebook will not recognize PHP as a browser and therefore won't fetch anything. So we define a browser here */  
        ini_set('user_agent', 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3');

        $options = array(
            'http' => array(
                'header'  => "Authorization: Bearer ".get_option('socialstream_twitterbearer')."\r\n",
                'method'  => 'GET'
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($this->feed_uri, false, $context);
        
        $updates = json_decode($result);
        
        $link = 'http://twitter.com/'.$this->username.'/status/';
        
        foreach ($updates as $tweet){
            $item = new pnct_socialstream_item();
            $item->external_id = (string)$tweet->id_str;
            $item->timestamp = strtotime($tweet->created_at);
            if($item->idAndStampUnchanged()){
                continue;
            }
            
            $item->type = 'twitter';
            $item->user_id = $this->user_id;
            $item->url = $link.$tweet->id_str;
            $item->published = $item->timestamp;
            
            $desc = (string)$tweet->text;
            //Converts UTF-8 into ISO-8859-1 to solve special symbols issues
            $content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $desc);

            $item->content = $content;
            
            $item->save();
        }
    }
    
}