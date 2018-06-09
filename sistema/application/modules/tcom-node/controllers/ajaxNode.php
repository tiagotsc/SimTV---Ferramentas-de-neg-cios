<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class AjaxNode extends MX_Controller
{
    
    /**
     * AjaxTelecom::__construct()
     * 
     * @return
     */
    public function __construct(){
        parent::__construct();
        
        # Configurações do sistema
        include_once('configSistema.php');
        
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
    
    
    
}