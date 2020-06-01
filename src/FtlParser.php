<?php

/**
 * Classe para interpretacao de templates estilo XML.
 * Versao condensada inspirada na classe FTL desenvolvida por Matin Wernstahl
 *
 * @note - Algumas funcoes e nomes baseadas na classe FTL de Martin Wernstahl <m4rw3r@gmail.com>
 *
 * @package FTL
 * @subpackage Parser
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @copyright - Copyright (c) 2020, Wanderlei Santana <sans.pds@gmail.com>
 *
 * @version - 20200529094200
 */

require_once 'FtlAbstract.php';

class FtlParser extends FtlAbstract
{
    # @var int - indica a dupla de caracter que inicia e termina uma tag... exemplo <, {, [
    # @todo - a ser implementado
    protected $char_entite = 0;  # valores aceitos 0 = </> , 1 = {/}, 2 = [/]

    /**
     * Implementacao single ton
     *
     * @static FtlParser
     */
    private static $_instance;

    /**
     * pilha
     *
     * @var type
     */
    protected $_stack;

    /**
     * Guarda a Arvore gerada a partir do Template
     *
     * @var Array
     */
    protected $_tree;

    # @var string - objeto que realiza uma chamada ao metodo compile(...)
    protected static $tag_caller = null;

    /**
     * Construct
     */
    protected function __construct()
    {
    }
    
    /**
     * evita clone
     */
    private function __clone()
    {
    }

