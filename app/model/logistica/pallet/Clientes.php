<?php

use Adianti\Database\TRecord;

/**
 * Clientes Active Record
 * @author  Ademilson
 */
class Clientes extends TRecord
{
    const TABLENAME = 'SA1010';
    const PRIMARYKEY= 'A1_COD';
    const IDPOLICY =  'max'; // {max, serial}
        
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('A1_NOME');
        parent::addAttribute('A1_CGC');
        parent::addAttribute('A1_LOJA');

    }
}
