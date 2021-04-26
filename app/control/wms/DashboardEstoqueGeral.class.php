<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * DashboardEstoqueGeral
 *
 * @version    1.0
 * @package    DashboardEstoqueGeral
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class DashboardEstoqueGeral extends TPage
{
    private $datagrid;
    private $totalPicking;
    private $totalLiberado;   
    private $totalTransitoSaida;
    private $totalRevisao;
    private $totalProd = array();
    private $totalProdRot = array();
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        $this->totalPicking = 0;

        parent::__construct();
        
        try
        {
             $html = new THtmlRenderer('app/resources/wms_estoque_geral_dashboard.html');
            
            $totalEndBlock       = new THtmlRenderer('app/resources/info-box.html');         
            $indicadorPick       = new THtmlRenderer('app/resources/info-box.html');   
            $indicadorPickTotal  = new THtmlRenderer('app/resources/info-box.html');  
            $indicadorLib        = new THtmlRenderer('app/resources/info-box.html');    
            $indicadorTotalLib   = new THtmlRenderer('app/resources/info-box.html');   
            $indicadorTotalRev   = new THtmlRenderer('app/resources/info-box.html');
            $indicadorRev        = new THtmlRenderer('app/resources/info-box.html');
            $indicadorTotalGer   = new THtmlRenderer('app/resources/info-box.html'); 
            $indicadorEstoqueGer = new THtmlRenderer('app/resources/info-box.html');  
            $indicadorTranSaida  = new THtmlRenderer('app/resources/info-box.html');
            
            $indicadorRot        = new THtmlRenderer('app/resources/google_pie_chart.html');
            $produtos            = new THtmlRenderer('app/resources/google_column_chart.html');
            

            $indicadorPick->enableSection('main', ['title' => ('Total Picking'), 'icon' => 'boxes',  'background' => 'blue', 'value' => (float)TSession::getValue('total_picking')]);                     
            $indicadorPickTotal->enableSection('main', ['title' => ('Picking'), 'icon' => 'boxes',  'background' => 'blue', 'value' => (float)TSession::getValue('total_picking')]);  
            $indicadorLib->enableSection('main', ['title' => ('Liberado'), 'icon' => 'boxes',  'background' => 'blue', 'value' => (float)TSession::getValue('total_lib')]);          
            $indicadorTotalLib->enableSection('main', ['title' => ('Total (Liberado + Picking)'), 'icon' => 'boxes',  'background' => 'green', 'value' =>  (float)TSession::getValue('total_lib') + (float)TSession::getValue('total_picking') ] );      
            $indicadorTotalRev->enableSection('main', ['title' => ('Revisão'), 'icon' => 'boxes',  'background' => 'yellow', 'value' => (float)TSession::getValue('total_rev')]);  
            $indicadorRev->enableSection('main', ['title' => ('Revisão'), 'icon' => 'boxes',  'background' => 'yellow ', 'value' => (float)TSession::getValue('total_rev')]);  
            $indicadorTotalGer->enableSection('main', ['title' => ('Total (Liberado + Revisão)'), 'icon' => 'boxes',  'background' => 'green', 'value' => (float)TSession::getValue('total_rev') +  (float)TSession::getValue('total_lib')]);  
            $indicadorEstoqueGer->enableSection('main', ['title' => ('Total Geral'), 'icon' => 'boxes',  'background' => 'green', 'value' => (float)TSession::getValue('total_rev') +  (float)TSession::getValue('total_lib') + (float)TSession::getValue('total_picking')]);  
            $indicadorTranSaida->enableSection('main', ['title' => ('Trânsito de saída'), 'icon' => 'boxes',  'background' => 'orange', 'value' => (float)TSession::getValue('total_tran')]);  
            $totalEndBlock->enableSection('main', ['title' => ('Total End. bloqueados'), 'icon' => 'boxes',  'background' => 'red ', 'value' => (float)$this->countEndBlock()]);

            // replace the main section variables   
            $this->totalProd = $this->getEstGeralChart();
            $data   = array();
            $data[] = [ 'Caixas', 'Item' ];

            foreach ($this->totalProd as $row) 
            {
              $data[] = [$row['ITEM'], $row['QTDE']];
            }
       


            $this->totalProdRet = $this->getEstGeralRot();

            $data1   = array();
            $data1[] = [ 'Caixas', 'Rotatividade' ];
            foreach ($this->totalProdRet as $row) 
            {
              $data1[] = [$row['ROT'], $row['QTDE']];
            }
  
            $indicadorRot->enableSection('main', ['data'   => json_encode($data1),
            'width'  => '100%',
            'height' => '500px',
            'title'  => 'Estoque Vs Rotatividade',
            'ytitle' => 'Caixas', 
            'xtitle' => 'Classificação',
            'uniqid' => uniqid()]); 
             
            
            $produtos->enableSection('main', ['data'   => json_encode($data),
                                              'width'  => '100%',
                                              'height' => '500px',
                                              'title'  => 'Estoque Geral',
                                              'ytitle' => 'Caixas', 
                                              'xtitle' => 'Produto',
                                              'uniqid' => uniqid()]); 
                                                       

            $html->enableSection('main', ['indicadorPick'      => $indicadorPick,
                                          'indicadorLib'       => $indicadorLib,
                                          'indicadorTotalLib'  => $indicadorTotalLib,
                                          'indicadorTotalRev'  => $indicadorTotalRev,
                                          'indicadorTotalGer'  => $indicadorTotalGer,
                                          'indicadorPickTotal' => $indicadorPickTotal,
                                          'indicadorRev'       => $indicadorRev,
                                          'indicadorEstoqueGer'=> $indicadorEstoqueGer,
                                          'indicadorTranSaida' => $indicadorTranSaida,
                                          'totalEndBlock'      => $totalEndBlock,
                                          'produtos'           => $produtos,
                                          'indicadorRot'       => $indicadorRot
                                          ]);

            // creates one datagrid
            $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
            $this->datagrid->style = 'width:100%';
            $this->datagrid->setHeight(300);
            $this->datagrid->makeScrollable();
            
            // create the datagrid columns
            $produto       = new TDataGridColumn('PRODUTO',         'Produto',            'left',   '10%');
            $item          = new TDataGridColumn('ITEM',            'Item.',              'left',   '20%');
            $rot           = new TDataGridColumn('ROT',             'Rot.',               'center', ' 5%');
            $und           = new TDataGridColumn('UND',             'Und.',               'center', ' 5%');
            $endSep        = new TDataGridColumn('END_SEP',         'End. Sep.',          'center', ' 5%');
            $qtdeLb        = new TDataGridColumn('QTDE_LIB',        'Qtde Liberada',      'center', ' 5%');  
            $qtdePk        = new TDataGridColumn('QTDE_PICK',       'Picking',            'center', ' 5%');          
            $qtdeRv        = new TDataGridColumn('QTDE_REV',        'Revisão',            'center', ' 5%');   
            $qtdeRes       = new TDataGridColumn('QTDE_RES',        'Ressuprimento',      'center', ' 5%');    
            $qtde_egeral   = new TDataGridColumn('QTDE_EGERAL',     'Qtde Geral Disp.',   'center', ' 5%');
            $tranSaida     = new TDataGridColumn('QTDE_TRAN_SAIDA', 'Transito de Saida',  'center', ' 5%');    
            $estGeral      = new TDataGridColumn('ESTGERAL',        'Total Geral',        'center', ' 5%');    
            $status        = new TDataGridColumn('STATUS',          'Status',             'center', '10%');
            
            // add the columns to the datagrid, with actions on column titles, passing parameters
            $this->datagrid->addColumn($produto);
            $this->datagrid->addColumn($item);
            $this->datagrid->addColumn($rot);
            $this->datagrid->addColumn($und);
            $this->datagrid->addColumn($endSep);
            $this->datagrid->addColumn($qtdeLb);
            $this->datagrid->addColumn($qtdePk);
            $this->datagrid->addColumn($qtdeRv);
            $this->datagrid->addColumn($qtdeRes);
            $this->datagrid->addColumn($qtde_egeral);
            $this->datagrid->addColumn($tranSaida);
            $this->datagrid->addColumn($estGeral);
            $this->datagrid->addColumn($status);
            
            // creates the datagrid model
            $this->datagrid->createModel();

            // search box
            $input_search = new TEntry('input_search');
            $input_search->placeholder = 'Buscar';
            $input_search->setSize('100%');
            
            // enable fuse search by column name
            $this->datagrid->enableSearch($input_search, 'PRODUTO, ITEM, STATUS');

            $panel = new TPanelGroup('Estoque Geral');        
            $panel->addHeaderWidget($input_search);
            $panel->add($this->datagrid);

            $panel->addHeaderActionLink( 'PDF', new TAction([$this, 'exportAsPDF'], ['register_state' => 'false']), 'far:file-pdf red' );
            $panel->addHeaderActionLink( 'CSV', new TAction([$this, 'exportAsCSV'], ['register_state' => 'false']), 'fa:table blue' );
          
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            $container->add($panel);            
            parent::add($container);            
        }
        catch (Exception $e)
        {
            parent::add($e->getMessage());
        }
    }

    /**
     * Load the data into the datagrid
     */
    function onReload()
    {        
        $this->datagrid->clear();
    }
    /**
     * getEsGeral retorna objeto com estoque atual
     * @return StdClass Estoque 
     */
    function getEstGeral()
    {
      
              $query = "SELECT [FAMILIA],
              [MARCA],
               [CATEGORIA],
               [SUBCATEGORIA],
              [PRODUTO],
               [ITEM],
               [ROT],
               [UND],
               [END_RET],	
               [END_SEP],
               [QTDE_EG],
               [QTDE_SEP],	
              [APTO_SEP], 
               [STATUS_COD],
               [STATUS]
        FROM(
        SELECT PROD.PRODUTO    AS 'PRODUTO', 
               PROD.DESCRICAO  AS 'ITEM', 
               PROD.ROTATIV    AS 'ROT', 
               PROD.UNIDADE    AS 'UND', 
               PROD.APTO_SEP   AS 'APTO_SEP',
             CONCAT(PROD.PREDIO_RET, '.', PROD.RUA_RET, '.', CAST(PROD.BLOCO_RET AS VARCHAR), '.', CAST(PROD.APTO_RET AS VARCHAR)) AS 'END_RET',
             CONCAT(PROD.PREDIO_SEP, '.', PROD.RUA_SEP, '.', CAST(PROD.BLOCO_SEP AS VARCHAR), '.', CAST(PROD.APTO_SEP AS VARCHAR)) AS 'END_SEP',
             'FAMILIA'      = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z6' AND X5_CHAVE = B1_XFAMILI),
             'MARCA'        = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z8' AND X5_CHAVE = B1_XMARCA),
             'CATEGORIA'    = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z9' AND X5_CHAVE = B1_XCATEGO),
             'SUBCATEGORIA' = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z4' AND X5_CHAVE = B1_XSUBCAT),
               ESTQ.STATUS     AS 'STATUS_COD', 
               ESTQ.QTDE_EG    AS 'QTDE_EG', 
               ESTQ.QTDE_SEP   AS 'QTDE_SEP', 
             STAP.DESCRICAO  AS 'STATUS'
        FROM TAB_PROD PROD
        LEFT OUTER JOIN TAB_ESTQ ESTQ ON PROD.PRODUTO = ESTQ.PRODUTO AND PROD.DONO = ESTQ.DONO  
        LEFT OUTER JOIN TAB_STAP STAP ON ESTQ.STATUS = STAP.STATUS
        LEFT JOIN Protheus_Producao.dbo.SB1010 SB1 on SB1.B1_COD COLLATE Latin1_General_CI_AS = PROD.PRODUTO AND D_E_L_E_T_ = '' 
        WHERE PROD.DONO = '001'
        ) AS RES";     

        try
        {
            TTransaction::open('sisdep'); // abre uma transação            
            $conn = TTransaction::get(); // obtém a conexão

            // realiza a consulta
            $result = $conn->query($query);
            $i = 0;    
            foreach ($result as $row) // exibe os resultados
            { 
                $qtde_lib  = 0;
                $qtde_rev  = 0;
                $qtde_res  = 0;
                $qtde_pick = 0;
                $qtde_tran_saida = 0;

                //qtde em transito de saida status = 4
                if($row['STATUS_COD'] == "4")
                {
                  $qtde_tran_saida  = (float)$row['QTDE_EG']; 
                }
              
                //qtde liberada status = 1
                if($row['STATUS_COD'] == "1")
                {
                  $qtde_lib = (float)$row['QTDE_EG']; 
                }
              
                //qtde revisao status = 5
                if($row['STATUS_COD'] == "5")
                {
                  $qtde_rev = (float)$row['QTDE_EG']; 
                }
              
                //qtde ressuprimento
                if($row['STATUS_COD'] == "6")
                {
                  $qtde_res = (float)$row['QTDE_EG']; 
                }
              
                //qtde picking
                if($row['APTO_SEP'] == 1 && $row['STATUS_COD'] == '1')
                {
                  $qtde_pick += $row['QTDE_SEP'];
                }
              
                if($row['APTO_SEP'] == 1 && $row['STATUS_COD'] == '5')
                {
                  $qtde_pick += $row['QTDE_EG'];
                }
              
                $qtde_pick += $qtde_res;
                $qtde_pick -= $qtde_rev; 
              
                //Estoque Total liberado 
                $qtde_egeral = $qtde_lib + $qtde_res;
              
                // Estoque geral total (menos transito de saida)    
                $estoque_geral = $qtde_egeral + $qtde_pick;

                //Totalizadores
                $this->totalPicking  += $qtde_pick;
                $this->totalLiberado += $qtde_lib;
                $this->totalRevisao  += $qtde_rev;
                $this->totalTransitoSaida += $qtde_tran_saida;

               
                TSession::setValue('total_picking', $this->totalPicking);                              
                TSession::setValue('total_lib', $this->totalLiberado);
                TSession::setValue('total_rev', $this->totalRevisao);
                TSession::setValue('total_tran', $this->totalTransitoSaida);
                
                $totalProd[$i] = array('PRODUTO' => $row['PRODUTO'], 'ITEM' => $row['ITEM'], 'QTDE' => $estoque_geral);

                $item = new StdClass;
                $item->FAMILIA          = trim($row['FAMILIA']);
                $item->MARCA            = trim($row['MARCA']);
                $item->CATEGORIA        = trim($row['CATEGORIA']);
                $item->SUBCATEGORIA     = trim($row['SUBCATEGORIA']);
                $item->PRODUTO          = trim($row['PRODUTO']);
                $item->ITEM             = trim($row['ITEM']);
                $item->ROT              = trim($row['ROT']); 
                $item->UND              = trim($row['UND']);
                $item->END_RET          = trim($row['END_RET']);
                $item->END_SEP          = trim($row['END_SEP']);
                $item->QTDE_EG          = $row['QTDE_EG'];
                $item->QTDE_SEP         = $row['QTDE_SEP'];
                $item->QTDE_LIB         = $qtde_lib;
                $item->QTDE_REV         = $qtde_rev;
                $item->QTDE_RES         = $qtde_res;
                $item->QTDE_TRAN_SAIDA  = $qtde_tran_saida;
                $item->QTDE_PICK        = $qtde_pick;
                $item->QTDE_EGERAL      = $qtde_egeral;
                $item->ESTGERAL         = $estoque_geral;
                $item->STATUS_COD       = $row['STATUS_COD'];
                $item->STATUS           = $row['STATUS'];
                $this->datagrid->addItem($item);
                $i++;                 
            }        
            TTransaction::close(); // fecha a transação.
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        } 
        
        return $totalProd;
    }

     /**
     * getEstGeralChart retorna objeto com estoque atual
     * @return StdClass Estoque 
     */
    function getEstGeralChart()
    {
      
              $query = "SELECT [FAMILIA],
              [MARCA],
               [CATEGORIA],
               [SUBCATEGORIA],
              [PRODUTO],
               [ITEM],
               [ROT],
               [UND],
               [END_RET],	
               [END_SEP],
               [QTDE_EG],
               [QTDE_SEP],	
              [APTO_SEP], 
               [STATUS_COD],
               [STATUS]
        FROM(
        SELECT PROD.PRODUTO    AS 'PRODUTO', 
               PROD.DESCRICAO  AS 'ITEM', 
               PROD.ROTATIV    AS 'ROT', 
               PROD.UNIDADE    AS 'UND', 
               PROD.APTO_SEP   AS 'APTO_SEP',
             CONCAT(PROD.PREDIO_RET, '.', PROD.RUA_RET, '.', CAST(PROD.BLOCO_RET AS VARCHAR), '.', CAST(PROD.APTO_RET AS VARCHAR)) AS 'END_RET',
             CONCAT(PROD.PREDIO_SEP, '.', PROD.RUA_SEP, '.', CAST(PROD.BLOCO_SEP AS VARCHAR), '.', CAST(PROD.APTO_SEP AS VARCHAR)) AS 'END_SEP',
             'FAMILIA'      = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z6' AND X5_CHAVE = B1_XFAMILI),
             'MARCA'        = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z8' AND X5_CHAVE = B1_XMARCA),
             'CATEGORIA'    = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z9' AND X5_CHAVE = B1_XCATEGO),
             'SUBCATEGORIA' = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z4' AND X5_CHAVE = B1_XSUBCAT),
               ESTQ.STATUS     AS 'STATUS_COD', 
               ESTQ.QTDE_EG    AS 'QTDE_EG', 
               ESTQ.QTDE_SEP   AS 'QTDE_SEP', 
             STAP.DESCRICAO  AS 'STATUS'
        FROM TAB_PROD PROD
        LEFT OUTER JOIN TAB_ESTQ ESTQ ON PROD.PRODUTO = ESTQ.PRODUTO AND PROD.DONO = ESTQ.DONO  
        LEFT OUTER JOIN TAB_STAP STAP ON ESTQ.STATUS = STAP.STATUS
        LEFT JOIN Protheus_Producao.dbo.SB1010 SB1 on SB1.B1_COD COLLATE Latin1_General_CI_AS = PROD.PRODUTO AND D_E_L_E_T_ = '' 
        WHERE PROD.DONO = '001'
        ) AS RES";     

        try
        {
            TTransaction::open('sisdep'); // abre uma transação            
            $conn = TTransaction::get(); // obtém a conexão

            // realiza a consulta
            $result = $conn->query($query);
            $i = 0;    
            foreach ($result as $row) // exibe os resultados
            { 
                $qtde_lib  = 0;
                $qtde_rev  = 0;
                $qtde_res  = 0;
                $qtde_pick = 0;
                $qtde_tran_saida = 0;

                //qtde em transito de saida status = 4
                if($row['STATUS_COD'] == "4")
                {
                  $qtde_tran_saida  = (float)$row['QTDE_EG']; 
                }
              
                //qtde liberada status = 1
                if($row['STATUS_COD'] == "1")
                {
                  $qtde_lib = (float)$row['QTDE_EG']; 
                }
              
                //qtde revisao status = 5
                if($row['STATUS_COD'] == "5")
                {
                  $qtde_rev = (float)$row['QTDE_EG']; 
                }
              
                //qtde ressuprimento
                if($row['STATUS_COD'] == "6")
                {
                  $qtde_res = (float)$row['QTDE_EG']; 
                }
              
                //qtde picking
                if($row['APTO_SEP'] == 1 && $row['STATUS_COD'] == '1')
                {
                  $qtde_pick += $row['QTDE_SEP'];
                }
              
                if($row['APTO_SEP'] == 1 && $row['STATUS_COD'] == '5')
                {
                  $qtde_pick += $row['QTDE_EG'];
                }
              
                $qtde_pick += $qtde_res;
                $qtde_pick -= $qtde_rev; 
              
                //Estoque Total liberado 
                $qtde_egeral = $qtde_lib + $qtde_res;
              
                // Estoque geral total (menos transito de saida)    
                $estoque_geral = $qtde_egeral + $qtde_pick;
                $totalProd[$i] = array('PRODUTO' => $row['PRODUTO'], 'ITEM' => $row['ITEM'], 'QTDE' => $estoque_geral);     
                $i++;    
            }        
            TTransaction::close(); // fecha a transação.
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        } 
        
        return $totalProd;
    }
   /**
     * getEstGeralRot retorna objeto com estoque atual
     * @return StdClass Estoque 
     */
     function getEstGeralRot()
     {
       
               $query = "SELECT [FAMILIA],
               [MARCA],
                [CATEGORIA],
                [SUBCATEGORIA],
               [PRODUTO],
                [ITEM],
                [ROT],
                [UND],
                [END_RET],	
                [END_SEP],
                [QTDE_EG],
                [QTDE_SEP],	
               [APTO_SEP], 
                [STATUS_COD],
                [STATUS]
         FROM(
         SELECT PROD.PRODUTO    AS 'PRODUTO', 
                PROD.DESCRICAO  AS 'ITEM', 
                PROD.ROTATIV    AS 'ROT', 
                PROD.UNIDADE    AS 'UND', 
                PROD.APTO_SEP   AS 'APTO_SEP',
              CONCAT(PROD.PREDIO_RET, '.', PROD.RUA_RET, '.', CAST(PROD.BLOCO_RET AS VARCHAR), '.', CAST(PROD.APTO_RET AS VARCHAR)) AS 'END_RET',
              CONCAT(PROD.PREDIO_SEP, '.', PROD.RUA_SEP, '.', CAST(PROD.BLOCO_SEP AS VARCHAR), '.', CAST(PROD.APTO_SEP AS VARCHAR)) AS 'END_SEP',
              'FAMILIA'      = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z6' AND X5_CHAVE = B1_XFAMILI),
              'MARCA'        = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z8' AND X5_CHAVE = B1_XMARCA),
              'CATEGORIA'    = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z9' AND X5_CHAVE = B1_XCATEGO),
              'SUBCATEGORIA' = (SELECT X5_DESCRI FROM Protheus_Producao.dbo.SX5010 WHERE X5_TABELA = 'Z4' AND X5_CHAVE = B1_XSUBCAT),
                ESTQ.STATUS     AS 'STATUS_COD', 
                ESTQ.QTDE_EG    AS 'QTDE_EG', 
                ESTQ.QTDE_SEP   AS 'QTDE_SEP', 
              STAP.DESCRICAO  AS 'STATUS'
         FROM TAB_PROD PROD
         LEFT OUTER JOIN TAB_ESTQ ESTQ ON PROD.PRODUTO = ESTQ.PRODUTO AND PROD.DONO = ESTQ.DONO  
         LEFT OUTER JOIN TAB_STAP STAP ON ESTQ.STATUS = STAP.STATUS
         LEFT JOIN Protheus_Producao.dbo.SB1010 SB1 on SB1.B1_COD COLLATE Latin1_General_CI_AS = PROD.PRODUTO AND D_E_L_E_T_ = '' 
         WHERE PROD.DONO = '001'
         ) AS RES";     
 
         try
         {
             TTransaction::open('sisdep'); // abre uma transação            
             $conn = TTransaction::get(); // obtém a conexão
 
             // realiza a consulta
             $result = $conn->query($query);
             $i = 0;    
             $total_a = 0;
             $total_b = 0;
             $total_c = 0;
             foreach ($result as $row) // exibe os resultados
             { 
                 $qtde_lib  = 0;
                 $qtde_rev  = 0;
                 $qtde_res  = 0;
                 $qtde_pick = 0;
                 $qtde_tran_saida = 0;
                 
                 //qtde em transito de saida status = 4
                 if($row['STATUS_COD'] == "4")
                 {
                   $qtde_tran_saida  = (float)$row['QTDE_EG']; 
                 }
               
                 //qtde liberada status = 1
                 if($row['STATUS_COD'] == "1")
                 {
                   $qtde_lib = (float)$row['QTDE_EG']; 
                 }
               
                 //qtde revisao status = 5
                 if($row['STATUS_COD'] == "5")
                 {
                   $qtde_rev = (float)$row['QTDE_EG']; 
                 }
               
                 //qtde ressuprimento
                 if($row['STATUS_COD'] == "6")
                 {
                   $qtde_res = (float)$row['QTDE_EG']; 
                 }
               
                 //qtde picking
                 if($row['APTO_SEP'] == 1 && $row['STATUS_COD'] == '1')
                 {
                   $qtde_pick += $row['QTDE_SEP'];
                 }
               
                 if($row['APTO_SEP'] == 1 && $row['STATUS_COD'] == '5')
                 {
                   $qtde_pick += $row['QTDE_EG'];
                 }
               
                 $qtde_pick += $qtde_res;
                 $qtde_pick -= $qtde_rev; 
               
                 //Estoque Total liberado 
                 $qtde_egeral = $qtde_lib + $qtde_res;            
                 // Estoque geral total (menos transito de saida)    
                 $estoque_geral = $qtde_egeral + $qtde_pick;                 
                  
                 /* Totalizadir rotatividade */ 
                 if ($row['ROT']   == 'A') 
                 {
                   $total_a += $estoque_geral;
                 }
                 elseif($row['ROT'] == 'B') 
                 {
                   $total_b += $estoque_geral;
                 }
                 elseif($row['ROT'] == 'C') 
                 {
                   $total_c += $estoque_geral;
                 }                
             }        
             TTransaction::close(); // fecha a transação.
             $totalProdRot[0] = array('ROT' => 'Curva A', 'QTDE' => $total_a);    
             $totalProdRot[1] = array('ROT' => 'Curva B', 'QTDE' => $total_b);
             $totalProdRot[2] = array('ROT' => 'Curva C', 'QTDE' => $total_c);
         }
         catch (Exception $e)
         {
             new TMessage('error', $e->getMessage());
         } 
         
         return $totalProdRot;
     }  
    /**
     * countEndBlock
     */
    public function countEndBlock()
    {
        $query = "SELECT COUNT((TE.PREDIO + '.' + CAST( TE.RUA AS VARCHAR) + '.' + CAST (TE.BLOCO AS VARCHAR) + '.' + CAST(TE.APTO AS VARCHAR))) AS 'TOTAL'
                  FROM TAB_END TE
                  WHERE BLOQUEADO_SAIDA = -1
                  AND STATUS = 4";     

        try
        {
         TTransaction::open('sisdep'); // abre uma transação            
          $conn = TTransaction::get(); // obtém a conexão
        
           // realiza a consulta
           $result = $conn->query($query);
           $res = 0;
           foreach ($result as $row) // exibe os resultados
           {       
              $res = $row['TOTAL'];    
           }

           return $res;
        
         TTransaction::close(); // fecha a transação.
        }
        catch (Exception $e)
        { 
         new TMessage('error', $e->getMessage());
        }
    }  
    
     /**
     * Export datagrid as PDF
     */
    public function exportAsPDF($param)
    {
        try
        {
            // string with HTML contents
            $html = clone $this->datagrid;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/datagrid-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Export datagrid as CSV
     */
    public function exportAsCSV($param)
    {
        try
        {
            // get datagrid raw data
            $data = $this->datagrid->getOutputData();
            
            if ($data)
            {
                $file    = 'app/output/estoque_atual.csv';
                $handler = fopen($file, 'w');
                foreach ($data as $row)
                {
                    fputcsv($handler, $row);
                }
                
                fclose($handler);
                parent::openFile($file);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * shows the page
     */
    function show()
    {
        $this->onReload();
        $this->getEstGeral();      
        parent::show();
    }
   
}
