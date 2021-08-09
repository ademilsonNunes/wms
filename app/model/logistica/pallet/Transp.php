<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class Transp extends TRecord
{
    const TABLENAME = 'SA4010';
    const PRIMARYKEY= 'A4_COD';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('A4_COD');
        parent::addAttribute('A4_NOME');

    }


    
}
