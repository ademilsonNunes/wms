<?php

use Adianti\Control\TPage;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;

/**
 * ContasAPagarDashBoard
 *
 * @version    1.0
 * @package    ContasAPagarDashBoard.class
 * @subpackage wms
 * @author     Ademilson NUnes
 * @copyright  Copyright (c) 2021 Sobel Suprema Insdustria de produtos de limpeza LTDA. (http://www.sobelsuprema.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ContasAPagarDashBoard extends TPage
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
        $frame->src = 'https://www.powerbi.com/view?r=eyJrIjoiMzY3MTA4ZmYtYjA4Yy00YTA2LTgyZDUtNGRiZGE1ZTUyNjMxIiwidCI6Ijc0MjgwOGM5LWQyMjEtNGI0OS05NTc0LTY2MjBlZTY2YmYwZSJ9';
        $frame->frameborder = '0';
        $frame->allowFullScreen = 'true';

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($frame);
        parent::add($vbox);
    }       
}