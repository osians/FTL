<?php

require_once __DIR__ . '/../../src/TagManager.php';

class Page extends TagManager
{
    private $info = array(
        'meta_description' => "Meta description",
        'meta_keywords' => "Palavras, Chaves, do, Site, Separadas, por, Virgula",
        'current_lang' => "pt-br",
        'page_title' => "Galeria de Fotos",
        'theme_url' => "",
        'site_url' => "",
        'site_title' => "Nome do Meu Site",
        'charset' => "UTF-8",
    );

    # metodo padrao, chamado sempre que nao e' especificado um metodo
    public function tag_page($bind)
    {
        # convertendo array para objeto
        $args = (object)$bind['args'];

        if(isset($args->get)) {
            return $this->tag_get($bind);
        }
    }


    public function tag_navigation( $_bind_ = array() )
    {
        $this->navigation = Query::select('*')->from('navigation')->where('status = 1')->exec();

        $parser = FtlParser::getInstance();
        $parser->setCaller(get_class($this));

        $this->curr_item_nav = 0;

        $__str__ = parent::openHtmlTag( $_bind_['args'] );

        foreach($this->navigation as $nav):
            $__str__ .= $parser->compile( $_bind_['content'] ) ;
            $this->curr_item_nav++;
        endforeach;

        $__str__.= parent::closeHtmlTag( $_bind_['args'] );

        $parser->resetCaller();

        return $__str__;
    }

    public function tag_get($_bind_ = array())
    {
    	$args = (object)$_bind_['args'];
    	return (isset($this->info[$args->get])) ? $this->info[$args->get] : '';
    }

}
