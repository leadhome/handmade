<?php

/**
 * @see Zend_Paginator_Adapter_DbSelect
 */
require_once 'ZFDoctrine/Paginator/Adapter/DoctrineQuery.php';

/**
 * @see ZendX_JQuery_JqGrid_Adapter_Interface
 */
require_once 'ZendX/JQuery/JqGrid/Adapter/Interface.php';

/**
 * JqGrid Doctrine Adapter
 * 
 * @package ZendX_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author andy.roberts
 */

class ZendX_JQuery_JqGrid_Adapter_Doctrine extends ZFDoctrine_Paginator_Adapter_DoctrineQuery implements ZendX_JQuery_JqGrid_Adapter_Interface
{
    protected $_operator = array(
        'EQUAL' => '= ?' , 
        'NOT_EQUAL' => '!= ?' , 
        'LESS_THAN' => '< ?' , 
        'LESS_THAN_OR_EQUAL' => '<= ?' , 
        'GREATER_THAN' => '> ?' , 
        'GREATER_THAN_OR_EQUAL' => '>= ?' , 
        'BEGIN_WITH' => 'LIKE ?' , 
        'NOT_BEGIN_WITH' => 'NOT LIKE ?' , 
        'END_WITH' => 'LIKE ?' , 
        'NOT_END_WITH' => 'NOT LIKE ?' , 
        'CONTAIN' => 'LIKE ?' , 
        'NOT_CONTAIN' => 'NOT LIKE ?'
    );

    /**
     * Sort the result set by a specified column.
     *
     * @param string $field Column name
     * @param string $direction Ascending (ASC) or Descending (DESC)
     * @return void
     */
    public function sort($field, $direction)
    {
        if (isset($field)) {
            $this->_query->orderBy($field . ' ' . $direction);
        }
    }

    public function  getItems($offset, $itemsPerPage)
    {
        if ($itemsPerPage !== null) {
            $this->_query->limit($itemsPerPage);
        }
        
        if ($offset !== null) {
            $this->_query->offset($offset);
        }
        
        $items = $this->_query->execute();
        
//        if (count($this->_alias) > 0)
//            foreach ($items as $key=>$item)
//                foreach ($this->_alias as $akey=>$aitem)
//                    $items[$key]->$akey = $item->$aitem['model']->$aitem['field'];

        return $items;
    }

    /**
     * Filter the result set based on criteria.
     *
     * @param string $field Column name
     * @param string $value Value to filter result set
     * @param string $operation Search operator
     */
    public function filter($filter, $options = array())
    {
        
//        if (! array_key_exists($expression, $this->_operator)) {
//            return;
//        }
        
        if (isset($options['multiple'])) {
            return $this->_multiFilter($filter, $options);
        }
        
        return $this->_query->addWhere($field . ' ' . $this->_operator[$expression], $this->_setWildCardInValue($expression, $value));
    }

    /**
     * Multiple filtering
     * 
     * @return
     */
    private function _multiFilter($rules, $options = array())
    {
        $boolean = strtoupper($options['boolean']);
        foreach ($rules as $rule) {
            if ($boolean == 'OR') {
                $this->_query->orWhere($rule['field'] . ' ' . $this->_operator[$rule['expression']], $this->_setWildCardInValue($rule['expression'], $rule['value']));
            } else {
                $this->_query->addWhere($rule['field'] . ' ' . $this->_operator[$rule['expression']], $this->_setWildCardInValue($rule['expression'], $rule['value']));
            }
        }
    }

    /**
     * Place wildcard filtering in value
     *
     * @return string
     */
    private function _setWildCardInValue($expression, $value)
    {
        switch (strtoupper($expression)) {
            case 'BEGIN_WITH':
            case 'NOT_BEGIN_WITH':
                $value = $value . '%';
                break;
            
            case 'END_WITH':
            case 'NOT_END_WITH':
                $value = '%' . $value;
                break;
            
            case 'CONTAIN':
            case 'NOT_CONTAIN':
                $value = '%' . $value . '%';
                break;
        }
        
        return $value;
    }
}