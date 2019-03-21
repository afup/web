<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * HTML QuickForm Alternate Select
 *
 * This file must be included *after* HTML/QuickForm.php
 *
 * HTML_QuickForm plugin that changes a select into a group of radio buttons
 * or checkboxes with an optional textbox for other options not listed. If
 * the select element is listed as multiple, then it will be rendered with
 * checkboxes, otherwise it is rendered with radio buttons.
 *
 * PHP Versions 4 and 5
 *
 * @category    HTML
 * @package     HTML_QuickForm_altselect
 * @author      David Sanders (shang.xiao.sanders@gmail.com)
 * @license     http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version     Release: @package_version@
 * @link        http://pear.php.net/package/HTML_QuickForm_altselect
 * @see         HTML_QuickForm_select
 */

require_once 'HTML/QuickForm/select.php';

/**
* Replace PHP_EOL constant
*
*  category    PHP
*  package     PHP_Compat
* @link        http://php.net/reserved.constants.core
* @author      Aidan Lister <aidan@php.net>
* @since       PHP 5.0.2
*/
if (!defined('PHP_EOL')) {
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        // Windows
        case 'WIN':
            define('PHP_EOL', "\r\n");
            break;

        // Mac
        case 'DAR':
            define('PHP_EOL', "\r");
            break;

        // Unix
        default:
            define('PHP_EOL', "\n");
    }
}

// {{{ HTML_QuickForm_altselect

/**
 * HTML QuickForm Alternate Select
 *
 * HTML_QuickForm plugin that changes a select into a group of radio buttons
 * or checkboxes with an optional textbox for other options not listed. If
 * the select element is listed as multiple, then it will be rendered with
 * checkboxes, otherwise it is rendered with radio buttons.
 *
 * @category    HTML
 * @package     HTML_QuickForm_altselect
 * @author      David Sanders (shang.xiao.sanders@gmail.com)
 * @license     http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version     Release: @package_version@
 * @link        http://pear.php.net/package/HTML_QuickForm_altselect
 * @see         HTML_QuickForm_select
 */
class HTML_QuickForm_altselect extends HTML_QuickForm_select
{
    // {{{ properties

    /**
     * Include the other text field for non-listed entry.
     *
     * @var     bool
     * @access  public
     */
    var $includeOther = false;

    /**
     * Other text type: 'text' or 'textarea'
     *
     * @var     string
     * @access  public
     * @see     setIncludeOther(), includeOther
     */
    var $includeOtherType = 'text';

    /**
     * Label for the Other option.
     *
     * @var     string
     * @access  public
     */
    var $otherLabel = 'Other';

    /**
     * Text label to go in front of other text field (singular mode).
     *
     * @var     string
     * @access  public
     */
    var $otherText = 'If other please specify:';

    /**
     * Text label to go in front of other text field (multiple mode).
     *
     * @var     string
     * @access  public
     */
    var $otherTextMultiple = 'Other:';

    /**
     * Delimiter between subelements.  Use br to go vertical, or nbsp to go 
     * horizontal.
     * 
     * @var     string
     * @access  public
     */
    var $delimiter = '<br />';

    /**
     * Rather than render with a delimiter you may choose to render as a HTML
     * list.
     *
     * @var     string
     * @access  public
     * @see     delimiter
     */
    var $list_type;

    /**
     * Other value storage.
     *
     * @var     string
     * @access  private
     */
    var $_otherValue;
 
    /**
     * Associative array of attributes for each of the individual form elements.
     * NOTE: use "_qf_other" for the other radio button, and "_qf_other_text" 
     * for the text field.
     * 
     * @var      array     Associative array of attributes (see HTML_Common)
     * @access   private
     */
    var $_individualAttributes;

    // }}}
    // {{{ HTML_QuickForm_altselect

