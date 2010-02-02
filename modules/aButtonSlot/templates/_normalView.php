<?php if ($editable): ?>
  <?php // Normally we have an editor inline in the page, but in this ?>
  <?php // case we'd rather use the picker built into the media plugin. ?>
  <?php // So we link to the media picker and specify an 'after' URL that ?>
  <?php // points to our slot's edit action. Setting the ajax parameter ?>
  <?php // to false causes the edit action to redirect to the newly ?>
  <?php // updated page. ?>
  <?php // Wrap controls in a slot to be inserted in a slightly different ?>
  <?php // context by the _area.php template ?>

  <?php // Very short labels so sidebar slots don't have wrap in their controls. ?>
  <?php // That spoils assumptions that are being made elsewhere that they will ?>
  <?php // amount to only one row. TODO: find a less breakage-prone solution to that problem. ?>
  <?php slot("a-slot-controls-$name-$permid") ?>
  	<li class="a-controls-item choose-image">
  	  <?php include_partial('aImageSlot/choose', array('action' => 'aButtonSlot/image', 'buttonLabel' => 'Choose image', 'label' => 'Select an Image', 'class' => 'a-btn icon a-media', 'type' => 'image', 'constraints' => $constraints, 'itemId' => $itemId, 'name' => $name, 'slug' => $slug, 'permid' => $permid)) ?>

  	</li>

    <?php include_partial('a/simpleEditButton',
      array('name' => $name, 'permid' => $permid, 'label' => 'URL', 'title' => 'Set URL', 'controlsSlot' => false)) ?>
  <?php end_slot() ?>
<?php endif ?>

<?php if ($item): ?>
  <ul class="a-button">
    <li class="a-button-image">
    <?php $embed = str_replace(
      array("_WIDTH_", "_HEIGHT_", "_c-OR-s_", "_FORMAT_"),
      array($dimensions['width'], 
        $dimensions['height'],
        $dimensions['resizeType'],
        $dimensions['format']),
        $embed) ?>
    <?php if ($link): ?>
      <?php $embed = "<a class=\"a-button-link\" href=\"$link\">$embed</a>" ?>
    <?php endif ?>
    <?php echo $embed ?>
    </li>
    <?php if (isset($img_title)): ?>
      <li class="a-button-title"><?php echo $img_title ?></li>
    <?php endif ?>
    <?php if ($description): ?>
      <li class="a-button-description"><?php echo $item->description ?></li>
    <?php endif ?>
  </ul>
<?php else: ?>
  <?php if ($defaultImage): ?>
  <ul class="a-button default">
      <li class="a-button-image">
        <?php echo image_tag($defaultImage) ?>
      <li>
    </ul>
  <?php endif ?>
<?php endif ?>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {

		$('.a-button a').hover(function(){
			$(this).children('img').fadeTo(0,.5);
		},function(){
			$(this).children('img').fadeTo(0,1);			
		});

		<?php if (0): ?>
		// I am saving this one, It's an OVERLAY that we can apply colors to, rather than a simple opacity toggle on hover
		// $('.a-button a').append('<span class="button-hover-overlay"></span>');
		// $('.button-hover-overlay').fadeTo(0,.5).hide().parent().hover(function(){
		// 	$(this).children('span').show();
		// }, function(){
		// 	$(this).children('span').hide();
		// })
		<?php endif ?>
	});
</script>