<div id="a-search">
  <form id="a-search-global" action="<?php echo url_for('a/search') ?>" method="get" class="a-search-form">
    <input type="text" name="q" value="<?php echo htmlspecialchars($sf_params->get('q')) ?>" class="a-search-field" id="a-search-cms-field" /> 
    <input type="image" src="/apostrophePlugin/images/a-special-blank.gif" class="submit" value="Search Pages" />
  </form>
</div>

<script type="text/javascript" charset="utf-8">
	aInputSelfLabel('#a-search-cms-field', 'Search');
</script>