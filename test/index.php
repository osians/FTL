<?php 

    # require ftl 
    require_once __DIR__ . '/../src/FtlParser.php';

    # iniciando instancia do Analizador sintatico...
    $ftl = FtlParser::getInstance();

    $ftl->setOptions(array(
        'tag_prefix' => 'sys',
        'class_path' => __DIR__ . '/classes/'
    ));

    # obtendo template a ser utilizado ...
    $template = file_get_contents(__DIR__ . '/templates/index.html');

    # analisando e convertendo template para html...
    echo $ftl->render($template);
