<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Ademilson Nunes
 */
class ProdOP extends TRecord
{
    const TABLENAME = 'GETPRODOP';
    const PRIMARYKEY= 'OP';
    const IDPOLICY =  'serial'; // {max, serial}
    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('OP');
          parent::addAttribute('QUANT');
          parent::addAttribute('QUANT_APON');
          parent::addAttribute('SALDO');
          parent::addAttribute('EMISSAO');
          parent::addAttribute('DTFIN_OP');
          parent::addAttribute('DTPREV_ENT_OP');
          parent::addAttribute('CATEGORIA');
          parent::addAttribute('SUBCATEGORIA');
          parent::addAttribute('CODIGO');
          parent::addAttribute('ITEM');
          parent::addAttribute('DTULTAAPON');
          parent::addAttribute('HRAULTAPON');   

    }
    
}
