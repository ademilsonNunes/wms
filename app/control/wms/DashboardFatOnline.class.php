<?php

/*
    ini_set('log_errors','On');
    ini_set('display_errors','Off');
    ini_set('error_reporting', E_ALL );
    define('WP_DEBUG', false);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);
*/

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
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br) //
 * @license    http://www.adianti.com.br/framework-license
 */
 class DashboardFatOnline extends TPage
 {
    function __construct()
    {
        $css = new TELement('link');
        $css->href  = 'app/templates/{template}/css/main.css';
        $css->rel   = 'stylesheet';
        $css->type  = 'text/css';
        $css->media = 'screen'; 

        parent::__construct();

        try 
        {           
            $html      = new THtmlRenderer('app/resources/dashboard_fatonline.html');          
            
            $fatDia    = $this->getFat();
            $fatMes    = $this->getFatAc();
            $diaUtil   = $this->getDiaUtil(); 
            $diasUteis = $this->getDiasUteis();      
            $carteira  = $this->getCarteira();                  

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

            $totalCaixasMediaDia = round(((float)$totalCaixasAc / (float)$diaUtil),3);     
            $totalmediofat = ( (float)$fatMes->LIQ / $diaUtil );        
            
            //Média mês
            //$mediomes = ($totalmediofat / $totalCaixasMediaDia) / 1000  ;      
            $mediomes = ($totalmediofat / $totalCaixasMediaDia)  ;      
            
            //Projeção
            $totalcaixasprojecao = ($totalCaixasMediaDia * $diasUteis);
            $totalvalorprojecao  = ($totalmediofat * $diasUteis);
           // $mediaprojecao      = ( $totalvalorprojecao / $totalcaixasprojecao) / 1000;
            $mediaprojecao      = ( $totalvalorprojecao / $totalcaixasprojecao);

            //Carteira
            $totalcaixascarteira = (float)$carteira->QTDE;
            $totalvalorcarteira  = (float)$carteira->LIQ;
            $mediocarteira       = (float)$carteira->PRECO_MEDIO;
 
            $html->enableSection('main', ['totalcaixas'         => $totalCaixas, 
                                          'totalfat'            => $totalFat,
                                          'mediofat'            => $medioFat,
                                          'totalcaixasac'       => $totalCaixasAc, 
                                          'totalfatac'          => $totalFatAc,
                                          'mediofatac'          => $medioFatAc,
                                          'diautil'             => $diaUtil,
                                          'mediacaixasdia'      => $totalCaixasMediaDia,
                                          'totalmediofat'       => 'R$ ' . number_format($totalmediofat, 2, ',', '.'),
                                          'mediomes'            => 'R$ ' . number_format($mediomes, 2, ',', '.'),
                                          'diasuteis'           => $diasUteis,
                                          'totalcaixasprojecao' => $totalcaixasprojecao,
                                          'totalvalorprojecao'  => 'R$ ' . number_format($totalvalorprojecao, 2, ',', '.'),
                                          'mediaprojecao'       => 'R$ ' . number_format($mediaprojecao, 2, ',', '.'),
                                          'totalcaixascarteira' =>  number_format($totalcaixascarteira, 0, ',', '.'),
                                          'totalvalorcarteira'  => 'R$ ' . number_format($totalvalorcarteira, 2, ',', '.'),
                                          'mediocarteira'       => 'R$ ' . number_format($mediocarteira, 2, ',', '.')                                 
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
     * getCarteira()
     */
     function getCarteira()
     {
         $query = "EXEC TCARTEIRA ";
 
         try 
         {
             TTransaction::open('bisobel');
             $conn = TTransaction::get();
             $result = $conn->query($query);
             
             $cart = new StdClass;
             foreach ($result as $res) 
             {
                  $cart->QTDE         = $res['QTDE'];
                  $cart->LIQ          = $res['LIQ'];
                  $cart->PRECO_MEDIO  = $res['PREÇO.MEDIO'];
             }
 
             return $cart;
             
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
    //     $query = "EXEC FATSOBEL '" . $dataIni . "', '"  . date('Ymd', strtotime("-1 days")) .  "'";
 
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
         $query = "SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( (SELECT EOMONTH ( getdate() )) ) + 2 AS QTDDIASEUTEIS";
 
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

         $query = "SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( getdate())  + 0.5 DIAUTIL";
         //$query = "SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( getdate()) -1 DIAUTIL";
 
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
         
        //return 11.5;
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
 