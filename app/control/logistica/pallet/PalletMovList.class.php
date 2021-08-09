<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
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
        $this->setDefaultOrder('ID', 'asc');         // defines the default order
        $this->setLimit(100);
        
       // $criteria = new TCriteria;
       // $criteria->add(new TFilter('age',  '<', 16), TExpression::OR_OPERATOR); 
      //  $this->setCriteria($criteria); // define a standard filter

    
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
        $id->setSize('5%');
        $ROMANEIO->setSize('10%');
        $CODTRANSP->setSize('10%');
        $trasp  ->setSize('40%');
        $DTEMISSAO->setSize('10%');
   
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
        $column_dtemi = new TDataGridColumn('DTEMISSAO', 'Dt.Emissão', 'left');
        $column_dtemi->setTransformer(array($this, 'formatDate'));
        $column_tipo  = new TDataGridColumn('TIPO', 'Tipo', 'left');
        $column_mot   = new TDataGridColumn('TES', 'Motivo', 'left');
        $column_qtd   = new TDataGridColumn('QTDE', 'Qtde', 'left');

        // add the columns to the DataGrid  
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_rom);
        $this->datagrid->addColumn($column_tran);
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
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
  //      $this->datagrid->addAction($action2 ,_t('Activate/Deactivate'), 'fa:power-off orange');

        $this->datagrid->addAction($action3 ,_t('Delete'), 'far:trash-alt red');
        
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
        $container->add($panel);
        
        parent::add($container);
    }
    

    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }
    
}
