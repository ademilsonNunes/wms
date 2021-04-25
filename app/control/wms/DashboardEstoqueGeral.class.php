<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
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
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        try
        {
          //  $html = new THtmlRenderer('app/resources/wms_estoque_geral_dashboard.html');
            

            $indicator1 = new THtmlRenderer('app/resources/info-box.html');    
            $indicator1->enableSection('main', ['title' => ('Total bloqueado'), 'icon' => 'boxes',  'background' => 'orange', 'value' => (float)$this->countEndBlock()]);
          
         //   $html->enableSection('main', ['indicator1' => $indicator1]);

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

            $panel = new TPanelGroup('Estoque Geral');        
            $panel->add($this->datagrid);
            
            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($indicator1);
            $container->add($panel);            
            parent::add($container);
            TTransaction::close();
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

           foreach ($result as $row) // exibe os resultados
           { 
             $qtde_lib  = 0;
             $qtde_rev  = 0;
             $qtde_res  = 0;
             $qtde_pick = 0;
             $qtde_tran_saida = 0;

              //qtde liberada status = 1
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
              
              //Estoque Geral
              $qtde_egeral = $qtde_lib + $qtde_res;
              $estoque_geral = $qtde_egeral + $qtde_pick;
              $this->totalPicking += $qtde_pick;

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


           }
        
         TTransaction::close(); // fecha a transação.
        }
        catch (Exception $e)
        {
         new TMessage('error', $e->getMessage());
        }
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
     * shows the page
     */
    function show()
    {
        $this->onReload();
        parent::show();
    }
   
}
