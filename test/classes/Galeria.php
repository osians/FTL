<?php

require_once __DIR__ . '/../../src/TagManager.php';

class Galeria extends TagManager
{
    # @reference - http://www.w3schools.com/tags/ref_standardattributes.asp
    protected $html_tag_attributes = array (
        'accesskey',
        'id',
        'class',
        'dir',
        'for',
        'title',
        'lang',
        'spellcheck',
        'style',
        'tabindex',
    );

    protected $fotos = array (
        array( 'src' => 'templates/fotos/foto01.jpg', 'alt' => 'texto alternativo 01', 'title' => 'Título da Imagem 01' ),
        array( 'src' => 'templates/fotos/foto02.jpg', 'alt' => 'texto alternativo 02', 'title' => 'Título da Imagem 02' ),
        array( 'src' => 'templates/fotos/foto03.jpg', 'alt' => 'texto alternativo 03', 'title' => 'Título da Imagem 03' ),
        array( 'src' => 'templates/fotos/foto04.jpg', 'alt' => 'texto alternativo 04', 'title' => 'Título da Imagem 04' ),
        array( 'src' => 'templates/fotos/foto05.jpg', 'alt' => 'texto alternativo 05', 'title' => 'Título da Imagem 05' ),
        array( 'src' => 'templates/fotos/foto06.jpg', 'alt' => 'texto alternativo 06', 'title' => 'Título da Imagem 06' ),
        array( 'src' => 'templates/fotos/foto07.jpg', 'alt' => 'texto alternativo 07', 'title' => 'Título da Imagem 07' ),
        array( 'src' => 'templates/fotos/foto08.jpg', 'alt' => 'texto alternativo 08', 'title' => 'Título da Imagem 08' ),
        array( 'src' => 'templates/fotos/foto09.jpg', 'alt' => 'texto alternativo 09', 'title' => 'Título da Imagem 09' ),
        array( 'src' => 'templates/fotos/foto10.jpg', 'alt' => 'texto alternativo 10', 'title' => 'Título da Imagem 10' ),
        array( 'src' => 'templates/fotos/foto11.jpg', 'alt' => 'texto alternativo 11', 'title' => 'Título da Imagem 11' ),
        array( 'src' => 'templates/fotos/foto12.jpg', 'alt' => 'texto alternativo 12', 'title' => 'Título da Imagem 12' ),
        array( 'src' => 'templates/fotos/foto13.jpg', 'alt' => 'texto alternativo 13', 'title' => 'Título da Imagem 13' ),
        array( 'src' => 'templates/fotos/foto14.jpg', 'alt' => 'texto alternativo 14', 'title' => 'Título da Imagem 14' ),
        array( 'src' => 'templates/fotos/foto15.jpg', 'alt' => 'texto alternativo 15', 'title' => 'Título da Imagem 15' ),
        array( 'src' => 'templates/fotos/foto16.jpg', 'alt' => 'texto alternativo 16', 'title' => 'Título da Imagem 16' ),
    );

    /* metodo default */
    public function tag_galeria($bind = array())
    {
        $retorno = "";
        $parser = FtlParser::getInstance();
        $parser->setCaller(get_class($this));

        $this->curr_item_nav = 0;

        $retorno .= $this->_openHtmlTag($bind['args']);

        foreach ($this->fotos as $foto) {
            $retorno .= $parser->compile($bind['content']);
            $this->curr_item_nav++;
        }

        $retorno .= $this->_closeHtmlTag($bind['args']);
        return $retorno;
    }


    protected function _openHtmlTag($args)
    {
        if (!isset($args['tag'])) {
            return '';
        }

        $obj = (object)$args;
        $retorno = "<{$obj->tag}";

        foreach($this->html_tag_attributes as $attr) {
            $retorno .= isset($obj->{$attr})
                ? " $attr='" . $obj->{$attr} . "'" : "";
        }

        return "{$retorno}>";
    }

    protected function _closeHtmlTag($args)
    {
        return isset($args['tag']) ? "</{$args['tag']}>" : '';
    }

    public function tag_url()
    {
        return $this->fotos[$this->curr_item_nav]['src'];
    }

    public function tag_title()
    {
        return $this->fotos[$this->curr_item_nav]['title'];
    }

    public function tag_src()
    {
        return $this->fotos[$this->curr_item_nav]['src'];
    }
    
    public function tag_alt()
    {
        return $this->fotos[$this->curr_item_nav]['alt'];
    }
}