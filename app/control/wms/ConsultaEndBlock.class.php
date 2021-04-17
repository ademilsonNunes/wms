<?php
/**
 * ConsultaEndBlock
 *
 * @version    1.0
 * @package    Consultar informações endereços bloqueados para saída.
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ConsultaEndBlock extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%';
        
        // create the datagrid columns
        $end       = new TDataGridColumn('ENDEREÇO',   'Endereço',   'center', '10%');
        $prod      = new TDataGridColumn('PRODUTO',    'Produto',    'left',   '30%');
        $descr     = new TDataGridColumn('DESCRIÇÃO',  'Descrição',  'left',   '30%');
        $qtde      = new TDataGridColumn('QTDE',       'Qtde',       'left',   '30%');
        $qtdePall  = new TDataGridColumn('CXS_PALLET', 'Cxs Pallet', 'left',   '30%');
        
        // add the columns to the datagrid, with actions on column titles, passing parameters
        $this->datagrid->addColumn($end);
        $this->datagrid->addColumn($prod);
        $this->datagrid->addColumn($descr);
        $this->datagrid->addColumn($qtde);
        $this->datagrid->addColumn($qtdePall);
        
        // creates the datagrid model
        $this->datagrid->createModel();
        
        $panel = new TPanelGroup('Endereços Bloqueados para saída');
        $panel->add( $this->datagrid );
        $panel->addFooter('footer');
        
    //    $panel->addHeaderActionLink( 'PDF', new TAction([$this, 'exportAsPDF'], ['register_state' => 'false']), 'far:file-pdf red' );
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

        $query = "SELECT(TE.PREDIO + '.' + CAST( TE.RUA AS VARCHAR) + '.' + CAST (TE.BLOCO AS VARCHAR) + '.' + CAST(APTO AS VARCHAR)) AS 'ENDEREÇO',
                  TE.PRODUTO   AS 'PRODUTO',
                  TP.DESCRICAO AS 'DESCRIÇÃO',
	              TE.QTDE AS 'QTDE',
	              TE.CXS_PALLET AS 'CXS_PALLET'
                  FROM TAB_END TE
                  LEFT JOIN TAB_PROD TP ON TP.PRODUTO = TE.PRODUTO
                  WHERE BLOQUEADO_SAIDA = -1
                  AND STATUS = 4";     

        try
        {
         TTransaction::open('sisdep'); // abre uma transação            
          $conn = TTransaction::get(); // obtém a conexão
        
           // realiza a consulta
           $result = $conn->query($query);
        
           foreach ($result as $row) // exibe os resultados
           {       
              $item = new StdClass;
              $item->ENDEREÇO     = $row['ENDEREÇO'];
              $item->PRODUTO      = $row['PRODUTO'];
              $item->DESCRIÇÃO    = $row['DESCRIÇÃO'];
              $item->QTDE         = $row['QTDE'];
              $item->CXS_PALLET   = $row['CXS_PALLET'];
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
                $file    = 'app/output/end_block.csv';
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
