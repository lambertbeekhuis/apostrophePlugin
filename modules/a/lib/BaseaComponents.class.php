<?php

/**
 * a components.
 *
 * @package    simplesite
 * @subpackage a
 * @author     P'unk Ave
 */
class BaseaComponents extends aBaseComponents
{
  public function executeBreadcrumb(sfRequest $request)
  {
    // Use our caching proxy implementation of getAncestors
    $this->page = aTools::getCurrentPage();
    $this->ancestorsInfo = $this->page->getAncestorsInfo();
  }
  public function executeSubnav(sfRequest $request)
  {

  }
  
  public function executeTabs(sfRequest $request)
  {
    $this->page = aTools::getCurrentPage();
    if (!$this->page)
    {
      // Tabs on non-CMS pages are relative to the home page
      $this->page = aPageTable::retrieveBySlug('/');
    }
    $ancestorsInfo = $this->page->getAncestorsInfo();
    if (!count($ancestorsInfo))
    {
      $ancestorsInfo = array($this->page);	
    }
    $homeInfo = $ancestorsInfo[0];
    
    // Show archived tabs only to those who are potential editors.
    $this->tabs = $this->page->getTabsInfo(!(aTools::isPotentialEditor() &&  $this->getUser()->getAttribute('show-archived', true, 'a')), $homeInfo);
    if (sfConfig::get('app_a_home_as_tab', true))
    {
      array_unshift($this->tabs, $homeInfo);
    }
    $this->draggable = $this->page->userHasPrivilege('edit');
  }
  
  public function executeSlot()
  {
    $this->setup();
    $controller = $this->getController();
    if ($controller->componentExists($this->type, "normalView"))
    {
      $this->normalModule = $this->type;
    }
    else
    {
      $this->normalModule = "a";
    }
    if ($controller->componentExists($this->type, "editView"))
    {
      $this->editModule = $this->type;
    }
    else
    {
      $this->editModule = "a";
    }
  }

  public function executeEditView()
  {
    $this->setup();
  }

  public function executeNormalView()
  {
    $this->setup();
  }

  public function executeArea()
  {
    $this->page = aTools::getCurrentPage();
    $this->slots = $this->page->getArea($this->name, $this->addSlot, sfConfig::get('app_a_new_slots_top', true));
    $this->editable = $this->page->userHasPrivilege('edit');
    $user = $this->getUser();
    // Clean this up for nicer templates
    $this->refresh = (isset($this->refresh) && $this->refresh);
    $this->preview = (isset($this->preview) && $this->preview);
    $id = $this->page->id;
    $name = $this->name;
    if ($this->refresh)
    {
      if ($user->hasAttribute("area-options-$id-$name", "a"))
      {
        $this->options = $user->getAttribute("area-options-$id-$name", array(), "a");
      }
      else
      {
        // BZZT: probably a hack attempt
        throw new sfException("executeArea without options");
      }
    }
    else
    {
      $user->setAttribute("area-options-$id-$name", $this->options, "a");
    }
    $this->infinite = $this->getOption('infinite');
    if (!$this->infinite)
    {
      // Watch out for existing slots of the wrong type, which might contain data
      // that is incompatible with the singleton slot's type. That can happen if you
      // switch slot types in the template, or change from an area to a singleton slot.
      // Also ignore anything after the first slot (again, that can happen if you
      // switch from an area to a singleton slot)
      if (count($this->slots) > 1)
      {
        // Get the first one without being tripped up by the fact that it's a hash
        foreach ($this->slots as $key => $slot)
        {
          break;
        }
        $this->slots = array($key => $slot);
      }
      if (count($this->slots))
      {
        // Get the first one without being tripped up by the fact that it's a hash
        foreach ($this->slots as $key => $slot)
        {
          break;
        }
        if ($slot->type !== $this->options['type'])
        {
          $this->slots = array();
        }
      }
      if (!count($this->slots))
      {
        if (!isset($this->options['type']))
        {
          throw new sfException('Must specify type when embedding a singleton slot');
        }
        $this->slots[1] = $this->page->createSlot($this->options['type']);
        $this->slots[1]->setEditDefault(false);
      }
    }
  }
  public function executeNavigation(sfRequest $request)
  {
    // What page are we starting from?
    // Navigation on non-CMS pages is relative to the home page
    if (!$this->page = aTools::getCurrentPage())
    {
      $this->page = aPageTable::retrieveBySlug('/');
    }
    if(!$this->activePage = aPageTable::retrieveBySlug($this->activeSlug))
    {
      $this->activePage = $this->page;
    }
    if(!$this->rootPage = aPageTable::retrieveBySlug($this->rootSlug))
    {
      $this->rootPage = $this->activePage;
    }

    // We build different page trees depending on the navigation type that was requested
    if (!$this->type)
    {
      $this->type = 'tree';
    }
    
    $class = 'aNavigation'.ucfirst($this->type);
    
    if (!class_exists($class))
    {
      throw new sfException(sprintf('Navigation type "%s" does not exist.', $class));
    }

    $this->navigation = new $class($this->rootPage, $this->activePage, $this->options);
        
    $this->draggable = $this->page->userHasPrivilege('edit');
    
    // Users can pass class names to the navigation <ul>
    $this->classes = '';
    if (isset($this->options['classes']))
    {
      $this->classes .= $this->options['classes'];
    }
    $this->nest = 0;
    // The type of the navigation also is used for styling
    $this->classes .= ' ' . $this->type;
    $this->navigation = $this->navigation->getItems();
    if(count($this->navigation) == 0)
    {
      return sfView::NONE;
    }
    
  }
}
