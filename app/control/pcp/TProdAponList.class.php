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
 * TProdAponlist
 * @version    1.0
 * @package    produção
 * @subpackage pcp
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TProdAponList extends TPage
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
        $this->setActiveRecord('ProdApon');         
        $this->setDefaultOrder('HRA', 'DESC');        
        $this->setLimit(100);

//        $dataAnt = date('Y-m-d', strtotime('-7 days'));
        $dataAnt = date('Y-m-d');
        $day     =  date('Y-m-d');
 
        $criteria = new TCriteria;
        $criteria->setProperty('order', 'HRA');
        $criteria->setProperty('direction', 'desc');                 
        $criteria->add(new TFilter('EMISSAO', 'BETWEEN', $dataAnt, $day));
        $this->setCriteria($criteria);              
        
        $this->addFilterField('EMISSAO',    '>=',  'EMISSAO');        
        $this->addFilterField('EMISSAO',    '<=',  'EMISSAO1');        
  
        // creates the form
        $this->form = new BootstrapFormBuilder('form_apon');
        $this->form->setFormTitle('Monitoramento - Apontamento de produção');

        $dtini = new TDate('EMISSAO');      
        $dtfin = new TDate('EMISSAO1');      

        $dtini->setValue(date('d/m/Y'));
        $dtfin->setValue(date('d/m/Y'));
              
        $dtini->setMask('dd/mm/yyyy');
        $dtfin->setMask('dd/mm/yyyy');        

        $dtini->setDatabaseMask('yyyy-mm-dd');        
        $dtfin->setDatabaseMask('yyyy-mm-dd');    
        
           /* Mini dashboard */ 
           $total_apon   = $this->getTotalVolume($dtini->getPostData(),    $dtfin->getPostData());
           $total_cat1   = $this->getVolumeCategoria('LIMPA VIDROS',       $dtini->getPostData(), $dtfin->getPostData());
           $total_cat2   = $this->getVolumeCategoria('DESENGORDURANTE',    $dtini->getPostData(), $dtfin->getPostData());
           $total_cat3   = $this->getVolumeCategoria('LIMPADOR PERFUMADO', $dtini->getPostData(), $dtfin->getPostData());
           $total_cat4   = $this->getVolumeCategoria('ALVEJANTE',          $dtini->getPostData(), $dtfin->getPostData());
           $total_cat5   = $this->getVolumeCategoria('MULTI-USO',          $dtini->getPostData(), $dtfin->getPostData());
           $total_cat6   = $this->getVolumeCategoria('REMOVEDOR',          $dtini->getPostData(), $dtfin->getPostData());
           $total_cat7   = $this->getVolumeCategoria('AMACIANTE',          $dtini->getPostData(), $dtfin->getPostData());
           $total_cat8   = $this->getVolumeCategoria('LAVA ROUPAS',        $dtini->getPostData(), $dtfin->getPostData());
           $total_cat9   = $this->getVolumeCategoria('DESINFETANTE',       $dtini->getPostData(), $dtfin->getPostData());
           $total_cat10  = $this->getVolumeCategoria('LAVA LOUCAS',        $dtini->getPostData(), $dtfin->getPostData());
           $total_cat11  = $this->getVolumeCategoria('AGUA SANITARIA',     $dtini->getPostData(), $dtfin->getPostData());

           $vbox = new TVBox;
           $vbox->style = 'width: 100%';
           
           $div = new TElement('div');
           $div->class = "row";
           
           $indicator1  = new THtmlRenderer('app/resources/info-box.html');        
           $indicator2  = new THtmlRenderer('app/resources/info-box.html');
           $indicator3  = new THtmlRenderer('app/resources/info-box.html');
           $indicator4  = new THtmlRenderer('app/resources/info-box.html');
           $indicator5  = new THtmlRenderer('app/resources/info-box.html');        
           $indicator6  = new THtmlRenderer('app/resources/info-box.html');
           $indicator7  = new THtmlRenderer('app/resources/info-box.html');
           $indicator8  = new THtmlRenderer('app/resources/info-box.html');
           $indicator9  = new THtmlRenderer('app/resources/info-box.html');        
           $indicator10 = new THtmlRenderer('app/resources/info-box.html');
           $indicator11 = new THtmlRenderer('app/resources/info-box.html');
           $indicator12 = new THtmlRenderer('app/resources/info-box.html');
           
           
           $indicator1->enableSection('main', ['title'     => 'LIMPA VIDROS',
           'icon'       => 'box',
           'background' => 'green',
           'value'      =>  number_format((float)$total_cat1, 0, ',', '.'), ] );

           $indicator2->enableSection('main', ['title'      => 'DESENGORDURANTE',
          'icon'       => 'box',
          'background' => 'green',
          'value'      => number_format((float)$total_cat2, 0, ',', '.'), ] );
   
           $indicator3->enableSection('main', ['title'      => 'LIMPADOR PERFUMADO',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat3, 0, ',', '.'), ] );


           $indicator4->enableSection('main', ['title'      => 'ALVEJANTE',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat4, 0, ',', '.'), ] );
       
           $indicator5->enableSection('main', ['title'      => 'MULTI-USO',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat5, 0, ',', '.'), ] );

           $indicator6->enableSection('main', ['title'      => 'REMOVEDOR',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat6, 0, ',', '.'), ] );

           $indicator7->enableSection('main', ['title'      => 'AMACIANTE',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat7, 0, ',', '.'), ] );

           $indicator8->enableSection('main', ['title'      => 'LAVA ROUPAS',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat8, 0, ',', '.'), ] );

           $indicator9->enableSection('main', ['title'      => 'DESINFETANTE',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat9, 0, ',', '.'), ] );


           $indicator10->enableSection('main', ['title'      => 'LAVA LOUCAS',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat10, 0, ',', '.'), ] );

           $indicator11->enableSection('main', ['title'      => 'AGUA SANITARIA',
           'icon'       => 'box',
           'background' => 'green',
           'value'      => number_format((float)$total_cat11, 0, ',', '.'), ] );

           
           $indicator12->enableSection('main', ['title'      => 'TOTAL APONTADO',
           'icon'       => 'box',
           'background' => 'orange',
           'value'      => number_format((float)$total_apon, 0, ',', '.'), ] );
  
       
           $div->add( $i1 = TElement::tag('div',  $indicator1) );           
           $div->add( $i2 = TElement::tag('div',  $indicator2) );
           $div->add( $i3 = TElement::tag('div',  $indicator3) );
           $div->add( $i4 = TElement::tag('div',  $indicator4) );           
           $div->add( $i5 = TElement::tag('div',  $indicator5) );         
           $div->add( $i6 = TElement::tag('div',  $indicator6) );    
           $div->add( $i7 = TElement::tag('div',  $indicator7) );    
           $div->add( $i8 = TElement::tag('div',  $indicator8) );    
           $div->add( $i9 = TElement::tag('div',  $indicator9) );    
           $div->add( $i10 = TElement::tag('div', $indicator10) );    
           $div->add( $i11 = TElement::tag('div', $indicator11) );    
           $div->add( $i12 = TElement::tag('div', $indicator12) );    


           $i1->class = 'col-sm-3';
           $i2->class = 'col-sm-3';
           $i3->class = 'col-sm-3';
           $i4->class = 'col-sm-3';
           $i5->class = 'col-sm-3';
           $i6->class = 'col-sm-3';
           $i7->class = 'col-sm-3';           
           $i8->class = 'col-sm-3'; 
           $i9->class = 'col-sm-3'; 
           $i10->class = 'col-sm-3'; 
           $i11->class = 'col-sm-3'; 
           $i12->class = 'col-sm-3'; 
           $vbox->add($div);

           /*Fim dashboard */
         
      
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

        $column_op          = new TDataGridColumn('OP',        'O.P.', 'left');
        $column_lote        = new TDataGridColumn('LOTE',      'Lote', 'left');
        $column_codprod     = new TDataGridColumn('CODPROD',   'Codigo', 'left');
        $column_produto     = new TDataGridColumn('PRODUTO',   'Item', 'left');
        $column_tipo        = new TDataGridColumn('TIPO',      'Tipo', 'left');
        $column_qtde        = new TDataGridColumn('QTDE',      'Qtde.', 'left');
        //$column_operacao    = new TDataGridColumn('OPERACAO',  'O.P.', 'left');
       // $column_usuario     = new TDataGridColumn('USUARIO',   'O.P.', 'left');
        $column_familia     = new TDataGridColumn('FAMILIA',   'Familia', 'left');
       // $column_marca       = new TDataGridColumn('MARCA',     'O.P.', 'left');
        $column_cat         = new TDataGridColumn('CATEGORIA', 'Cat.', 'left');  
        $column_subcat      = new TDataGridColumn('SUBCATEGORIA', 'Sub.Cat', 'left');
        $column_emissao     = new TDataGridColumn('EMISSAO', 'Emissão', 'left');
        $column_hra         = new TDataGridColumn('HRA', 'Hra.', 'left');
        
        
        $column_emissao->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        // add the columns to the DataGrid  
        $this->datagrid->addColumn($column_op);
        $this->datagrid->addColumn($column_lote);
        $this->datagrid->addColumn($column_codprod);
        $this->datagrid->addColumn($column_produto);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_familia);
        $this->datagrid->addColumn($column_cat);
        $this->datagrid->addColumn($column_subcat);
        $this->datagrid->addColumn($column_emissao);
        $this->datagrid->addColumn($column_hra);


        // creates the datagrid column actions        
        $column_op->setAction(new TAction([$this, 'onReload']), ['order' => 'OP']);
        $column_hra->setAction(new TAction([$this, 'onReload']), ['order' => 'HRA']);
        $column_codprod->setAction(new TAction([$this, 'onReload']), ['order' => 'CODPROD']);        
        $column_produto->setAction(new TAction([$this, 'onReload']), ['order' => 'PRODUTO']);

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
        $container->add($vbox);
        $container->add($panel);
        
        parent::add($container);
    }

     /**
     * getTotalVolume
     * @param mixed 
     * @return string
     */
    public function getTotalVolume($dtini, $dtfin)
    {   
        
        $query = "SELECT SUM(QTDE) AS VOLUME
                  FROM GETAPONPRD
                  WHERE EMISSAO BETWEEN '{$dtini}' AND '{$dtfin}' ";
        
        try 
        {
            TTransaction::open('protheus');
            $conn = TTransaction::get();
            $result = $conn->query($query);
            
            $fat = new StdClass;
            foreach ($result as $res) 
            {
                 $fat->VOLUME  = $res['VOLUME'];
            }
          
            return (string)$fat->VOLUME;
            
            TTransaction::close();
        } catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }

    }

    
     /**
     * getTotalVolume
     * @param mixed 
     * @return string
     */
    public function getVolumeCategoria($categoria, $dtini, $dtfin)
    {   
        
        $query = "SELECT SUM(QTDE) AS VOLUME
                  FROM GETAPONPRD
                  WHERE EMISSAO BETWEEN '{$dtini}' AND '{$dtfin}' 
                  AND CATEGORIA = '{$categoria}'  
                  ";

                 
        
        try 
        {
            TTransaction::open('protheus');
            $conn = TTransaction::get();
            $result = $conn->query($query);
            
            $fat = new StdClass;
            $fat->VOLUME = '';
            foreach ($result as $res) 
            {
                 $fat->VOLUME  = $res['VOLUME'];
            }
          
            return (string)$fat->VOLUME;
            
            TTransaction::close();
        } catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }

    }
    
}
