<?php
/**
 * 
 * PluginaPageToCategoryTable
 * This class has been auto-generated by the Doctrine ORM Framework
 * @package    apostrophePlugin
 * @subpackage    model
 * @author     P'unk Avenue <apostrophe@punkave.com>
 */
class PluginaPageToCategoryTable extends Doctrine_Table
{

  /**
   * 
   * Returns an instance of this class.
   * @return object PluginaPageToCategoryTable
   */
  public static function getInstance()
  {
    return Doctrine_Core::getTable('PluginaPageToCategory');
  }

  /**
   * DOCUMENT ME
   * @param mixed $old_id
   * @param mixed $new_id
   */
  public function mergeCategory($old_id, $new_id)
  {
    Doctrine_Core::getTable('aCategory')->mergeCategory($old_id, $new_id, 'aPageToCategory', 'category_id', true, 'page_id');
  }

}