<?php echo flash(); ?>

<form action="#" method="post" enctype="multipart/form-data">

	<div>
		<b><?php echo __('Description'); ?></b>
		<textarea name="description" rows="5" style="margin-top:10px"></textarea>
	</div>

	<div>
		<b><br /><?php echo __('Files'); ?><br /><br /></b>
		<div class="file" ><input type="file" name="files[0]" multiple=""/></div>
		<br /><a id="add-file" href="#"><?php echo __('Add another file'); ?></a>
	</div>

	<br /><br /><input type="submit" value="<?php echo __('Add resource') ?>" />

</form>


<script>
jQuery(document).ready(function($) {
	$('#add-file').click(function() {
		var files 	= $('div.file');
		var file 	= files.last();
		var cloned 	= file.clone(false);
		var input 	= cloned.find('input');
		input.val('');
		input.attr('name', "files["+files.length+"]");
		cloned.insertAfter(file);   
	});
});
</script>