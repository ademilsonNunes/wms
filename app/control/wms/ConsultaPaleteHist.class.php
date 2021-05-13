<?php
/**
 * ConsultaPaleteHist
 *
 * @version    1.0
 * @package    Consultar informações de produtos pelo ID do Pallet
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ConsultaPaleteHist extends TPage
{
    private $form;
    
    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();
        
        // create the form
        $this->form = new BootstrapFormBuilder;

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'min-width: 1900px';
        
         $end        = new TDataGridColumn('END',         'End.',        'center', '5%');
         $codprod    = new TDataGridColumn('CODPROD',     'Cod.',        'center', '5%');
         $item       = new TDataGridColumn('ITEM',        'Item',        'center', '5%');
         $qtde       = new TDataGridColumn('QTDE',        'Qtde',        'center', '5%');
         $lote       = new TDataGridColumn('LOTE',        'Lote',        'center', '5%');
         $ref        = new TDataGridColumn('REF',         'Ref.',        'center', '5%');
         $dtval      = new TDataGridColumn('DTVAL',       'Dt.Val',      'center', '5%');
//         $dtmov      = new TDataGridColumn('DTMOV',       'Dt.Mov',      'center', '10%');
//         $hrmov      = new TDataGridColumn('HRMOV',       'Hr.Mov',      'center', '10%');
//         $usuario    = new TDataGridColumn('USUARIO',     'Usuário',     'center', '5%');
         $nome       = new TDataGridColumn('NOME',        'Nome',        'center', '5%');
         $turno      = new TDataGridColumn('TURNO',       'Turno',       'center', '1%');
 //        $tipomov    = new TDataGridColumn('TIPOMOV',     'Tipo Mov.',   'center', '10%');
 //        $tipodoc    = new TDataGridColumn('TIPODOC',     'Tipo Doc',    'center', '10%');
         $hrconf     = new TDataGridColumn('HORA_CONF',   'Dt. Conf',    'center', '10%');
 //        $dtconf     = new TDataGridColumn('DTCONF',      'Dt. Conf',    'center', '10%');
 //        $motivoCanc = new TDataGridColumn('MOTIVO_CANC', 'Mot. Canc.',  'center', '10%');
 //        $tipo       = new TDataGridColumn('TIPO',        'Tipo',        'center', '10%');
         $descrTipo  = new TDataGridColumn('DESCRTIPO',   'Desc. Tipo',  'center', '10%');

         $this->datagrid->addColumn($end);
         $this->datagrid->addColumn($codprod);   
         $this->datagrid->addColumn($item);      
         $this->datagrid->addColumn($qtde);      
         $this->datagrid->addColumn($lote);      
         $this->datagrid->addColumn($ref);       
         $this->datagrid->addColumn($dtval);     
  //       $this->datagrid->addColumn($dtmov);     
  //       $this->datagrid->addColumn($hrmov);     
  //       $this->datagrid->addColumn($usuario);   
         $this->datagrid->addColumn($nome);      
         $this->datagrid->addColumn($turno);     
 //        $this->datagrid->addColumn($tipomov);   
 //        $this->datagrid->addColumn($tipodoc);   
 //        $this->datagrid->addColumn($dtconf);    
         $this->datagrid->addColumn($hrconf); 
//         $this->datagrid->addColumn($motivoCanc);
//         $this->datagrid->addColumn($tipo);      
         $this->datagrid->addColumn($descrTipo); 

         // creates the datagrid model
         $this->datagrid->createModel();
    
    // creates the page navigation
       //   $this->pageNavigation = new TPageNavigation;
       //   $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        // turn on horizontal scrolling inside panel body
         $panel = new TPanelGroup('Histórico do Pallet');
         $panel->getBody()->style = "overflow-x:auto;";
         $panel->add($this->form);
         $panel->add($this->datagrid);
         $panel->addFooter($this->pageNavigation);
        
        // create the form fields
        $barcode = new TBarCodeInputReader('Leitura');
        
        $barcode->setSize( '100%');
        
        $barcode->setChangeAction( new TAction( [$this, 'onChange'] ) );
        
        $this->form->addFields( [new TLabel('Código')], [$barcode, new TLabel('ID Pallet')] );
        
        $this->form->addAction('Show', new TAction(array($this, 'onShow')), 'far:check-circle green');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));  
        $vbox->add($panel);
        parent::add($vbox);
    }
    
    /**
     *
     */
    public static function onChange($param)
    {
        //new TMessage('info', '<b>onChange</b><br>'.str_replace(',', '<br>', json_encode($param)));
    }
    

      /**
     * Load the data into the datagrid
     */
     function onReload()
     {
         $this->datagrid->clear();
     } 
    /**
     * Show the form content
     */
    public function onShow($param)
    {
        $this->datagrid->clear();
        
        $data = $this->form->getData();
        $this->form->setData($data); // put the data back to the form
        $pallet = (float)$data->Leitura;
        
        $query = "SELECT [END],
                         [CODPROD],
                         [ITEM],
                         [UND],
                         [QTDE],
                         [LOTE],
                         [REF],
                         [DT.VAL],
                         [DT.MOV],
                         [HR.MOV],
                         [USUARIO],
                         [NOME],
                         [TURNO],
                         [TIPO.MOV],
                         [TIPO.DOC],
                         [DT_CONF],
                         [MOTIVO_CANC],
                         [TIPO],
                         [DESCR.TIPO],
                         [HORA_CONF]
                  FROM (
                  SELECT CAST(MOV. PREDIO AS VARCHAR) + '.' + CAST(MOV.RUA AS VARCHAR) + '.'  + CAST(MOV. BLOCO AS VARCHAR) + '.' +  CAST(MOV. APTO AS VARCHAR) AS 'END', 
                  MOV.PRODUTO     AS 'CODPROD', 
                  PROD.DESCRICAO  AS 'ITEM' , 
                  PROD.UNIDADE    AS 'UND', 
                  MOV.QTDE        AS 'QTDE', 
                  MOV.LOTE        AS 'LOTE',  
                  MOV.REFERENCIA  AS 'REF', 
                  MOV.DATA_VAL    AS 'DT.VAL', 
                  MOV.DATA_MOV    AS 'DT.MOV', 
                  MOV.HORA_MOV    AS 'HR.MOV', 
                  MOV.USUARIO     AS 'USUARIO', 
                  TF.DESCRICAO    AS 'NOME',
                  TF.TURNO        AS 'TURNO',
                  MOV.TIPO_MOV    AS 'TIPO.MOV', 
                  MOV.TIPODOC     AS 'TIPO.DOC', 
                  MOV.DATA_CONF   AS 'DT_CONF', 
                  MOV.HORA_CONF   AS 'HORA_CONF',
                  MOV.MOTIVO_CANC AS 'MOTIVO_CANC', 
                  TMOV.TIPO       AS 'TIPO',
                  TMOV.DESCRICAO  AS 'DESCR.TIPO'
                  FROM  ARQ_MOV  MOV 
                  LEFT OUTER JOIN  TAB_TMOV  TMOV ON MOV. TIPO_MOV  = TMOV. TIPO 
                  LEFT OUTER JOIN  TAB_PROD  PROD ON MOV. PRODUTO  = PROD. PRODUTO  AND MOV. DONO  = PROD. DONO 
                  LEFT JOIN TAB_FUNC TF ON TF.CODIGO = MOV.USUARIO
                  WHERE MOV. DONO    = '001' 
                  AND MOV.NUM_PALLET = {$pallet}
                  )AS RESULT
                  ORDER BY 
                  [DT.MOV]  ASC, 
                  [HR.MOV]  ASC  ";    

        try
        {
           TTransaction::open('sisdep'); // abre uma transação            
            $conn = TTransaction::get(); // obtém a conexão
             // realiza a consulta
             $result = $conn->query($query);
                    
             foreach ($result as $row)
             {  

                $item = new StdClass;
                $item->END         = $row['END'];
                $item->CODPROD     = $row['CODPROD'];
                $item->ITEM        = $row['ITEM'];
                $item->UND         = $row['UND'];
                $item->QTDE        = $row['QTDE'];
                $item->LOTE        = $row['LOTE'];
                $item->REF         = $row['REF'];
                $item->DTVAL       = $row['DT.VAL'];
                $item->DTMOV       = $row['DT.MOV'];
                $item->HRMOV       = $row['HR.MOV'];
                $item->USUARIO     = $row['USUARIO'];
                $item->NOME        = $row['NOME'];
                $item->TURNO       = $row['TURNO'];
                $item->TIPOMOV     = $row['TIPO.MOV'];
                $item->TIPODOC     = $row['TIPO.DOC'];
                $item->DTCONF      = $row['DT_CONF']; 
                $item->HORA_CONF   = $row['HORA_CONF'];   
                $item->MOTIVO_CANC = $row['MOTIVO_CANC'];
                $item->TIPO        = $row['TIPO'];  
                $item->DESCRTIPO   = $row['DESCR.TIPO'];                 
                $this->datagrid->addItem($item);
      
             }
                echo '</table>';
         
 
           TTransaction::close(); // fecha a transação.
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