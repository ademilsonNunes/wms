<?php
/**
 * PalletForm
 *
 * @version    1.0
 * @package    logistica
 * @subpackage pallet
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PalletForm extends TPage
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
        
        parent::setTargetContainer('adianti_right_panel');
        $this->setAfterSaveAction( new TAction(['ServicoList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('protheus');              // defines the database
        $this->setActiveRecord('Romaneio');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_MovPallet');
        $this->form->setFormTitle('Movimentação de Pallet');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );
        

        // create the form fields
     //   $id = new TEntry('id');
  //      $nome = new TEntry('Transportador');
        // creates the form
        $this->form = new BootstrapFormBuilder('form_MovPallet');
        $this->form->setFormTitle('Retorno de Pallet');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );

         // create the form fields
         $id           = new TEntry('CODIGO');
         $dt_emissao   = new TDate('DTEMISSAO');
         $transp       = new TEntry('ZZQ_DESTRA');

         $motorista    = new TEntry('MOTORISTA');
         $veiculo      = new TEntry('VEICULO');
         $placa        = new TEntry('PLACA');
         $lacre        = new TEntry('LACRE');
         $qtdeEnt      = new TEntry('QTDE_ENT');
         $qtdeSai      = new TEntry('QTDE_SAI');
         $qtdeQuebrado = new TEntry('QUEBRADO');
         $saldo        = new TEntry('SALDO');
        
         $romaneio   = new TDBSeekButton('ZZQ_ROMANE', 'protheus', 'form_MovPallet', 'Romaneio', 'ZZQ_ROMANE');
         $romaneio->setDisplayMask('{ZZQ_ROMANE} - {ZZQ_DESTRA}  ');
         $romaneio->setDisplayLabel('Transportadora');
         
         /*
         $customer_id->setAuxiliar($customer_name);
         */

        // add the fields
        $this->form->addFields( [ new TLabel('Codigo') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Transpotadora') ], [ $transp ] );
        $this->form->addFields( [ new TLabel('Romaneio (senha)') ], [ $romaneio ] );
        $this->form->addFields( [ new TLabel('Dt Emissao') ], [ $dt_emissao ] );

        $this->form->addFields( [ new TLabel('Motorista')],     [$motorista]);
        $this->form->addFields( [ new TLabel('Veiculo')],       [$veiculo]);
        $this->form->addFields( [ new TLabel('Placa')],         [$placa]);
        $this->form->addFields( [ new TLabel('Lacre')],         [$lacre]);
        $this->form->addFields( [ new TLabel('Qtde. Entrada')], [$qtdeEnt]);
        $this->form->addFields( [ new TLabel('Qtde. Saída')],   [$qtdeSai]);
        $this->form->addFields( [ new TLabel('Quebrado')],      [$qtdeQuebrado]);
        $this->form->addFields( [ new TLabel('Saldo')],         [$saldo]);


        $dt_emissao->addValidation('Dt Emissao', new TRequiredValidator);
        $romaneio->addValidation('Romaneio', new TRequiredValidator);

         // set sizes
         $id->setSize('100%');
         $dt_emissao->setSize('100%');
         $dt_emissao->setMask('dd/mm/yyyy');
         $romaneio->setSize('100%');

         $dt_emissao->setValue(date('d-m-Y'));
         $romaneio->setMinLength(0);
         $id->setEditable(FALSE);
         $transp->setEditable(FALSE);         



        //$valor = new TNumeric('valor', 2, ',', '.', true);
       // $tipo_servico_id = new TDBUniqueSearch('tipo_servico_id', 'erphouse', 'TipoServico', 'id', 'nome');
       //$ativo = new TRadioGroup('ativo');


        // add the fields
        /*
        $this->form->addFields( [ new TLabel('Id') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('Valor') ], [ $valor ] );
        $this->form->addFields( [ new TLabel('Tipo Servico') ], [ $tipo_servico_id ] );
        $this->form->addFields( [ new TLabel('Ativo') ], [ $ativo ] );
 
        $nome->addValidation('Nome', new TRequiredValidator);
        $valor->addValidation('Valor', new TRequiredValidator);
        $tipo_servico_id->addValidation('Tipo de serviço', new TRequiredValidator);
        $ativo->addValidation('Ativo', new TRequiredValidator);


        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');
        $valor->setSize('100%');
        $tipo_servico_id->setSize('100%');
        $ativo->setSize('100%');
        $ativo->addItems( ['Y' => 'Sim', 'N' => 'Não'] );
        $ativo->setLayout('horizontal');
        $tipo_servico_id->setMinLength(0);
        $ativo->setValue('Y');
        
        $id->setEditable(FALSE);
        */
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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
}
