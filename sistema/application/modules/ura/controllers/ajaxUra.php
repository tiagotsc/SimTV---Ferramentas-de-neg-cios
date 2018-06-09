<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ajaxUra extends MX_Controller {

    /**
     * relatorio::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->load->model('ura/ura_model','ura');
        
	} 
     
	/**
     * relatorio::index()
     * 
     * Lista os relatórios existentes
     * 
     */
	public function index()
	{ 
	   
	}
    
    public function dadosDashboard(){
        
        $resDados['dados'] = $this->ura->nodesCadastrados('Ativo');
        $this->load->view('view_json',$resDados);

    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */