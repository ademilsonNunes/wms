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
    
    private $motivo;

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

    }

    /**
     * 
     */
    function get_motivo()
    {
     
        if (empty($this->motivo))
        {
            $this->motivo = new CadTES($id);
        }
              
        return $this->motivo->MOTIVO;
        
    }

    
}