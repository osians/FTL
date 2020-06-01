<?php

require_once __DIR__ . '/SingleTonInterface.php';
require_once __DIR__ . '/TagInterface.php';

abstract class TagManager implements TagInterface, SingleTonInterface
{
    /**
     * Array de dados Parseados pelo FtlParser
     *
     * @var Array
     */
    protected $_bind = array();

    /**
     * SingleTon
     *
     * @return TagManager
     */
    final public static function getInstance()
    {
        static $instances = array();
        $calledClass = get_called_class();

        if (!isset($instances[$calledClass])) {
            $instances[$calledClass] = new $calledClass();
        }

        return $instances[$calledClass];
    }
    
    /**
     * @see TagInterface::setBind()
     *
     * @return $this
     */
    public function setBind($bind)
    {
        $this->_bind = $bind;
        return $this;
    }
    
    /**
     * @see TagInterface::geBind()
     *
     * @param bool $asObject 
     *
     * @return Array | StdClass
     */
    public function getBind($asObject = false)
    {
        return ($asObject == false) ? $this->_bind : (object) $this->_bind;
    }
    
    /**
     * Avoid Clones
     */
    final private function __clone()
    {
    }
}
