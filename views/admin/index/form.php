<?php echo flash(); ?>

<form action="#" method="post" enctype="multipart/form-data">

	<div>
		<b><?php echo __('Description'); ?></b>
		<textarea name="description" rows="5" style="margin-top:10px"></textarea>
	</div>

	<div>
		<b><br /><?php echo __('Files'); ?><br /><br /></b>
		<div class="file" ><input type="file" name="files[0]" /></div>
		<br /><a id="add-file" href="#"><?php echo __('Add another file'); ?></a>
	</div>

	<br /><br /><input type="submit" value="<?php echo __('Add resource') ?>" />

</form>


<script>
jQuery(document).ready(function($) {
	$('#add-file').click(function() {
		var file = $('div.file').last();
		var cloned = file.clone(false);
		cloned.find('input').val('');
		cloned.insertAfter(file);   
	});
});
</script>