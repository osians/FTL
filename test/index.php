<?php 

    # requerindo livraria ftl 
    require_once __DIR__ . '/../src/FtlParser.php';

    header ('Content-type: text/html; charset=UTF-8');
    
    # development mode
    error_reporting(-1);
    ini_set('display_errors', 1);

    # algumas definicoes...
    define( 'DS', DIRECTORY_SEPARATOR ) ;
    define( 'ROOT', getcwd() );
    define( 'TEMPLATES', ROOT . DS . 'templates' . DS );

    /**
     * Converte um array para Objeto
     *
     * @param  array 
     *
     * @return StdClass object
     */
    function oo($array = null)
    {
        return json_decode(json_encode($array));
    }

    # iniciando instancia do Analizador sintatico...
    $ftl = FtlParser::getInstance();

    # definindo configuracoes...
    $config = array(
        'tag_prefix' => 'sys', 
        'class_path' => __DIR__ . '/classes/',
    );

    # setando configuracoes na classe...
    $ftl->setOptions($config);

    # obtendo template a ser utilizado ...
    $template = file_get_contents( TEMPLATES . 'index.html' );

    # analisando e convertendo template para html...
    echo $ftl->render($template);




