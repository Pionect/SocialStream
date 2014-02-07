<?php /* @var $item pnct_socialstream_item */ ?>
<div class="ss_item image <?php echo $item->type; ?>" data-url="<?php echo $item->url; ?>">
    <div class="ss_content" title="<?php echo $item->content; ?>" style="background-image:url(<?php echo $item->thumbnail; ?>)">
    </div>
    <div class="ss_username">
        <?php echo $item->post_name; ?>
    </div>
    <div class="ss_typeicon"></div>
</div>