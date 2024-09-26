<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

class HTML_QuickForm extends \HTML_QuickForm
{
    public function getElements()
    {
        return $this->_elements;
    }
}
