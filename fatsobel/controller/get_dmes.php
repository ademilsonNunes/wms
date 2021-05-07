<?php 
  
   define('DB_HOST'        , "192.168.0.16");
   define('DB_USER'        , "sa");
   define('DB_PASSWORD'    , "S0b3l!4dm.");
   define('DB_NAME'        , "BISOBEL");
   define('DB_DRIVER'      , "sqlsrv");
  
   require_once ($_SERVER['DOCUMENT_ROOT'] ."/fatsobel/class/Conexao.php");
  
   try
   {
       $Conexao    = Conexao::getConnection();
       $query      = $Conexao->query("SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( (SELECT EOMONTH ( getdate() )) ) +0 AS QTDDIASEUTEIS") ;
      // $query      = $Conexao->query("SELECT BISOBEL.dbo.fncQtde_Dias_Uteis_Mes( (SELECT EOMONTH ( getdate() )) ) AS QTDDIASEUTEIS") ;       
       $calendario   = $query->fetchAll(PDO::FETCH_ASSOC); 
        
       echo json_encode($calendario, JSON_UNESCAPED_UNICODE);  
   }
   catch(Exception $e)
   {    
       echo $e->getMessage();
       exit;
   }
  

?>