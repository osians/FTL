<?php

class FtlAbstract
{
    /**
     * prefixo para a linguagem de template
     *
     * @var string
     */
    protected $_tagPrefix;
 
    /**
     * Caminho classes com metodos chamados nos templates
     *
     * @var string
     */
    protected $_classPath = null;

    /**
     * Seta Tag que identifica as chamadas FTL
     *
     * @param String $tagPrefix
     *
     * @return FtlParser
     */
    public function setTagPrefix($tagPrefix = 'sys')
    {
        $this->_tagPrefix = $tagPrefix;
        return $this;
    }
    
    /**
     * Retorna prefixo de identificacao de Tags FTL
     *
     * @return string
     */
    public function getTagPrefix()
    {
        return $this->_tagPrefix;
    }
    
    /**
     * Seta caminho para as Classes chamadas no Template
     *
     * @param String $classPath
     *
     * @return FtlParser
     */
    public function setClassPath($classPath = null)
    {
        $this->_classPath = $classPath;
        return $this;
    }
    
    /**
     * Retornar caminho das classes chamadas no Template
     *
     * @return String
     */
    public function getClassPath()
    {
        return $this->_classPath;
    }
    
    /**
     * Tenta carregar uma classe solicitada do template 
     * 
     * @param  string $classname - nome da classe
     *
     * @return bool - se false erro, true indica classe carregada
     * 
     * @throws Exception
     */
    protected function _loadClass($classname)
    {
        if (class_exists($classname)) {
            return true;
        }
        
        if (null === $this->getClassPath()) {
            throw new Exception("Arquivo com a classe '{$classname}' não encontrado.");
        }

        $filename = $this->getClassPath() . ucfirst($classname) . '.php';

        if (file_exists($filename)) {
            require_once ($filename);
        }
        
        if (!class_exists($classname)) {
            throw new Exception("Classe '{$classname}' não encontrada no sistema.");
        }
            
        return true;
    }
}
