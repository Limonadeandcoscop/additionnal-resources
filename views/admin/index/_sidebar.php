<div id="harvester-duplicates" class="panel">
<h4><?php echo __('Additional resources') ?></h4>
<?php /*
<ul>
<?php foreach($items as $item): ?>
    <li>
    <?php echo link_to_item(
            'Item #' . $item->id,
            array(),
            'show',
            $item
        ); ?>
    </li>
    <?php release_object($item); ?>
<?php endforeach; ?>
</ul>
*/
?>
<br /><a href="<?php echo url('additional-resources/index/add/item_id/'.$item->id) ?>"><?php echo __('Add additional resource'); ?></a><br /><br />
</div>