
<?php

    ini_set('log_errors','On');
    ini_set('display_errors','Off');
    ini_set('error_reporting', E_ALL );
    define('WP_DEBUG', false);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);


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
 * TProgEmbarque
 * @version    1.0
 * @package    logistica
 * @subpackage pallet
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TProgEmbarque extends TPage
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

        $this->setDatabase('protheus');                 // defines the database
        $this->setActiveRecord('ProgEmb');           // defines the active record
    
        $this->setDefaultOrder('DTCARR', 'DESC');        
        // defines the default order
        $this->setLimit(100);

       
        /* Carga Inicial dia menos 3 (Validar com operação) 
            Ajustar conforme acompanhamento e monitoramento de uso da rotina.
         */ 
       $dataAnt = date('Y-m-d', strtotime('-4 days'));
       $day =  date('Y-m-d', strtotime('+7 days'));

        $criteria = new TCriteria;
        $criteria->setProperty('order', 'ROMANEIO');
        $criteria->setProperty('direction', 'desc');                 
        $criteria->add(new TFilter('DTCARR', 'BETWEEN', $dataAnt, $day));
      

        $this->setCriteria($criteria);                 // define a standard filter
   
        //$campoData->setMask('dd/mm/yyyy');            

        $this->addFilterField('ROMANEIO', 'like', 'ROMANEIO');   // filterField, operator, formField
        $this->addFilterField('CODTRANSP',   '=', 'CODTRANSP');      // filterField, operator, formField
        $this->addFilterField('CLIENTE',  'like', 'CLIENTE'); // filterField, operator, formField
        $this->addFilterField('DTCARR',   '>=',  'DTCARR'); // filterField, operator, formField
        $this->addFilterField('DTCARR',   '<=',  'DTCARR1'); // filterField, operator, formField
        $this->addFilterField('STATUS',   'like',  'STATUS'); // filterField, operator, formField
        

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_mov_pallet');
        $this->form->setFormTitle('Monitoramento - Programação de Embarque');

        // create the form fields

        $ROMANEIO   = new TDBSeekButton('ROMANEIO', 'protheus', 'form_search_mov_pallet', 'Romaneio', 'ROMANEIO');
      //  $ROMANEIO->setDisplayMask('{ZZQ_ROMANE} - {ZZQ_DESTRA}  ');
        //$ROMANEIO->setDisplayLabel('Transportadora');  

        $CODTRANSP   = new TDBSeekButton('CODTRANSP', 'protheus', 'form_search_mov_pallet', 'Transp', 'A4_NOME');
        $CODTRANSP->setDisplayMask('{A4_NOME}');
        $CODTRANSP->setDisplayLabel('Transportadora');    
        $trasp = new TEntry('Transp');
        $CODTRANSP->setAuxiliar($trasp);

        $DTINI = new TDate('DTCARR');
        $DTFIN = new TDate('DTCARR1');

        $DTINI->setValue(date('Y-m-d'));
        $DTFIN->setValue(date('Y-m-d'));

    
                /* Mini dashboard */ 
                $per_plano_atingido = 1;
                $total_em_romaneio  = $this->getTotalVolume('EM ROMANEIO',$DTINI->getPostData(), $DTFIN->getPostData());
                $total_faturado     = $this->getTotalVolume('FATURADO',$DTINI->getPostData(), $DTFIN->getPostData());
                $total              = (float)$total_faturado + (FLOAT)$total_em_romaneio;
                $per_plano_atingido = ($total_faturado / $total) * 100;

                $vbox = new TVBox;
                $vbox->style = 'width: 100%';
                
                $div = new TElement('div');
                $div->class = "row";
                
                $indicator1 = new THtmlRenderer('app/resources/info-box.html');
                $indicator2 = new THtmlRenderer('app/resources/info-box.html');
                $indicator3 = new THtmlRenderer('app/resources/info-box.html');
                $indicator4 = new THtmlRenderer('app/resources/info-box.html');
                
                
                $indicator1->enableSection('main', ['title'     => 'Em romaneio',
                                                   'icon'       => 'box',
                                                   'background' => 'orange',
                                                   'value'      =>  number_format((float)$total_em_romaneio, 0, ',', '.'), ] );

                $indicator2->enableSection('main', ['title'      => 'Faturado',
                                                    'icon'       => 'box',
                                                    'background' => 'green',
                                                    'value'      => number_format((float)$total_faturado, 0, ',', '.'), ] );
        
                $indicator3->enableSection('main', ['title'      => 'Total',
                'icon'       => 'box',
                'background' => 'red',
                'value'      => number_format((float)$total, 0, ',', '.'), ] );

                $indicator4->enableSection('main', ['title'      => '% Realiz.',
                'icon'       => 'box',
                'background' => 'green',
                'value'      => number_format((float)$per_plano_atingido, 0, ',', '.') . '%', ] );
        
                $div->add( $i1 = TElement::tag('div', $indicator1) );
                $div->add( $i2 = TElement::tag('div', $indicator2) );
                $div->add( $i3 = TElement::tag('div', $indicator3) );
                $div->add( $i4 = TElement::tag('div', $indicator4) );
         
                $i1->class = 'col-sm-3';
                $i2->class = 'col-sm-3';
                $i3->class = 'col-sm-3';
                $i4->class = 'col-sm-3';
        
                $vbox->add($div);
                /*Fim dashboard */

        $status =  new TCombo('STATUS');

        $items = [ 'FATURADO'    => 'FATURADO', 
                   'EM ROMANEIO' => 'EM ROMANEIO',
                   'ENTREGUE'    => 'ENTREGUE' 
                ];
        $status->addItems($items);
       
        // add the fields        
        $this->form->addFields( [ new TLabel('Romaneio') ],  [ $ROMANEIO ] );
        $this->form->addFields( [ new TLabel('Cod.Transp') ],[ $CODTRANSP] );
        $this->form->addFields( [ new TLabel('Dt.Inicial') ],[ $DTINI] );
        $this->form->addFields( [ new TLabel('Dt.Final') ],  [ $DTFIN] );
        $this->form->addFields( [ new TLabel('Status') ],  [ $status] );
                    
        // set sizes
        $ROMANEIO->setSize('50%');
        $CODTRANSP->setSize('10%');
        $trasp  ->setSize('40%');
        $DTINI->setSize('50%');
        $DTFIN->setSize('50%');
        $status->setSize('50%');
        $trasp->setEditable(FALSE);
        // keep the form filled during navigation with session data
         $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
          $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
          $btn->class = 'btn btn-sm btn-primary';
          $this->form->addActionLink(_t('New'), new TAction(['PalletMovForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
       // $this->datagrid->enablePopover('Obs:', ' <b> {OBSTRANSP} </b>');   
         
        // creates the datagrid columns

        $column_rom     = new TDataGridColumn('ROMANEIO', 'Romaneio', 'left');
        $column_emp     = new TDataGridColumn('EMPRESA', 'Emp.', 'left');
        $column_nf      = new TDataGridColumn('NFISCAL', 'NFiscal.', 'left');
        $column_tipo    = new TDataGridColumn('TIPO', 'Tipo', 'left');
        $column_pv      = new TDataGridColumn('PEDVEN', 'Ped.Ven', 'left');
        $column_tranp   = new TDataGridColumn('TRANSP',   'Transp.',  'left');

        $column_placa   = new TDataGridColumn('PLACA', 'Placa', 'left');
        $column_veic    = new TDataGridColumn('TPVEIC', 'Veic.', 'left');

        $column_cliente = new TDataGridColumn('CLIENTE',  'Cliente.', 'left');
        $column_uf      = new TDataGridColumn('ESTADO', 'UF', 'left');
        $column_mun     = new TDataGridColumn('CIDADE', 'Cidade', 'left');
        $column_status  = new TDataGridColumn('STATUS',   'Status.',  'left');
        $column_dtcarr  = new TDataGridColumn('DTCARR',   'Dt.Carr.', 'left');
        $column_obs     = new TDataGridColumn('OBSTRANSP', 'Obs. Transp.', 'left');

      //  $column_dtcarr->setTransformer(array($this, 'formatDate'));

        $column_dtnf    = new TDataGridColumn('DTNF',     'DT.NF.',   'left');
      //  $column_dtnf->setTransformer(array($this, 'formatDate'));

        $column_dtpor   = new TDataGridColumn('DTSAIDAPORTARIA',  'Dt.Sai.Portaria',   'left');
        $column_hr      = new TDataGridColumn('HRSAIDA',  'Hr.Saída',   'left');
        $column_vol     = new TDataGridColumn('VOLUME',  'Volume',   'left');
        
    
        // add the columns to the DataGrid  

        $this->datagrid->addColumn($column_rom);

        $this->datagrid->addColumn($column_tranp);
        $this->datagrid->addColumn($column_cliente);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_mun);        

        $this->datagrid->addColumn($column_status);
        $this->datagrid->addColumn($column_dtcarr);
        $this->datagrid->addColumn($column_dtnf);
        $this->datagrid->addColumn($column_dtpor);
        $this->datagrid->addColumn($column_hr);
        $this->datagrid->addColumn($column_vol);
        
        $this->datagrid->addColumn($column_emp);  
        $this->datagrid->addColumn($column_nf);    
        $this->datagrid->addColumn($column_tipo);   
        $this->datagrid->addColumn($column_pv);     
        $this->datagrid->addColumn($column_placa);
        $this->datagrid->addColumn($column_veic);  
        $this->datagrid->addColumn($column_obs);
        
    
        $column_dtcarr->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $column_dtnf->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $column_dtpor->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        // creates the datagrid column actions        
        $column_rom->setAction(new TAction([$this, 'onReload']), ['order' => 'ROMANEIO']);
        $column_tranp->setAction(new TAction([$this, 'onReload']), ['order' => 'TRANSP']);
        $column_cliente->setAction(new TAction([$this, 'onReload']), ['order' => 'CLIENTE']);
        $column_status->setAction(new TAction([$this, 'onReload']), ['order' => 'STATUS']);
        $column_dtcarr->setAction(new TAction([$this, 'onReload']), ['order' => 'DTCARR']);
        $column_dtnf->setAction(new TAction([$this, 'onReload']), ['order' => 'DTNF']);
        $column_dtpor->setAction(new TAction([$this, 'onReload']), ['order' => 'DTSAIDAPORTARIA']);
    

       // $action1 = new TDataGridAction(['PalletMovForm', 'onEdit'], ['ROMANEIO'=>'{ROMANEIO}', 'register_state' => 'false']);
       // $action2 = new TDataGridAction( [$this, 'onPrint'], ['ROMANEIO'=>'{ROMANEIO}']);
        
       // $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
       //$this->datagrid->addAction($action2 ,_t('Activate/Deactivate'), 'fa:power-off orange');
       // $this->datagrid->addAction($action2 ,'Imprimir', 'fa:print');
        
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

           if (!empty($transp->A4_NOME))
           {
             $content = $transp->A4_NOME;   
           }
           else
           {
            $content = '';
           }
                            
      
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
     * getTotalVolume
     * @param mixed 
     * @return string
     */
    public function getTotalVolume($status, $dtini, $dtfin)
    {   
        
        $query = "SELECT SUM(VOLUME) AS VOLUME
                  FROM GETPROGROM
                  WHERE STATUS ='{$status}'                  
                  AND DTCARR BETWEEN '{$dtini}' AND '{$dtfin}' ";
        
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
