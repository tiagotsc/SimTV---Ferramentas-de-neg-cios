<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class callcenter extends Base {
    
    const perVisualizar = 307;
    
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
           
	} 
     
	/**
	 * dashboard::index()
     * 
     * Lista os relatórios existentes
	 * 
	 * @return
	 */
	public function acomcall()
	{ 
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));

        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_conteudo');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	
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
	 * dashboard::callcenter()
     * 
     * Dashboard do Callcenter, Acompanhamento Diário
	 * 
	 * @return
	 */
    public function index(){
        #$this->output->enable_profiler(TRUE);
        $this->load->model('telefonia/fatura_model','faturaModel');
        
        $dashboardPermitidos = $this->dashboard->dashboardPermitidos($this->session->userdata('permissoes')); 
        foreach($dashboardPermitidos as $dP){
            $dashboard[] = $dP->cd_grafico;
        }

        $dados['dashboardPermitidos'] = $dashboard;

        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perVisualizar, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'dashboard/view_callcenter',$dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
        $this->layout->show('layout');
        
    }



    /**
     * dashboard::callcenter()
     *
     * Dashboard do Callcenter, json
     *
     * @return
     */

    public function dadosGraficoCallcenter($data_inicio, $data_final) {
        //var_dump($data_final);
        $dadosJson = file_get_contents('http://telefonia-devel.simtv.com.br/json/get-dados/acom-callcenter/'.$data_inicio.'/'.$data_final);
        echo $dadosJson;
    }

}
