<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of File
 *
 * @author yura
 */
class pEngine_Auth_Storage_SessionFile  implements Zend_Auth_Storage_Interface {
    const TMP_DIR = 'pEngine_Auth';
    const NAMESPACE_DEFAULT = 'pEngine_Auth';
    const MEMBER_DEFAULT = 'file';

    protected $_dir;

    protected $_namespace;

    protected $_member;

    protected $_content=null;

    protected $_isFileRead=false;

    protected $_isFileWrite=false;

    protected $_pathFileWrite=null;
    /**
     * Object to proxy $_SESSION storage
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    
    public function __construct($tmpDir = self::TMP_DIR, $namespace=self::NAMESPACE_DEFAULT,$member=self::MEMBER_DEFAULT){
        $this->_dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.$tmpDir;
        $this->_namespace = $namespace;
        $this->_member = $member;
        $this->_session = new Zend_Session_Namespace($this->_namespace);

    }

    protected function getContent(){
        if(!$this->_isFileRead){
            $this->_isFileRead=true;
            if(isset($this->_session->{$this->_member})){
                if(is_file($this->_session->{$this->_member})){
                    $this->_content = unserialize(file_get_contents($this->_session->{$this->_member}));
                }
            }
        }
        return $this->_content;
    }

    protected function preFileWrite(){
        if(!$this->_isFileWrite){
            $this->_isFileWrite = true;
            if(!isset($this->_session->{$this->_member})){
                if(!is_dir($this->_dir)){
                    mkdir($this->_dir,0777,true);
                }
                $this->_session->{$this->_member} = tempnam($this->_dir, 'auth');
            }
            $this->_pathFileWrite = $this->_session->{$this->_member};
        }
    }

    /**
     * Returns true if and only if storage is empty
     *
     * @throws Zend_Auth_Storage_Exception If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty(){
        return !$this->getContent();
    }

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws Zend_Auth_Storage_Exception If reading contents from storage is impossible
     * @return mixed
     */
    public function read(){
        return $this->getContent();
    }

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws Zend_Auth_Storage_Exception If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents){
        $this->_content = $contents;
        $this->preFileWrite();

    }

    /**
     * Clears contents from storage
     *
     * @throws Zend_Auth_Storage_Exception If clearing contents from storage is impossible
     * @return void
     */
    public function clear(){
        unlink($this->_session->{$this->_member});
        unset($this->_session->{$this->_member});
        $this->_content=null;
        $this->_isFileRead=false;
        $this->_isFileWrite=false;
        $this->_pathFileWrite=null;
    }

    public function  __destruct() {
        if($this->_isFileWrite){
            file_put_contents($this->_pathFileWrite, serialize($this->_content));
        };
    }
}

