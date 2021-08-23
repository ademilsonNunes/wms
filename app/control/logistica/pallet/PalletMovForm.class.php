<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * PalletMovForm
 *
 * @version    1.0
 * @package    logistica
 * @subpackage pallet
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PalletMovForm extends TWindow
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
      //  parent::setTitle('Window');
        
        // with: 500, height: automatic
        parent::setSize(0.6, null); // use 0.6, 0.4 (for relative sizes 60%, 40%)
        
        //parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction( new TAction(['PalletMovList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('bisobel');              // defines the database
        $this->setActiveRecord('MovPallet');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_mov_pallet');
        $this->form->setFormTitle('Movimentação de Paletes');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );
        
        // create the form fields
        $id          = new TEntry('ID');    
        $DTEMISSAO   = new TDate('DTEMISSAO');
        $QTDE        = new TEntry('QTDE');
        $MOTORISTA   = new TEntry('MOTORISTA');
        $RG          = new TEntry('RG');
        $PLACA       = new TEntry('PLACA');
        $VEIC        = new TEntry('VEICULO');
        $OBS         = new TText('OBS');
        $PESO        = new TEntry('PESO');
        $QTDCX       = new TEntry('QTDE_CXS');

        $TIPO = new TCombo('TIPO');
        $TIPO->enableSearch();
        $TIPO->addItems(['S'=>'<b>Saida</b>','E'=>'<b>Entrada</b>']);  
        $TIPO->setValue('S');

        $ITEM   = new TDBSeekButton('ITEM', 'bisobel', 'form_mov_pallet', 'CadProd', 'ITEM');
        $ITEM->setDisplayMask('{ITEM}  ');
        $ITEM->setDisplayLabel('Item'); 
        $item = new TEntry('item_palete');    
        $ITEM->setAuxiliar($item);

        $MOTIVO   = new TDBSeekButton('TES', 'bisobel', 'form_mov_pallet', 'CadTES', 'MOTIVO');
        $MOTIVO->setDisplayMask('{MOTIVO}');
        $MOTIVO->setDisplayLabel('MOTIVO'); 
        $motivo = new TEntry('Motivo_palete');    
        $MOTIVO->setAuxiliar($motivo);

        $ROM   = new TDBSeekButton('ROMANEIO', 'protheus', 'form_mov_pallet', 'Romaneio', 'ZZQ_ROMANE');
        $ROM->setDisplayMask('{ZZQ_ROMANE} - {ZZQ_DESTRA}  ');
        $ROM->setDisplayLabel('Transportadora');  

        $CODTRANSP   = new TDBSeekButton('CODTRANSP', 'protheus', 'form_mov_pallet', 'Transp', 'A4_NOME');
        $CODTRANSP->setDisplayMask('{A4_NOME}');
        $CODTRANSP->setDisplayLabel('Transportadora');    
        $trasp = new TEntry('Transp');
        $CODTRANSP->setAuxiliar($trasp);

        $codclinte   = new TDBSeekButton('CODCLIENTE', 'protheus', 'form_mov_pallet', 'Clientes', 'A1_NOME');
        $codclinte->setDisplayMask('{A1_NOME} - {A1_LOJA} - {A1_CGC}');
        $codclinte->setDisplayLabel('Clientes');    
        $cliente= new TEntry('Transp');
        $codclinte->setAuxiliar($cliente);        

        $btnBuscar = $this->form->addAction('Buscar dados', new TAction([$this, 'onChangeAction']), 'fa:find');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_mov_pallet');
        $this->form->setFormTitle('Movimentação de Paletes');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );
         
        // add the fields
        $this->form->addFields( [ new TLabel('Id') ],         [ $id ] );
        $this->form->addFields( [ new TLabel('Tipo') ],       [ $TIPO ] );
        $this->form->addFields( [ new TLabel('Dt.Emissão') ], [ $DTEMISSAO ] );
        $this->form->addFields( [ new TLabel('Romaneio (Senha)') ],[ $ROM ] );   
        $this->form->addFields(  [$btnBuscar]  );    
        $this->form->addFields( [ new TLabel('Cod.Cliente') ], [ $codclinte ] );
        $this->form->addFields( [ new TLabel('Cod.Transp') ], [ $CODTRANSP ] );
        $this->form->addFields( [ new TLabel('Item') ],       [ $ITEM ] );
        $this->form->addFields( [ new TLabel('Motivo') ],     [ $MOTIVO ] );
        $this->form->addFields( [ new TLabel('Motorista') ],  [ $MOTORISTA ] );
        $this->form->addFields( [ new TLabel('RG') ],         [ $RG ] );
        $this->form->addFields( [ new TLabel('Placa') ],      [ $PLACA ] );
        $this->form->addFields( [ new TLabel('Veiculo') ],    [ $VEIC ] );
        $this->form->addFields( [ new TLabel('Peso') ],       [ $PESO ] );
        $this->form->addFields( [ new TLabel('Qtde.Cxs') ],   [ $QTDCX ] );
        $this->form->addFields( [ new TLabel('OBS') ],        [ $OBS ] );
        $this->form->addFields( [ new TLabel('QTDE') ],       [ $QTDE ] );

         // set sizes        
         $id->setSize('100%');
         $id->setEditable(FALSE);
         $CODTRANSP->setSize('20%');
         $trasp->setSize('70%');
         $codclinte->setSize('20%');
         $cliente->setSize('70%');
         $ROM->setSize('20%');
         $TIPO->setSize('50%');
         $ITEM->setSize('20%');
         $item->setSize('70%');
         $MOTIVO->setSize('20%');
         $motivo->setSize('70%');
    //     $DTEMISSAO->setValue(date('Y-m-d'));        
    //     $DTEMISSAO->setMask('dd/mm/yyyy');     
         $trasp->setEditable(FALSE);
         $item->setEditable(FALSE);
         $item->setEditable(FALSE);
         $motivo->setEditable(FALSE);
         $cliente->setEditable(FALSE);
        
                
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';


        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');

     //  $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        //  $TIPO->addValidation('TIPO', new TRequiredValidator); 
        /*
        $CODTRANSP->addValidation('CODTRANSP', new TRequiredValidator);
        $QTDE->addValidation('QTDE', new TRequiredValidator); 
        $DTEMISSAO->addValidation('DTEMISSAO', new TRequiredValidator); 
        $ITEM->addValidation('ITEM', new TRequiredValidator); 
        $MOTIVO->addValidation('MOTIVO', new TRequiredValidator); 
        */
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
    //  $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    /**
     * Close side panel
     */
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

    /**
     * Action to be executed when the user changes the romaneio field
     * 
     */
    public static function onChangeAction($param)
    {
        
        $obj = new StdClass;
    
        try 
        { 
            TTransaction::open('protheus'); // open transaction
            $conn = TTransaction::get(); // get PDO connection
            
            // run query
            $result = $conn->query("SELECT ZZQ_ROMANE, 
                                           ZZQ_TRANSP, 
                                           ZZQ_DESTRA, 
                                           ZZQ_DESVEI,
                                           ZZK_NOME,
                                           ZZK_RGVI,
                                           ZZK_OBSERV,
                                           ZZK_AUTORI,
                                           ZZQ_PESO,
                                           ZZQ_QTDCXS
                                    FROM ZZQ010 ZZQ
                                    LEFT JOIN ZZK010 ZZK ON ZZK.ZZK_ROMANE = ZZQ_ROMANE	AND ZZK.D_E_L_E_T_ = ''
                                    WHERE ZZQ_ROMANE = '{$param['ROMANEIO']}' AND ZZQ.D_E_L_E_T_ = '' ");
            
            // show results 
            foreach ($result as $row) 
            { 
                $obj->ROMANEIO   = $row['ZZQ_ROMANE']; 
                $obj->CODTRANSP  = $row['ZZQ_TRANSP'];
                $obj->Transp     = $row['ZZQ_DESTRA'];
                $obj->MOTORISTA  = $row['ZZK_NOME'];
                $obj->PLACA      = $row['ZZK_AUTORI'];
                $obj->VEICULO    = $row['ZZQ_DESVEI'];
                $obj->RG         = $row['ZZK_RGVI'];
                $obj->PESO       = $row['ZZQ_PESO'];
                $obj->QTDE_CXS   = str_replace(",","", number_format($row['ZZQ_QTDCXS']) );
            } 
            TTransaction::close(); // close transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        } 
        TForm::sendData('form_mov_pallet', $obj);
            
    }
}
