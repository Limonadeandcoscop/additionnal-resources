<?php

$head = array('bodyclass' => 'additional-resources primary', 
              'title' => html_escape(__('Additional Resources')));
echo head($head);
?>
<h3><?php echo __('Edit an addtionnal resource') ?></h3>

<?php echo flash(); ?>

<?php require('form.php'); ?>

<?php echo foot(); ?>
