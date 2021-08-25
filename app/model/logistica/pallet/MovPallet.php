<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  <your-name-here>
 */
class MovPallet extends TRecord
{
    const TABLENAME = 'MOV_PALLET';
    const PRIMARYKEY= 'ID';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $transp;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ID');
        parent::addAttribute('CODTRANSP');
        parent::addAttribute('ROMANEIO');
        parent::addAttribute('DTEMISSAO');
        parent::addAttribute('QTDE');
        parent::addAttribute('TIPO');
        parent::addAttribute('TES');
        parent::addAttribute('MOTORISTA');
        parent::addAttribute('PLACA');
        parent::addAttribute('VEICULO');
        parent::addAttribute('OBS');
        parent::addAttribute('RG');
        parent::addAttribute('ITEM');
        parent::addAttribute('PESO');
        parent::addAttribute('QTDE_CXS');
        parent::addAttribute('TPFRETE');

    }
    
}
