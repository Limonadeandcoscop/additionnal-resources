<?php if (!count($resources)): ?>

	<p><?php echo __('There\'s no resource for this item') ?></p>

<?php else: ?>

	<?php foreach ($resources as $resource): ?>
		<p class="description"><?php echo $resource->description ?></p>
		<?php $files = $resource->getFiles(); ?>
		<?php if (count($files)): ?>
			<?php foreach ($files as $file): ?>
				<div class="pdf-file"><a href="<?php echo $file->getUrl() ?>" target="_blank"><?php echo $file->original_filename ?></a></div>
			<?php endforeach; ?>				
		<?php endif; ?>			
	<?php endforeach; ?>	

<?php endif; ?>

<br /><br />