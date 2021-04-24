<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
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
 * FormUserList
 *
 * @version    1.0
 * @package    Consultar informações sobre usuários conectados na RF
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class FormUserList extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
        // dashbord info
        $indicator1 = new THtmlRenderer('app/resources/info-box.html');
        $indicator1->enableSection('main', ['title' => ('Usuários conectados'), 'icon' => 'users',  'background' => 'blue', 'value' => (float)$this->coutUserAtivo()]);

        $reflash = new THtmlRenderer('app/resources/reflash.html');

        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width:100%';
        
        // create the datagrid columns  
        $ip          = new TDataGridColumn('COLETORID',   'IP',          'center', '10%');
        $func        = new TDataGridColumn('FUNCIONARIO', 'Usuário',     'left',   '10%');
        $colab       = new TDataGridColumn('NOME',        'Colaborador', 'left',   '30%');
        $funcao      = new TDataGridColumn('FUNCAO',      'Função',      'left',   '30%');
        $funcao1     = new TDataGridColumn('FUNCAO1',     'Função1',     'left',   '30%');
    //    $funcao2     = new TDataGridColumn('FUNCAO2',     'Função2',    'left',   '30%');
        $turno       = new TDataGridColumn('TURNO',       'Turno',       'left',   '30%');
        $tarefa      = new TDataGridColumn('TAREFA',      'Tarefa',      'left',   '30%');
        $referencia  = new TDataGridColumn('REFERENCIA',  'Referência',  'left',   '30%');
        $end         = new TDataGridColumn('ENDERECO',    'Endereço',    'left',   '30%');
        $destino     = new TDataGridColumn('DESTINO',     'Destino',     'left',   '30%');
        
        // add the columns to the datagrid, with actions on column titles, passing parameters
        $this->datagrid->addColumn($ip);
        $this->datagrid->addColumn($func);
        $this->datagrid->addColumn($colab);
        $this->datagrid->addColumn($funcao);
        $this->datagrid->addColumn($funcao1);
  //      $this->datagrid->addColumn($funcao2);
        $this->datagrid->addColumn($turno);
        $this->datagrid->addColumn($tarefa);
        $this->datagrid->addColumn($referencia);
        $this->datagrid->addColumn($end);
        $this->datagrid->addColumn($destino);

        
        // creates the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $panel = new TPanelGroup('Usuarios conectados via RF');
        $panel->add($indicator1);        
        $panel->add($reflash);
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        $panel->addHeaderActionLink( 'PDF', new TAction([$this, 'exportAsPDF'], ['register_state' => 'false']), 'far:file-pdf red' );
        $panel->addHeaderActionLink( 'CSV', new TAction([$this, 'exportAsCSV'], ['register_state' => 'false']), 'fa:table blue' );
            
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);
        parent::add($vbox);
    }
    
    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();

        $query = "SELECT COLETORID,
                  FUNCIONARIO,
                  DESCRICAO   AS NOME ,
                  FUNCAO,
                  FUNCAO1,
                  FUNCAO2,
                  TURNO,
                  REFERENCIA,
                  TAREFA,
                  ENDERECO,
                  DESTINO
                  FROM TAB_COL TC 
                  INNER JOIN TAB_FUNC TF ON TF.CODIGO = TC.FUNCIONARIO
                  WHERE FUNCIONARIO <> ''";     

        try
        {
         TTransaction::open('sisdep'); // abre uma transação            
          $conn = TTransaction::get(); // obtém a conexão
        
           // realiza a consulta
           $result = $conn->query($query);
        
           foreach ($result as $row) // exibe os resultados
           {       
              $item = new StdClass;
              $item->COLETORID   = $row['COLETORID'];
              $item->FUNCIONARIO = $row['FUNCIONARIO']; 
              $item->NOME        = $row['NOME'];
              $item->FUNCAO      = $row['FUNCAO'];
              $item->FUNCAO1     = $row['FUNCAO1'];
              $item->FUNCAO2     = $row['FUNCAO2'];
              $item->TURNO       = $row['TURNO'];
              $item->REFERENCIA  = $row['REFERENCIA'];
              $item->TAREFA      = $row['TAREFA'];
              $item->ENDERECO    = $row['ENDERECO'];
              $item->DESTINO     = $row['DESTINO'];
 
              $this->datagrid->addItem($item);
           }
        
         TTransaction::close(); // fecha a transação.
        }
        catch (Exception $e)
        {
         new TMessage('error', $e->getMessage());
        }
    }
    
    public function coutUserAtivo()
    {
        $query = "SELECT COUNT(FUNCIONARIO) AS 'TOTAL'
                  FROM TAB_COL TC 
                  WHERE FUNCIONARIO <> ''";     

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
