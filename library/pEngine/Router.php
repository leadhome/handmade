<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Router
 *
 * @author yura
 */
class pEngine_Router
{
    static protected $routeName;

    /**
     * Set Route in Router
     * @param Router_Model_Route $recorder
     * @param Zend_Controller_Router_Route $route
     */
    static public function setRoute(Router_Model_Route $recorder, Zend_Controller_Router_Route $route)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();

        self::$routeName[$recorder->id]= self::_getName($recorder);
        $router->addRoute(& self::$routeName[$recorder->id], $route);
    }

    /**
     * Return name route
     * @param int $id
     * @return string | null
     */
    static public function getName($id=null)
    {
        return isset(self::$routeName[$id]) ? self::$routeName[$id] : null;
    }

    /**
     * Return id route
     * @param string $name
     * @return int | false
     */
    static public function getId($name)
    {
        return array_search($name, self::$routeName);
    }


    static public function _getName($row)
    {
        if($row instanceof Doctrine_Record)
            $data = self::_getNameWithRecorder($row);
        else
            $data = self::_getNameWithArray($row);
        sort($data);
        $name = implode('_',$data);
        return $name;
    }

    static protected function _getNameWithRecorder(Router_Model_Route $row)
    {
        $data[]=$row->module;
        $data[]=$row->controller;
        $data[]=$row->action;

        foreach($row->Req AS $req)
        {
            $data[]=$req->name;
        }

        return $data;
    }

    static protected function _getNameWithArray(array $row)
    {

        $row[$row['module']]='';
        unset($row['module']);
        $row[$row['controller']]='';
        unset($row['controller']);
        $row[$row['action']]='';
        unset($row['action']);

        return array_keys($row);
    }

}

