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
       $query      = $Conexao->query("EXEC FATSOBEL '" . date('Ymd') . "', '"  . date('Ymd') .  "'");
       
       $carteira   = $query->fetchAll(PDO::FETCH_ASSOC); 
        
       echo json_encode($carteira, JSON_UNESCAPED_UNICODE);  
   }
   catch(Exception $e)
   {    
       echo $e->getMessage();
       exit;
   }
  

?>