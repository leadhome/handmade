<?php
class pEngine_Form_Element_Elementset extends Zend_Form_Element_Xhtml
{
	public $elements = array();

	public function addElement($element)
	{
		if($element instanceof  Zend_Form_Element) {
			$this->elements[] = $element;
		}
	}

	public function addElements($elements)
	{
		if($elements && is_array($elements)) {
			foreach($elements as $element) {
				$this->addElement($element);
			}
		}
	}

    public function render(Zend_View_Interface $view = null)
    {
        $content = '';
		if(count($this->elements) > 0) {
			foreach($this->elements as $el) {
				$content .= $el->render();
			}
		}

		if ($this->_isPartialRendering) {
			return '';
		}

		if (null !== $view) {
			$this->setView($view);
		}

		foreach ($this->getDecorators() as $decorator) {
			$decorator->setElement($this);
			$content = $decorator->render($content);
		}
		return $content;
	}

    public function isValid($value, $context = null)
	{
		if(count($this->elements) > 0) {
			foreach($this->elements as $el) {
				if(!$el->isValid()) {
					return false;
				}
			}
		}
	}

    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $getId = create_function('$decorator',
                                     'return $decorator->getElement()->getId()
                                             . "-element";');
            $this->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                 ->addDecorator('HtmlTag', array('tag' => 'dd',
                                                 'id'  => array('callback' => $getId)))
                 ->addDecorator('HtmlTag', array('tag' => 'div',
                                                 'id'  => array('callback' => $getId)))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
        return $this;
    }
}
