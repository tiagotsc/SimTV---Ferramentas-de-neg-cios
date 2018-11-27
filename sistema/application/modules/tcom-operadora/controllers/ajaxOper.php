<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ajaxOper extends Base {
     
     private $logDados;
     const modulo = 'tcom-operadora';
     const controller = 'operadora';
     const pastaView = 'operadora';
     const tabela = 'tcom_oper';
     const assunto = 'Operadora (Ponto A)';
     const modelAssunto = 'toperadora';
     const perModulo = 274;
     const perPesq = 310;
     const perCadEdit = 311;
     const perDeletar = 312;
     
    /**
     * relatorio::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('operadora_model','toperadora');
        $this->load->model('tcom-contrato/contrato_model','contrato');
        $this->load->model('tcom-contrato/contratoValor_model','contratoValor');        
        
	} 
     
	public function dadosFaturamento(){
        #$_POST['id'] = 686;
        if(!isset($_POST['id'])){
            return $this->load->view('view_json',array());
        }
       
        $resDados['dados'] = $this->toperadora->dadosFaturamento($this->input->post('id'));
        #echo '<pre>'; print_r($resDados['dados']); exit();
        $this->load->view('view_json',$resDados);
       
	}
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */