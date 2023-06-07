<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class Clientes extends TRecord
{
    const TABLENAME = 'CLIENTEATIVO';
    const PRIMARYKEY= 'CODCLI';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODCLI');
        parent::addAttribute('CLIENTE');

    }


    
}
