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
 * TDashboardFatOnlineOper
 *
 * @version    1.0
 * @package    TDashboardFatOnlineOper
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
 class TDashboardFatOnlineOper extends TPage
 {
    private $fatDia      = 0;            
    private $fatMes      = 0;
    private $diaUtil     = 0;
    private $diasUteis   = 0;
    private $carteira    = 0;
    private $totalProd   = 0;
    private $totalCarreg = 0;
    private $totalDev    = 0;    
     
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

         $totalCaixas = (float)$this->fatDia->QTDE;
         $totalCaixas = number_format($totalCaixas,0,',', '.');

    //     $totalFat = (float)$this->fatDia->LIQ;
//         $totalFat = 'R$ ' . number_format($totalFat,2,',', '.');
         
//         $medioFat = (float)$this->fatDia->PRECO_MEDIO;
 //        $medioFat = 'R$ ' . number_format($medioFat,2,',', '.');

         //Carteira 
         $totalcaixascarteira = (float)$this->carteira->QTDE;
   //      $totalvalorcarteira  = (float)$this->carteira->LIQ;
 //        $mediocarteira       = (float)$this->carteira->PRECO_MEDIO;
         $totalProd           = (float)$this->totalProd;
         $totalCarreg         = (float)$this->totalCarreg;
         $totalDevCaixas      = (float)$this->totalDev->QTDE;
   //      $totalDevValor       = (float)$this->totalDev->LIQ;

         //Bonificação Verba e Contrato
         $bcv =  $this->getBCV( '', '' );

         /*
        echo '<pre>';
        echo print_r($bcv)  ;
        echo '<pre>';
        */

/*
         $totalBonifCaixas = (float)$bcv[0][0]['QTDE'];
         $totalBonifValor  = (float)$bcv[0][0]['LIB'];

         $totalVerbaQtde   = (float)$bcv[0][1]['QTDE'];
         $totalVerbaLiq    = (float)$bcv[0][1]['LIQ'];

         $totalContrQtde   = (float)$bcv[0][2]['QTDE'];
         $totalContrLiq    = (float)$bcv[0][2]['LIQ'];
*/

         $html->enableSection('main', ['totalcaixas'         =>  $totalCaixas, 
  //                                     'totalfat'            =>  $totalFat,
    //                                   'mediofat'            =>  $medioFat,
                                       'totalcaixascarteira' =>  number_format($totalcaixascarteira, 0, ',', '.'),
       //                                'totalvalorcarteira'  => 'R$ ' . number_format($totalvalorcarteira, 2, ',', '.'),
                                //       'mediocarteira'       => 'R$ ' . number_format($mediocarteira, 2, ',', '.'),
                                       'totalproducao'       =>  number_format($totalProd, 0, ',', '.'),
                                       'data_hora'           =>  date('d/m/Y H:i:s') ,
                                       'total_carreg'        =>  number_format($totalCarreg , 0, ',', '.'),
                                       'total_dev'           =>  number_format($totalDevCaixas , 0, ',', '.')//,   
                                     //  'total_dev_vlr'       =>  'R$ ' . number_format($totalDevValor , 2, ',', '.') 
                                      ]);


         $this->form->addAction('Buscar', new TAction(array($this, 'onSend')), 'far:check-circle green');
             
            $container = new TVBox;
            $container->style = 'width: 100%';
            $panel = new TPanelGroup('Resumo de Operações');        
            $panel->add($this->form);
        //    $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($panel);  
            $container->add($html); 
            parent::add($container);           
    }


     /**
     * Simulates an save button
     * Show the form content
     */
     public function onSend()
     {
         $data = $this->form->getData();         

         $this->form->setData($data);  

         $dataini = $data->dataini;
         $datafin = $data->datafin;    
         
         //configura data no padrão ISO (utilizado pelo protheus em formato varchar)
         $dataini = substr($dataini, 6, 4) . substr($dataini, 3, 2) . substr($dataini, 0, 2);
         $datafin = substr($datafin, 6, 4) . substr($datafin, 3, 2) . substr($datafin, 0, 2);
         
         $this->fatDia      = $this->getFatAc($dataini, $datafin);
         $this->carteira    = $this->getCarteira();
         $this->totalProd   = (float)$this->getProducao($dataini, $datafin);
         $this->totalCarreg = (float)$this->getCarreg($dataini, $datafin);
         $this->totalDev    = $this->getDev($dataini, $datafin);
         $this->bcv         = $this->getBCV($dataini, $datafin);
    
     }


    /**
     * getFat()
     */
    function getFat($dataini, $datafin)
    {
        if ($dataini == '' ) 
        {
            $dataini = date('Ymd');
        }

        if ($datafin == '') 
        {
            $datafin = date('Ymd');
        }

        $query = "EXEC FATSOBEL '" . $dataini . "', '"  .  $datafin .  "'";

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
     * getDev()
     */
     function getDev($dataini, $datafin)
     {
         if ($dataini == '' ) 
         {
             $dataini = date('Ymd');
         }
 
         if ($datafin == '') 
         {
             $datafin = date('Ymd');
         }
 
         $query = "EXEC DEVSOBEL '" . $dataini . "', '"  .  $datafin .  "'";
 
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
             }
 
             return $fat;
             
             TTransaction::close();
         } catch (Exception $e) 
         {
             new TMessage('error', $e->getMessage());
         }
     }


    /**
     * Retorna totalizador de verba, bonificação e contrato dentro do período
     * getBCV()
     */
     function getBCV($dataini, $datafin)
     {
         if ($dataini == '' ) 
         {
             $dataini = date('Ymd');
         }
 
         if ($datafin == '') 
         {
             $datafin = date('Ymd');
         }
 
         $query = "EXEC BVCSOBEL '" . $dataini . "', '"  .  $datafin .  "'";
 
         try 
         {
             TTransaction::open('bisobel');
             $conn = TTransaction::get();
             $result = $conn->query($query);
             
             $fat = array();
             $i = 0;
             foreach ($result as $res) 
             {
                  $fat[$i]['TIPO']  = $res['TIPO'];
                  $fat[$i]['QTDE']  = $res['QTDE'];
                  $fat[$i]['LIQ']   = $res['LIQ'];
                  $i++;
             }  
             return $fat;
             
             TTransaction::close();
         } catch (Exception $e) 
         {
             new TMessage('error', $e->getMessage());
         }
     }


   /**
     * getCarreg()
     */
     function getCarreg($dataini, $datafin)
     {
        if ($dataini == '' ) 
        {
            $dataini = date('Ymd');
        }

        if ($datafin == '') 
        {
            $datafin = date('Ymd');
        }

         $carreg = 0;
         $query = "    SELECT SUM(MOV. QTDE ) AS 'QTDE'
                       FROM  ARQ_MOV  MOV 
                       LEFT OUTER JOIN  TAB_TMOV  TMOV ON MOV. TIPO_MOV  = TMOV. TIPO 
                       LEFT OUTER JOIN  TAB_PROD  PROD ON MOV. PRODUTO  = PROD. PRODUTO  AND MOV. DONO  = PROD. DONO 
                       LEFT JOIN TAB_FUNC TF ON TF.CODIGO = MOV.USUARIO
                       WHERE MOV. DONO    = '001' 
                       AND  MOV.DATA_CONF BETWEEN '{$dataini}' AND '{$datafin}'
                       AND MOV. TIPO_MOV  IN ('S2', 'R2')  ";
          try 
          {
              TTransaction::open('sisdep');
              $conn   = TTransaction::get();
              $result = $conn->query($query);
      
              foreach ($result as $res) 
              {
                 $carreg = (float)$res['QTDE'];
              }            
                          
              return $carreg;
              
              TTransaction::close();
          }catch (Exception $e) 
          {
              new TMessage('error', $e->getMessage());
          }

     }


    /**
     * getProducao()
     */
     function getProducao($dataini, $datafin)
     {
        if ($dataini == '' ) 
        {
            $dataini = date('Ymd');
        }

        if ($datafin == '') 
        {
            $datafin = date('Ymd');
        }

         $prod = 0;
         $query = "SELECT SUM(D3_QUANT) AS 'QTDE'  	   
                   FROM SD3010 SD3  INNER JOIN SB1010 SB1 ON SB1.B1_COD =  SD3.D3_COD  
                   WHERE D3_EMISSAO BETWEEN CAST('{$dataini}' AS DATE) AND CAST('$datafin' AS DATE)  
                   AND D3_ESTORNO =  ''  
                   AND D3_CF   = 'PR0'
                   AND D3_TIPO = 'PA'
                   AND SD3.D_E_L_E_T_ = ''  
                   AND SB1.D_E_L_E_T_ = '' ";
          try 
          {
              TTransaction::open('protheus');
              $conn   = TTransaction::get();
              $result = $conn->query($query);
      
              foreach ($result as $res) 
              {
                 $prod = (float)$res['QTDE'];
              }            
                          
              return $prod;
              
              TTransaction::close();
          }catch (Exception $e) 
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
     function getFatAc( $dataini, $datafin )
     {          
          if ($dataini == '' ) 
          {
              $dataini = date('Ymd');
          }
  
          if ($datafin == '') 
          {
              $datafin = date('Ymd');
          }

         $query = "EXEC FATSOBEL '" . $dataini . "', '"  . $datafin .  "'";
 
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
         parent::show();
     }


 }
 