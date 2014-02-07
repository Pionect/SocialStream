<?php $usertype = SOCIALSTREAM_USERTYPE; 

if($single):?>
<p>This widget will display socialstream items from the one user defined in the settings</p>
<?php else:
//options: all_users, `use id from page as id`, pick a fixed user
endif;
$var = 'format'; $formats = array('slider','static'); ?>
<br/>
<label for="<?php echo $this->get_field_id($var); ?>">Formfactor:</label>
<select id="<?php echo $this->get_field_id($var); ?>" class="widefat" name="<?php echo $this->get_field_name($var); ?>">
<?php foreach($formats as $format): ?>
    <option value="<?php echo $format;?>" <?php selected($instance[$var],$format); ?>><?php echo ucfirst($format);?></option>
<?php endforeach; ?>
</select><br/>

<?php $var = 'itemlimit'; ?>
<label for="<?php echo $this->get_field_id($var); ?>">Item limit:</label>
<input class="widefat"
       id="<?php echo $this->get_field_id($var); ?>"
       name="<?php echo $this->get_field_name($var); ?>"
       type="text"
       value="<?php echo $instance[$var]; ?>"
       placeholder ="10"/>
<br/>
<br/>
<?php $var = 'buttons'; ?>
<label for="<?php echo $this->get_field_id($var); ?>">Left/right buttons:</label>
<label><input id="<?php echo $this->get_field_id($var); ?>" name="<?php echo $this->get_field_name($var); ?>" type="radio" <?php checked($instance[$var],'on'); ?> value="on"/> On</label>
<label><input id="<?php echo $this->get_field_id($var); ?>" name="<?php echo $this->get_field_name($var); ?>" type="radio" <?php checked($instance[$var],'off'); ?> value="off"/> Off</label>
<br/>
<br/>
<?php $var = 'indicators'; ?>
<label for="<?php echo $this->get_field_id($var); ?>">Indicators:</label>
<label><input id="<?php echo $this->get_field_id($var); ?>" name="<?php echo $this->get_field_name($var); ?>" type="radio" <?php checked($instance[$var],'on'); ?> value="on"/> On</label>
<label><input id="<?php echo $this->get_field_id($var); ?>" name="<?php echo $this->get_field_name($var); ?>" type="radio" <?php checked($instance[$var],'off'); ?> value="off"/> Off</label><br/>
<br/>
<?php /*$var = 'size'; $sizes = array('small','large'); ?>
<label for="<?php echo $this->get_field_id($var); ?>">Size:</label>
<select id="<?php echo $this->get_field_id($var); ?>" class="widefat" name="<?php echo $this->get_field_name($var); ?>">
<?php foreach($sizes as $size): ?>
    <option value="<?php echo $size; ?>" <?php selected($instance[$var],$size); ?>><?php echo ucfirst($size); ?></option>
<?php endforeach; ?>
</select>
<br/>*/ ?>