<div class="wrap">
    <?php if(array_key_exists('twitter',$_GET)): ?>
        <div class="error"><p>Your Twitter consumer data wasn't correct.</p></div>
    <?php endif; ?>
    <?php screen_icon(); ?>
    <h2>Social Stream</h2>
    <?php /*<form method="post" action="options.php"> 
        <?php settings_fields( 'socialstream' ); ?>
        <h3>Instellingen</h3>
        <label for="color" style="display: inline-block;width: 150px;">Color</label>
        <input type="text" name="socialstream_color" value="<?php echo get_option('socialstream_color'); ?>" /><br/>
        <small>For example: #333333;</small>
         <?php submit_button(); ?>
    </form>
    <hr> */ ?>
    <h3>Twitter setup</h3>
    <?php if(!get_option('socialstream_twitterbearer')): ?>
    <form method="post" action="admin-post.php?action=pnct_socialstream_getTwitterBearer"> 
        <p>Twitter needs some manual work to get working.
        <ol><li>Go to <a href="https://dev.twitter.com/apps" target="_blank">dev.twitter.com/apps</a></li>
            <li>Register an application for example 'Social Stream'</li>
            <li>Copy the Consumer key & Consumer secret</li>
        </ol>
        <label for="twitter_key" style="display: inline-block;width: 150px;">Consumer key</label>
        <input type="text" name="twitter_key" /><br/>
        <label for="twitter_secret" style="display: inline-block;width: 150px;">Consumer secret</label>
        <input type="text" name="twitter_secret"/><br/>
         <?php submit_button(); ?>
    </form>
    <?php else: ?>
        <p>Your Twitter application has been setup correctly.</p>
    <?php endif; ?>
    <hr>
    <p><small>Last import on <?php echo get_option('socialstream_lastdate'); ?></small></p>
    <a href="admin-post.php?action=pnct_socialstream_startcron" class="button-secondary">Run import manually</a>
    <br/><br/>
    <small>The next automated import and export is scheduled at <?php date_default_timezone_set('Europe/Amsterdam'); echo date('d-m-Y H:i',wp_next_scheduled('pnct_socialstream_import')); ?></small>
</div>