<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class Redes extends TRecord
{
    const TABLENAME = 'GETREDE';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODIGO');
        parent::addAttribute('REDE');

    }


    
}
