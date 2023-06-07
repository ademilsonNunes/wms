<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Ademilson Nunes
 */
class ProgEmbConsolid extends TRecord
{
    const TABLENAME = 'GETPROGROM_CONS';
    const PRIMARYKEY= 'ROMANEIO';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $transp;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('ROMANEIO'); 
          parent::addAttribute('TPVEIC');
          parent::addAttribute('DTCARR');
          parent::addAttribute('CLIENTE');
          parent::addAttribute('VOLUME');       
   
    }
    
}
