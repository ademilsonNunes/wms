<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class Vendedores extends TRecord
{
    const TABLENAME = 'SA3010';
    const PRIMARYKEY= 'A3_COD';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('A3_COD');
        parent::addAttribute('A3_NOME');

    }


    
}
