<?php
/**
 * ConsultaPalete
 *
 * @version    1.0
 * @package    Consultar informações de produtos pelo ID do Pallet
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ConsultaPalete extends TPage
{
    private $form;
    
    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();
        
        // create the form
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Consulta Palete');
        
        // create the form fields
        $barcode = new TBarCodeInputReader('Leitura');
        
        $barcode->setSize( '100%');
        
        $barcode->setChangeAction( new TAction( [$this, 'onChange'] ) );
        
        $this->form->addFields( [new TLabel('Código')], [$barcode, new TLabel('ID Pallet')] );
        
        $this->form->addAction('Show', new TAction(array($this, 'onShow')), 'far:check-circle green');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
    
    /**
     *
     */
    public static function onChange($param)
    {
        //new TMessage('info', '<b>onChange</b><br>'.str_replace(',', '<br>', json_encode($param)));
    }
    
    /**
     * Show the form content
     */
    public function onShow($param)
    {
        $data = $this->form->getData();
        $this->form->setData($data); // put the data back to the form
        $pallet = (float)$data->Leitura;
        
        $query = "SELECT (PREDIO + '.' + RUA + '.' + CAST(BLOCO AS VARCHAR) + '.' + CAST(APTO AS VARCHAR)) AS 'END',     
                  AM.PRODUTO,
                  TR.DESCRICAO,                 
                  QTDE,
                  LOTE,
                  DATA_ENT,
                  DATA_VAL,
                  AM.CXS_PALLET,
                  BLOQUEADO
                  FROM ARQ_MXE AM
                  LEFT JOIN TAB_PROD TR ON TR.PRODUTO = AM.PRODUTO
                  WHERE NUM_PALLET = {$pallet}";    

        try
        {
           TTransaction::open('sisdep'); // abre uma transação            
            $conn = TTransaction::get(); // obtém a conexão
            
             // realiza a consulta
             $result = $conn->query($query);
         
             foreach ($result as $row) // exibe os resultados
             {   
                 $res = '<b>Endereço: </b>'. $row['END'] . '<br/>' . '<b>Produto: </b>' . $row['PRODUTO'] . '<br/>' .'<b>Descrição: </b> ' . $row['DESCRICAO']. '<br/>' . '<b>Qtde: </b> ' . $row['QTDE'] . '<br/>' . '<b>Lote: </b> ' . $row['LOTE'] . '<br/>' . '<b>Data Ent.: </b> ' . $row['DATA_ENT'] . '<br/>' . '<b>Data Val.: </b> ' . $row['DATA_VAL'] . '<br/>' . '<b>Cxs_Pallet: </b> ' . $row['CXS_PALLET'] . '<br/>';
                new TMessage('info',  $res);            
                 
             }
 
           TTransaction::close(); // fecha a transação.
        }
        catch (Exception $e)
        {
           new TMessage('error', $e->getMessage());
        }
    }
}