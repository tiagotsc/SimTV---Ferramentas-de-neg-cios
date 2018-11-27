<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class ajaxViabilidade extends MX_Controller
{
    
	/**
	 * ajaxViabilidade::__construct()
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
	 * ajaxViabilidade::carregaNodes()
	 * 
     * Pega os dados do contrato informado
     * 
	 */
    public function dadosContrato(){
        #$_POST['id'] = 83;
        $this->load->model('viabilidade_model','viabilidade', 'viabilidade');
        $resDados['dados'] = $this->viabilidade->dadosContrato($this->input->post('id'));
        $this->load->view('view_json',$resDados);
        
    }
    
    public function pegaOperadorasUnidade(){
        
        #$_POST['unidade'] = 'N';
        $this->load->model('viabilidade_model','viabilidade', 'viabilidade');
        $resDados['dados'] = $this->viabilidade->operadorasUnidade($this->input->post('unidade'));
        $this->load->view('view_json',$resDados);
        
    }
                
}
