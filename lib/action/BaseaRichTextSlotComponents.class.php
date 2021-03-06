<?php
/**
 * @package    apostrophePlugin
 * @subpackage    action
 * @author     P'unk Avenue <apostrophe@punkave.com>
 */
class BaseaRichTextSlotComponents extends aSlotComponents
{

  /**
   * DOCUMENT ME
   */
  public function executeEditView()
  {
    $this->setup();
    $this->initialHtml();
    // Careful, don't clobber a form object provided to us with validation errors
    // from an earlier pass
    if (!isset($this->form))
    {
      $this->form = new aRichTextForm($this->id, $this->options);
      $this->form->setDefault('value', $this->value);
    }
  }

  /**
   * DOCUMENT ME
   */
  public function executeNormalView()
  {
    $this->setup();
    $this->initialHtml();
    // We don't recommend doing this at the FCK level,
    // let it happen here instead so what is stored in the
    // db can be clean markup
    $this->value = aHtml::obfuscateMailto($this->value);
  }

  protected function initialHtml()
  {
    if (isset($this->options['initialHtml']))
    {
      if (!strlen($this->value))
      {
        $this->value = $this->options['initialHtml'];
      }
    }
  }
}