    /**
     * Constructor.  Used to distinguish the attributes array which should be 
     * an associative array of options to either a typical HTML attribute string
     * or another associative array
     * 
     * @param  string    $elementName  select name attribute
     * @param  mixed     $elementLabel label(s) for the select
     * @param  mixed     $options      data to be used to populate options
     * @param  mixed     $attributes   an associative array of option value 
     *                                 -> attributes. Each attribute is either 
     *                                 a typical HTML attribute string or an
     *                                 associative array.
     *                                 NOTE: use "_qf_other" for the other radio
     *                                 button, "_qf_other_text" for the 
     *                                 text field and "_qf_all" to apply the
     *                                 attributes to all the option elements.
     * @return void
     */
    function HTML_QuickForm_altselect($elementName = null,
                                      $elementLabel = null,
                                      $options = null,
                                      $attributes = null)
    {
        if (func_get_args()) {
            HTML_QuickForm_select::HTML_QuickForm_select($elementName,
                                                         $elementLabel,
                                                         $options);
            $this->_individualAttributes = $attributes;
        }
    }

    // }}}
    // {{{ toHtml

    /**
     * Render the HTML_QuickForm element.
     *
     * @access  public
     * @return  string The rendered HTML
     */
    function toHtml()
    {
        return $this->getElements(false);
    }

    // }}}
    // {{{ getElements

