<?php /* @var $item pnct_socialstream_item */ 
$content = unserialize($item->content); ?>
<div class="ss_item <?php echo $item->type; ?>" data-url="<?php echo $item->url; ?>">
    <div class="ss_content" title="<?php echo $item->content; ?>" style="background-image:url(<?php echo $item->thumbnail; ?>)">
    </div>
    <div class="ss_username">
        <?php echo $item->post_name; ?>
    </div>
    <div class="ss_typeicon"></div>
</div>