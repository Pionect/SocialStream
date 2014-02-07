<div class="wrap">
    <?php if(array_key_exists('twitter',$_GET)): ?>
        <div class="error"><p>Your Twitter consumer data wasn't correct.</p></div>
    <?php endif; ?>
    <?php screen_icon(); ?>
    <h2>Social Stream</h2>
    <form method="post" action="options.php"> 
        <?php settings_fields( 'socialstream' ); ?>
        <h3><?php _e('Settings');?></h3>
        <?php /*  Choose social media platforms */ ?>
        <?php /* <label for="color" style="display: inline-block;width: 150px;">Color</label>
        <input type="text" name="socialstream_color" value="<?php echo get_option('socialstream_color'); ?>" /><br/>
        <small>For example: #333333;</small> */ ?>
        
        <strong>How to use this plugin</strong><br/>
        <?php $usertype = SOCIALSTREAM_USERTYPE ?>
        <label><input type="radio" <?php checked($usertype,'single'); ?> class="usertype" name="socialstream_usertype" value="single"> single user</label><br/>
        <label><input type="radio" <?php checked($usertype,'wp_users'); ?> class="usertype" name="socialstream_usertype" value="wp_users"> wp_users </label><br/>
        <label><input type="radio" <?php checked($usertype,'wp_post'); ?> class="usertype" name="socialstream_usertype" value="wp_post"> wp_post</label><br/>
        <select id="post_types" name="socialstream_user_posttype" style="display:none;">
            <option value="">Pick a post type</option>
            <?php $args = array('public'=>true);
            $post_types = get_post_types( $args, 'names' ); 
            $user_posttype = get_option('socialstream_user_posttype');
            foreach ( $post_types as $post_type ) {
               echo '<option value="' . $post_type . '" '.selected($usertype,$post_type).'>' . $post_type . '</option>';
            } ?>
        </select>
        <br/>
        <!-- In case there is only a single user -->
        <div id="single_user_accounts">
            <strong>Single user accounts</strong><br/>
            <label for="socialstream_useraccounts[twitter]" style="display: inline-block;width: 150px;">twitter user</label>
            <input type="text" name="socialstream_useraccounts[twitter]" value="<?php echo $accounts['twitter']; ?>" /><br/>
            <label for="socialstream_useraccounts[facebook]" style="display: inline-block;width: 150px;">facebook user</label>
            <input type="text" name="socialstream_useraccounts[facebook]" value="<?php echo $accounts['facebook'];  ?>" /><br/>
            <label for="socialstream_useraccounts[flickr]" style="display: inline-block;width: 150px;">flick user</label>
            <input type="text" name="socialstream_useraccounts[flick]" value="<?php echo $accounts['flickr'];  ?>" /><br/>
            <label for="socialstream_useraccounts[vimeo]" style="display: inline-block;width: 150px;">vimeo user</label>
            <input type="text" name="socialstream_useraccounts[vimeo]" value="<?php echo $accounts['vimeo'];  ?>" /><br/>
            <label for="socialstream_useraccounts[instagram]" style="display: inline-block;width: 150px;">Instagram user</label>
            <input type="text" name="socialstream_useraccounts[instagram]" value="<?php echo $accounts['instagram']; ?>" /><br/>
        </div>
        <?php submit_button('Save settings'); ?>
        <script>
            jQuery('.usertype').change(function(){
                userTypeUI();
            })
            function userTypeUI(){
                var $ = jQuery,
                    value = jQuery('.usertype:checked').val();
                if(value=='wp_post'){
                    $('#post_types').show();
                } else {
                    $('#post_types').hide();
                }
                
                if(value=='single'){
                    $('#single_user_accounts').show();
                } else {
                    $('#single_user_accounts').hide();
                }
            }
            userTypeUI();
        </script>
        <hr>
        <h3>Instagram setup</h3>
        <?php if(!get_option('socialstream_instagram_clientid')): ?>
            TODO: explain what steps to take...<br/>
            <label for="socialstream_instagram_clientid" style="display: inline-block;width: 150px;">Client ID</label>
            <input type="text" name="socialstream_instagram_clientid" /><br/>
             <?php submit_button('Save Instagram settings'); ?>
        <?php else: ?>
            <p>Your instagram application has been setup correctly.</p>
            <input type="hidden" name="socialstream_instagram_clientid" value="<?php echo get_option('socialstream_instagram_clientid'); ?>"  />
        <?php endif; ?>
    </form>
    <hr>
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
         <?php submit_button('Save Twitter settings'); ?>
    </form>
    <?php else: ?>
        <p>Your Twitter application has been setup correctly.</p>
    <?php endif; ?>
    <hr>
    <p><small>Last import on <?php echo get_option('socialstream_lastdate'); ?></small></p>
    <a href="admin-post.php?action=pnct_socialstream_startcron" class="button-secondary">Run import manually</a>
    <br/><br/>
    <small>The next automated import and export is scheduled at <?php date_default_timezone_set('Europe/Amsterdam'); echo date('d-m-Y H:i',wp_next_scheduled('pnct_socialstream_import')); ?></small>
    <br/><br/>
    <a href="admin-post.php?action=pnct_socialstream_truncate" class="button-secondary">Clear the complete socialstream</a>
</div>