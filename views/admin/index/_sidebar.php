<div id="harvester-duplicates" class="panel">
	<h4><?php echo __('Additional resources') ?></h4>
	<ul>
	<?php foreach($resources as $resource): ?>
	    <li>
	    	<a href="<?php echo url('additional-resources/index/edit/resource_id/'.$resource->id) ?>"><?php echo cut_string($resource->title, 120) ?></a>
	    </li>
	<?php endforeach; ?>
	</ul>
	<br /><a href="<?php echo url('additional-resources/index/add/item_id/'.$item->id) ?>"><?php echo __('Add additional resource'); ?></a><br /><br />
</div>

<?php if (OaipmhHarvesterPlugin::isTopLevelItem($item)): ?>
	<div id="harvester-duplicates" class="panel">
		<h4><?php echo __('Top level PDF file') ?></h4>
		<br />
		<?php if (AdditionalResource::itemHasPdfFile($item)): ?>
			<a href="<?php echo url('additional-resources/index/edit-pdf/item_id/'.$item->id) ?>"><?php echo __('Edit PDF file'); ?></a>
		<?php else: ?>	
			<a href="<?php echo url('additional-resources/index/add-pdf/item_id/'.$item->id) ?>"><?php echo __('Add PDF file'); ?></a>
		<?php endif; ?>
		<br /><br />

	</div>
<?php endif; ?>	