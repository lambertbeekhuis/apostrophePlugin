<?php use_helper('jQuery') ?>
<?php if ($editable): ?>
  <?php // JqueryReloadedPlugin won't let us assemble our own URL and force a query string, ?>
  <?php // so let's go jQuery native ?>
  <form method="POST" action="#" class="a-slot-form" name="a-slot-form-<?php echo $id ?>" id="a-slot-form-<?php echo $id ?>" style="display: <?php echo $showEditor ? "block" : "none" ?>">
  <?php include_component($editModule, 
    "editView", 
    array(
      "name" => $name,
      "type" => $type,
      "permid" => $permid,
      "options" => $options,
      "validationData" => $validationData)) ?>
	<ul class="a-controls a-slot-save-cancel-controls">  
		<?php	// JB Note: I moved the submit button javascript down to the bottom ?>
	  <li><input type="submit" name="Save" value="Save" class="submit a-submit" 
	    id="<?php echo 'a-slot-form-submit-' . $name . '-' . $permid ?>" /></li>
		<?php // JB Note: I moved the cancel javascript down to the bottom  ?>
	  <li><?php echo button_to_function("Cancel", "", array("class" => "a-submit a-cancel", 'id' => 'a-slot-form-cancel-'.$name.'-'.$permid, )) ?></li>
	</ul>
  </form>
  <script>
  $('#a-slot-form-<?php echo $id ?>').submit(function() {
    $.post(
      <?php // These fields are the context, not something the user gets to edit. So rather than ?>
      <?php // creating a gratuitous collection of hidden form widgets that are never edited, let's ?> 
      <?php // attach the necessary context fields to the URL just like Doctrine forms do. ?>
      <?php // We force a query string for compatibility with our simple admin routing rule ?>
      <?php echo json_encode(url_for("$type/edit") . '?' . http_build_query(array('slot' => $name, 'permid' => $permid, 'slug' => $slug, 'real-slug' => $realSlug))) ?>, 
      $('#a-slot-form-<?php echo $id ?>').serialize(), 
      function(data) {
        $('#a-slot-content-<?php echo $id ?>').html(data)
      }, 
      'html'
    );
    return false;
  });
  </script>  
<?php endif ?>
<?php if ($editable): ?>
  <div class="a-slot-content-container <?php echo $outlineEditable ? " a-editable" : "" ?>" id="a-slot-content-container-<?php echo $name ?>-<?php echo $permid ?>" style="display: <?php echo $showEditor ? "none" : "block"?>">
<?php endif ?>
<?php include_component($normalModule, 
  "normalView", 
  array(
    "name" => $name,
    "type" => $type,
    "permid" => $permid,
    "options" => $options)) ?>
<?php if ($editable): ?>
  </div>
<?php endif ?>

<?php if ($editable): ?>
  <script type="text/javascript">
  $(document).ready(function() {

    var normalView = $('#a-slot-<?php echo $name ?>-<?php echo $permid ?>');

		// CANCEL
		$('#a-slot-form-cancel-<?php echo $name ?>-<?php echo $permid ?>').click(function(){
  		$(normalView).children('.a-slot-content').children('.a-slot-content-container').fadeIn();
  		$(normalView).children('.a-slot-content').children('.a-slot-form').hide();
  		$(this).parents('.a-slot').find('.a-slot-controls .edit').removeClass('editing-now');
 			$(this).parents('.a-area.singleton').find('.a-area-controls .edit').removeClass('editing-now'); // for singletons
  	});

		// SAVE 
  	$('#a-slot-form-submit-<?php echo $name ?>-<?php echo $permid ?>').click(function(){
  			$(this).parents('.a-slot').find('.a-slot-controls .edit').removeClass('editing-now');
  			$(this).parents('.a-area.singleton').find('.a-area-controls .edit').removeClass('editing-now'); // for singletons
  			window.a.callOnSubmit('<?php echo $id ?>');
  			return true;
  	});

	<?php if ($showEditor): ?>
		var editBtn = $('#a-slot-edit-<?php echo $name ?>-<?php echo $permid ?>');
		editBtn.parent().addClass('editing-now');
	<?php endif; ?>

  });
  </script>
<?php endif ?>
