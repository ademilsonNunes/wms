<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TDropDown;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * PalletCadTESList
 * @version    1.0
 * @package    logistica
 * @subpackage pallet
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PalletCadTESList extends TPage
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
        $this->setActiveRecord('CadTES');           // defines the active record
        $this->setDefaultOrder('ID', 'asc');         // defines the default order
        $this->setLimit(10);
      //  $this->setCriteria($criteria); // define a standard filter

    
        $this->addFilterField('ID', '=', 'ID'); // filterField, operator, formField
        $this->addFilterField('TIPO', 'like', 'TIPO'); // filterField, operator, formField
        $this->addFilterField('MOTIVO', 'like', 'MOTIVO'); // filterField, operator, formField


        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_cadprod');
        $this->form->setFormTitle('tipos de entrada e saida');
        

        // create the form fields
        $id   = new TEntry('ID');
        $TIPO = new TEntry('TIPO');
        $MOTIVO = new TEntry('MOTIVO');
        
        // add the fields
        $this->form->addFields( [ new TLabel('ID') ], [ $id ] );
        $this->form->addFields( [ new TLabel('TIPO') ], [ $TIPO ] );
        $this->form->addFields( [ new TLabel('MOTIVO') ], [ $MOTIVO ] );

        // set sizes
        $id->setSize('100%');
        $TIPO->setSize('100%');
        $MOTIVO->setSize('100%');

        // keep the form filled during navigation with session data
       // $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['PalletCadTESForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
    //    $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');   

        // creates the datagrid columns
        $column_id   = new TDataGridColumn('ID', 'Id', 'center', '10%');
        $column_TIPO = new TDataGridColumn('TIPO', 'TIPO', 'left');
        $column_MOTIVO = new TDataGridColumn('MOTIVO', 'MOTIVO', 'left');

        // add the columns to the DataGrid  
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_TIPO);
        $this->datagrid->addColumn($column_MOTIVO);
        
 
        // creates the datagrid column actions
        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => 'ID']);
        $column_TIPO->setAction(new TAction([$this, 'onReload']), ['order' => 'TIPO']);
        $column_MOTIVO->setAction(new TAction([$this, 'onReload']), ['order' => 'MOTIVO']);
        
        
        $action1 = new TDataGridAction(['PalletCadTESForm', 'onEdit'], ['ID'=>'{ID}', 'register_state' => 'false']);
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
    
    /**
     * Turn on/off an user
     */
    public function onTurnOnOff($param)
    {
        /*
        try
        {
            TTransaction::open('erphouse');
            $servico = Servico::find($param['id']);
            
            if ($servico instanceof Servico)
            {
                $servico->ativo = $servico->ativo == 'Y' ? 'N' : 'Y';
                $servico->store();
            }
            
            TTransaction::close();
            
            $this->onReload($param);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        */
    }
}
