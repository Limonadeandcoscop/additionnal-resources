<?php if (!count($resources)): ?>

	<p><?php echo __('There\'s no resource for this item') ?></p>

<?php else: ?>

	<?php foreach ($resources as $resource): ?>
		<div class="resource">
			<?php $user = get_record_by_id("User", $resource->user_id); ?>
			<p class="title"><strong><?php echo $resource->title ?></strong></p>
			<p class="author">
				<?php echo __('By') ?> : <?php echo $user->name ?>
				<br /><span class="date"><?php echo $resource->created ?></span>
			</p>
			<?php if (strlen(trim($resource->description))): ?>
				<p class="description"><?php echo $resource->description ?></p>
			<?php endif; ?>				
			<?php $files = $resource->getFiles(); ?>
			<?php if (count($files)): ?>
				<?php foreach ($files as $file): ?>
					<div class="pdf-file"><a href="<?php echo $file->getUrl() ?>" target="_blank"><?php echo $file->original_filename ?></a></div>
				<?php endforeach; ?>				
			<?php endif; ?>			
		</div>
	<?php endforeach; ?>	

<?php endif; ?>

<style>
.resource {
	border:1px dotted #ccc; 
	padding:0 10px;
	margin-bottom:20px;
}
</style>
<br /><br />