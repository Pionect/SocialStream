<?php 
Class pnct_socialstream_item{
    
    public $user_id;
    public $type;
    public $external_id;
    public $url;
    public $content;
    public $thumbnail;
    public $timestamp; // originele updated timestamp voor vergelijking bij import
    public $published; // timestamp voor sortering
    
    public function initDB(){
        global $wpdb;
        $sql = $wpdb->prepare("
CREATE TABLE IF NOT EXISTS `%ssocialstream`  (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT(20) UNSIGNED NOT NULL,
    `type` ENUM('twitter','facebook','vimeo','flickr','instagram') NOT NULL,
    `external_id` VARCHAR(255) NULL DEFAULT NULL,
    `url` VARCHAR(255) NOT NULL,
    `content` TEXT NULL,
    `thumbnail` VARCHAR(255) NULL DEFAULT NULL,
    `timestamp` VARCHAR(50) NOT NULL,
    `published` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `FK_socialstream_wp_posts` (`user_id`)
);", $wpdb->prefix);
        //removed the ForeignKey for the case that there is only a single user in the options.
        /*,	CONSTRAINT `FK_socialstream_wp_posts` FOREIGN KEY (`user_id`) REFERENCES `wp_posts` (`ID`) */ 
        $wpdb->query($sql);
    }
    
    public function cleanupDB(){
        global $wpdb;
        $sql = $wpdb->prepare("DROP TABLE IF EXISTS `%ssocialstream`",$wpdb->prefix);
        $wpdb->query($sql);
    }
    
    public function save(){
        global $wpdb;
        $sql = $wpdb->prepare('INSERT INTO wp_socialstream (user_id,type,external_id,url,content,thumbnail,timestamp,published) VALUES (%d,%s,%s,%s,%s,%s,%s,FROM_UNIXTIME(%d))',
          $this->user_id,$this->type,$this->external_id,$this->url,$this->content,$this->thumbnail,$this->timestamp,$this->published);
        $wpdb->query($sql);
    }
    
    public function readAll($limit=10){
        global $wpdb;
        switch (SOCIALSTREAM_USERTYPE)
        {
            case 'single':
                $sql = $wpdb->prepare('SELECT ss.*
                FROM wp_socialstream ss
                ORDER BY published DESC
                LIMIT  %d',$limit);
                break;
            case 'wp_users':
                break;
            case 'wp_post':
                $sql = $wpdb->prepare('SELECT ss.*, p.post_title as post_name
                FROM wp_socialstream ss
                JOIN wp_posts p ON ss.user_id = p.ID
                ORDER BY published DESC
                LIMIT  %d',$limit);
                break;
        }
        return $this->fillObjects($wpdb->get_results($sql));
    }
    
    public function readAllByUser($user_id,$limit=10){
        global $wpdb;
        $sql = $wpdb->prepare('SELECT ss.*, p.post_title as post_name, meta_value as \'username\'
FROM wp_socialstream ss
JOIN wp_posts p ON ss.user_id = p.ID
INNER JOIN wp_postmeta wpm ON wpm.post_id = ss.user_id AND wpm.meta_key = \'twitter_username\'
WHERE ss.user_id = %d
ORDER BY published DESC
LIMIT  %d',$user_id,$limit);
        return $this->fillObjects($wpdb->get_results($sql));
    }
    
    public function idAndStampUnchanged(){
        global $wpdb;
        $sql = $wpdb->prepare( "SELECT timestamp FROM wp_socialstream WHERE external_id = %s", $this->external_id);
        $stamp = $wpdb->get_var($sql);
        if( $stamp != ""){
            if($stamp == $this->timestamp){
                return true;//identical stamp
            } else {
                return false; // changed stamp
            }
        } else {
            return false;//new stamp
        }
    }
    
    /**
     * Convert the result of $wpdb->get_results() into an array of clean objects
     * @param type $query the result of $wpdb->get_results();
     * @return array  containing items
     */
    function fillObjects($rows){
        $out =  array();
        foreach($rows as $row){
            $a = new pnct_socialstream_item();
            foreach($row as $var => $value){
                $a->$var = $value;
            }
            $out[] = $a;
        }
        return $out;
    }
    
    public function makeLink(){
        $string = $this->content;
        /*** make sure there is an http:// on all URLs ***/
        $string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
        /*** make all URLs links ***/
        $string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</A>",$string);
        /*** make all emails hot links ***/
        $string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A HREF=\"mailto:$1\">$1</A>",$string);

        $this->content = $string;
    }
}