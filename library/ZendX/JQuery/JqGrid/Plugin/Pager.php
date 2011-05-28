<?php

/**
 * @see ZendX_JQuery_JqGrid_Plugin_Abstract
 */
require_once 'ZendX/JQuery/JqGrid/Plugin/Abstract.php';

/**
 * Display a pagination interface on grid for navigating through pages,
 * and providing buttons for common row operations.
 *
 * @package ZendX_JQuery_JqGrid
 * @copyright Copyright (c) 2005-2009 Warrant Group Ltd. (http://www.warrant-group.com)
 * @author Andy Roberts
 */

class ZendX_JQuery_JqGrid_Plugin_Pager extends ZendX_JQuery_JqGrid_Plugin_Abstract
{

    public function preRender()
    {
        $pagerName = $this->_grid->getId() . '_pager';

//        $js = sprintf('%s("#%s").navGrid("#%s",{%s})',
//                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
//                $this->_grid->getId(),
//                $pagerName,
//                'edit:true,add:true,del:true');
//
//        $js .= ';';

        $html = '<div id="' . $pagerName . '"></div>';

//        $this->addOnLoad($js);
        $this->addHtml($html);

        $this->_grid->setOption('pager', $pagerName);
    }

    public function postRender()
    {    // Not implemented
    }

    public function preResponse()
    {    // Not implemented
    }

    public function postResponse()
    {    // Not implemented
    }
}