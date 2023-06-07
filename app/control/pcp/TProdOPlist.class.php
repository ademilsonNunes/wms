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
 * @package    produção
 * @subpackage pcp
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TProdOPlist extends TPage
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
        $this->setActiveRecord('ProdOP');         
        $this->setDefaultOrder('EMISSAO', 'DESC');        
        $this->setLimit(100);

        $dataAnt = date('d/m/Y', strtotime('-7 days'));
        $day =  date('d/m/Y');

        $criteria = new TCriteria;
        $criteria->setProperty('order', 'DTULTAAPON');
        $criteria->setProperty('direction', 'desc');                 
        $criteria->add(new TFilter('EMISSAO', 'BETWEEN', $dataAnt, $day));
        $this->setCriteria($criteria);              
        
        $this->addFilterField('EMISSAO',    '>=',  'EMISSAO');        
        $this->addFilterField('EMISSAO',    '<=',  'EMISSAO1');        
  
        // creates the form
        $this->form = new BootstrapFormBuilder('form_op');
        $this->form->setFormTitle('Monitoramento - Produção por O.P');

        $dtini = new TDate('EMISSAO');      
        $dtfin = new TDate('EMISSAO1');      
        
        $dtini->setMask('dd/mm/yyyy');
        $dtfin->setMask('dd/mm/yyyy');

        $dtini->setDatabaseMask('dd/mm/yyyy');        
        $dtfin->setDatabaseMask('dd/mm/yyyy');    
      
        // add the fields             
        $this->form->addFields( [ new TLabel('Dt.Inicial') ],[ $dtini] );
        $this->form->addFields( [ new TLabel('Dt.Final') ]  ,[ $dtfin] );
                    
        // set sizes
        $dtini->setSize('50%');
        $dtfin->setSize('50%');

        // keep the form filled during navigation with session data
         $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $column_op          = new TDataGridColumn('OP', 'O.P.', 'left');
        $column_quant       = new TDataGridColumn('QUANT', 'Quant.', 'left');
        $column_qtd_apon    = new TDataGridColumn('QUANT_APON', 'Quant. apon.', 'left');
        $column_saldo       = new TDataGridColumn('SALDO', 'Saldo', 'left');
        $column_emissao     = new TDataGridColumn('EMISSAO', 'Emissão', 'left');
        $column_dt_fin_op   = new TDataGridColumn('DTFIN_OP', 'Dt. Fin. Op', 'left');
        $column_dt_prev     = new TDataGridColumn('DTPREV_ENT_OP', 'Prev. Entr', 'left');
        $column_codigo      = new TDataGridColumn('CODIGO', 'Codigo', 'left');
        $column_cat         = new TDataGridColumn('CATEGORIA', 'Categ.', 'left');
        $column_subcat      = new TDataGridColumn('SUBCATEGORIA', 'Sub.Cat.', 'left');
        $column_item        = new TDataGridColumn('ITEM',   'Item.', 'left');
        $column_dt_ult_apon = new TDataGridColumn('DTULTAAPON', 'Dt.Ult.Apon.', 'left');
        $column_hr_ult_apon = new TDataGridColumn('HRAULTAPON', 'Hr.Ult.Apon.', 'left');
        
        // add the columns to the DataGrid  
        $this->datagrid->addColumn($column_op);
        $this->datagrid->addColumn($column_quant);     
        $this->datagrid->addColumn($column_qtd_apon);   
        $this->datagrid->addColumn($column_saldo);      
        $this->datagrid->addColumn($column_emissao);    
        $this->datagrid->addColumn($column_dt_fin_op);  
        $this->datagrid->addColumn($column_dt_prev);    
        $this->datagrid->addColumn($column_codigo);   
        $this->datagrid->addColumn($column_cat);   
        $this->datagrid->addColumn($column_subcat);                  
        $this->datagrid->addColumn($column_item);      
        $this->datagrid->addColumn($column_dt_ult_apon);
        $this->datagrid->addColumn($column_hr_ult_apon);

        // creates the datagrid column actions        
        $column_op->setAction(new TAction([$this, 'onReload']), ['order' => 'OP']);
        $column_hr_ult_apon->setAction(new TAction([$this, 'onReload']), ['order' => 'HRAULTAPON']);
        $column_codigo->setAction(new TAction([$this, 'onReload']), ['order' => 'CODIGO']);        
        $column_item->setAction(new TAction([$this, 'onReload']), ['order' => 'ITEM']);

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
