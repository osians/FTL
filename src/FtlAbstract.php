<?php

class FtlAbstract
{
    /**
     * {} Symbol
     *
     * @const int
     */
    const CURLY_BRACKETS = 0;

    /**
     * [] Symbol
     *
     * @const int
     */    
    const SQUARE_BRACKETS = 1;
    
    /**
     * <> Symbol
     *
     * @const int
     */
    const GREATER_THAN_LESS_THAN = 2;
    
    /**
     * Caracteres de Abertura e Fechamento de Tags... exemplo <, {, [
     * Valores aceitos 2 (default) = </> , 0 = {/}, 1 = [/]
     * 
     * @var integer
     */
    protected $_openCloseSymbol = 2;
    
    /**
     * prefixo para a linguagem de template
     *
     * @var string
     */
    protected $_tagPrefix = 'sys';
 
    /**
     * Caminho classes com metodos chamados nos templates
     *
     * @var string
     */
    protected $_classPath = null;

    /**
     * Seta simbolo de abertura e fechamento de tags
     *
     * @param Char $ocs
     *
     * @return $this
     */
    public function setOpenCloseSymbol($ocs = FtlAbstract::GREATER_THAN_LESS_THAN)
    {
        $this->_openCloseSymbol = $ocs;
        return $this;
    }
    
    /**
     * Retorna simbolo de abertura de tags
     *
     * @return type
     */
    public function getOpenSymbol()
    {
        if ($this->_openCloseSymbol == FtlAbstract::CURLY_BRACKETS) {
            return "{";
        }
        
        if ($this->_openCloseSymbol == FtlAbstract::SQUARE_BRACKETS) {
            return "\[";
        }
            
        if ($this->_openCloseSymbol == FtlAbstract::GREATER_THAN_LESS_THAN) {
            return "<";
        }
    }

    /**
     * Retorna simbolo de fechamento de tags
     *
     * @return type
     */
    public function getCloseSymbol()
    {
        if ($this->_openCloseSymbol == FtlAbstract::CURLY_BRACKETS) {
            return "}";
        }
        
        if ($this->_openCloseSymbol == FtlAbstract::SQUARE_BRACKETS) {
            return "\}";
        }
            
        if ($this->_openCloseSymbol == FtlAbstract::GREATER_THAN_LESS_THAN) {
            return ">";
        }
    }
    
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