    /**
     * Arrange the buttons/boxes and other bits either concatenated as a html
     * string or in an array.  When this element is registered as a group, 
     * getElements should act in the same way as HTML_QuickForm_group::getElements().
     * (Therefore the default must be to format as an array)
     * 
     * @param   bool $formatArray set true for an array (default), false for HTML
     * @access  public
     * @see     HTML_QuickForm_group::getElements()
     * @return  mixed Array or HTML string
     */
    function getElements($formatArray = true)
    {
        $html_func_to_use = $this->_flagFrozen ? 'getFrozenHtml' : 'toHtml';
        $is_multiple = $this->getMultiple();

        if ($formatArray) {
            $elements = array();
        } else {
            $preHtml = '';
            $postHtml = '';
            $htmlArray = array();
            $tabs = $this->_getTabs();
            
            if ($this->getComment() != '') {
                $preHtml .= '<!-- ' . $this->getComment() . ' //-->' . PHP_EOL;
            }
        }

        if ($this->includeOther &&
            !defined('HTML_QUICKFORM_ALTSELECT_JS_DISABLE_ELEMENT')) {
            $disable_element_js = <<<EOT
<script type="text/javascript">
//<![CDATA[
function _qf_altselect_disableElement(e,disable)
{
  if (!disable) {
    e.disabled = false;
    e.style.backgroundColor = '#ffffff';
  } else {
    e.disabled = true;
    e.style.backgroundColor = '#cccccc';
  }
}
//]]>
</script>
EOT;

            if ($formatArray && !$this->_flagFrozen) {
                $elements['_qf_altselect_disableElement'] =& 
                HTML_QuickForm::createElement('static',
                                              '_qf_altselect_disableElement',
                                              null,
                                              $disable_element_js);
            } else {
                $javascript = $disable_element_js;
            }
            define('HTML_QUICKFORM_ALTSELECT_JS_DISABLE_ELEMENT',true);
        } else {
            $javascript = '';
        }

        $myName = $this->getName();
        if ($is_multiple) {
            $myName .= '[]';
        }


        foreach ($this->_options as $option) {
            if ($is_multiple) {
                $element =& HTML_QuickForm::createElement('checkbox',$myName);
                //xxx - qf won't take a value as constructor argument
                $element->updateAttributes(array('value' => $option['attr']['value']));
            } else {
                $element =& HTML_QuickForm::createElement('radio',
                                                          $myName,
                                                          null,
                                                          null,
                                                          $option['attr']['value']);
                if ($this->includeOther) {
                    $element->updateAttributes(array(
                        'onclick' => "_qf_altselect_disableElement(this.form.elements[this.name + '_qf_other'],true);"));
                }
            }

            if (isset($this->_individualAttributes['_qf_all'])) {
                $element->updateAttributes($this->_individualAttributes['_qf_all']);
            }

            if (isset($this->_individualAttributes[$option['attr']['value']])) {
                $element->updateAttributes($this->_individualAttributes[$option['attr']['value']]);
            }
                
            if (is_array($this->_values) && in_array((string)$option['attr']['value'], $this->_values)) {
                $element->setChecked(true);
            }

            if ($formatArray) {
                if ($this->_flagFrozen) {
                    $element->freeze();
                }
                $elements[$option['attr']['value']] =& $element;
            } else {
                // write our own label instead of adding text to the radio/cbox
                // as we may want to render without any text when doing from a group
                $htmlArray['_qf_' . $option['attr']['value']] = $tabs .
                                                                $element->$html_func_to_use() .
                                                                '<label for="' . $element->getAttribute('id') . '">' .
                                                                $option['text'] .
                                                                '</label>';
            }
        }

        if ($this->includeOther) {
            if (!$is_multiple) {
                //
                // create the other radio button
                //

                $element =& HTML_QuickForm::createElement('radio',
                                                          $myName,
                                                          null,
                                                          null,
                                                          '_qf_other');

                if (isset($this->_individualAttributes['_qf_other'])) {
                    $element->updateAttributes($this->_individualAttributes['_qf_other']);
                }

                $element->updateAttributes(array(
                    'onclick'=>"_qf_altselect_disableElement(this.form.elements[this.name+'_qf_other'],false);this.form.elements[this.name+'_qf_other'].focus();this.form.elements[this.name+'_qf_other'].select();"));

                if (is_array($this->_values) &&
                    in_array('_qf_other', $this->_values)) {
                    $element->setChecked(true);
                }

                $other_msg = $this->otherText;

                if ($formatArray) {
                    if ($this->_flagFrozen) {
                        $element->freeze();
                    }
                    $elements['_qf_other'] =& $element;
                } else {
                    $htmlArray['_qf_other'] = $tabs .
                                              $element->$html_func_to_use() . 
                                              '<label for="' . $element->getAttribute('id') . '">' .
                                              $this->otherLabel .
                                              '</label>';
                    $preHtml .= $javascript;
                }

                $textName = $myName.'_qf_other';
            } else {
                $other_msg = $this->otherTextMultiple;
                $textName = $myName;
            }

            //
            // create the 'other' text element
            //

            $other_id = 'qf_' . uniqid('');
            $element =& HTML_QuickForm::createElement($this->includeOtherType,
                                                      $textName,
                                                      null,
                                                      array('id'=>$other_id));

            if (isset($this->_individualAttributes['_qf_other_text'])) {
                $element->updateAttributes($this->_individualAttributes['_qf_other_text']);
            }


            // if either the other button is selected, or some text is entered 
            // (meaning _qf_other will also be a value), then set the value of
            // the other value in the text field.
            if (is_array($this->_values) &&
                in_array('_qf_other', $this->_values) &&
                isset($this->_otherValue)) {
                $element->updateAttributes(array('value' => $this->_otherValue));
            }
            // otherwise just disable it
            // only disable with javascript otherwise if the browser doesn't have
            // javascript, then we'll never be able to enter the other text
            else if (!$is_multiple) {
                $disable_js = <<<EOT
<script type="text/javascript">
//<![CDATA[
_qf_altselect_disableElement(document.getElementById('$other_id'),true);
//]]>
</script>
EOT;

                if ($formatArray && !$this->_flagFrozen) {
                    $elements['disable_js'] =& HTML_QuickForm::createElement('static',
                                                                             'disable_js',
                                                                             null,
                                                                             $disable_js);
                } else {
                    $postHtml .= $disable_js;
                }
            }

            if ($formatArray) {
                if ($this->_flagFrozen) {
                    $element->freeze();
                }
                $elements[$textName] =& $element;
            } else {
                $tempHtml = $tabs . '<label ';
                if ($this->includeOtherType === 'textarea') {
                    $tempHtml .= 'style="vertical-align: top;" ';
                }
                $tempHtml .= 'for="' . $element->getAttribute('id') . '">' .
                              $other_msg .
                              '</label> ' .
                              $element->$html_func_to_use();
                $htmlArray['_qf_other_text'] = $tempHtml;
            }
        }

        if ($formatArray) {
            return $elements;
        } else {
            if ($this->list_type === 'ul' || $this->list_type === 'ol') {
                $tempHtml = $preHtml . PHP_EOL .
                            '<' . $this->list_type . '>' . PHP_EOL;
                foreach ($htmlArray as $key => $piece) {
                    $tempHtml .= '<li ';
                    $id = $this->getAttribute('id');
                    if ($id !== null) {
                        $tempHtml .= 'id="' . $key . '_' . $id . '" ';
                    }
                    if ($key === '_qf_other' || $key === '_qf_other_text') {
                        $tempHtml .= 'class="' . $key . '">';
                    } else {
                        $tempHtml .= 'class="_qf_option">';
                    }
                    $tempHtml .= $piece . '</li>' . PHP_EOL;
                }
                $tempHtml .= '</' . $this->list_type . '>' . PHP_EOL .
                             $postHtml;
                return $tempHtml;
            } else {
                return $preHtml . PHP_EOL .
                       implode($this->delimiter . PHP_EOL, $htmlArray) . PHP_EOL .
                       $postHtml;
            }
        }
    }

