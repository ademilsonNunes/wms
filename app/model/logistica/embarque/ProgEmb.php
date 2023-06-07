<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Ademilson Nunes
 */
class ProgEmb extends TRecord
{
    const TABLENAME = 'GETPROGROM';
    const PRIMARYKEY= 'PEDVEN';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $transp;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
          parent::addAttribute('PEDVEN'); 
          parent::addAttribute('EMPRESA');
          parent::addAttribute('CODTRANSP');
          parent::addAttribute('TPVEIC');
          parent::addAttribute('TRANSP');
          parent::addAttribute('TEL');
          parent::addAttribute('ROMANEIO');
          parent::addAttribute('TPNF');
          parent::addAttribute('TIPO');
          parent::addAttribute('STATUS');
          parent::addAttribute('NFISCAL');
          parent::addAttribute('VEND');
          parent::addAttribute('CLIENTE');
          parent::addAttribute('LOJA');
          parent::addAttribute('CIDADE');
          parent::addAttribute('ESTADO');
          parent::addAttribute('DTEMISSAO');
          parent::addAttribute('DTAGENDA');
          parent::addAttribute('DTCARR');
          parent::addAttribute('DTNF');
          parent::addAttribute('DTCANHOTO');
          parent::addAttribute('DTBAIXATITULO');
          parent::addAttribute('DTVENCTO');
          parent::addAttribute('DTSAIDAPORTARIA');
          parent::addAttribute('HRSAIDA');
          parent::addAttribute('PLACA');
          parent::addAttribute('CONDPGTO');
          parent::addAttribute('VOLUME');
          parent::addAttribute('PESO');
          parent::addAttribute('VALOR');
          parent::addAttribute('DTRETIRADA');
          parent::addAttribute('OBSTRANSP');
          parent::addAttribute('SUPERVISOR');

   
    }
    
}
