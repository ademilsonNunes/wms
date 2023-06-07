<?php

use Adianti\Database\TRecord;

/**
 * ContaReceber Active Record
 * @author  Ademilson Nunes
 */
class PedXRom extends TRecord
{
    const TABLENAME = 'GETPVXROM';
    const PRIMARYKEY= 'NUMPEDIDO';
    const IDPOLICY =  'serial'; // {max, serial}
    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
          parent::__construct($id, $callObjectLoad);
          parent::addAttribute('TP');
          parent::addAttribute('TIPO');	
          parent::addAttribute('CATEGORIA');	
          parent::addAttribute('SUBCATEGORIA');	
          parent::addAttribute('PRODUTO');	
          parent::addAttribute('DESCRICAO');	
          parent::addAttribute('FAMILIA');	 
          parent::addAttribute('NUMPEDIDO');	
          parent::addAttribute('ROMANEIO');	
          parent::addAttribute('NF');	
          parent::addAttribute('EMISSAO');	
          parent::addAttribute('ENTREGA');	
          parent::addAttribute('ITEM');	
          parent::addAttribute('SALDO');	
          parent::addAttribute('CODCLIENTE');	
          parent::addAttribute('CLIENTE');	
          parent::addAttribute('REDE');	
          parent::addAttribute('ESTADO');	
          parent::addAttribute('MUNICIPIO'); //ans	
          parent::addAttribute('CODVEND');	
          parent::addAttribute('VENDEDOR');	
          parent::addAttribute('CODSUPER');	
          parent::addAttribute('SUPERVISOR');	
          parent::addAttribute('RESERVA');	
          parent::addAttribute('STATUS');	//ans
          parent::addAttribute('ROTA');	
          parent::addAttribute('PESOLIQUIDO');	
          parent::addAttribute('PESOBRUTO');	
          parent::addAttribute('TPVEICULO');	
          parent::addAttribute('OBSPEDIDO');	
          parent::addAttribute('OBSSOBEL');	
          parent::addAttribute('OBSPROTHEUS');	
          parent::addAttribute('REQAGENDA');	
          parent::addAttribute('DTAGENDAMENTO1');	
          parent::addAttribute('HORAAGENDAMENTO1');	
          parent::addAttribute('DTAGENDAMENTO2');	
          parent::addAttribute('HORAAGENDAMENTO2');	
          parent::addAttribute('DTAGENDAMENTO3');	
          parent::addAttribute('HORAAGENDAMENTO3');	
          parent::addAttribute('DTCARREGAMENTO');	
          parent::addAttribute('TRANSPROMANEIO');	
          parent::addAttribute('DESCRICAOTRANSPROMANEIO');	
          parent::addAttribute('TIPOVEICULO');		
          parent::addAttribute('TIPOCARGA');		
          parent::addAttribute('PRCVENDA');		
          parent::addAttribute('TPFRETEPV');		
          parent::addAttribute('PEDCLIENTE');		
          parent::addAttribute('HRAENTREGA');		
          parent::addAttribute('LOJAREDE');	

    }
    
}
