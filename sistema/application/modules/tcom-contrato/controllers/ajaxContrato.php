<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class ajaxContrato extends MX_Controller
{
    
	/**
	 * ajaxViabilidadeResp::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
		parent::__construct();
        
        # Configurações do sistema
        include_once('configSistema.php');
        
        $this->load->model('tcom-contrato/contrato_model','contrato');
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
    }
    
    /**
	 * ajaxViabilidadeResp::dadosContrato()
	 * 
     * Pega dados da viabilidade
     * 
	 */
    public function dadosContrato(){
        
        #$_POST['id'] = 81;
        
        if(!isset($_POST['id'])){
            return $this->load->view('view_json',array());
        }
        
        $resDados['dados']['equipamentos'] = $this->contrato->equipamentosAssociados($this->input->post('id'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    public function listaHistoricos(){
        
        #$_POST['id'] = 92;
        
        if($this->input->post('id')){
            $resDados['dados'] = $this->contrato->listaHistoricos($this->input->post('id'));
        }else{
            $resDados['dados'] = false;
        }
        
        $this->load->view('view_json',$resDados);
    }
    
    public function valoresContrato(){
        
        #$_POST['id'] = 2228;
        
        $resDados['dados'] = $this->contrato->valoresContrato($this->input->post('id'));
        $this->load->view('view_json',$resDados);
        
    }
    
    public function operadorasFaturamento(){
        
        #$_POST['pai'] = 6;
        $this->load->model('tcom-operadora/operadora_model','operadora');
        $resDados['dados'] = $this->operadora->operadorasFaturamento($this->input->post('pai'));
        $this->load->view('view_json',$resDados);
        
    }
       
                
}
