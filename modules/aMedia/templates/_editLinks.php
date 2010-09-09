<?php
  // Compatible with sf_escaping_strategy: true
  $mediaItem = isset($mediaItem) ? $sf_data->getRaw('mediaItem') : null;
?>
<?php use_helper('I18N', 'jQuery') ?>
<?php if ($mediaItem->userHasPrivilege('edit')): ?>
	<ul class="a-ui a-controls">
    <li><?php echo link_to(a_("Edit"), "aMedia/edit", array("query_string" => http_build_query(array("slug" => $mediaItem->getSlug())), "class" => "a-btn icon a-edit")) ?></li>

		<?php if ($mediaItem->getDownloadable() && $sf_params->get('action') == 'show'): ?>
	  <li class="a-media-download-original">
	     <?php // download link ?>
	     <?php echo link_to(__("Download Original", null, 'apostrophe'), "aMediaBackend/original?".http_build_query(array(
	             "slug" => $mediaItem->getSlug(),
	             "format" => $mediaItem->getFormat())),
	              array(
					"class"=>"a-btn icon a-download"
					))?>
		</li>
		<?php endif ?>

		<li><?php echo link_to(__("Delete", null, 'apostrophe'), "aMedia/delete?" . http_build_query(array("slug" => $mediaItem->getSlug())),array("confirm" => __("Are you sure you want to delete this item?", null, 'apostrophe'), "class"=>"a-btn icon a-delete no-label")) ?></li>

	</ul>
<?php endif ?>
