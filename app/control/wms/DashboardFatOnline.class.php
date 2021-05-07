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
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * DashboardFatOnline
 *
 * @version    1.0
 * @package    DashboardFatOnline
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
 class DashboardFatOnline extends TPage
 {
    function __construct()
    {
        parent::__construct();

        try 
        {           
            $html      = new THtmlRenderer('app/resources/dashboard_fatonline.html');          
            
            $fatDia    = $this->getFat();
            $fatMes    = $this->getFatAc();
            $diaUtil   = $this->getDiaUtil(); 
            $diasUteis = $this->getDiasUteis();                        

            $totalCaixas = (float)$fatDia->QTDE;
            $totalCaixas = number_format($totalCaixas,0,',', '.');

            $totalFat = (float)$fatDia->LIQ;
            $totalFat = 'R$ ' . number_format($totalFat,2,',', '.');
            
            $medioFat = (float)$fatDia  ->PRECO_MEDIO;
            $medioFat = 'R$ ' . number_format($medioFat,2,',', '.');
 
            $totalCaixasAc = (float)$fatMes->QTDE;
            $totalCaixasAc = number_format($totalCaixasAc,0,',', '.');

            $totalFatAc = (float)$fatMes->LIQ;
            $totalFatAc = 'R$ ' . number_format($totalFatAc,2,',', '.');
            
            $medioFatAc = (float)$fatMes->PRECO_MEDIO;
            $medioFatAc = 'R$ ' .  number_format($medioFatAc,2,',', '.');

            $totalCaixasMediaDia = round(($totalCaixasAc / $diaUtil),3);     
            $totalmediofat = ( (float)$fatMes->LIQ / $diaUtil );        

            $mediomes =  $totalmediofat / $totalCaixasMediaDia ;      
          
            $html->enableSection('main', ['totalcaixas'    => $totalCaixas, 
                                          'totalfat'       => $totalFat,
                                          'mediofat'       => $medioFat,
                                          'totalcaixasac'  => $totalCaixasAc, 
                                          'totalfatac'     => $totalFatAc,
                                          'mediofatac'     => $medioFatAc,
                                          'diautil'        => $diaUtil,
                                          'mediacaixasdia' => $totalCaixasMediaDia,
                                          'totalmediofat'  => 'R$ ' . number_format($totalmediofat, 2, ',', '.'),
                                          'mediomes'       => $mediomes
                                         ]);

            $container = new TVBox;
            $container->style = 'width: 100%';

            $panel = new TPanelGroup('Faturamento On-line');        
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($panel);  
            $container->add($html); 
            parent::add($container);           

        } catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }


    /**
     * getFat()
     */
    function getFat()
    {
        $query = "EXEC FATSOBEL '" . date('Ymd') . "', '"  . date('Ymd') .  "'";

        try 
        {
            TTransaction::open('bisobel');
            $conn = TTransaction::get();
            $result = $conn->query($query);
            
            $fat = new StdClass;
            foreach ($result as $res) 
            {
                 $fat->QTDE         = $res['QTDE'];
                 $fat->LIQ          = $res['LIQ'];
                 $fat->PRECO_MEDIO  = $res['PREÇO.MEDIO'];
            }

            return $fat;
            
            TTransaction::close();
        } catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }

     /**
     * getFatAc()
     */
     function getFatAc()
     {
         $dataIni = date('Ym') . '01';
         $query = "EXEC FATSOBEL '" . $dataIni . "', '"  . date('Ymd') .  "'";
 
         try 
         {
             TTransaction::open('bisobel');
             $conn = TTransaction::get();
             $result = $conn->query($query);
             
             $fat = new StdClass;
             foreach ($result as $res) 
             {
                  $fat->QTDE         = $res['QTDE'];
                  $fat->LIQ          = $res['LIQ'];
                  $fat->PRECO_MEDIO  = $res['PREÇO.MEDIO'];
             }
 
             return $fat;
             
             TTransaction::close();
         } catch (Exception $e) 
         {
             new TMessage('error', $e->getMessage());
         }
     }

    /**
     * getDiasUteis()
     */
     function getDiasUteis()
     {
         $query = "SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( (SELECT EOMONTH ( getdate() )) ) +0 AS QTDDIASEUTEIS";
 
         try 
         {
             TTransaction::open('bisobel');
             $conn = TTransaction::get();
             $result = $conn->query($query);
             
             $dias = 0;
             foreach ($result as $res) 
             {
                 $dias = $res['QTDDIASEUTEIS'];
             }
 
             return $dias;
             
             TTransaction::close();
         } catch (Exception $e) 
         {
             new TMessage('error', $e->getMessage());
         }
     }
     
     /*
      * getDiaUtil()
      */
     function getDiaUtil()
     {
         $query = "SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( getdate()) +0 DIAUTIL";
 
         try 
         {
             TTransaction::open('bisobel');
             $conn = TTransaction::get();
             $result = $conn->query($query);
             
             $dias = 0;
             foreach ($result as $res) 
             {
                 $dias = $res['DIAUTIL'];
             }
 
             return $dias;
             
             TTransaction::close();
         } catch (Exception $e) 
         {
             new TMessage('error', $e->getMessage());
         }
     }

    /**
     * shows the page
     */
     function show()
     {
      //   $this->getFat();

         parent::show();
     }


 }
 