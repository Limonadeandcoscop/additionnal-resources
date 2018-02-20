<?php echo flash(); ?>

<form action="#" method="post" enctype="multipart/form-data">

	<div>
		<b><?php echo __('Title'); ?></b>
		<input type="text" style="width:100%;" name="title" value="<?php echo @$resource->title; ?>" />
	</div>

	<div>
		<b><?php echo __('Description'); ?></b>
		<textarea name="description" rows="5"><?php echo @$resource->description; ?></textarea>
	</div>

	<div>
		<b><br /><?php echo __('Files'); ?><br /><br /></b>
		
			<?php if(isset($resource)): ?>
				<?php foreach ($resource->getFiles() as $file): ?>
				<div class="pdf-file">
					<a class="pdf-icon" target="_blank" href="<?php echo $file->getUrl() ?>"><img src="<?php echo WEB_ROOT ?>/plugins/AdditionalResources/images/pdf.png"></a><br />
					<a target="_blank" href="<?php echo $file->getUrl() ?>"><?php echo $file->original_filename ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="delete-pdf-link" href="<?php echo $file->id; ?>"><?php echo __('Delete this file') ?></a>
					<input type="hidden" name="delete-pdf[<?php echo $file->id ?>]>" class="delete-pdf" />
				</div>
				<?php endforeach; ?>
			<?php endif; ?>	

			<div class="file" >
				<input type="file" name="files[0]" multiple=""/>
			</div>
		
		<br /><a id="add-file" href="#"><?php echo __('Add another file'); ?></a>
	</div>

	<br /><br /><input type="submit" value="<?php echo __('Save resource') ?>" />

	<a href="<?php echo html_escape(url('additional-resources/index/delete-confirm/id/'.$resource->id)) ?>" class="delete-confirm red button">Delete resource</a>
	
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

    $('.delete-pdf-link').click(function() {
    	var id = $(this).attr('href');
    	var img = $(this).parents('.pdf-file').find('img');
    	var input = $(this).parents('.pdf-file').find('input.delete-pdf');

    	if (input.val() == '1') {
			img.css('opacity', '1');
    		input.val('');	
    	} else {
    		img.css('opacity', '.5');
    		input.val('1');	
    	}
        return false;
    });
   

});
</script>

<style>
.pdf-file {
	clear:both;
	display: block;
	height:40px;
	margin-bottom: 10px;
	line-height:15px;
}

.pdf-icon img {
	float:left;
	height: 40px;
	margin-right:5px;
}
</style>