<?php
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashsgo extends Base {

	/**
	 * dashboard::__construct()
	 *
	 * Responsável por controlar o dashboard
	 *
	 * @return
	 */
	public function __construct(){

		parent::__construct();

		//$this->load->model('Dashboard_model','dashboard');

	}

	public function index(){
		#$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));

		$this->layout->region('html_header', 'view_html_header');
		#$this->layout->region('menu', 'view_menu', $menu);
		$this->layout->region('menu_lateral', 'view_menu_lateral');
		$this->layout->region('corpo', 'dashsgo_view');
		$this->layout->region('rodape', 'view_rodape');
		$this->layout->region('html_footer', 'view_html_footer');
		// Então chama o layout que irá exibir as views parciais...
		$this->layout->show('layout');

	}

}
