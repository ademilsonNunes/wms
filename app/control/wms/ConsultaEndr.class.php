<?php
/**
 * ConsultaPalete
 *
 * @version    1.0
 * @package    Consultar informações de produtos pelo Endereço
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ConsultaEndr extends TPage
{
    private $form;
    
    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();
        
        // create the form
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Consulta Endereço');
        
        // create the form fields
        $barcode = new TBarCodeInputReader('Leitura');
        
        $barcode->setSize( '100%');
        
        $barcode->setChangeAction( new TAction( [$this, 'onChange'] ) );
        
        $this->form->addFields( [new TLabel('Consulta')], [$barcode, new TLabel('Endereço')] );
        
        $this->form->addAction('Show', new TAction(array($this, 'onShow')), 'far:check-circle green');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     *
     */
    public static function onChange($param)
    {
    }
    
    /**
     * Show the form content
     */
    public function onShow($param)
    {
        $data = $this->form->getData();
        $this->form->setData($data); // put the data back to the form
        $endereco = $data->Leitura;
        $end = explode('.', strtoupper($endereco));

        $predio = $end[0];
        $rua    = $end[1];
        $bloco  = (float)$end[2];
        $apto   = (float)$end[3];
       
        $query = "SELECT (AM.PREDIO + '.' + AM.RUA + '.' + CAST(AM.BLOCO AS VARCHAR) + '.' + CAST(AM.APTO AS VARCHAR)) AS 'END',     
                  AM.PRODUTO,
                  TR.DESCRICAO,                 
                  AM.QTDE,
                  AM.QTDE_RES,
                  AM.NUM_PALLET,
                  LOTE,
                  DATA_ENT,
                  DATA_VAL,
                  AM.CXS_PALLET,
                  AM.BLOQUEADO,
				  TE.USO
                  FROM ARQ_MXE AM
                  LEFT JOIN TAB_PROD TR ON TR.PRODUTO = AM.PRODUTO
                  LEFT JOIN TAB_END  TE  ON TE.PREDIO = AM.PREDIO AND TE.RUA = AM.RUA AND TE.BLOCO = AM.BLOCO AND TE.APTO = AM.APTO 
                  WHERE AM.PREDIO   =   '{$predio}'
                  AND   AM.RUA      =   '{$rua}'
                  AND   AM.BLOCO    =    {$bloco}
                  AND   AM.APTO     =    {$apto}";  

        try
        {
           TTransaction::open('sisdep'); // abre uma transação            
            $conn = TTransaction::get(); // obtém a conexão
            
             // realiza a consulta
             $result    = $conn->query($query);             
             $qtde_pick = 0;
             $qtde      = 0;
             $uso       = 'N';
             $idPall    = '';

             foreach ($result as $row) // exibe os resultados
             {  
                $uso    =  $row['USO'];     
                $end    =  $row['END'];
                $prod   =  $row['PRODUTO'];
                $desc   =  $row['DESCRICAO'];
                $qtde   += $row['QTDE'];
                $qtdSep =  $row['QTDE_RES'];
                $lote   =  $row['LOTE'];
                $dtEnt  =  $row['DATA_ENT'];
                $dtVal  =  $row['DATA_VAL'];
                $cxPall =  $row['CXS_PALLET'];   
                $idPall =  $row['NUM_PALLET'];            
                $qtde_pick += ($row['QTDE'] - $row['QTDE_RES']);                
             }

             TTransaction::close(); // fecha a transação.

             if($uso =='S')
             {
                  $res = '<b>Endereço: </b>'.  $end  . '<br/>' . '<b>Produto: </b>' .$prod . '<br/>' .'<b>Descrição: </b> ' .  $desc . '<br/>' . '<b>Qtde: </b> ' . $qtde_pick . '<br/>';                 
             }
             else
             {
                 $res = '<b>Endereço: </b>'. $end . '<br/>' . '<b>Produto: </b>' .  $prod . '<br/>' .'<b>Descrição: </b> ' .  $desc . '<br/>' . '<b>Qtde: </b> ' . $qtde . '<br/>' . '<b>Lote: </b> ' . $lote . '<br/>' . '<b>Data Ent.: </b> ' .  $dtEnt . '<br/>' . '<b>Data Val.: </b> ' .  $dtVal . '<br/>' . '<b>Cxs_Pallet: </b> ' . $cxPall . '<br/>' . '<b>Pallet:</b> ' . $idPall ;   
             }

             new TMessage('info',  $res);    
            

        }
        catch (Exception $e)
        {
           new TMessage('error', $e->getMessage());
        }
    
    }
}