    // }}}
    // {{{ exportValue

    /**
     * Exports the value.
     *
     * If the other value is set, this will be exported if in singular mode
     * and the other radio button is selected.  Otherwise if in multiple mode
     * the other value is added to the array of values.
     *
     * @param   array   $submitValues submitValues values submitted
     * @param   bool    $assoc        propagate on to 
     *                                HTML_QuickForm_select::exportValue()
     * @access  public
     * @return  mixed   Single value or array of values
     */
    function exportValue(&$submitValues, $assoc = false)
    {
        if (!$this->includeOther) {
            return parent::exportValue($submitValues, $assoc);
        }

        if ($this->getMultiple()) {
            // kinda defeats the purpose of using exportValue to return only 
            // allowed options

            $value = $this->_findValue($submitValues);

            // if nothing was posted, then grab the defaults
            if (is_null($value)) {
                $value = $this->getValue();
            }

            // _findValue may return a scalar if that's what was posted
            if (is_array($value)) {
                // remove the empty string from the other option
                foreach ($value as $key => $item) {
                    if ($item === '') {
                        unset($value[$key]);
                    }
                }
            } else {
                // if a scalar, force an array to be exported
                $value = array($value);
            }

            return $this->_prepareValue($value, $assoc);
        } else {
            // NB: If an array was submitted with an other value, then it will 
            // not be exported, as we're expecting '_qf_other' to be submitted
            // in singular mode.

            $myName = $this->getName();

            // XXX
            $this->addOption(null,'_qf_other');
            $value = parent::exportValue($submitValues, $assoc);
            // XXX
            array_pop($this->_options);

            if (is_array($value) && $value[$myName] == '_qf_other' ||
                $value == '_qf_other') {
                if (isset($submitValues[$myName.'_qf_other'])) {
                    $value = $submitValues[$myName.'_qf_other'];
                } else {
                    $value = $this->_otherValue;
                }
            }

            return $this->_prepareValue($value, $assoc);
        }
    }

    // }}}
    // {{{ setSelected

    /**
     * Set the selected options.  If a non-listed option is specified, it
     * will go into the other text field.  Note at this point, the other and
     * multiple attributes may not have been set.
     *
     * @param   mixed  $values array or comma delimited string of selected values
     * @access  public
     * @return  void
     */
    function setSelected($values)
    {
        parent::setSelected($values);

        //
        // we need to do some extra work here in case the other 
        // option will be/has been set... 
        //
        $other_values = array();

        foreach ($this->_values as $value) {
            // if we are in singular mode and the other button is selected from
            // the submit values then we'll need to record the real other value
            // in _otherValue
            if ($value == '_qf_other') {
                $myName = $this->getName();
                
                $this->_otherValue = @$_REQUEST[$myName.'_qf_other'];
                
                // we only need to grasp the first other value, because we
                // are in singular mode, so we'll return...
                return;
            }
            // otherwise the real other value might be listed in _values
            // from setSelected('junk') or if we're in multiple mode and it was
            // submitted...
            // if we find something not part of the options then we record it in
            // _otherValue and set the _qf_other as part of the values
            else {
                $found = false;
                foreach ($this->_options as $option) {
                    if ((string) $value == (string) $option['attr']['value']) {
                        $found = true;
                    }
                }

                if (!$found) {
                    $this->_values[] = '_qf_other';
                    $other_values[] = $value;
                }
            }
        }

        if (!empty($other_values)) {
            $this->_otherValue = implode(',',$other_values);
        }
    }

