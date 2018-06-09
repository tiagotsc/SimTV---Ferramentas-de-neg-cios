<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ura extends Base {
     
     private $logDados;
     
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
        $this->logDados['modulo'] = 'Ura';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('ura_model','ura');
        
	} 
     
	/**
     * relatorio::index()
     * 
     * Lista os relatórios existentes
     * 
     */
	public function index()
	{ 
	   
	}
    
    /**
     * ura::operadoras()
     * 
     * Tela inicial de pesquisa da operadora
     * 
     * @return
     */
    public function fichaNode(){
        
        $dados['permissor'] = $this->dadosBanco->unidade();
        $dados['nodes'] = $this->ura->nodes();
        $dados['tipos'] = $this->ura->tipos();

        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(261, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'node/view_frm_node', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');   
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function salvarNode(){
        
        try{
            
            $status = $this->ura->insereNode();
                
            #$this->logDados['descricao'] = utf8_encode('ura - Cadastra empréstimo');
            #$this->logDados['acao'] = 'INSERT'; 
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        #$this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Node gravado com sucesso!</strong></div>');
            
            redirect(base_url('ura/fichaNode')); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao gravar node, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('ura/fichaNode'));
            
        }
        
    }
    
    /**
     * ura::pesqOperadora()
     * 
     * Pesquisa a operadora
     * 
     * @param mixed $nome Nome da operadora para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesq(){

        $mostra_por_pagina = 30;
        
        $this->crud->ini();

        $par = $this->crud->getParMetodo();
        #echo '<pre>'; print_r($par); exit();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->ura->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['permissor'] = $this->dadosBanco->unidade();
        $crud['tipos'] = $this->ura->tipos();
 
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');

        if(in_array(260, $this->session->userdata('permissoes'))){
        
            #$this->layout->region('corpo', self::pastaView.'/view_psq', $crud);
            $this->layout->region('corpo', 'node/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }

    
    public function mudarStatusNode(){
        
        try{
        
            $status = $this->ura->alteraStatusNode(); 
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Status alterado com sucesso!</strong></div>');
            redirect(base_url('ura/ura/pesq'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar status, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('ura/ura/pesq'));
        
        }
        
    }
    
    public function mudarStatusNodeTodos(){
        
        try{
        
            $status = $this->ura->alteraStatusNodeTodos(); 
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Status alterado dos nodes selecionados com sucesso!</strong></div>');
            redirect(base_url('ura/ura/pesq'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar status dos nodes selecionados, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('/ura/ura/pesq'));
        
        }
        
    }
    
    public function dashboardNode(){
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'percod' => 'Permissor', 
                                'nodeNro' => 'Node',
                                'tipo' => 'Descri&ccedil;&atilde;o',
                                'dataInicio' => 'Data In&iacute;cio',
                                'dataFim' => 'Data Fim',
                                'origem' => 'Origem',
                                'status' => 'Status',
                                'observacao' => 'Obs',
                                );
        $dados['dados'] = $this->ura->nodesCadastrados('Ativo');
        
        #echo '<pre>'; print_r($dados['dados']); exit();
        
        if(in_array(263, $this->session->userdata('permissoes'))){
        
            $this->load->view('node/view_dashboard',$dados);
        
        }else{
            
            $this->load->view('view_permissao');
            
        }
        
        
    }
    
    public function pegaTodasObs(){
        #$_POST['idNode'] = 3077;
        $resDados['dados'] = $this->ura->pegaTodasObs($this->input->post('idNode'));
        $this->load->view('view_json',$resDados);
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */