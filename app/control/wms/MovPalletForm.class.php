<?php
/**
 * MovPallet
 *
 * @version    1.0
 * @package    MovPalletForm
 * @subpackage wms
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

class MovPalletForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; 

    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();  

       $this->setDatabase('protheus');              // defines the database
       $this->setActiveRecord('Romaneio');     // defines the active record

        // creates the form
        $this->form = new BootstrapFormBuilder('form_MovPallet');
        $this->form->setFormTitle('Retorno de Pallet');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );

         // create the form fields
         $id = new TEntry('CODIGO');
         $dt_emissao = new TDate('DTEMISSAO');
        
         $romaneio   = new TDBSeekButton('ZZQ_ROMANE', 'protheus', 'form_MovPallet', 'Romaneio', 'ZZQ_ROMANE');
         $romaneio->setDisplayMask('{ZZQ_ROMANE} - {ZZQ_DESTRA}  ');
         $romaneio->setDisplayLabel('Transportadora');
         
         /*
         $customer_id->setDisplayMask('{name} - {city->name} - {city->state->name}');
         $customer_id->setDisplayLabel('Informações do cliente');
         $customer_id->setAuxiliar($customer_name);
         */
        // add the fields
        $this->form->addFields( [ new TLabel('Codigo') ], [ $id ] );
        $this->form->addFields( [ new TLabel('Romaneio (senha)') ], [ $romaneio ] );
        $this->form->addFields( [ new TLabel('Dt Emissao') ], [ $dt_emissao ] );


        $dt_emissao->addValidation('Dt Emissao', new TRequiredValidator);
        $romaneio->addValidation('Romaneio', new TRequiredValidator);

         // set sizes
         $id->setSize('100%');
         $dt_emissao->setSize('100%');
         $dt_emissao->setMask('dd/mm/yyyy');
         $romaneio->setSize('100%');

         $dt_emissao->setValue(date('Y-m-d'));
         $romaneio->setMinLength(0);
         $id->setEditable(FALSE);

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
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {

    }

    /**
     * Close side panel
     */
    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

}