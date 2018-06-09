<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class ajaxFaturamento extends MX_Controller
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
        
        $this->load->model('tcom-faturamento/faturamento_model','faturamento');
        $this->load->model('tcom-faturamento/delin_model','delin');
        $this->load->model('tcom-faturamento/notafiscal_model','notafiscal');
        
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
    public function dadosTabelaDinamica(){
        
        #if($this->session->userdata('cd') == 6){
        #$_POST['tipo_acao'] = 'delin';
        #$_POST['data'] = '06/2017';
        #}
        $resDados['dados'] = ($this->input->post('tipo_acao'))? $this->faturamento->dadosTabelaDinamica(): false;
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
    
    public function listarArquivosAcao(){
        # $_POST['tipo_acao'] = 'delin'; 
        # $_POST['ano'] = '2017';
        $resDados['dados'] = ($this->input->post('tipo_acao') and $this->input->post('ano'))? $this->delin->delinArquivos(): false;
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
        
    }
                
}
