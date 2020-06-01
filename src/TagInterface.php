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
     */
    public function setBind($bind);    
}
