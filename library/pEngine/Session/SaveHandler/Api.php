<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author yura
 */
final class pEngine_Session_SaveHandler_Api implements Zend_Session_SaveHandler_Interface{
    
    /**
     * Live time
     * @var Int 
     */
    private $livetime;
    /**
     * Open Session - retrieve resources
     *
     * @param string $save_path
     * @param string $name
     */    
    public function open($save_path, $name){}

    /**
     * Close Session - free resources
     *
     */
    public function close(){}

    /**
     * Read session data
     *
     * @param string $id
     */
    public function read($id){
        $session = pEngine_Api::factory()
            ->setMethod('session.get')
            ->setParam('id', $id)
            ->query();
        if(isset($session)){
            if($session->time+$this->getLiveTime()<time()){
                $this->destroy($id);
            }else{
                return $session->data;
            }
        }
        return '';
    }

    /**
     * Write Session - commit data to resource
     *
     * @param string $id
     * @param mixed $data
     */
    public function write($id, $data){
        pEngine_Api::factory()
            ->setMethod('session.set')
            ->setParam('id', $id)
            ->setParam('time', time()+$this->getLiveTime())
            ->setParam('data', $data)
            ->query();
    }

    /**
     * Destroy Session - remove data from resource for
     * given session id
     *
     * @param string $id
     */
    public function destroy($id){
        pEngine_Api::factory()
            ->setMethod('session.delete')
            ->setParam('id', $id)
            ->query();
    }

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     *
     * @param int $maxlifetime
     */
    public function gc($maxlifetime){}

    public function getLiveTime(){
        return (int) ini_get('session.gc_maxlifetime');
    }
}
