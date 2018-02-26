<?php

$head = array('bodyclass' => 'additional-resources primary', 
              'title' => html_escape(__('Additional Resources')));
echo head($head);
?>
<h3><?php echo __('Add a PDF file') ?></h3>

<?php echo flash(); ?>

<form action="#" method="post" enctype="multipart/form-data">

	<div>
		<div class="file" >
			<input type="file" name="pdf-file" multiple=""/>
		</div>
	</div>

	<br /><br /><input type="submit" value="<?php echo __('Save') ?>" />

</form>

<?php echo foot(); ?>

