<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * ReportGFEForm
 *
 * @version    1.0
 * @package    ReportGFEForm
 * @subpackage GFE
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
 class ReportGFEForm extends TPage
 {
     
    function __construct()
    {
        $css = new TELement('link');
        $css->href  = 'app/templates/{template}/css/main.css';
        $css->rel   = 'stylesheet';
        $css->type  = 'text/css';
        $css->media = 'screen'; 

        parent::__construct();
        $html      = new THtmlRenderer('app/resources/dashboard_resumo_oper.html');    

         // create the form
         $this->form = new BootstrapFormBuilder;
         $this->form->generateAria(); 

         $dataIni = new TDate('dataini');
         $dataFin = new TDate('datafin');

         $dataIni->setMask('dd/mm/yyyy');
         $dataFin->setMask('dd/mm/yyyy');

         $dataIni->setValue(date('d/m/Y'));
         $dataFin->setValue(date('d/m/Y'));

         
         $this->form->addFields([new TLabel('Data inicial')], [$dataIni]);
         $this->form->addFields([new TLabel('Data Final')], [$dataFin]);

         $this->onSend();

   
         $this->form->addAction('Buscar', new TAction(array($this, 'onSend')), 'far:check-circle green');
             
            $container = new TVBox;
            $container->style = 'width: 100%';
            $panel = new TPanelGroup('Resumo de Operações');        
            $panel->add($this->form);
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($panel);  
            $container->add($html); 
            parent::add($container);           
    }

?>