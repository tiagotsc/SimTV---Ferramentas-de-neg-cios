<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class notaDebCred extends Base {
     
     private $logDados;
     const modulo = 'tcom-faturamento';
     const controller = 'faturamento';
     const pastaView = 'faturamento';
     const tabela = 'tcom_debcred';
     const assunto = 'Nota';
     const modelAssunto = 'notaDebCred';
     const perModulo = 274;
     const perValores = 407;
     
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
        #$this->load->model('faturamento_model',self::modelAssunto);
        #$this->load->model('delin_model','delin');
        $this->load->model('tcom-contrato/contrato_model','contrato');
        #$this->load->model('tcom-contrato/contratoValor_model','contratoValor');
        #$this->load->model('tcom-operadora/operadora_model','operadora');
        #$this->load->model('tcom-faturamento/notafiscal_model','notafiscal');
        $this->load->model('tcom-faturamento/nota_debcred_model',self::modelAssunto);
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('TELECOM', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('TELECOM', $this->session->userdata('permissoes')));
        
	} 
     
	/**
     * Telefonia::index()
     * 
     * Tela inicial da telefonia
     * 
     * @return
     */
    function index()
    { 
        
        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'tcom/view_principal');
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function ajaxDadosNotas(){
        
        $modelAssunto = self::modelAssunto;
        
        $resDados['dados'] = $this->$modelAssunto->dadosNotas($this->input->post('idContrato'));
        $this->load->view('view_json',$resDados);
    }
    
    public function salvar(){
        
        $modelAssunto = self::modelAssunto;

        #array_pop($_POST);
        
        if($this->input->post('nota_id')){
            
            try{
                $status = $this->$modelAssunto->atualiza();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Atualiza '.self::assunto.' - '.$this->input->post('tipo').' ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' - '.$this->input->post('tipo').' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/pesqContr/'.$this->input->post('nome_contrato')));
            
        }else{
            
            try{
            
                $status = $this->$modelAssunto->insere();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Cadastra '.self::assunto.' - '.$this->input->post('tipo').' ('.$status.')';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' - '.$this->input->post('tipo').' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/pesqContr/'.$this->input->post('nome_contrato'))); 
            
        }
        
    }
    
    public function deleta(){
        
        $modelAssunto = self::modelAssunto;
        
        try{
            $status = $this->$modelAssunto->delete();
            
            $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Apaga '.self::assunto;
            $this->logDados['acao'] = 'DELETE'; 
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' apagada com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url(self::modulo.'/'.self::controller.'/pesq'));

    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */