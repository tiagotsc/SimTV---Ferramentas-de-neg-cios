<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class AjaxTelecom extends MX_Controller
{
    
	/**
	 * AjaxTelecom::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
		parent::__construct();
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
    }
    
    /**
	 * AjaxTelecom::carregaNodes()
	 * 
     * Pega os nodes do SIGA de acordo com o permissor
     * 
	 */
    public function carregaNodesSiga(){
        
        $this->load->model('ura/ura_model','ura');
        $resDados['dados'] = $this->ura->nodes($this->input->post('permissor'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * AjaxTelecom::carregaNodes()
	 * 
     * Pega os nodes INTERNO de acordo com ID
     * 
	 */
    public function carregaNodesInterno(){

        $this->load->model('node_model','node');
        $resDados['dados'] = $this->node->nodes($this->input->post('unidade'), false, false);
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    public function dadosNovoAssinante(){
        #$_POST['permissor'] = 61; $_POST['assinante'] = 17095;
        $this->load->model('edificacao_model','edificacao');
        
        if($this->input->post('permissor') and $this->input->post('assinante')){
        
            $resDados['dados'] = $this->edificacao->dadosNovoAssinante($this->input->post('permissor'), $this->input->post('assinante'));
        
        }else{
            $resDados['dados'] = array();
        }
        $this->load->view('view_json',$resDados);
    }
    
    public function dadosAssinanteMudancaEndereco(){
        #$_POST['permissor'] = 51; $_POST['assinante'] = 1341870;
        $this->load->model('edificacao_model','edificacao');
        
        if($this->input->post('permissor') and $this->input->post('assinante')){
        
            $resDados['dados'] = $this->edificacao->dadosAssinanteMudancaEndereco($this->input->post('permissor'), $this->input->post('assinante'));
        
        }else{
            $resDados['dados'] = array();
        }
        $this->load->view('view_json',$resDados);
        
    }
    
    public function existeEndereco(){
        
        #$_POST['endereco'] = 'TEODULO ALBUQUERQUE';
        #$_POST['cidade'] = 'SALVADOR';
        #$_POST['cd_estado'] = '5';
        #$_POST['bairro'] = 'CABULA VI';
        #$_POST['numero'] = '1';
        
        $this->load->model('edificacao_model','edificacao');
        $resDados['dados'] = $this->edificacao->existeEndereco();
        $this->load->view('view_json',$resDados);
        
    }
                
}
