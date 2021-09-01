<?php

namespace plugins\TFSistDFe;

use Adianti\Widget\Base\TElement; 
use Adianti\Widget\Dialog\TMessage;

/**
 * TFSistDFe
 */
class TFSistDFe extends TElement
{   
    private $curl = '';
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct('div');
     //   $this->id = 'tfsisDFe' . uniqid();
     //   $this->elements = array();
        
        $this->curl = curl_init();
        curl_setopt_array($this->curl, array(
          CURLOPT_URL => 'https://www.fsist.com.br/usuario/comandos.aspx?t=login&rad=02418839941677482&usuario=sobelsuprema&senha=Ade*ade@4522',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Cookie: FSistSessao=ec3ose4fovpu5zn2bz2n55r4; UsuarioID=729432484; monitorv=3'
          ),
        ));
        
        $response = curl_exec($this->curl);

        if($response <> 'OK')
        {
          new TMessage('info', 'Falha na comunicação com o webservice.');
        }
      
    }

    /**
     * TODO - Tratar número de pagínas gerar loop do resultado e agregar em um único JSON
     * getNFeDevJSON()
     */
    public function getNFeDevJSON($numpag)
    {
      $result = array();
      
     for ($i=0; $i <= $numpag ; $i++) 
     { 
       
        curl_setopt_array($this->curl, array(
          CURLOPT_URL => 'https://www.fsist.com.br/usuario/monitorlista3.aspx?t=tabela&tipo=nfe',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('cols' => '[{"name":"emissao","title":"Emissão","align":"center","visible":true},{"name":"etiqueta","title":"Etiquetas","align":"left","visible":true},{"name":"chave","title":"Chave","align":"center","visible":true},{"name":"<opcoes>","title":"","align":"center","visible":true},{"name":"numero","title":"Número","align":"right","visible":true},{"name":"serie","title":"Série","align":"center","visible":true},{"name":"tipo","title":"Tipo","align":"center","visible":true},{"name":"valor","title":"Valor","align":"right","visible":true},{"name":"emitcnpjcpf","title":"CNPJ/CPF","titleTop":{"title":"Emitente"},"visible":true},{"name":"emit","title":"Nome","titleTop":{"title":"Emitente"},"visible":true},{"name":"emitie","title":"IE","titleTop":{"title":"Emitente"},"visible":true},{"name":"emituf","title":"UF","align":"center","titleTop":{"title":"Emitente"},"visible":true},{"name":"destcnpjcpf","title":"CNPJ/CPF","titleTop":{"title":"Destinatário"},"visible":true},{"name":"dest","title":"Nome","titleTop":{"title":"Destinatário"},"visible":true},{"name":"destie","title":"IE","titleTop":{"title":"Destinatário"},"visible":true},{"name":"destuf","title":"UF","align":"center","titleTop":{"title":"Destinatário"},"visible":true},{"name":"transpcnpjcpf","title":"CNPJ/CPF","titleTop":{"title":"Transportador"},"visible":true},{"name":"transp","title":"Nome","titleTop":{"title":"Transportador"},"visible":true},{"name":"transpie","title":"IE","titleTop":{"title":"Transportador"},"visible":true},{"name":"transpuf","title":"UF","align":"center","titleTop":{"title":"Transportador"},"visible":true},{"name":"status","title":"Status","align":"center","visible":true}]','filtroCNPJCPFTipo' => 'null','filtroCNPJCPF' => '','buscarPorTipo' => 'Personalizado','buscarPor' => '[CFOPs] "1201 ou 1202 ou 1203 ou 1204 ou 1208 ou 1209 ou 1410 ou 1411 ou 1503 ou 1504 ou 1505 ou 1506 ou 1553 ou 1660 ou 1661 ou 1662 ou 1918 ou 1919 ou 2201 ou 2202 ou 2203 ou 2204 ou 2208 ou 2209 ou 2410 ou 2411 ou 2503 ou 2504 ou 2505 ou 2506 ou 2553 ou 2660 ou 2661 ou 2662 ou 2918 ou 2919 ou 3201 ou 3202 ou 3211 ou 3503 ou 3553 ou 5201 ou 5202 ou 5208 ou 5209 ou 5210 ou 5410 ou 5411 ou 5412 ou 5413 ou 5503 ou 5553 ou 5555 ou 5556 ou 5660 ou 5661 ou 5662 ou 5918 ou 5919 ou 6201 ou 6202 ou 6208 ou 6209 ou 6210 ou 6410 ou 6411 ou 6412 ou 6413 ou 6503 ou 6553 ou 6555 ou 6556 ou 6660 ou 6661 ou 6662 ou 6918 ou 6919 ou 7201 ou 7202 ou 7210 ou 7211 ou 7553 ou 7556" [Tipo] Saída','data1' => '29/05/2021','data2' => (string)date('d/m/Y'),'pagina' =>  $i ),
          CURLOPT_HTTPHEADER => array(
            'Cookie: FSistSessao=ec3ose4fovpu5zn2bz2n55r4; UsuarioID=729432484; monitorv=3'
          ),
        ));
           
         $response = curl_exec($this->curl);
         //$result .= json_decode($response);
         array_push($result, json_decode($response));
     }
     
      curl_close($this->curl);
     // $response = json_decode($result, true);
      //$response = json_encode($response);
     
      return $result;
    }
}
