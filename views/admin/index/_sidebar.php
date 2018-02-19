<div id="harvester-duplicates" class="panel">
<h4><?php echo __('Additional resources') ?></h4>
<ul>
<?php foreach($resources as $resource): ?>
    <li>
    	<a href="<?php echo url('additional-resources/index/edit/resource_id/'.$resource->id) ?>"><?php echo cut_string($resource->description, 120) ?></a>
    </li>
<?php endforeach; ?>
</ul>
<br /><a href="<?php echo url('additional-resources/index/add/item_id/'.$item->id) ?>"><?php echo __('Add additional resource'); ?></a><br /><br />
</div>