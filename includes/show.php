<?php

function pnct_socialstream_show( $atts ){
    extract( shortcode_atts( array(
		'user'      => false,
        'format'    => 'slider',
        'itemcount' => 3,
        'size'      => 'small',
        'itemlimit' => 10,
        'buttons'   => true,
        'indicators'=> true
	), $atts ) );
        
    $accepted_formats = array('slider','static');
    if(!in_array($format,$accepted_formats))$format = 'slider';
    
    $accepted_sizes = array('small','large');
    if(!in_array($size,$accepted_sizes)) $size = 'small';
    
    $itemcount = (int)$itemcount;
    if(!is_int($itemcount))$itemcount = 3;

    $itemlimit = (int)$itemlimit;
    if(!is_int($itemlimit))$itemlimit = 10;
    
    if($user!==FALSE && SOCIALSTREAM_USERTYPE != 'single'){
        $user = (int)$user;
        $item = new pnct_socialstream_item();

        if(is_int($user)){
            $item->user_id = $user;
            $items = $item->readAllByUser($user,$itemlimit);
            die('a');
        } else {
            echo 'USER attribute was not numeric';
        }
    } else {
        $item = new pnct_socialstream_item();
        $items = $item->readAll($itemlimit);
    }
    
    $id = rand(100,999);
    
    $return = '<div id="ss_slider'.$id.'" class="'.$size.'">';
    $return .= '<div class="ss_itemsframe">';
        $return .= '<div class="ss_items">';
        foreach($items as $item){
            ob_start();
            include SOCIALSTREAM_DIR."/assets/item.$item->type.php";
            $return .= ob_get_contents();
            ob_end_clean();
        }
        $return .= '</div>';//end ss_items;
    $return .= '</div>';
    if($buttons){
        $return .= '<div class="buttonleft"></div><div class="buttonright"></div>';
    }
    $return .= '</div>';
    
    if($indicators){
        $return .= '<div id="ss_indicators'.$id.'">';
        for($i=0;$i<ceil(count($items)/$itemcount);$i++){$return.='<div></div>';}
        $return .= '</div>';
    }
    
    $return .= '<script>var ss_slider;
        jQuery(document).ready(function($){
        ss_slider = new PNCTSLIDER({
            $slidesframe:    jQuery("#ss_slider'.$id.'"),
            $slidescontainer:jQuery("#ss_slider'.$id.' .ss_items"),
            $indicators:     jQuery("#ss_indicators'.$id.' > div"),
            slideCount:      '.ceil(count($items)/$itemcount).',
            autoplay:        true,
            timerSpeed:      4000';
    if($buttons){
        $return .= '$leftbutton:     jQuery("#ss_slider'.$id.' .buttonleft"), 
            $rightbutton:    jQuery("#ss_slider'.$id.' .buttonright")';
    }
    $return .= '    });
    });</script>';
    echo $return;
}
add_shortcode( 'socialstream', 'pnct_socialstream_show' );

class Socialstream_Widget extends WP_Widget 
{
    public function __construct() {
		parent::__construct(
			'Socialstream_widget', // Base ID
			'Socialstream widget', // Name
			array( 'description' => 'Show a slideshow in the sidebar' ) // Args
		);
	}
    
    public function widget( $args, $instance ) {
        $atts =  array(
            //'user' => false,
            'format' => $instance['format'],
            'itemcount' => 1,
            //'size' => 'small',
            'itemlimit' => $instance['itemlimit'],
            'buttons' => ($instance['buttons']=='on'?true:false),
            'indicators' => ($instance['indicators']=='on'?true:false),
        );
        
        pnct_socialstream_show($atts);
	}
    
    public function form($instance) {
        include SOCIALSTREAM_DIR.'/assets/widget_form.php';
    }
    
    function update($new_instance, $old_instance) {
        $new_instance['itemlimit'] = (int)$new_instance['itemlimit'];
        return $new_instance;        
    }
    
}

//register NMI widgets
function register_socialstream_widget() { 
    $class = 'Socialstream_Widget';
    if(class_exists($class)){
        register_widget($class);
    }
 }
add_action('widgets_init', 'register_socialstream_widget', 50);