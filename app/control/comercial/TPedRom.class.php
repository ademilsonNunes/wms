<?php
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
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
 * TProdOPlist
 * @version    1.0
 * @package    comercial
 * @subpackage pedidos x romaneio
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPedRom extends TPage
{
    protected $form;     
    protected $datagrid; 
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

        $this->setDatabase('protheus');               
        $this->setActiveRecord('PedXRom');         
        $this->setDefaultOrder('NUMPEDIDO', 'DESC');        
        $this->setLimit(100);

        $dataAnt = date('Y-m-d', strtotime('-7 days'));
        $day =  date('Y-m-d');

        $criteria = new TCriteria;
        $criteria->setProperty('order', 'NUMPEDIDO');
        $criteria->setProperty('direction', 'desc');                 
        $criteria->add(new TFilter('EMISSAO', 'BETWEEN', $dataAnt, $day));
        $this->setCriteria($criteria);              
        
        $this->addFilterField('EMISSAO',    '>=',  'EMISSAO');        
        $this->addFilterField('EMISSAO',    '<=',  'EMISSAO1');        
  
        // creates the form
        $this->form = new BootstrapFormBuilder('form_op');
        $this->form->setFormTitle('Monitoramento - Pedidos X Romaneio');

        $dtini = new TDate('EMISSAO');      
        $dtfin = new TDate('EMISSAO1');             
    
        $dtini->setValue(date('Y-m-d'));
        $dtfin->setValue(date('Y-m-d'));

        $dtini->setMask('dd/mm/yyyy');
        $dtfin->setMask('dd/mm/yyyy');

        $dtini->setDatabaseMask('yyyy-mm-dd');        
        $dtfin->setDatabaseMask('yyyy-mm-dd');    
      
        // add the fields             
        $this->form->addFields( [ new TLabel('Dt.Inicial') ],[ $dtini] );
        $this->form->addFields( [ new TLabel('Dt.Final') ]  ,[ $dtfin] );
                    
        // set sizes
        $dtini->setSize('30%');
        $dtfin->setSize('30%');

        // keep the form filled during navigation with session data
         $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $column_tp          = new TDataGridColumn('TP',  'Tp.', 'left');
        $column_tipo        = new TDataGridColumn('TIPO', 'Tipo', 'left');
        $column_cat         = new TDataGridColumn('CATEGORIA', 'Cat.', 'left');
        $column_subcat      = new TDataGridColumn('SUBCATEGORIA', 'Sub.Cat.', 'left');
        $column_prod        = new TDataGridColumn('PRODUTO', 'Prod.', 'left');
        $column_descr       = new TDataGridColumn('DESCRICAO','Item', 'left');
        $column_pv          = new TDataGridColumn('NUMPEDIDO','Num.PV', 'left');
        $column_rom         = new TDataGridColumn('ROMANEIO', 'Romaneio', 'left');
        $column_nf          = new TDataGridColumn('NF', 'N.Fiscal', 'left');
        $column_emissao     = new TDataGridColumn('EMISSAO', 'Dt.Emissão', 'left');

        $column_item        = new TDataGridColumn('ITEM', 'Item', 'left');
      //  $column_saldo       = new TDataGridColumn('SALDO', 'Saldo', 'left');
        $column_codcli      = new TDataGridColumn('CODCLIENTE', 'Cod.Cli', 'left');
        $column_cliente     = new TDataGridColumn('CLIENTE', 'Cliente', 'left');
        $column_rede        = new TDataGridColumn('REDE', 'Rede', 'left');
        $column_estado      = new TDataGridColumn('ESTADO', 'UF', 'left');
 
        $column_mun         = new TDataGridColumn('MUNICIPIO', 'Cod.Cli', 'left');
        $column_codvend     = new TDataGridColumn('CODVEND', 'Cod.Ven', 'left');
        $column_vendedor    = new TDataGridColumn('VENDEDOR', 'Vend.', 'left');
        $column_codsuper    = new TDataGridColumn('CODSUPER', 'Cod.Super', 'left');
        $column_supervisor  = new TDataGridColumn('SUPERVISOR', 'Super', 'left');
        //$column_reserva     = new TDataGridColumn('RESERVA', 'Reserva', 'left');
        $column_status      = new TDataGridColumn('STATUS', 'Status', 'left');
         
        // add the columns to the DataGrid  
        $this->datagrid->addColumn($column_tp);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_cat);
        $this->datagrid->addColumn($column_subcat);
        $this->datagrid->addColumn($column_prod);
        $this->datagrid->addColumn($column_descr);
        $this->datagrid->addColumn($column_pv);
        $this->datagrid->addColumn($column_rom);
        $this->datagrid->addColumn($column_nf);
        $this->datagrid->addColumn($column_emissao);
        $this->datagrid->addColumn($column_item);    
      //  $this->datagrid->addColumn($column_saldo);   
        $this->datagrid->addColumn($column_codcli);  
        $this->datagrid->addColumn($column_cliente); 
        $this->datagrid->addColumn($column_rede);    
        $this->datagrid->addColumn($column_estado);  
        $this->datagrid->addColumn($column_mun);       
        $this->datagrid->addColumn($column_codvend);   
        $this->datagrid->addColumn($column_vendedor);  
        $this->datagrid->addColumn($column_codsuper);  
        $this->datagrid->addColumn($column_supervisor);
        $this->datagrid->addColumn($column_status);    
        // creates the datagrid column actions        
        $column_tp->setAction(new TAction([$this, 'onReload']), ['order' => 'TP']);

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
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
     //   $container->add($vbox);
        $container->add($panel);
        
        parent::add($container);
    }
    
}
