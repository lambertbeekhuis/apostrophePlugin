<?php use_helper('jQuery', 'I18N') ?>

<?php if ($editable): ?>

  <form method="POST" action="#" class="a-slot-form" name="a-slot-form-<?php echo $id ?>" id="a-slot-form-<?php echo $id ?>" style="display: <?php echo $showEditor ? "block" : "none" ?>">

  <?php include_component($editModule, 
    "editView", 
    array(
      "name" => $name,
      "type" => $type,
      "permid" => $permid,
      "options" => $options,
      "updating" => $updating,
      "validationData" => $validationData)) ?>

	<ul class="a-controls a-slot-save-cancel-controls">  
	  <li>
			<input type="submit" name="Save" value="<?php echo htmlspecialchars(__('Save')) ?>" class="submit a-submit" id="<?php echo 'a-slot-form-submit-' . $id ?>" />
		</li>
	  <li>
			<?php echo button_to_function(__("Cancel"), "", array("class" => "a-cancel", 'id' => 'a-slot-form-cancel-' . $id )) ?>
		</li>
	</ul>

  </form>

	<script type="text/javascript" charset="utf-8">
		$('#a-slot-form-<?php echo $id ?>').submit(function() {
	    $.post(
	      <?php // These fields are the context, not something the user gets to edit. So rather than ?>
	      <?php // creating a gratuitous collection of hidden form widgets that are never edited, let's ?> 
	      <?php // attach the necessary context fields to the URL just like Doctrine forms do. ?>
	      <?php // We force a query string for compatibility with our simple admin routing rule ?>
	      <?php echo json_encode(url_for($type . 'Slot/edit') . '?' . http_build_query(array('slot' => $name, 'permid' => $permid, 'slug' => $slug, 'real-slug' => $realSlug))) ?>, 
	      $('#a-slot-form-<?php echo $id ?>').serialize(), 
	      function(data) {
	        $('#a-slot-content-<?php echo $id ?>').html(data);
	      }, 
	      'html'
	    );
	    return false;
  	});
  </script>  
<?php endif ?>

<?php if ($editable): ?>
  <div class="a-slot-content-container <?php echo $outlineEditable ? " a-editable" : "" ?>" id="a-slot-content-container-<?php echo $id ?>" style="display: <?php echo $showEditor ? "none" : "block"?>">
<?php endif ?>

<?php include_component($normalModule, 
  "normalView", 
  array(
    "name" => $name,
    "type" => $type,
    "permid" => $permid,
    "updating" => $updating,
    "options" => $options)) ?>

<?php if ($editable): ?>
  </div>
<?php endif ?>

<?php if ($editable): ?>
  <script type="text/javascript">
  $(document).ready(function() {

    var view = $('#a-slot-<?php echo $id ?>');

		// CANCEL
		$('#a-slot-form-cancel-<?php echo $id ?>').click(function(){
  		$(view).children('.a-slot-content').children('.a-slot-content-container').fadeIn();
  		$(view).children('.a-controls-item variant').fadeIn();
  		$(view).children('.a-slot-content').children('.a-slot-form').hide();
  		$(view).find('.editing-now').removeClass('editing-now');
 			$(view).parents('.a-area.singleton').find('.editing-now').removeClass('editing-now'); // for singletons
  	});

		// SAVE 
  	$('#a-slot-form-submit-<?php echo $id ?>').click(function(){
  			$(this).parents('.a-slot').find('.a-slot-controls .edit').removeClass('editing-now');
  			$(this).parents('.a-area.singleton').find('.a-area-controls .edit').removeClass('editing-now'); // for singletons
  			window.apostrophe.callOnSubmit('<?php echo $id ?>');
  			return true;
  	});

	<?php if ($showEditor): ?>
		var editBtn = $('#a-slot-edit-<?php echo $id ?>');
		editBtn.parent().addClass('editing-now');
	<?php endif; ?>

  });  
  </script>
<?php endif ?>

<?php if ($sf_request->isXmlHttpRequest()): ?>
  <?php // Changing the variant only refreshes the content, not the outer wrapper and controls. However, ?>
  <?php // we do assign a CSS class to the outer wrapper based on the variant ?>
  <?php $variants = aTools::getVariantsForSlotType($type, $options) ?>
  <?php if (count($variants)): ?>
    <script type="text/javascript">
      $(document).ready(function() {
        var outerWrapper = $('#a-slot-<?php echo $id ?>');
        <?php foreach ($variants as $variant => $data): ?>
          <?php if ($slot->variant !== $variant): ?>
            outerWrapper.removeClass(<?php echo json_encode($variant) ?>);
          <?php else: ?>
            outerWrapper.addClass(<?php echo json_encode($variant) ?>);
          <?php endif ?>
        <?php endforeach ?>
      });
    </script>
  <?php endif ?>
<?php endif ?>