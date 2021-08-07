<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class MovPallet extends TRecord
{
    const TABLENAME = 'MOV_PALLET';
    const PRIMARYKEY= 'CODIGO';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODIGO');
        parent::addAttribute('CODTRANSP');
        parent::addAttribute('ROMANEIO');
        parent::addAttribute('DTEMISSAO');
        parent::addAttribute('QTDE');
        parent::addAttribute('QTDE_QUEBRADO');
        parent::addAttribute('OPER');

    }

    
    public function get_romaneio()
    {
        return Romaneio::find($this->ZZQ_ROMANE);
    }
    
}
