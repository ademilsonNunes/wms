<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class PedidoItensSobel extends TRecord
{
    const TABLENAME = 'GETITENSPV_SOBEL';
    const PRIMARYKEY= 'NUMPV';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('NUMPV');
        parent::addAttribute('ITEM');
        parent::addAttribute('CODPROD');
        parent::addAttribute('PRODUTO');
        parent::addAttribute('UND');
        parent::addAttribute('QTDE');
        parent::addAttribute('VLRTAB');
        parent::addAttribute('DESCONTO');
        parent::addAttribute('VALORVENDA');
        parent::addAttribute('PRCVEN');
        parent::addAttribute('TOTAL');
        parent::addAttribute('HASHPT');
    
    }


    
}