    // }}}
    // {{{ setIncludeOther

    /**
     * Set the select to include the 'other' textfield/textarea.
     * 
     * @param  bool/string $include If bool: whether to include the other text field,
     *                              else if string: either 'text' or 'textarea'
     * @access public
     * @return void
     */
    function setIncludeOther($include = true)
    {
        if ($include === 'text' || $include === 'textarea') {
            $this->includeOther = true;
            $this->includeOtherType = $include;
        } else {
            $this->includeOther = (bool) $include;
            $this->includeOtherType = 'text';
        }
    }

    // }}}
    // {{{ setDelimiter

    /**
     * Set the delimiter.
     * 
     * @param  string  $delimiter delimiter to use between the subelements
     * @access public
     * @return void
     */
    function setDelimiter($delimiter)
    {
        if (!is_string($delimiter)) {
            $this->delimiter = '<br />';
        } else {
            $this->delimiter = $delimiter;
        }
    }

    // }}}
    // {{{ setList

    /**
     * Set the options to render as an ordered/unordered list
     *
     * @param string $list_type The list type
     * @access public
     * @return void
     */

    function setListType($list_type)
    {
        $this->list_type = $list_type;
    }

    // }}}
    // {{{ setGroup

    /**
     * Tell this element to act like a group when being accepted.
     * 
     * @param  bool   $is_group whether to act like a group or not
     * @see    HTML_QuickForm_element::_type
     * @access public
     * @return void
     */
    function setGroup($is_group = true)
    {
        $this->_type = $is_group ? 'group' : 'select';
    }

    // }}}
    // {{{ accept

   /**
    * Accepts a renderer.  Overload select in case we'd like to see the 
    * checkboxes/radio buttons in a group when renderering with another
    * renderer.
    *
    * This function was copied from HTML_QuickForm_group::accept() and
    * modified for use with this class.
    *
    * @param  HTML_QuickForm_Renderer $renderer the QF renderer
    * @param  bool                    $require  whether a group is required
    * @param  string                  $error    an error message associated with
                                               a group
    * @see    HTML_QuickForm_group::accept()
    * @access public
    * @return void
    */
    function accept(&$renderer, $required = false, $error = null)
    {
        // if not asked to act like a group, then pass off to regular accept method
        if ($this->_type != 'group') {
            return parent::accept($renderer, $required, $error);
        }
        $this->_separator = null;
        $this->_appendName = null;
        $this->_required = array();


// Beginning of code from HTML_QuickForm_group::accept() 
// ---8<---

        //$this->_createElementsIfNotExist();
        $renderer->startGroup($this, $required, $error);
        $name = $this->getName();

// --->8---

        // use our method to get the elements instead
        $this->_elements = $this->getElements();

// ---8<---

        foreach (array_keys($this->_elements) as $key) {
            $element =& $this->_elements[$key];
            
            if ($this->_appendName) {
                $elementName = $element->getName();
                if (isset($elementName)) {
                    $element->setName($name .
                                      '[' .
                                      (strlen($elementName)? $elementName: $key) .
                                      ']');
                } else {
                    $element->setName($name);
                }
            }

            $required = !$element->isFrozen() &&
                        in_array($element->getName(), $this->_required);

            $element->accept($renderer, $required);

            // restore the element's name
            if ($this->_appendName) {
                $element->setName($elementName);
            }
        }
        $renderer->finishGroup($this);

// --->8---
    }

    // }}}
    // {{{ getElementName

    /**
     * Returns the name of this element. Used by HTML_QuickForm::getSubmitValue()
     * when this element is registered as a group.
     * 
     * @see     HTML_QuickForm::getSubmitValue()
     * @access  public
     * @return  string
     */
    function getElementName()
    {
        return $this->getName();
    }

    // }}}
}

// }}}

if (class_exists('HTML_QuickForm')) {
    HTML_QuickForm::registerElementType('altselect',
                                        'HTML/QuickForm/altselect.php',
                                        'HTML_QuickForm_altselect');
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>
