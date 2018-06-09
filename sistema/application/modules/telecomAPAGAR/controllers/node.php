<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class node extends Base {
     
     private $logDados;
     private $perModulo = 274;
     private $perPesq = 276;
     private $perCadEdit = 277;
     private $perDeletar = 278;
     
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
        $this->logDados['modulo'] = 'Telecom - node';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('ura/ura_model','ura');
        $this->load->model('node_model','node');
        
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
        
        if(in_array($this->perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'view_principal');
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function pesqNode(){
        
        #$this->output->enable_profiler(TRUE);
        $mostra_por_pagina = 30;
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->node->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        #$postEncode = $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['permissor'] = $this->dadosBanco->unidade();
        $crud['bairros'] = $this->node->campoDistinctNode('bairro');
        $crud['perEditarCadastrar'] = $this->perCadEdit;
        $crud['perExcluir'] = $this->perDeletar;
 
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        
        
        if(in_array($this->perPesq, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'node/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function fichaNode($id = false){
        
        $this->crudModel->setTabela('sistema.tcom_node');
        $this->crudModel->setCampoId('id');
        
        if($id){
            
            $dados = $this->crudModel->dadosId($id);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
             
				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $dados['celulasSelecionadas'] = $this->node->celulasNode($id);
            
        }else{
            
            $campos = $this->crudModel->camposTabela();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            $dados['celulasSelecionadas'] = array();
            
        }
        
        $dados['permissor'] = $this->dadosBanco->unidade();
        $dados['estado'] = $this->dadosBanco->estado();
        $dados['nodes'] = $this->ura->nodes();
        $dados['celulas'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);

        if(in_array($this->perCadEdit, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'node/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function salvarNode(){
        
        array_pop($_POST);
        
        if($this->input->post('id')){
            
            try{
            
                $status = $this->node->atualiza();
                $this->logDados['descricao'] = 'Telecom - Node - Atualiza node ('.$this->input->post('id').')';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Node salvo com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar node, caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url($this->session->userdata('indexPHP').'telecom/node/fichaNode/'.$this->input->post('id'))); 
            
        }else{
            
            try{
            
                $status = $this->node->insere();
                $this->logDados['descricao'] = 'Telecom - Node - Cadastra node ('.$status.')';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $this->logGeral->grava($this->logDados);
            
            $_POST['id'] = $status;
            
            if($status){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Node salvo com sucesso!</strong></div>');
            }else{
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar node, caso o erro persiste comunique o administrador!</div>');
            }
            redirect(base_url($this->session->userdata('indexPHP').'telecom/node/fichaNode/'.$this->input->post('id'))); 
            
        }
        
    }
    
    public function deletaNode(){
        
        $this->crudModel->setTabela('sistema.tcom_node');
        $this->crudModel->setCampoId('id');
        
        try{
            $status = $this->crudModel->delete();
            
            $this->logDados['descricao'] = 'Telecom - Node - Apaga node';
            $this->logDados['acao'] = 'DELETE'; 
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        $this->logGeral->grava($this->logDados);
        
        if($status){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Node apagado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao node, caso o erro persiste comunique o administrador!</div>');
        }
        redirect(base_url($this->session->userdata('indexPHP').'telecom/node/pesqNode'));

    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */