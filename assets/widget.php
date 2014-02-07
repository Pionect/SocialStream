<div class="content-block single widget-block">
    <?php if($block->link): ?><a class="link-wrapper" href="<?php echo $block->url; ?>" title="<?php echo $block->title; ?>"><?php endif;?>
        <div class="inner-block-content">
            <h2 class="widget-block-title"><?php echo $block->title; ?></h2>
            <?php if($block->link): ?><span class="readmore">+</span><?php endif;
            echo $block->description; ?>
        </div>
        <?php if($block->img):?><img src="<?php echo $block->img;?>" alt="<?php echo $block->title; ?>"><?php endif; ?>
    <?php if($block->link): ?></a><?php endif; ?>
</div>