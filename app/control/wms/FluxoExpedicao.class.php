<?php
/**
 * FluxoExpedicao
 *
 * @version    1.0
 * @package    Consultar informações de romaneios em fluxo de expedição.
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FluxoExpedicao extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();

           // creates one datagrid
           $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
           $this->datagrid->style = 'width:100%';
           
           // create the datagrid columns
           $end       = new TDataGridColumn('REFERENCIA', 'Referencia',  'center', '10%');
           $prod      = new TDataGridColumn('PRODUTO',    'Produto',    'left',   '30%');
           $descr     = new TDataGridColumn('DESCRIÇÃO',  'Descrição',  'left',   '30%');
           $qtde      = new TDataGridColumn('QTDE',       'Qtde',       'left',   '40%');
           $qtdeSep   = new TDataGridColumn('QTDE_SEP',   'Qtde Sep.',   'left',   '40%');
           $saldo     = new TDataGridColumn('SALDO',      'Saldo',      'left',   '40%');
           $qtdePall  = new TDataGridColumn('CXS_PALLET', 'Cxs Pallet', 'left',   '30%');
                   
           
           // add the columns to the datagrid, with actions on column titles, passing parameters
           $this->datagrid->addColumn($end);
           $this->datagrid->addColumn($prod);
           $this->datagrid->addColumn($descr);
           $this->datagrid->addColumn($qtde);
           $this->datagrid->addColumn($qtdeSep);
           $this->datagrid->addColumn($saldo); 
           $this->datagrid->addColumn($qtdePall);
           
           // creates the datagrid model
           $this->datagrid->createModel();
           
           // search box
           $input_search = new TEntry('input_search');
           $input_search->placeholder = _t('Search');
           $input_search->setSize('100%');
            
           $this->datagrid->enableSearch($input_search, 'REFERENCIA');

           $panel = new TPanelGroup('Romaneios em fluxo de expedição');
           $panel->addHeaderWidget($input_search);
           $panel->add($this->datagrid)->style = 'overflow-x:auto';
           $panel->addFooter('footer');
           $panel->add( $this->datagrid );

           $panel->addHeaderActionLink( 'CSV', new TAction([$this, 'exportAsCSV'], ['register_state' => 'false']), 'fa:table blue' );
           
           // wrap the page content using vertical box
           $vbox = new TVBox;
           $vbox->style = 'width: 100%';
           $vbox->add($panel);
           parent::add($vbox);
       }
       
       /**
        * Load the data into the datagrid
        */
       function onReload()
       {
           $this->datagrid->clear();
   
           $query = "SELECT [REFERENCIA],
                            [PRODUTO],
                            [DESCRIÇÃO],
                            [QTDE],
                            [QTDE_SEP],
                            'SALDO' = ([QTDE] - QTDE_SEP),
                            [CXS_PALLET]
                     FROM(        
                     SELECT AI.REFERENCIA AS 'REFERENCIA', 
                            PRODUTO       AS 'PRODUTO',
                            (SELECT DESCRICAO FROM TAB_PROD WHERE PRODUTO = AI.PRODUTO ) AS 'DESCRIÇÃO',
                             SUM(QTDE) AS QTDE,
                             (SELECT SUM(QTDE)
                             FROM ARQ_SAI
                             WHERE REFERENCIA = AI.REFERENCIA
                             AND PRODUTO = AI.PRODUTO
                             GROUP BY REFERENCIA, 
                                      PRODUTO) AS QTDE_SEP,
                             (SELECT CXS_PALLET FROM TAB_PROD WHERE PRODUTO = AI.PRODUTO ) AS CXS_PALLET
                     FROM ARQ_ITEM AI
                     LEFT JOIN ARQ_TRAN AT ON AT.REFERENCIA = AI.REFERENCIA
                     WHERE AI.TIPO = 'R' 
                     AND AT.STATUS IN ('P', NULL)
                     AND AT.PROCESSO = 'T'
                     AND AT.SETOR1 <> ''
                     AND AT.STATUSSEP <> 'F'
                     AND DATA BETWEEN  (SELECT CONVERT(VARCHAR,CAST(GETDATE() - 5 AS DATETIME),112)) AND (SELECT CONVERT(VARCHAR,CAST(GETDATE() AS DATETIME),112)) 
                     GROUP BY AI.REFERENCIA,	
                              PRODUTO
                     )AS RESULT";     
   
           try
           {
            TTransaction::open('sisdep'); // abre uma transação            
             $conn = TTransaction::get(); // obtém a conexão
           
              // realiza a consulta
              $result = $conn->query($query);
           
              foreach ($result as $row) // exibe os resultados
              {       
                 $item = new StdClass;
                 $item->REFERENCIA   = $row['REFERENCIA'];
                 $item->PRODUTO      = $row['PRODUTO'];
                 $item->DESCRIÇÃO    = $row['DESCRIÇÃO'];
                 $item->QTDE         = (int)$row['QTDE'];
                 $item->SALDO        = (int)$row['SALDO'];
                 $item->QTDE_SEP     = (int)$row['QTDE_SEP'];
                 $item->CXS_PALLET   = (int)$row['CXS_PALLET'];
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
                   $file    = 'app/output/fluxo_expedicao.csv';
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
           parent::show();
       }
        
        
}