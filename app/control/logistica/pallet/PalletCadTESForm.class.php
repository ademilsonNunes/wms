<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * PalletCadTESForm
 *
 * @version    1.0
 * @package    logistica
 * @subpackage pallet
 * @author     Ademilson Nunes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PalletCadTESForm extends TPage
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
        $this->setAfterSaveAction( new TAction(['PalletCadProdList', 'onReload'], ['register_state' => 'true']) );

        $this->setDatabase('bisobel');              // defines the database
        $this->setActiveRecord('CadTES');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_CadProd');
        $this->form->setFormTitle('Cadastro de tipo de entrada e saida');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );
        
        // create the form fields
        $id   = new TEntry('ID');
        $TIPO = new TEntry('TIPO');
        $MOTIVO = new TEntry('MOTIVO');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_cadprod');
        $this->form->setFormTitle('Cadastro de Motivos de Entrada e saida');
        $this->form->setClientValidation(true);
        $this->form->setColumnClasses( 2, ['col-sm-5 col-lg-4', 'col-sm-7 col-lg-8'] );
         
        // add the fields
        $this->form->addFields( [ new TLabel('Codigo') ], [ $id ] );
        $this->form->addFields( [ new TLabel('TIPO') ],  [ $TIPO ] );
        $this->form->addFields( [ new TLabel('MOTIVO') ], [ $MOTIVO ] );

         // set sizes
         $id->setSize('100%');
         $id->setEditable(FALSE);
         //$TIPO->setEditable(FALSE);         

        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
    //         $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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
