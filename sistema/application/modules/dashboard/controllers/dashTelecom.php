<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dashTelecom extends Base {
    
    const acessoGrafico = 13;
    
    /**
     * dashboard::__construct()
     * 
     * Responsável por controlar o dashboard
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->load->model('Dashboard_model','dashboard');
        $this->load->model('dashboard/Dashtelecom_model','tcomModel');
           
	} 
    
    /**
	 * dashboard::registraAcesso()
     * 
     * Log de acessos ao dashboard
	 * 
	 * @return
	 */
    public function registraAcesso(){
        
        $this->dashboard->gravaAcesso();
        
    }
    
    /**
	 * dashboard::index()
     * 
     * Dashboard de Telecom
	 * 
	 * @return
	 */
    public function index(){
        #$this->output->enable_profiler(TRUE);
        
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes'),'array'); 
        $dados['dashboardPermitidos'] = array_column($dashboardPermitidos, 'cd_grafico');
        $dados['unidadeContratos'] = $this->tcomModel->unidadesContratos('P');
        $dados['acessoGrafico'] = self::acessoGrafico;

        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'dashboard/view_telecom',$dados);
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */