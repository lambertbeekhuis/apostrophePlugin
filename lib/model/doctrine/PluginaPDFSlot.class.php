<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginaPDFSlot extends BaseaPDFSlot
{
  public function refreshSlot()
  {
    return aImageSlot::refreshImageSlot($this);
  }
}