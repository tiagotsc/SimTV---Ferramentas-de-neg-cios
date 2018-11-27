<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class imposto extends Base {
     
     private $logDados;
     const modulo = 'tcom-fiscal';
     const controller = 'imposto';
     const pastaView = 'imposto';
     const tabela = 'tcom_imposto';
     const assunto = 'Imposto';
     const modelAssunto = 'statushist';
     const perModulo = 274;
     const perPesq = 405;
     const perCadEdit = 406;
     const perDeletar = 407;
     
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
        $this->load->model('imposto_model',self::modelAssunto);
        $this->load->model('tcom-contrato/contrato_model','contrato');
        
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
    
    public function pesq(){
        
        $mostra_por_pagina = 30;
        $modelAssunto = self::modelAssunto;
        
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;

        $resultado = $this->$modelAssunto->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        $resultado['tabela'] = self::assunto;
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['perEditarCadastrar'] = self::perCadEdit;
        $crud['perExcluir'] = self::perDeletar;
        $crud['assunto'] = self::assunto;
        $crud['estados'] = $this->$modelAssunto->estados();
        $crud['servicos'] = $this->$modelAssunto->servicosPesquisa();
 
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        
        
        if(in_array(self::perPesq, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function ficha($id = false){
        
        $modelAssunto = self::modelAssunto;
        
        $this->crudModel->setTabela(BANCO_TELECOM.'.'.self::tabela);
        $this->crudModel->setCampoId('id');
        
        if($id){
            
            $dados = $this->crudModel->dadosId($id);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->crudModel->camposTabela();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
        }
        
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller; 
        $dados['estados'] = $this->$modelAssunto->estados();   
        $dados['servicos'] = $this->$modelAssunto->servicos('A'); 
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $menu);

        if(in_array(self::perCadEdit, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function salvar(){
        
        $modelAssunto = self::modelAssunto;
        
        array_pop($_POST);
        
        if($this->input->post('id')){
            
            try{
                $status = $this->$modelAssunto->atualiza();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Atualiza '.self::assunto.' ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
        }else{
            
            try{
            
                $status = $this->$modelAssunto->insere();
                $this->logDados['descricao'] = ucfirst(self::modulo).' - '.self::assunto.' - Cadastra '.self::assunto.' ('.$status.')';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>'.self::assunto.' salva com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar '.strtolower(self::assunto).', caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url(self::modulo.'/'.self::controller.'/ficha/'.$this->input->post('id'))); 
            
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