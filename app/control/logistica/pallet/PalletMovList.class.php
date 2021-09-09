<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * PalletMovList
 * @version    1.0
 * @package    logistica
 * @subpackage pallet
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PalletMovList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;    
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();       

        $this->setDatabase('bisobel');               // defines the database
        $this->setActiveRecord('MovPallet');           // defines the active record
    //
        $this->setDefaultOrder('ID', 'desc');         // defines the default order
        $this->setLimit(10);
        
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'ID');
        $criteria->setProperty('direction', 'desc');
        $this->setCriteria($criteria); // define a standard filter

    
        $this->addFilterField('ID', '=', 'ID'); // filterField, operator, formField
        $this->addFilterField('ROMANEIO', 'like', 'ROMANEIO'); // filterField, operator, formField
        $this->addFilterField('CODTRANSP', 'like', 'CODTRANSP'); // filterField, operator, formField
        $this->addFilterField('DTEMISSAO', 'like', 'DTEMISSAO'); // filterField, operator, formField


        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_mov_pallet');
        $this->form->setFormTitle('Movimentação de Paletes');
        

        // create the form fields
        $id        = new TEntry('ID');
        $ROMANEIO   = new TDBSeekButton('ROMANEIO', 'protheus', 'form_search_mov_pallet', 'Romaneio', 'ZZQ_ROMANE');
        $ROMANEIO->setDisplayMask('{ZZQ_ROMANE} - {ZZQ_DESTRA}  ');
        $ROMANEIO->setDisplayLabel('Transportadora');  

        $CODTRANSP   = new TDBSeekButton('CODTRANSP', 'protheus', 'form_search_mov_pallet', 'Transp', 'A4_NOME');
        $CODTRANSP->setDisplayMask('{A4_NOME}');
        $CODTRANSP->setDisplayLabel('Transportadora');    
        $trasp = new TEntry('Transp');
        $CODTRANSP->setAuxiliar($trasp);
        $DTEMISSAO = new TDate('DTEMISSAO');
        
        // add the fields
        $this->form->addFields( [ new TLabel('ID') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Romaneio') ],  [ $ROMANEIO ] );
        $this->form->addFields( [ new TLabel('Cod.Transp') ],[ $CODTRANSP] );
        $this->form->addFields( [ new TLabel('Dt.Emissão') ],[ $DTEMISSAO] );

        // set sizes
        $id->setSize('50%');
        $ROMANEIO->setSize('50%');
        $CODTRANSP->setSize('10%');
        $trasp  ->setSize('40%');
        $DTEMISSAO->setSize('30%');
   
         $trasp->setEditable(FALSE);
        // keep the form filled during navigation with session data
       // $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['PalletMovForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
    //    $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');   
         
        // creates the datagrid columns
        $column_id    = new TDataGridColumn('ID', 'Id', 'center', '10%');
        $column_rom   = new TDataGridColumn('ROMANEIO', 'Romaneio', 'left');
        $column_tran  = new TDataGridColumn('CODTRANSP', 'Cod.Transp', 'left');
        $column_transp  = new TDataGridColumn('CODTRANSP', 'Trasp.', 'left');
        $column_transp->setTransformer(array($this, 'getTransp'));
        $column_dtemi = new TDataGridColumn('DTEMISSAO', 'Dt.Emissão', 'left');
        $column_dtemi->setTransformer(array($this, 'formatDate'));
        $column_tipo  = new TDataGridColumn('TIPO', 'Tipo', 'left');
        $column_tipo->setTransformer(array($this, 'getTipoMov'));
        $column_mot   = new TDataGridColumn('TES', 'Motivo', 'left');
        $column_mot->setTransformer(array($this, 'getMotivo'));

    //    $column_mot   = new TDataGridColumn( $this->MovPallet->motivo, 'Motivo', 'left');
        $column_qtd   = new TDataGridColumn('QTDE', 'Qtde', 'left');

        // add the columns to the DataGrid  
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_rom);
        $this->datagrid->addColumn($column_tran);
        $this->datagrid->addColumn($column_transp);
        $this->datagrid->addColumn($column_dtemi);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_mot);
        $this->datagrid->addColumn($column_qtd);
 
        // creates the datagrid column actions
        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => 'ID']);
        $column_rom->setAction(new TAction([$this, 'onReload']), ['order' => 'ROMANEIO']);
        $column_tran->setAction(new TAction([$this, 'onReload']), ['order' => 'CODTRANSP']);
        $column_dtemi->setAction(new TAction([$this, 'onReload']), ['order' => 'DTEMISSAO']);
        $column_tipo->setAction(new TAction([$this, 'onReload']), ['order' => 'TIPO']);
        $column_mot->setAction(new TAction([$this, 'onReload']), ['order' => 'MOTIVO']);
        $column_qtd->setAction(new TAction([$this, 'onReload']), ['order' => 'QTDE']);
        
        $action1 = new TDataGridAction(['PalletMovForm', 'onEdit'], ['ID'=>'{ID}', 'register_state' => 'false']);
      //  $action2 = new TDataGridAction([$this, 'onTurnOnOff'], ['id'=>'{ID}']);
        $action3 = new TDataGridAction([$this, 'onDelete'], ['ID'=>'{ID}']);

        $action2 = new TDataGridAction( [$this, 'onPrint'], ['ID'=>'{ID}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
  //      $this->datagrid->addAction($action2 ,_t('Activate/Deactivate'), 'fa:power-off orange');

        $this->datagrid->addAction($action3 ,_t('Delete'), 'far:trash-alt red');
        $this->datagrid->addAction($action2 ,'Imprimir', 'fa:print');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
      //  $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }

    
    /**
     * getTransp
     * @param mixed $id 
     * @return void 
     */
    public function getTransp($id)
    {
        $content = '';
        try
       {
           TTransaction::open('protheus');
           $transp = Transp::find($id);
           $content = $transp->A4_NOME;                     
      
           TTransaction::close();

           return $content;
              
       }
       catch (Exception $e)
       {
           new TMessage('error', $e->getMessage());
           TTransaction::rollback();
       }

    }

    /**
     * Get motivo da movimentacao
     * @param mixed $tes 
     * @return string $motivo 
     */
    public function getMotivo($tes)
    {       
       
        $content = '';
        try
       {
           TTransaction::open('bisobel');
           $tes = CadTES::find($tes);
           $content = $tes->MOTIVO;                     
      
           TTransaction::close();

           return $content;
              
       }
       catch (Exception $e)
       {
           new TMessage('error', $e->getMessage());
           TTransaction::rollback();
       }
    }    
   

    /**
     * getRetornoPalete
     * @param mixed $romaneio 
     * @return string
     */
    public function getRetornoPalete($romaneio)
    {   
        
        $query = "SELECT QTDE 
                  FROM MOV_PALLET
                  WHERE ROMANEIO = '{$romaneio}'
                  AND TIPO = 'E' ";

        try 
        {
            TTransaction::open('bisobel');
            $conn = TTransaction::get();
            $result = $conn->query($query);
            
            $fat = new StdClass;
            foreach ($result as $res) 
            {
                 $fat->QTDERET  = $res['QTDE'];
            }

            return (string)$fat->QTDERET;
            
            TTransaction::close();
        } catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }

    }

    /**
     * formatDate
     * @param mixed $date 
     * @param mixed $object 
     * @return string 
     */
    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    } 


    /**
     * getTipoMov
     * @param stringg $tipo 
     * @return string 
     */
    public function getTipoMov($tipo)
    {
        if($tipo == 'S')
        {
            $tipo = 'Saída'; 
        }
        else
        {
              $tipo = 'Entrada';
        }

        return $tipo;

    }

   /**
    * onPrint
    * @param mixed $param 
    * @return void 
    */
    public function onPrint($param)
    {
        TTransaction::open('bisobel');
        $movPallet = MovPallet::find($param['ID']);
        
        if($movPallet->TIPO == 'S')
        {
            $tipo = 'Saída'; 
        }
        else
        {
              $tipo = 'Entrada';
        }

    if($movPallet->TIPO == 'S')
    {
     $html      = new THtmlRenderer('app/resources/palete_comprovante.html');   
    
     try
     {
         TTransaction::open('bisobel');
         $movPallet = MovPallet::find($param['ID']);
         
         if($movPallet->TIPO = 'S')
         {
             $tipo = 'Saída'; 
         }
         else
         {
               $tipo = 'Entrada';
         }
         
         $html->enableSection('main', ['transp' => $movPallet->CODTRANSP, 
                                      'motorista' => $movPallet->MOTORISTA,
                                      'rg' => $movPallet->RG,
                                      'dtemissao' => $this->formatDate($movPallet->DTEMISSAO, $this),
                                      'placa' => $movPallet->PLACA,
                                      'tipo' => $tipo,
                                      'qtde' => $movPallet->QTDE,
                                      'transpNome' => $this->getTransp($movPallet->CODTRANSP),
                                      'romaneio' => $movPallet->ROMANEIO,
                                      'qtdeRet'  => $this->getRetornoPalete($movPallet->ROMANEIO)
                                      ]); 
    
         TTransaction::close();
           
     }
     catch (Exception $e)
     {
         new TMessage('error', $e->getMessage());
         TTransaction::rollback();
     }

     try
     { 
         $container = new TVBox;
         $container->style = 'width: 100%';  
         $container->add($html); 
       
         // string with HTML contents        
       //  $contents = file_get_contents('app/resources/palete_comprovante.html') . $html->getContents();
         
         // converts the HTML template into PDF
         $dompdf = new \Dompdf\Dompdf();
         $dompdf->loadHtml($container);
       //  $dompdf->loadHtml($contents);
        // $dompdf->setPaper('A4', 'landscape');
         $dompdf->setPaper('A4', 'portrait');
         $dompdf->render();
         
         $file = 'app/output/palete_comprovante.pdf';
         
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

    }else{

        new TMessage('info', 'Favor imprimir processo de saída');

    }


    }
    
}
