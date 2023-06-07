<?php
use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TTable;
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
 * TCarteira
 * @version    1.0
 * @package    comercial
 * @subpackage Pedidos de Venda - carteira de pedidos
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */


 class TCarteira extends TPage
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
        $this->setDataBase('protheus');
        $this->setActiveRecord('CarteiraPedidos');
        //$this->setDefaultOrder('NUMPV', 'ASC');        
        $this->setLimit(100);


        $this->addFilterField('NUMPV',  'like', 'NUMPV'); // filterField, operator, formField      
        $this->addFilterField('STATUS',  'like', 'STATUS'); // filterField, operator, formField        
        $this->addFilterField('SUP',  'like', 'SUP'); // filterField, operator, formField
        $this->addFilterField('CODVEND',  'like', 'CODVEND'); // filterField, operator, formField
        $this->addFilterField('CODREDE',  'like', 'CODREDE'); // filterField, operator, formField
        $this->addFilterField('CODCLI',  'like', 'CODCLI'); // filterField, operator, formField
        $this->addFilterField('CANAL',  'like', 'CANAL'); // filterField, operator, formField
        $this->addFilterField('EMP',  'like', 'EMP'); // filterField, operator, formField

         // cria o form
        $this->form = new BootstrapFormBuilder('form_cart');
        $this->form->setFormTitle('Monitoramento - Carteira de Pedidos');        
          

        $sup =  new TCombo('SUP');

        $sups = [ 'MARCOS-000284'         => 'MARCOS', 
                   'EDER-ESTADOS-000099'  => 'EDER-ESTADOS',
                   'REDES-000002'         => 'REDES', 
                   'GILBERTO-000597'      => 'EDSON II', 
                   'EDSON-000500'         => 'EDSON', 
                   'CALDER-000628'        => 'CALDER', 
                   'VICTOR-000023'        => 'VICTOR',
                   'MARCIO FAVARI-900001' => 'MARCIO FAVARI', 
                   'MOMESSO-000021'       => 'MOMESSO', 
                ];
        $sup->addItems($sups);

        $status =  new TCombo('STATUS');

        $items = [ 'FATURADO'      => 'FATURADO', 
                   'CREDITO'      => 'CREDITO', 
                   'AGENDAMENTO'  => 'AGENDAMENTO',
                   'LIB.ROMANEIO' => 'LIB.ROMANEIO', 
                   'ADM.VENDAS'   => 'ADM.VENDAS', 
                   'ROMANEIO'     => 'ROMANEIO', 
                   'REJEITADO'    => 'REJEITADO' 
                ];
        $status->addItems($items);


        
        $canal =  new TCombo('CANAL');

        $canais = [ 'DISTRIBUIDOR' => 'DISTRIBUIDOR', 
                   'VAREJO'        => 'VAREJO',
                   'C&C'           => 'C&C', 
                   'ATACADO'       =>  'ATACADO', 
                   'REDE'          => 'REDE', 
                   'KEY ACCOUNT'   => 'KEY ACCOUNT  ' 
                ];
        $canal->addItems($canais);

        
        $empresa = new TCombo('EMP');

        $empresas = [ 'SOBEL' => 'SOBEL', 
                      'JMT'   => 'JMT',
                       '3F'    => '3F', 
                     ];

        $empresa->addItems($empresas);

        $vend = new TDBSeekButton('CODVEND', 'protheus', 'form_cart', 'Vendedores', 'A3_NOME');
        $vend->setDisplayMask('{A3_NOME}');
        $vend->setDisplayLabel('Vendedor');    
        
        
        $vendedor = new TEntry('VEND');
        $vend->setAuxiliar($vendedor);
       

        $rede = new TDBSeekButton('CODREDE', 'protheus', 'form_cart', 'Redes', 'REDE');
        $rede->setDisplayMask('{REDE}');
        $rede->setDisplayLabel('Rede');    
                
        $redes = new TEntry('REDES');
        $rede->setAuxiliar($redes);

        $codPedido = new TEntry('NUMPV');

        $cliente = new TDBSeekButton('CODCLI', 'protheus', 'form_cart', 'Clientes', 'CLIENTE');
        $cliente->setDisplayMask('{CLIENTE}');
        $cliente->setDisplayLabel('Cliente');    
        
        
        $clientes = new TEntry('CLIENTES');
        $cliente->setAuxiliar($clientes);
      
        // add the fields       
        $this->form->addFields( [ new TLabel('Num.Pedido') ],[ $codPedido] );     
        $this->form->addFields( [ new TLabel('Empresa') ],[ $empresa] );     
        $this->form->addFields( [ new TLabel('Supervisor') ],[ $sup] );
        $this->form->addFields( [ new TLabel('Vendedor') ],[ $vend] );
   //     $this->form->addFields( [ new TLabel('Vendedor') ],[ $vendedor] );
        $this->form->addFields( [ new TLabel('Status') ],[ $status] );
        $this->form->addFields( [ new TLabel('Redes') ],[ $rede] );        
        $this->form->addFields( [ new TLabel('Canal') ],[ $canal] );        
        $this->form->addFields( [ new TLabel('Cliente') ],[ $cliente] );

        // set sizes
        $codPedido->setSize('20%');
        $status->setSize('20%');
        $empresa->setSize('20%');
        $canal->setSize('20%');
        $sup->setSize('20%');
        $vend->setSize('10%');
        $vendedor->setSize('30%'); 
        $vendedor->setEditable(FALSE);

        $rede->setSize('10%'); 
        $redes->setSize('30%'); 
        $redes->setEditable(FALSE);

        $cliente->setSize('10%'); 
        $clientes->setSize('30%'); 
        $clientes->setEditable(FALSE);


        // keep the form filled during navigation with session data
         $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->style = 'min-width: 1900px';        
        $this->datagrid->datatable = 'true';

    
        $emp           = new TDataGridColumn('EMP', 'Emp', 'left');
        $numpv         = new TDataGridColumn('NUMPV', 'Num.PV', 'left');
        $tppv          = new TDataGridColumn('TPPV', 'Tipo', 'left');
     //   $datapedido    = new TDataGridColumn('DATAPEDIDO', 'DataPortal', 'left');
        $dataEmissao   = new TDataGridColumn('EMISSAO', 'DataERP', 'left');
        $dtAgenda      = new TDataGridColumn('DTAGENDA',  '1ª Agenda', 'left');
        $dtAgenda2     = new TDataGridColumn('DTAGENDA1', '2ª Agenda', 'left');
        $dtAgenda3     = new TDataGridColumn('DTAGENDA2', '3ª Agenda', 'left');
        $dtCarr        = new TDataGridColumn('DTCARR', 'Prog.Emb', 'left');
        $status        = new TDataGridColumn('STATUS', 'Status', 'left');
        $cliente       = new TDataGridColumn('CLIENTE', 'Cliente', 'left');
        $sup           = new TDataGridColumn('SUP', 'Sup', 'left');
        $vend          = new TDataGridColumn('VEND', 'vend', 'left');
        $qtde          = new TDataGridColumn('QTDE', 'Qtde', 'left');
        $pesobruto     = new TDataGridColumn('PESOBRUTO', 'Peso bruto', 'left');
        $pesoliq       = new TDataGridColumn('PESOLIQ', 'Peso Liq.', 'left');
        $valorTabela   = new TDataGridColumn('VALORTABELA', 'Vlr Tab.', 'left');
      //  $Desc          = new TDataGridColumn('DESC', '', 'left');
        $vlVenda       = new TDataGridColumn('VLVENDA', 'Vlr Venda', 'left');
        $uf            = new TDataGridColumn('UF', 'UF', 'left');
        $Mun           = new TDataGridColumn('MUN', 'Mun.', 'left');
        $rede          = new TDataGridColumn('REDE', 'Rede', 'left');
        $canal         = new TDataGridColumn('CANAL', 'Canal', 'left');

        $tpfrete         = new TDataGridColumn('TPFRETE', 'Frete', 'left');
        $nfiscal         = new TDataGridColumn('NFISCAL', 'NFiscal', 'left');
    //    $emissaonf       = new TDataGridColumn('EMISSAONF', 'Emiss.NF', 'left');
    //    $chavenfe        = new TDataGridColumn('CHAVENFE', 'ChvNFE', 'left');
        $codtransp       = new TDataGridColumn('CODTRANSP', 'cod.Transp', 'left');
        $tpveic          = new TDataGridColumn('TPVEIC', 'Veículo', 'left');
        $dtsaidaportaria = new TDataGridColumn('DTSAIDAPORTARIA', 'Saída Portaria', 'left');
        $hrsaida         = new TDataGridColumn('HRSAIDA', 'Hr.Saída', 'left');
        $placa           = new TDataGridColumn('PLACA', 'Placa', 'left');
        $transp          = new TDataGridColumn('TRANSP', 'Transp', 'left');

        // add the columns to the DataGrid  
        $this->datagrid->addColumn($emp);          
        $this->datagrid->addColumn($numpv);        
        $this->datagrid->addColumn($tppv);      
   //     $this->datagrid->addColumn($datapedido);   
        $this->datagrid->addColumn($dataEmissao);  
        $this->datagrid->addColumn($dtAgenda);     
        $this->datagrid->addColumn($dtAgenda2);    
        $this->datagrid->addColumn($dtAgenda3);   
        $this->datagrid->addColumn($dtCarr);       
        $this->datagrid->addColumn($status);       
        $this->datagrid->addColumn($cliente);      
        $this->datagrid->addColumn($sup);          
        $this->datagrid->addColumn($vend);         
        $this->datagrid->addColumn($qtde);         
        $this->datagrid->addColumn($pesobruto);
        $this->datagrid->addColumn($pesoliq);      
        $this->datagrid->addColumn($valorTabela);
    //    $this->datagrid->addColumn($Desc);         
        $this->datagrid->addColumn($vlVenda);      
        $this->datagrid->addColumn($uf);           
        $this->datagrid->addColumn($Mun);          
        $this->datagrid->addColumn($rede);        
        $this->datagrid->addColumn($canal);     
        $this->datagrid->addColumn($tpfrete);           
        $this->datagrid->addColumn($nfiscal);           
      //  $this->datagrid->addColumn($emissaonf);         
      //  $this->datagrid->addColumn($chavenfe);          
        $this->datagrid->addColumn($codtransp);         
        $this->datagrid->addColumn($tpveic);            
        $this->datagrid->addColumn($dtsaidaportaria);   
        $this->datagrid->addColumn($hrsaida);           
        $this->datagrid->addColumn($placa);             
        $this->datagrid->addColumn($transp);            
        
         // add the actions
         $action1 = new TDataGridAction([$this, 'onView'],   ['EMP' => '{EMP}', 'NUMPV' => '{NUMPV}' ] );
         $this->datagrid->addAction($action1, 'Input', 'fa:search gray');
         

         // add the actions
         $action2 = new TDataGridAction([$this, 'onXml'],   ['EMP' => '{EMP}', 'NFISCAL' => '{NFISCAL}' ] );
         $this->datagrid->addAction($action2, 'XML', 'fas:external-link-alt');

        $pesobruto->setTransformer( function($value) {
            return number_format($value, 2, ',', '.');

        });

        
        $pesoliq->setTransformer( function($value) {
            
            return number_format($value, 2, ',', '.');

        });

        
        $valorTabela->setTransformer( function($value) {
            
            return number_format((float)$value, 2, ',', '.');

        });
        
        
        $vlVenda->setTransformer( function($value) {
            return number_format($value, 2, ',', '.');

        });

        /*
        $datapedido->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });
         */

         
        $dataEmissao->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });


        $dtsaidaportaria->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

     /*   $emissaonf->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        }); */


        $dtAgenda->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });

        $dtAgenda2->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });
        

        $dtAgenda3->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });
        
        $dtCarr->setTransformer( function($value) {
            return TDate::convertToMask($value, 'yyyy-mm-dd', 'dd/mm/yyyy');
        });
        
        
        // creates the datagrid column actions        
        $numpv->setAction(new TAction([$this, 'onReload']), ['order' => 'NUMPV']);

       // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        

         // search box
         $input_search = new TEntry('input_search');
         $input_search->placeholder = _t('Search');
         $input_search->setSize('100%');
         
         // enable fuse search by column name
         $this->datagrid->enableSearch($input_search, 'CLIENTE, VEND, SUP');

        $panel = new TPanelGroup('Pedido de venda', 'white');
      //  $panel->setTitle("Itens do Pedido");
        $panel->addHeaderWidget($input_search);
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

         // turn on horizontal scrolling inside panel body
        //   $panel->getBody()->style = "overflow-x:auto;";
        
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

     
    /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    public static function onView($param)
    {
        $numpedido = $param['NUMPV'];
        $emp = $param['EMP'];
        
        $win = TWindow::create('Pedido de Venda', 0.9, 0.9);


         // creates a Datagrid
        $win->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        $numpv       = new TDataGridColumn('NUMPV', 'Pedido', 'left');
        $item        = new TDataGridColumn('ITEM', 'Item', 'left');
        $codprod     = new TDataGridColumn('CODPROD', 'CodProd', 'left'); 
        $produto     = new TDataGridColumn('PRODUTO', 'Produto', 'left');
        $und         = new TDataGridColumn('UND', 'Und', 'left');
        $qtde        = new TDataGridColumn('QTDE', 'Qtde', 'left');
        $vlrtab      = new TDataGridColumn('VLRTAB', 'Valor.Tab', 'left');
        $desconto    = new TDataGridColumn('DESCONTO', 'Desconto %', 'left');
        $valorvenda  = new TDataGridColumn('VALORVENDA', 'Valor Venda', 'left');
        $prcven      = new TDataGridColumn('PRCVEN', 'Valor Unit.', 'left');
        $total       = new TDataGridColumn('TOTAL', 'Total', 'left');
        $hashpt      = new TDataGridColumn('HASHPT', 'Portal', 'left');
       
       
        $win->datagrid->addColumn($numpv);  
        $win->datagrid->addColumn($item);  
        $win->datagrid->addColumn($codprod);  
        $win->datagrid->addColumn($produto);  
        $win->datagrid->addColumn($und);  
        $win->datagrid->addColumn($qtde);  
        $win->datagrid->addColumn($vlrtab);  
        $win->datagrid->addColumn($desconto);  
        $win->datagrid->addColumn($valorvenda);  
        $win->datagrid->addColumn($prcven);  
        $win->datagrid->addColumn($total);  
        $win->datagrid->addColumn($hashpt);  

         // create the datagrid model
         $win->datagrid->createModel();
        
         // creates the page navigation
         //$win->pageNavigation = new TPageNavigation;
         //$win->pageNavigation->setAction(new TAction([$win, 'onReload']));
         
 
          // search box
          $input_search = new TEntry('input_search');
          $input_search->placeholder = _t('Search');
          $input_search->setSize('100%');
          
          // enable fuse search by column name
          $win->datagrid->enableSearch($input_search, 'CODPROD, PRODUTO');
 
         $panel = new TPanelGroup('Itens do Pedido', 'white');
         $panel->addHeaderWidget($input_search);         
         $panel->add($win->datagrid);
     //    $panel->addFooter($win->pageNavigation);

       /* Criar componente datagrid para a tela de itens do pedido */
        switch ($emp) 
        {
            case "SOBEL":

                try
                {
                    
                    TTransaction::open('protheus'); // open transaction
                    $criteria = new TCriteria;

                    // define criteria properties
                    $criteria->add(new TFilter('NUMPV', '=', $numpedido));
                    $criteria->setProperty('order' , 'ITEM');
                    
                    $repository = new TRepository('PedidoItensSobel');
                    $itens = $repository->load($criteria);
                    
                    foreach ($itens as $iten)
                    {
                        $item = new StdClass;
                        $item->NUMPV      = $iten->NUMPV;
                        $item->ITEM       = $iten->ITEM;
                        $item->CODPROD    = $iten->CODPROD;
                        $item->PRODUTO    = $iten->PRODUTO;
                        $item->UND        = $iten->UND;
                        $item->QTDE       = $iten->QTDE;
                        $item->VLRTAB     = number_format($iten->VLRTAB, 2, ',', '.');
                        $item->DESCONTO   = number_format($iten->DESCONTO, 2, ',', '.');
                        $item->VALORVENDA = number_format($iten->VALORVENDA,2, ',', '.');
                        $item->PRCVEN     = number_format($iten->PRCVEN,2, ',', '.');
                        $item->TOTAL      = number_format($iten->TOTAL,2, ',', '.');
                        $item->HASHPT     = $iten->HASHPT;
                                            
                        $win->datagrid->addItem($item);
                    }
                    TTransaction::close(); // close transaction
                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }
                
                break;
            case "3F":

                try
                {
                    TTransaction::open('protheus'); // open transaction
                    $criteria = new TCriteria;

                    // define criteria properties
                    $criteria->setProperty('order' , 'ITEM');
                    $criteria->add(new TFilter('NUMPV', '=', $numpedido));

                    $repository = new TRepository('PedidoItens3F');
                    $itens = $repository->load($criteria);
                    
                    foreach ($itens as $iten)
                    {
                        $item = new StdClass;
                        $item->NUMPV      = $iten->NUMPV;
                        $item->ITEM       = $iten->ITEM;
                        $item->CODPROD    = $iten->CODPROD;
                        $item->PRODUTO    = $iten->PRODUTO;
                        $item->UND        = $iten->UND;
                        $item->QTDE       = $iten->QTDE;
                        $item->VLRTAB     = number_format($iten->VLRTAB, 2, ',', '.');
                        $item->DESCONTO   = number_format($iten->DESCONTO, 2, ',', '.');
                        $item->VALORVENDA = number_format($iten->VALORVENDA,2, ',', '.');
                        $item->PRCVEN     = number_format($iten->PRCVEN,2, ',', '.');
                        $item->TOTAL      = number_format($iten->TOTAL,2, ',', '.');
                        $item->HASHPT     = $iten->HASHPT;
                                            
                        $win->datagrid->addItem($item);
                    }
                    TTransaction::close(); // close transaction
                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }
                
                break;

            case "JMT":

                try
                {
                    TTransaction::open('protheus'); // open transaction
                    $criteria = new TCriteria;

                    // define criteria properties
                    $criteria->setProperty('order' , 'ITEM');
                    $criteria->add(new TFilter('NUMPV', '=', $numpedido));
                    
                    $repository = new TRepository('PedidoItensJMT');
                    $itens = $repository->load($criteria);
                    
                    foreach ($itens as $iten)
                    {                                              
                       $item = new StdClass;
                       $item->NUMPV      = $iten->NUMPV;
                       $item->ITEM       = $iten->ITEM;
                       $item->CODPROD    = $iten->CODPROD;
                       $item->PRODUTO    = $iten->PRODUTO;
                       $item->UND        = $iten->UND;
                       $item->QTDE       = $iten->QTDE;
                       $item->VLRTAB     = number_format($iten->VLRTAB, 2, ',', '.');
                       $item->DESCONTO   = number_format($iten->DESCONTO, 2, ',', '.');
                       $item->VALORVENDA = number_format($iten->VALORVENDA,2, ',', '.');
                       $item->PRCVEN     = number_format($iten->PRCVEN,2, ',', '.');
                       $item->TOTAL      = number_format($iten->TOTAL,2, ',', '.');
                       $item->HASHPT     = $iten->HASHPT;
                                           
                       $win->datagrid->addItem($item);
                    }
                    TTransaction::close(); // close transaction
                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }

                break;
         }
        $win->add($panel);        
        $win->show();
    }
    

     /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    public static function onXML($param)
    {
        $nfiscal = trim($param['NFISCAL']);
        $emp = $param['EMP'];
        
    
       /* Criar componente datagrid para a tela de itens do pedido */
        switch ($emp) 
        {
            case "SOBEL":

                try
                {
                 
                       $curl = curl_init();

                       curl_setopt_array($curl, array(
                         CURLOPT_URL => 'http://192.168.0.15:8086/rest/GETXMLNFE?nfiscal='. $nfiscal . '&serie=1&ident=000001',
                         CURLOPT_RETURNTRANSFER => true,
                         CURLOPT_ENCODING => '',
                         CURLOPT_MAXREDIRS => 10,
                         CURLOPT_TIMEOUT => 0,
                         CURLOPT_FOLLOWLOCATION => true,
                         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                         CURLOPT_CUSTOMREQUEST => 'GET',
                       ));
                       
                       $response = curl_exec($curl);
                       
                       curl_close($curl);   
                       file_put_contents( $_SERVER['DOCUMENT_ROOT'] .'/wms/app/output/'.$nfiscal . '.xml', $response);
 
                       $file = $_SERVER['DOCUMENT_ROOT'] . '/wms/app/output/' . $nfiscal . '.xml';

                     /*
                     
                       header("Content-Description: File Transfer"); 
                       header("Content-disposition: attachment; filename='". $file . "'");
                       header('Content-type: "text/xml"; charset="utf8"');                      
                       readfile ($file);

                      */
                       $win = TWindow::create('Download XML NFe', 0.2, 0.2);
                       // creates a table
                       $table = new TTable;
                                           
                       // adds a row to the table
                       $row = $table->addRow();
                       $title = $row->addCell("<a href='" .''. '/wms/app/output/' . $nfiscal . ".xml'>Download</a>");
                       $title->colspan = 2;
    
                       $win->add($table);
                       
                       $win->show();

                    

                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }
                
                break;
            case "3F":

                try
                {
                    
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'http://192.168.0.15:8086/rest/GETXMLNFE?nfiscal='. $nfiscal . '&serie=1&ident=000003',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                    ));
                    
                    $response = curl_exec($curl);
                       
                    curl_close($curl);   
                    file_put_contents( $_SERVER['DOCUMENT_ROOT'] .'/wms/app/output/'.$nfiscal . '.xml', $response);

                    $file = $_SERVER['DOCUMENT_ROOT'] . '/wms/app/output/' . $nfiscal . '.xml';

                    $win = TWindow::create('Download XML NFe', 0.2, 0.5);
                    // creates a table
                    $table = new TTable;
                                        
                    // adds a row to the table
                    $row = $table->addRow();
                    $title = $row->addCell("<a href='" .''. '/wms/app/output/' . $nfiscal . ".xml'>Download</a>");
                    $title->colspan = 2;
 
                    $win->add($table);
                    $win->show();   
                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }
                
                break;

            case "JMT":

                try
                {
                    
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'http://192.168.0.15:8086/rest/GETXMLNFE?nfiscal='. $nfiscal . '&serie=1&ident=000002',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'GET',
                    ));

                    $response = curl_exec($curl);
                       
                    curl_close($curl);   
                    file_put_contents( $_SERVER['DOCUMENT_ROOT'] .'/wms/app/output/'.$nfiscal . '.xml', $response);

                    $file = $_SERVER['DOCUMENT_ROOT'] . '/wms/app/output/' . $nfiscal . '.xml';

                    $win = TWindow::create('Download XML NFe', 0.2, 0.2);
                    // creates a table
                    $table = new TTable;
                                        
                    // adds a row to the table
                    $row = $table->addRow();
                    $title = $row->addCell("<a href='" .''. '/wms/app/output/' . $nfiscal . ".xml'>Download</a>");
                    $title->colspan = 2;
 
                    $win->add($table);
                    $win->show();


                }
                catch (Exception $e)
                {
                    new TMessage('error', $e->getMessage());
                }

                break;
         }

    }
    /**
     * shows the page
     */
     /*
    function show()
    {
        $this->onReload();
        parent::show();
    }
    */
}
 ?>
