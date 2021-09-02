<?php
/**
 * CidadeForm
 *
 * @version    1.0
 * @package    MonitorDFe
 * @subpackage MonitorDFeDevList
 * @author     Ademilson Nunes 
 * @copyright  Copyright (c) 2021 Sobel Suprema Ind. Com. de Produtos de Limpeza LTDA (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
ini_set('memory_limit', '1000M');

use Adianti\Control\TPage;
use plugins\TFSistDFe\TFSistDFe;

class MonitorDFeDevList extends TPage
{
    /** @return void  */
    public function __construct()
    {
        parent::__construct();

        $dfe = new TFSistDFe();

        $notas = $dfe->getNFeDevJSON(200);        
         
        echo '<pre>';
        print_r($notas);
        
       // d($notas);
        
    }
}