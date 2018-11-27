<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dashTelecomAjax extends Base {
     
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
        $this->load->library('Util', '', 'util');  
        $this->load->model('dashboard/Dashtelecom_model','tcomModel');
           
	} 
    
    /**
	 * dashboard::index()
     * 
     * Dashboard de Telecom
	 * 
	 * @return
	 */
    public function index(){
        
        echo 'sem acesso'; exit();
        
    }
    
    public function historicoPendencias($unidade = 'todas'){
        
        $resultado = $this->tcomModel->historicoPendencias($unidade);
        if($resultado){
            foreach($resultado as $res){
                $idStatus[] = $res->idStatus;
                $quantidade[$res->idStatus] = $res->qtd;
                $nome[$res->idStatus] = $res->historico;
            }
        
            $resDados['dados']['status'] = $nome;
            foreach($idStatus as $sta){
                $resDados['dados'][$nome[$sta]] = $quantidade[$sta];
            }
        }else{
            $resDados['dados'] = array('status'=>'Nenhum', 'qtd' => 0);
        }
        $this->load->view('view_json',$resDados);
        
    }
    
    public function qtdContHistPend($unidade = 'todas'){
        
        $resDados['dados']['qtd'] = $this->tcomModel->qtdContHistPend($unidade);
        $this->load->view('view_json',$resDados);
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */