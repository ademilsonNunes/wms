<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class Motorista extends TRecord
{
    const TABLENAME = 'ZZK010';
    const PRIMARYKEY= 'ZZK_NUM';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ZZK_NUM');
        parent::addAttribute('ZZK_NOME');
        parent::addAttribute('ZZK_RGVI');
        parent::addAttribute('ZZK_AUTORI');
        parent::addAttribute('ZZK_OBSERV');

    }


    
}
