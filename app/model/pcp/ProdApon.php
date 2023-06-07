<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Ademilson Nunes
 */
class ProdAPon extends TRecord
{
    const TABLENAME = 'GETAPONPRD';
    const PRIMARYKEY= 'OP';
    const IDPOLICY =  'serial'; // {max, serial}
    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('OP');          
          parent::addAttribute('LOTE'); 
          parent::addAttribute('CODPROD'); 
          parent::addAttribute('PRODUTO'); 
          parent::addAttribute('TIPO'); 
          parent::addAttribute('QTDE'); 
          parent::addAttribute('OPERACAO'); 
          parent::addAttribute('USUARIO'); 
          parent::addAttribute('FAMILIA'); 
          parent::addAttribute('MARCA'); 
          parent::addAttribute('CATEGORIA'); 
          parent::addAttribute('SUBCATEGORIA'); 
          parent::addAttribute('EMISSAO'); 
          parent::addAttribute('HRA'); 

    }
    
}