    /**
     * Retorna instancia unica do Objeto
     *
     * @return FtlParser
     **/
    public static function getInstance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }
    
    /**
     * Retorna arvore gerada a partir do template
     *
     * @return Array
     */
    protected function _getTree()
    {
        return $this->_tree;
    }
    
    /**
     * Seta um ou mais parametros de configuracao desta classe
     *
     * @param array $options
     *
     * @return FtlParser
     */
    public function setOptions($options = array())
    {
        $this->setTagPrefix($options['tag_prefix'])
             ->setClassPath($options['class_path']);

        return $this;
    }

    /**
     * Funcao que transforma Template em uma Arvore
     *
     * @param  string - template que sera analisado
     *
     * @return string
     */
    protected function _parse($template)
    {
        return $this->_generateTree($template);
    }

    /**
     * Gera uma estrutura em formato de Pilha a partir do template,
     * identificando os blocos de sintaxe que devem ser convertidos
     *
     * @param string $template
     *
     * @return array
     */
    protected function _generateTree($template)
    {
        unset($this->_tree);
        unset($this->_stack);

        $this->_tree = array();
        $this->_stack = array(
            array('content' => &$this->_tree)
        );

        $this->_process($template);
        return $this->_tree;
    }

    /**
     * Analisa todos o template insere a estrutura dentro de $this->_tree
     *
     * @param string $template - Templatepara realizar a analise
     *
     * @return void
     */
    protected function _process($template)
    {
        $tmp = null;
        $parent = null;
        $data = null;
        $matches = null;
        
        // pegue tudo que iniciar com <tag: e que finalizar com />
        $pattern = '%([\w\W]*?)<(/)?' . $this->getTagPrefix() . ':([\w-:]+)([^>]*?)(/)?>([\w\W]*)%';
        
        while (preg_match($pattern, $template, $matches))
        {
            // redefinindo para nao pegar dados obsoletos
            unset($tmp);
            unset($parent);
            unset($data);

            list(, $preMatch, $isEndTag, $tag, $args, $isIndividual, $template) = $matches;

            $this->_tree[] = $preMatch;

            // e' uma tag individual? retorna algo como /
            if (!empty($isIndividual)) {
                $data = array(
                    'name' => $tag, /* nome da classe a ser chamada */
                    'args' => $this->_parseArgs($args),
                    'content' => array()
                );
                
                $this->_tree[] =& $data;
                continue;
            }

            // e' um bloco ?
            if(empty($isEndTag)) {
                // cria um novo bloco para a tag
                $data = array(
                    'name' => $tag,
                    'args' => $this->_parseArgs($args),
                    'content' => array()
                );

                // adiciona isso a estrutura
                $this->_tree[] =& $data;
                $this->_stack[] =& $data;

                $this->_tree =& $data['content'];
            }
            
            else{
                // fecha o bloco atual
                unset($this->_tree);
                $tmp = array_pop($this->_stack);
                
                if ($tag == $tmp['name']) {
                    // move para cima da estrutura
                    $parent =& $this->_stack[count($this->_stack) - 1];
                    $this->_tree =& $parent['content'];
                    continue;
                }
                
                throw new Exception("Final da Tag '{$tag}' nao foi encontrada", 1);
            }
        }
        
        $this->_tree[] = $template;
    }

    /**
     * Obtem os argumentos recebidos dentro de uma tag
     *
     * @param string $args - tag a ser analizada
     *
     * @return array
     **/
    protected function _parseArgs($args)
    {
        $arguments = array();
        $matches = array();
        
        preg_match_all(
            '@([\w-]+?)\s*=\s*(\'|")(.*?)\2@', 
            $args, 
            $matches, 
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
                $arguments[$match[1]] = $match[3];
        }

        return $arguments;
    }

    /**
     * Renderiza todo a estrutura encontrada para uma simples string
     *
     * @param string - template que sera analisado
     *
     * @return string
     */
    public function render($template)
    {
        $tree = $this->_parse($template);
        return $this->compile($tree);
    }

    /**
     * Compila a estrutura de Arvore chamando uma determinada acao para cada
     * tag/classe
     *
     * @param array $tree - a estrutura a ser analizada
     *
     * @return string
     */
    public function compile($tree)
    {
        $retorno = '';
        
        if (empty($tree)) {
            return $retorno;
        }
        
        if (is_array($tree) && isset($tree['name'])) {
            $tree = array($tree);
        }

        foreach ((Array)$tree as $element) {
            if(is_string($element)) {
                $retorno .= $element;
            }
            else if (!empty($element)) {
                $retorno .= $this->renderTag(
                    $element['name'],
                    $element['args'],
                    $element['content']
                );
            }
        }

        return $retorno;
    }

    /**
     * Contexto para renderizacao de uma tag dados seus argumentos
     *
     * @param string $tag - Nome da Tag
     * @param array  $args - Argumentos
     * @param array  $content - Conteudo interno, formato bloco
     * 
     * @return String
     * 
     * @throws Exception
     */
    public function renderTag($tag, $args, $content)
    {
        $bind = array('args' => $args, 'content' => $content);

        list($classename, $method) = $this->_getClassAndMethodFromTag($tag);

        # checa se classe existe e carrega ...
        parent::_loadClass($classename);

        # instanciando objeto...
        $object = $classename::getInstance();

        # checa se objeto existe...
        if (!is_object($object)) {
            throw new Exception("Erro ao contruir objeto a partir da classe '{$classename}'");
        }

        # checa se metodo existe...
        if (!method_exists($object, $method)) {
            # tenta chamar um metodo candidato que possa lidar com o metodo em falta ...
            if(method_exists($object, 'data_missing')) {
                $bind['request'] = $method;
                $method = 'data_missing';
            } else {
                throw new Exception("Metodo '{$method}' nao existe no Objeto '{$classename}'");
            }
        }

        # realizando chamada ao objeto/metodo e passando os parametros...
        return call_user_func(array($object, $method), $bind);
    }

    /**
     * Data uma tag, retorna o nome da Classe e o Metodo a ser chamado
     *
     * @param String $tag
     *
     * @return array - [classname, method]
     */
    protected function _getClassAndMethodFromTag($tag)
    {
        # se encontrarmos ":" em "name" significa objeto:tag_{metodo}...
        if (!(strpos($tag, ':') === false)) {
            list ($classename, $method) = explode(':', $tag);
            return array(ucfirst($classename), "tag_{$method}");
        }
        
        $classname = (null !== self::$tag_caller) ? ucfirst(self::$tag_caller) : ucfirst($tag);
        $method = "tag_{$tag}";
        
        return array($classname, $method);
    }
    
    /**
     * Informa quem e' o Tag Manager que esta realizando a chamada ao Parser
     **/
    public function setCaller($caller)
    {
        self::$tag_caller = $caller;
    }

    public function resetCaller()
    {
        self::$tag_caller = null;
    }
}
