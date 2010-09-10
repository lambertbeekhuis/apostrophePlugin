<?php use_helper('a') ?>
<?php // This is a copy of apostrophePlugin/modules/a/templates/layout.php ?>
<?php // It also makes a fine site-wide layout, which gives you global slots on non-page templates ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<?php // If this page is an admin page we don't want to present normal navigation relative to it. ?>
	<?php $page = aTools::getCurrentNonAdminPage() ?>
  <?php $root = aPageTable::retrieveBySlug('/') ?>
<head>
	<?php include_http_metas() ?>
	<?php include_metas() ?>
	<?php include_title() ?>
	<?php // 1.3 and up don't do this automatically (no common filter) ?>
	<?php // a_include_stylesheets has a built in caching combiner/minimizer when enabled ?>
  <?php a_include_stylesheets() ?>
	<?php a_include_javascripts() ?>
	<link rel="shortcut icon" href="/favicon.ico" />
	
	<!--[if lt IE 7]>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				apostrophe.IE6({'authenticated':<?php echo ($sf_user->isAuthenticated())? 'true':'false' ?>, 'message':<?php echo json_encode(__('You are using IE6! That is just awful! Apostrophe does not support editing using Internet Explorer 6. Why don\'t you try upgrading? <a href="http://www.getfirefox.com">Firefox</a> <a href="http://www.google.com/chrome">Chrome</a> 	<a href="http://www.apple.com/safari/download/">Safari</a> <a href="http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx">IE8</a>', null, 'apostrophe')) ?>});
			});
		</script>
	<![endif]-->	

	<!--[if lte IE 7]>
		<link rel="stylesheet" type="text/css" href="/apostrophePlugin/css/a-ie.css" />	
	<![endif]-->
		
</head>

<?php // body_class allows you to set a class for the body element from a template ?>
<body class="<?php if (has_slot('body_class')): ?><?php include_slot('body_class') ?><?php endif ?><?php if (($sf_user->isAuthenticated())): ?> logged-in<?php endif ?>">

	<?php include_partial('a/doNotEdit') ?>
  <?php include_partial('a/globalTools') ?>

	<div id="a-wrapper" class="a-wrapper">

    <?php // Note that just about everything can be suppressed or replaced by setting a ?>
    <?php // Symfony slot. Use them - don't write zillions of layouts or do layout stuff ?>
    <?php // in the template (except by setting a slot). To suppress one of these slots ?>
    <?php // completely in one line of code, just do: slot('a-whichever', '') ?>
      
    <?php if (has_slot('a-search')): ?>
      <?php include_slot('a-search') ?>
    <?php else: ?>
      <?php include_partial('a/search') ?>
    <?php endif ?>
    
    <?php if (has_slot('a-header')): ?>
      <?php include_slot('a-header') ?>
    <?php else: ?>
      <div id="a-header" class="a-header">
        <?php if (has_slot('a-logo')): ?>
          <?php include_slot('a-logo') ?>
        <?php else: ?>
          <?php a_slot('logo', 'aButton', array(
						'edit' => (isset($page) && $sf_user->hasCredential('cms_admin')) ? true : false,				
						'defaultImage' => '/apostrophePlugin/images/cmstest-sample-logo.png',
						'link' => '/', 
						'global' => true, 
						'width' => 360, 
						'flexHeight' => true, 
						'resizeType' => 's', 
					)) ?>
        <?php endif ?>
      </div>
    <?php endif ?>

		<?php if (has_slot('a-tabs')): ?>
			<?php include_slot('a-tabs') ?>
		<?php else: ?>
			<?php include_component('aNavigation', 'tabs', array('root' => $root, 'active' => $page, 'name' => 'main', 'draggable' => true, 'dragIcon' => false)) # Top Level Navigation ?>
		<?php endif ?>

		<?php if (has_slot('a-breadcrumb')): ?>
			<?php include_slot('a-breadcrumb') ?>
		<?php elseif ($page): ?>
			<?php include_component('aNavigation', 'breadcrumb', array('root' => $root, 'active' => $page, 'name' => 'component', 'separator' => ' /')) # Top Level Navigation ?>
		<?php endif ?>

    <?php if (has_slot('a-page-header')): ?>
			<?php include_slot('a-page-header') ?>
 		<?php endif ?>

		<?php if (has_slot('a-subnav')): ?>
			<?php include_slot('a-subnav') ?>
		<?php elseif ($page): ?>
			<?php include_component('a', 'subnav', array('page' => $page)) # Subnavigation ?>
		<?php endif ?>

		<div id="a-content" class="a-content clearfix">
			<?php echo $sf_data->getRaw('sf_content') ?>
		</div>
	
	  <?php include_partial('a/footer') ?>
	</div>

	<?php include_partial('a/globalJavascripts') ?>
	
	<?php // Drop in the document.ready stuff at the bottom. ?>
	<?php // If we try to do this sooner, it works for the template but ?>
	<?php // doesn't yet know about any js calls relating to the layout. ?>
  <?php a_include_js_calls() ?>

</body>
</html>
