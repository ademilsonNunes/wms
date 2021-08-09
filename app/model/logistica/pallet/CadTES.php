<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Ademilson
 */
class CadTES extends TRecord
{
    const TABLENAME = 'TES';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'max'; // {max, serial}

    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);        
        parent::addAttribute('TIPO');
        parent::addAttribute('MOTIVO');
    }
    
}
