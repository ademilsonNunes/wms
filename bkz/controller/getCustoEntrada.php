<?php 
   
   set_time_limit(3600); 
   error_reporting(0);
   
   use SimpleExcel\SimpleExcel;

   define('DB_HOST'        , "192.168.0.16");
   define('DB_USER'        , "sa");
   define('DB_PASSWORD'    , "S0b3l!4dm.");
   define('DB_NAME'        , "Protheus_Producao");
   define('DB_DRIVER'      , "sqlsrv");
  
   //require_once "../Class/Conexao.php";
     require_once ($_SERVER['DOCUMENT_ROOT'] ."/csobel/class/Conexao.php");
    // require_once "../Class/SimpleExcel/src/SimpleExcel/SimpleExcel.php";
      
   try
   {
   	  /*
       $sqlPA  = " SELECT  B1_COD,   "
                .          "B1_DESC, " 
                .          "B1_UM,   "
                .          "B1_GRUPO,"
                .          "B1_TIPO  "
                ." FROM SB1010                     "
                ." WHERE B1_TIPO = 'PA'            "
                ." AND B1_MSBLQL = '2'             "
                ." AND B1_UM = 'CX'                "
                ." AND B1_LOCPAD = '50'            "
                ." AND D_E_L_E_T_ = ''             "
                ." AND B1_DESC LIKE '%SUPREMA%'    "
                //." AND B1_COD NOT IN ('1401.19.03X05L', '1201.58.12X01L_', '1501.23.03X05LE', '1501.23.06X03LE', '1501.24.03X05LE', '1501.24.06X03LE', '1401.20.03X05L', '4001.05.03X05L', '4001.05P.03X05L', '4001.25.03X05L', '1401.23.03X05L', '1701.31.03X05L', '1701.32.03X05L', '1701.33.03X05L', '1701.33.24X500', '1701.51.03X05L', '1902.02.01X500', '1301.16.02X05L') ";
                ." AND B1_COD IN ('1001.01.03X05L', '1001.01.06X02L', '1001.01.12X01L', '4001.25.03X05L', '4001.05.03X05L','4001.05P.03X05L', '1101.01.B12X1,5', '1201.11.03X05L', '1201.13.06X02L', '1301.19.06X02L', '1301.15.03X05L', '1401.22.03X05L', '1401.23.24X500', '1501.25.03X05L', '1501.25.06X03L', '1501.25.12X01L', '1801.02.12X500', '1601.26.12X500', '1701.02.12X500', '1701.30.24X500', '6501.02.12X01L', '6501.02.12X500') ";
          */

           $sqlPA  = " SELECT  B1_COD,   "
                .          "B1_DESC, " 
                .          "B1_UM,   "
                .          "B1_GRUPO,"
                .          "B1_TIPO  "
                ." FROM SB1010                     "
                ." WHERE B1_TIPO = 'PA'            "
                ." AND B1_MSBLQL = '2'             "
                ." AND B1_UM = 'CX'                "
                ." AND B1_LOCPAD = '50'            "
                ." AND D_E_L_E_T_ = ''             "
                ." AND B1_DESC LIKE '%SUPREMA%'    ";


       $Conexao      = Conexao::getConnection();
       $queryPA      = $Conexao->query($sqlPA);       
       $resultPA     = $queryPA->fetchAll(PDO::FETCH_ASSOC);   
       
       echo " <table border='0' width = '100%'> ";

       $grupo         = '';
       $und           = '';
       $tipo          = '';
       $codigo        = '';
       $custoProduto  = '';

       $grupo1         = '';
       $und1           = '';
       $tipo1          = '';
       $codigo1        = '';
       $custoProduto1  = '';
       $codprod1       = '';
    
       $totalPIUnit    = '';
       $totalPINet     = '';

       $produtoAtual  =  '';

       $totalMP       =  '';
       $totalME       =  '';
       $totalPA       =  '';
       
       $tabelaCusto   =   array();
       $i             = 0;

       foreach ($resultPA as $pa) 
       {
           $tabelaCusto[$i]['CODIGO']     .= $pa['B1_COD'];
           $tabelaCusto[$i]['DESCRICAO']  .= $pa['B1_DESC'];

            echo "<tr><td><h3>". $pa['B1_COD'] ."</h3></td><td><h3>" . $pa['B1_DESC']. "</h3></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

            echo "<tr>";
            echo "<td><b>Código</b></td>";
            echo "<td><b>Descrição</b></td>";
            echo "<td><b>Tipo</b></td>";
            echo "<td><b>Grupo</b></td>";
            echo "<td><b>Unidade</b></td>";
            echo "<td><b>Qtde Estru.</b></td>";
            echo "<td><b>Custo NF</b></td>";
            echo "<td><b>Custo NET</b></td>"; 
            echo "<td><b>NF</b></td>";
            echo "<td><b>Cód Forn</b></td>";
            echo "<td><b>Dt. Ult. Entr.</b></td>";
            echo "<td><b>Custo Unit.</b></td>";
            echo "<td></td>";
            echo "</tr>";

            $sqlEstrutura = " SELECT G1_COMP    AS COMP, "   
                           ." G1_ZZDESCC        AS ITEM, "
                           ." G1_QUANT	        AS QTDE "
                           ." FROM SG1010 SG1 " 
                           ." WHERE G1_COD = '" . $pa['B1_COD'] ."'"
                           ." AND SG1.D_E_L_E_T_ = ''                              "
                           ." AND G1_COMP NOT IN ( 'MDDIRETA PROD', 'MDIRETA GGF' )";   
            
            $Conexao         = Conexao::getConnection();
            $queryEstrutura  = $Conexao->query($sqlEstrutura);       
            $resultEstrutura = $queryEstrutura->fetchAll(PDO::FETCH_ASSOC);   
            
            foreach ($resultEstrutura as $estrutura) 
            {

                $produto = getDataProd($estrutura['COMP']);
                
                foreach ($produto as $prod) 
                {
                    $codprod = $prod['B1_COD'];
                    $grupo   = $prod['B1_GRUPO'];
                    $und     = $prod['B1_UM'];
                    $tipo    = $prod['B1_TIPO'];


                }
                
                if ($tipo == 'PI') 
                {
                    $sqlEstruturaPI = " SELECT G1_COMP    AS COMP, "   
                           ." G1_ZZDESCC        AS ITEM, "
                           ." (G1_QUANT / 100)  AS QTDE "
                           ." FROM SG1010 SG1 " 
                           ." WHERE G1_COD = '" . $codprod ."'"
                           ." AND SG1.D_E_L_E_T_ = ''                              "
                           ." AND G1_COMP NOT IN ( 'MDDIRETA PROD', 'MDIRETA GGF', '8001.01.00001' )";   
            
                     $Conexao         = Conexao::getConnection();
                     $queryEstruturaPI  = $Conexao->query($sqlEstruturaPI);       
                     $resultEstruturaPI = $queryEstruturaPI->fetchAll(PDO::FETCH_ASSOC);  
                     
                     foreach ($resultEstruturaPI as $pi) 
                     {
                        $produto = getDataProd($pi['COMP']);
                
                        foreach ($produto as $prod) 
                        {
                            $codprod1 = $prod['B1_COD'];
                            $grupo1   = $prod['B1_GRUPO'];
                            $und1     = $prod['B1_UM'];
                            $tipo1    = $prod['B1_TIPO'];                            

                        }
                        
                         
                         $custo = getCustoProduto( $pi['COMP'] );
                         //var_dump($custo);
                         

                         //Estrutura PI

                        $totalPIUnit += $custo[0]['CUSTONET'] * $pi['QTDE'];
                        $totalPINet  += $custo[0]['CUSTONET'] * $pi['QTDE'];

                        echo "<tr>";
                        echo "<td>" . $pi['COMP']              . "</td>";
                        echo "<td>" . $pi['ITEM']              . "</td>";
                        echo "<td>" . $tipo1                   . "</td>";
                        echo "<td>" . $grupo1                  . "</td>";                 
                        echo "<td>" . $und1                    . "</td>";     
                        echo "<td>"    . number_format( $pi['QTDE']  , 6, ',', '')  . "</td>";
                        

                        echo "<td>R$ " . number_format($custo[0]['VLRUNIT'],  6,  ',', '')     . "</td>"; 
                        echo "<td>R$ " . number_format($custo[0]['CUSTONET'], 6, ',', '')    . "</td>"; 
  

                        echo "<td>" . $custo[0]['NF']          . "</td>"; 
                        echo "<td>" . $custo[0]['CODFORN']      . "</td>";    
                        echo "<td>" . DataConvert( $custo[0]['DTEMISSAO'] )   . "</td>";  
                        echo "<td>R$ " . number_format(($custo[0]['CUSTONET'] * $pi['QTDE']), 6, ',', '') . "</td>"; 
                        echo "<td></td>";  
                        echo "</tr>";

                     }                   
                }
                                

                $custo1 = getCustoProduto( $estrutura['COMP'] );
               // estrutura PA
                
                if  ($tipo <> 'PI')
                {
                    $totalME += ($custo1[0]['CUSTONET'] * $estrutura['QTDE']);
                }


                echo "<tr>";
                echo "<td>" . $estrutura['COMP']      . "</td>";
                echo "<td>" . $estrutura['ITEM']      . "</td>";
                echo "<td>" . ($tipo == 'PI' ? $tipo = $tipo : 'ME')  . "</td>";
                echo "<td>" . $grupo                  . "</td>";                 
                echo "<td>" . $und                    . "</td>"; 
                echo "<td>" . number_format( $estrutura['QTDE']  , 6, ',', '') . "</td>";

                if ($tipo == 'PI' ) 
                {                    
                    echo "<td> " /*.  number_format($totalPIUnit, 6, '.', '') */  . "</td>"; 
                    echo "<td> " /*.  number_format($totalPINet, 6, '.', '')   */ . "</td>"; 
                }
                else
                {
                    echo "<td>R$ " . number_format($custo1[0]['VLRUNIT'], 6, ',', '')      . "</td>"; 
                    echo "<td>R$ " . number_format($custo1[0]['CUSTONET'], 6, ',', '')     . "</td>"; 
                }

                echo "<td>" . $custo1[0]['NF']           . "</td>";     
                echo "<td>" . $custo1[0]['CODFORN']      . "</td>";    
                echo "<td>" .  DataConvert($custo1[0]['DTEMISSAO'])    . "</td>";  

                if ($tipo == 'PI') 
                {
                    $totalMP = $totalPINet * $estrutura['QTDE'];

                    echo "<td>R$ " . number_format($totalPINet, 6, ',', '') . "</td><td>(Litro)</td>";   
                }
                else
                {                    
                    echo "<td>R$ " . number_format(($custo1[0]['CUSTONET'] * $estrutura['QTDE']), 6, ',', '') . "</td>";   
                }

                echo "</tr>";
            }

            $totalPA = $totalME + $totalMP;            
            $tabelaCusto[$i]['CUSTO']  .= $totalPA;       

            echo "<tr>";
            echo "<td></td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td><b>Total MP</b></td>";
            echo "<td>R$ ".  number_format($totalMP, 6, ',', '') . "</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td></td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td><b>Total ME</b></td>";
            echo "<td>R$ " . number_format($totalME, 6, ',', '') ."</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td></td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td><b>Total PA</b></td>";
            echo "<td>R$ " . number_format($totalPA, 6, ',', '') . "</td>";
            echo "</tr>";
 
            $totalMP = '';
            $totalME = '';
            $totalPA = '';

            $totalPIUnit = '';    
            $totalPINet = '';                        
       }
       
       //var_dump($tabelaCusto);


   }
   catch(Exception $e)
   {
       echo $e->getMessage();
       exit;
   }
  
   

   /**
    * Retorna Custo do Produto Procurado
    **/ 
   function getCustoProduto ($codigo)
   {   
        $resultado = '';

        try
        {
       
            $Conexao    = Conexao::getConnection();
            $query      = $Conexao->query("EXEC PROC_GETULTCUSTOPROD '20180101', '" . date("Ymd") . "', '" . $codigo . "' ");
            $custo      = $query->fetchAll(PDO::FETCH_ASSOC); //Pulo do gato             
            //echo json_encode($condPgtos, JSON_UNESCAPED_UNICODE);
         
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            exit;
        }

        return $custo;
   }


   /**
    * Retorna Dados do Produto 
    **/ 
    function getDataProd ($codigo)
    {
        
         try
         {
        
             $Conexao    = Conexao::getConnection();
             $query      = $Conexao->query(" SELECT B1_COD, B1_GRUPO, B1_TIPO, B1_UM FROM SB1010 WHERE  B1_COD = '" . $codigo ."' AND D_E_L_E_T_ = ''");
             $produto    = $query->fetchAll(PDO::FETCH_ASSOC);   
            // echo json_encode($produto, JSON_UNESCAPED_UNICODE);

         }
         catch(Exception $e)
         {
             echo $e->getMessage();
             exit;
         }
 
         return $produto;
    }


    /*
     * Converte Data
     */ 
    function DataConvert($data)

    {
        if ($data <> '') 
        {
            $result = substr($data,8,2) . '/' . substr($data,5,2) . '/' . substr($data,0,4);
        }
        else
        {
            $result = '';
        }
       

        return $result;
    }
 

?>