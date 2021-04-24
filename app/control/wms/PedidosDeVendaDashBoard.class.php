<?php

use Adianti\Control\TPage;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;

/**
 * PedidosDeVendaDashBoard
 *
 * @version    1.0
 * @package    PedidosDeVendaDashBoard
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PedidosDeVendaDashBoard extends TPage
{ 
    /**
     * Page constructor
     */
    function __construct()
    {
        parent::__construct();
       
        $frame = new TElement('iframe');
        $frame->width  = '1259';
        $frame->height = '1020';
        $frame->src = 'https://app.powerbi.com/view?r=eyJrIjoiZGEzNWU0MTEtOTY4ZS00MDkzLTkxZmYtMGZiZmZkOWY0OGM0IiwidCI6Ijc0MjgwOGM5LWQyMjEtNGI0OS05NTc0LTY2MjBlZTY2YmYwZSJ9&pageName=ReportSection';
        $frame->frameborder = '0';
        $frame->allowFullScreen = 'true';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($frame);
        parent::add($vbox);
    }       
}