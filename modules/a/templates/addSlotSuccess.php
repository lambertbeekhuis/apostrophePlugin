<?php
  // Compatible with sf_escaping_strategy: true
  $name = isset($name) ? $sf_data->getRaw('name') : null;
  $options = isset($options) ? $sf_data->getRaw('options') : null;
  $type = isset($type) ? $sf_data->getRaw('type') : null;
?>
<?php use_helper('jQuery') ?>

<?php include_component('a', 'area', array('name' => $name, 'refresh' => true, 'addSlot' => $type, 'preview' => false, 'options' => $options))?>

<?php a_js_call('apostrophe.afterAddingSlot(?)', $name) ?>
<?php // AJAX actions need to do this directly ?>
<?php a_include_js_calls() ?>
