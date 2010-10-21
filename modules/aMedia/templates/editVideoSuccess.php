<?php
  // Compatible with sf_escaping_strategy: true
  $form = isset($form) ? $sf_data->getRaw('form') : null;
  $item = isset($item) ? $sf_data->getRaw('item') : null;
  $serviceError = isset($serviceError) ? $sf_data->getRaw('serviceError') : null;
?>
<?php use_helper('a') ?>

<?php slot('body_class') ?>a-media<?php end_slot() ?>

<div class="a-media-library">

<?php include_component('aMedia', 'browser') ?>

<div class="a-media-toolbar">
  <h3>
		<?php if ($item): ?> 
			<?php echo __('Editing Video: %title%', array('%title%' => $item->getTitle()), 'apostrophe') ?>
    <?php else: ?> 
			<?php echo __('Add Video', null, 'apostrophe') ?> 
		<?php endif ?>
   </h3>
</div>

<div class="a-media-items">				

  <?php if ($item): ?>
  	<?php $slug = $item->getSlug() ?>
  <?php else: ?>
  	<?php $slug = false ?>
  <?php endif ?>

  <?php // Post-form-validation error when we tried to get the thumbnail ?>
  <?php if (isset($serviceError)): ?>
  <h3><?php echo __('That is not a valid YouTube video URL.', null, 'apostrophe') ?></h3>
  <?php endif ?>

  <form method="POST" id="a-media-edit-form-0" enctype="multipart/form-data" action="<?php echo url_for(aUrl::addParams("aMedia/editVideo", array("slug" => $slug)))?>">

		<div class='a-form-row a-hidden'>
  		<?php echo $form->renderHiddenFields() ?>
		</div>

    <div class="a-form-row title">
      <?php echo $form['title']->renderLabel() ?>
      <?php if (!$sf_params->get('first_pass')): ?>
        <?php echo $form['title']->renderError() ?>
      <?php endif ?>
      <?php echo $form['title']->render() ?>
    </div>

    <?php if (isset($form['service_url'])): ?>
      <div class="a-form-row service-url">
        <?php echo $form['service_url']->renderRow() ?>
      </div>
    <?php endif ?>

    <?php if (isset($form['embed'])): ?>
      <div class="a-form-row embed">
        <?php echo $form['embed']->renderRow() ?>
      </div>
    <?php endif ?>

    <div class="a-form-row description">
      <?php echo $form['description']->renderLabel() ?>
      <?php echo $form['description']->renderError() ?>
      <?php echo $form['description']->render() ?>
    </div>

    <div class="a-form-row credit">
      <?php echo $form['credit']->renderLabel() ?>
      <?php echo $form['credit']->renderError() ?>
      <?php echo $form['credit']->render() ?>
    </div>

    <div class="a-form-row categories">
			<?php echo $form['categories_list']->renderLabel() ?>
			<?php if (!$sf_params->get('first_pass')): ?>
			  <?php echo $form['categories_list']->renderError() ?>
			<?php endif ?>
			<?php echo $form['categories_list']->render() ?>
		</div>

    <div class="a-form-row about-tags">
    <?php echo __('Tags should be separated by commas. Example: student life, chemistry, laboratory', null, 'apostrophe') ?>
    </div>

    <div class="a-form-row tags">
      <?php echo $form['tags']->renderLabel() ?>
      <?php echo $form['tags']->renderError() ?>
      <?php echo $form['tags']->render() ?>
    </div>

    <div class="a-form-row permissions">
      <?php echo $form['view_is_secure']->renderLabel() ?>
      <?php echo $form['view_is_secure']->renderError() ?>
      <?php echo $form['view_is_secure']->render() ?>
    </div>

    <ul class="a-ui a-controls">
      <li><input type="submit" value="<?php echo __('Save', null, 'apostrophe') ?>" class="a-btn a-submit" /></li>
      <?php if ($item): ?>
      <li><?php echo link_to('<span class="icon"></span>'.__("Delete", null, 'apostrophe'), "aMedia/delete?" . http_build_query(
          array("slug" => $slug)),
          array("confirm" => __("Are you sure you want to delete this item?", null, 'apostrophe'),
            "target" => "_top", "class"=>"a-btn icon a-delete")) ?></li>
      <?php endif ?>
			<li><?php echo link_to('<span class="icon"></span>'.__("Cancel", null, 'apostrophe'), "aMedia/resumeWithPage", array("class"=>"a-btn icon a-cancel")) ?></li>
    </ul>
  </form>
</div>

<?php include_partial('aMedia/itemFormScripts') ?>

</div>