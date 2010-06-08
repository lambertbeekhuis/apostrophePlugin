<?php use_helper('I18N', 'jQuery') ?>

<?php $imageInfo = aMediaTools::getAttribute('imageInfo') ?>
<?php $ids = aArray::getIds(aMediaTools::getSelection()) ?>
<?php $aspectRatio = aMediaTools::getAspectRatio() ?>

<?php foreach ($items as $item): ?>
<li id="a-media-selection-list-item-<?php echo $item->getId() ?>" class="a-media-selection-list-item">
	<?php $id = $item->getId() ?>
  <ul class="a-controls a-media-multiple-list-controls">	
	  <li><?php echo jq_link_to_remote(__("remove this item", null, 'apostrophe'),
    array(
      'url' => 'aMedia/multipleRemove?id='.$id,
      'update' => 'a-media-selection-list',
			'complete' => 'aUI("a-media-selection-list"); aMediaDeselectItem('.$id.'); aMediaUpdatePreview()', 
    ), array(
			'class'=> 'a-btn icon a-delete no-label',
			'title' => __('Remove', null, 'apostrophe'), )) ?>
		</li>
	</ul>	

	<div class="a-media-selected-item-drag-overlay" title="<?php echo __('Drag &amp; Drop to Order', null, 'apostrophe') ?>"></div>
	<div class="a-media-selected-item-overlay"></div>
  <img src="<?php echo url_for($item->getCropThumbnailUrl()) ?>" />

</li>
<?php endforeach ?>

<script type="text/javascript" charset="utf-8">

	function aMediaItemsIndicateSelected(cropOptions)
	{
	  var ids = cropOptions.ids;
	  aCrop.init(cropOptions);
		$('.a-media-selected-overlay').remove();		
		$('.a-media-selected').removeClass('a-media-selected');
		
	  var i;
	  for (i = 0; (i < ids.length); i++)
	  {
	    id = ids[i];
	    var selector = '#a-media-item-' + id;
	    if (!$(selector).hasClass('a-media-selected')) 
	    {
	      $(selector).addClass('a-media-selected');
			}
		}
	
		$('.a-media-item.a-media-selected').each(function(){
			$(this).children('.a-media-item-thumbnail').prepend('<div class="a-media-selected-overlay"></div>');
		});

	 	$('.a-media-selected-overlay').fadeTo(0, 0.66);
	}
	
	function aMediaUpdatePreview()
	{
	  $('#a-media-selection-preview').load('<?php echo url_for('aMedia/updateMultiplePreview') ?>', function(){
  	  // the preview images are by default set to display:none
	    $('#a-media-selection-preview li:first').addClass('current');
	    // set up cropping again; do hard reset to reinstantiate Jcrop
	    aCrop.resetCrop(true);
	  });
	}

	function aMediaDeselectItem(id)
	{
		$('#a-media-item-'+id).removeClass('a-media-selected');
		$('#a-media-item-'+id).children('.a-media-selected-overlay').remove();
	}

	$('.a-media-thumb-link').click(function(){
		$(this).addClass('a-media-selected');
	});

	$(document).ready(function() { // On page ready indicate selected items
	  var cropOptions = {
      ids: <?php echo json_encode($ids) ?>,
      aspectRatio: <?php echo $aspectRatio ?>,
      minimumWidth: <?php echo aMediaTools::getAttribute('minimum-width') ?>,
      minimumHeight: <?php echo aMediaTools::getAttribute('minimum-height') ?>,
      <?php // width height cropLeft cropTop cropWidth cropHeight hashed by image id ?>
      imageInfo: <?php echo json_encode(aMediaTools::getAttribute('imageInfo')) ?>
    };
	  
		aMediaItemsIndicateSelected(cropOptions);
		
		$('.a-media-selected-item-overlay').fadeTo(0,.35); //cross-browser opacity for overlay
		$('.a-media-selection-list-item').hover(function(){
			$(this).addClass('over');
		},function(){
			$(this).removeClass('over');			
		});
	});
</script>