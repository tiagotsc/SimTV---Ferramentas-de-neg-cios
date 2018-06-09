<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 06/10/15
 * Time: 17:22
 */

class Teste extends CI_Controller {

    public function index()
    {
        $menu['menu'] = false;
        $menu['menu_satva'] = false;

        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('corpo', 'teste/view_corpo');
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');

        $this->layout->show('layout');
    }

}