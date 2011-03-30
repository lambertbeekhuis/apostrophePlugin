<?php
/**
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * @package    apostrophePlugin
 * @subpackage    model
 * @author     P'unk Avenue <apostrophe@punkave.com>
 */
abstract class PluginaVideoSlot extends BaseaVideoSlot
{

  /**
   * DOCUMENT ME
   * @return mixed
   */
  public function isOutlineEditable()
  {
    // We have an edit button and don't use an in-place editor
    return false;
  }

  /**
   * DOCUMENT ME
   * @return mixed
   */
  public function getSearchText()
  {
    $text = "";
    $item = unserialize($this->value);
    // backwards compatibility with older stuff in trinity that
    // didn't have the text fields in the slot
    if (isset($item->title))
    {
      $text .= $item->title . "\n";
      $text .= $item->description . "\n";
      $text .= $item->credit . "\n";
    }
    return $text;
  }
  // We don't need refreshSlot anymore thanks to ON DELETE CASCADE
  // and the new simplified non-API-driven setup. TODO: it would be nice
  // to check in with YouTube here though, if we can do it without
  // getting banned for flooding.
}
