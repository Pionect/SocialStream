<?php

function pnct_socialstream_show( $atts ){
    extract( shortcode_atts( array(
		'user' => false,
        'format' => 'slider',
        'itemcount' => 3,
        'size' => 'small'
	), $atts ) );
    
    $accepted_formats = array('slider','static');
    if(!in_array($format,$accepted_formats))$format = 'slider';
    
    $accepted_sizes = array('small','large');
    if(!in_array($size,$accepted_sizes)) $size = 'small';
    
    $itemcount = (int)$itemcount;
    if(!is_int($itemcount))$itemcount = 3;

    
    if($user!==FALSE){
        $user = (int)$user;
        $item = new pnct_socialstream_item();

        if(is_int($user)){
            $item->user_id = $user;
            $items = $item->readAllByUser($user,15);
        } else {
            echo 'USER attribute was not numeric';
        }
    } else {
        $item = new pnct_socialstream_item();
        $items = $item->readAll(15);
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
    $return .= '<div class="buttonleft"></div><div class="buttonright"></div>';
    $return .= '</div>';
    
    if($size=="small"){
        $return .= '<div id="ss_indicators'.$id.'">';
        for($i=0;$i<ceil(count($items)/$itemcount);$i++){$return.='<div></div>';}
        $return .= '</div>';
        //$return .= '<style>.ss_typeicon {color:'.get_option('socialstream_color').'}</style>';
    } else {
        /*
<script>
    $('.ss_item.facebook .ss_content, .ss_item.twitter .ss_content'){
        
    }
</script>
*/
        //$return .= '<style>div[id^=ss_slider].large {background-color:'.get_option('socialstream_color').'}</style>';
    }
    
    $return .= '<script>var ss_slider;
        $(document).ready(function(){
        ss_slider = new PNCTSLIDER({
            $slidesframe:    $("#ss_slider'.$id.'"),
            $slidescontainer:$("#ss_slider'.$id.' .ss_items"),
            $indicators:     $("#ss_indicators'.$id.' > div"),
            slideCount:      '.ceil(count($items)/$itemcount).','.
            ($itemcount=='3'?'slideWidth:      1005,':'').
            'autoplay:       true,
            timerSpeed:      4000,
            $leftbutton:     $("#ss_slider'.$id.' .buttonleft"), 
            $rightbutton:    $("#ss_slider'.$id.' .buttonright")
        });
    });</script>';
    echo $return;
}
add_shortcode( 'socialstream', 'pnct_socialstream_show' );