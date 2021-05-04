<?php 

   //error_reporting(0);

   use SimpleExcel\SimpleExcel;

   define('DB_HOST'        , "192.168.0.15");
   define('DB_USER'        , "sa");
   define('DB_PASSWORD'    , "S0b3l!4dm.");
   define('DB_NAME'        , "Protheus_Producao");
   define('DB_DRIVER'      , "sqlsrv");
  
   require_once "../Class/Conexao.php";
   require_once "../Class/SimpleExcel/src/SimpleExcel/SimpleExcel.php";
      
   try
   {
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
                ." AND B1_COD NOT IN ('1401.19.03X05L', '1201.58.12X01L_', '1501.23.03X05LE', '1501.23.06X03LE', '1501.24.03X05LE', '1501.24.06X03LE', '1401.20.03X05L', '4001.05.03X05L', '4001.05P.03X05L', '4001.25.03X05L', '1401.23.03X05L', '1701.31.03X05L', '1701.32.03X05L', '1701.33.03X05L', '1701.33.24X500', '1701.51.03X05L', '1902.02.01X500', '1301.16.02X05L') ";
          
       $Conexao      = Conexao::getConnection();
       $queryPA      = $Conexao->query($sqlPA);       
       $resultPA     = $queryPA->fetchAll(PDO::FETCH_ASSOC);   

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
                           ." G1_QUANT	        AS QTDE "
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
                         //Estrutura PI
                        $totalPIUnit += $custo[0]['CUSTONET'] * $pi['QTDE'];
                        $totalPINet  += $custo[0]['CUSTONET'] * $pi['QTDE'];
                     }                   
                }
                                

                $custo1 = getCustoProduto( $estrutura['COMP'] );
               // estrutura PA
                
                if  ($tipo <> 'PI')
                {
                    $totalME += ($custo1[0]['CUSTONET'] * $estrutura['QTDE']);
                    $totalMP = $totalPINet * $estrutura['QTDE'];
                }

            }

            $totalPA = $totalME + $totalMP;    

            $tabelaCusto[$i]['CUSTO']  .= $totalPA;       

            $totalMP = '';
            $totalME = '';
            $totalPA = '';

             $totalPIUnit = '';    
            $totalPINet = '';                        
       }
       
       $arquivo = 'planilha_custo.xls';

       $html  = "<table border='0' width = '100%'>" ;
       $html .= "<tr>" ;
       $html .= "<td>CODIGO</td><td>DESCRICAO</td><td>CUSTO</td>" ;
       $html .= "</tr>" ;

       foreach ($tabelaCusto as $custo) 
       {
           $html .= '<tr>' ;
           $html .= '<td>' . $custo['CODIGO']    . '</td>' ;
           $html .= '<td>' . $custo['DESCRICAO'] . '</td>' ;
           $html .= '<td>' . $custo['CUSTO']     . '</td>' ;
           $html .= '</tr>' ;
       }

       $html .= "</table>" ;

       // Configurações header para forçar o download
       header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
       header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
       header ("Cache-Control: no-cache, must-revalidate");
       header ("Pragma: no-cache");
       header ("Content-type: application/x-msexcel");
       header ("Content-Disposition: attachment; filename=\"nome_arquivo.xls\"" );
       header ("Content-Description: PHP Generated Data" );

       echo $html;


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
            $query      = $Conexao->query("EXEC PROC_GETULTCUSTOPROD '20180601', '" . date("Ymd") . "', '" . $codigo . "' ");
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