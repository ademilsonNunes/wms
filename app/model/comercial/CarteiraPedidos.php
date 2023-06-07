<?php

use Adianti\Database\TRecord;

class CarteiraPedidos extends TRecord
{
    const TABLENAME = 'GETCARTEIRAPV';
    const PRIMARYKEY= 'NUMPV';
    const IDPOLICY =  'serial';

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('EMP');

        parent::addAttribute('TPFRETE');
        parent::addAttribute('NFISCAL');
       // parent::addAttribute('EMISSAONF');
       // parent::addAttribute('CHAVENFE');
        parent::addAttribute('CODTRANSP');
        parent::addAttribute('TPVEIC');
        parent::addAttribute('DTSAIDAPORTARIA');
        parent::addAttribute('HRSAIDA');
        parent::addAttribute('PLACA');
        parent::addAttribute('TRANSP');

        parent::addAttribute('NUMPV');
        parent::addAttribute('TPPV');
        parent::addAttribute('DATAPEDIDO');
        parent::addAttribute('EMISSAO');
        parent::addAttribute('DTAGENDA');
        parent::addAttribute('DTAGENDA1');
        parent::addAttribute('DTAGENDA2');
        parent::addAttribute('DTCARR');
        parent::addAttribute('STATUS');
        parent::addAttribute('CLIENTE');
        parent::addAttribute('SUP');
        parent::addAttribute('VEND');
        parent::addAttribute('CODVEND');
        parent::addAttribute('QTDE');
        parent::addAttribute('PESOBRUTO');
        parent::addAttribute('PESOLIQ');
        parent::addAttribute('VALORTABELA');
        parent::addAttribute('VLVENDA');
        parent::addAttribute('UF');
        parent::addAttribute('MUN');
        parent::addAttribute('REDE');
        parent::addAttribute('CODREDE');
        parent::addAttribute('CANAL');

    }



}




?>