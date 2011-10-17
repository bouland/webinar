<?php
	global $CONFIG;
	include dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bbb_api_conf.php';
	$plugin = find_plugin_settings('webinar');
?>
<p>
    <fieldset style="border: 1px solid; padding: 15px; margin: 0 10px 0 10px">
        <legend><?php echo elgg_echo('webinar:settings:label:server');?></legend>
        
        <label for="params[serverURL]"><?php echo elgg_echo('webinar:settings:label:serverURL');?></label><br/>
        <div class="example"><?php echo elgg_echo('webinar:settings:help:serverURL');?></div>
        <input type="text" name="params[serverURL]" value="<?php if (empty($plugin->serverURL)) {echo $url;} else {echo $plugin->serverURL;}?>"/><br/>
        
        <label for="params[serverSalt]"><?php echo elgg_echo('webinar:settings:label:serverSalt');?></label><br/>
        <div class="example"><?php echo elgg_echo('webinar:settings:help:serverSalt');?></div>
        <input type="text" name="params[serverSalt]" value="<?php if (empty($plugin->serverSalt)) {echo $salt;} else {echo $plugin->serverSalt;}?>"/><br/>

    </fieldset>
</p>
