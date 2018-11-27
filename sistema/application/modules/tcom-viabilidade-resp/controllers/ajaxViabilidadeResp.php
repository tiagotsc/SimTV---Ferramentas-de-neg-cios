<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class ajaxViabilidadeResp extends MX_Controller
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
        
        $this->load->model('tcom-viabilidade-resp/viabilidade_resp_model','viabilidadeResp');
        $this->load->model('tcom-viabilidade-resp/ativacao_model','ativacao');
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
  
    }
    
    /**
	 * ajaxViabilidadeResp::dadosViabilidade()
	 * 
     * Pega dados da viabilidade
     * 
	 */
    public function dadosViabilidade(){
        
        #$_POST['id'] = 6;
        
        if(!isset($_POST['id'])){
            return $this->load->view('view_json',array());
        }
        
        $this->load->model('tcom-viabilidade/viabilidade_model','viabilidade');
        
        $resDados['dados'] = $this->viabilidade->dadosViabilidade($this->input->post('id'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    public function dadosViabRespPorViabilidade(){
        #$_POST['id'] = 30;
        if(!isset($_POST['id'])){
            return $this->load->view('view_json',array());
        }
        
        $resDados['dados'] = $this->viabilidadeResp->dadosViabRespPorViabilidade($this->input->post('id'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
        
    }
    
    public function dadosCircuitoContrato(){
        #$_POST['idContrato'] = 81;
        $resDados['dados'] = $this->viabilidadeResp->dadosCircuitoContrato($this->input->post('idContrato'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * ajaxViabilidadeResp::carregaNodes()
	 * 
     * Pega os nodes INTERNO de acordo com ID
     * 
	 */
    public function comboNode(){
        #$_POST['unidade'] = 12;
        $resDados['dados'] = $this->viabilidadeResp->nodes(false, $this->input->post('unidade'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
                
}
