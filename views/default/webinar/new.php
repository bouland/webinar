<?php
//	action('webinar/new');
	$message = get_input('message');
	$joinURL = get_input('joinURL');
?>
<div>
<h1>Create Meeting</h1>
<div>
<?php echo $message ?>
</div>
<div>
<?php echo $joinURL ?>
</div>
</div>