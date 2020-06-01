<?php

/**
 * Contrato para implementar classes que oferecem serviço de Tags
 */
interface TagInterface
{
    /**
     * Seta os dados encontrados na Tag [args, content, request]
     *
     * @param Array $bind
     * 
     * @return TagInterface
     */
    public function setBind($bind);
    
    /**
     * Obtem Array de dados processados pelo FtlParser
     * 
     * @return Array
     */
    public function getBind();
}
