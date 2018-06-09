<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class teste extends Base {
     
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
        $this->logDados['modulo'] = 'Telecom/node';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->model('node_model','node');
        $this->load->library('Crud', '', 'crud');
        $this->load->model('base/crud_model','crudModel');
        
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
        
        #$this->util->setPositionMenu('');
        #$this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('TELECOM', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('TELECOM', $this->session->userdata('permissoes')));
        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        if(in_array(199, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'view_principal');
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function pesqViabilidade(){
        
        #$this->output->enable_profiler(TRUE);
        $mostra_por_pagina = 30;
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->viabilidade->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        
        $postEncode = $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        $this->layout->region('corpo', 'viabilidade/view_psq_viabilidade2', $crud);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function fichaViabilidade($id){
        
        $this->crudModel->setTabela('usuario');
        
        if($id){
            
            $this->crudModel->setCampoId('cd_usuario');
            
            $dados = $this->crudModel->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->crudModel->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
        
        }
        
        echo 'Ficha<br><br>';
        echo $id; exit();
    }
    
    public function salvarViabilidade(){
        
        $this->crudModel->setTabela('usuario');
        $this->crudModel->setCampoId('cd_usuario');
        
        array_pop($_POST);
        
        if($this->input->post('id')){
            
            try{
            
                $status = $this->crudModel->atualiza();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->crudModel->insere();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['id'] = $status;
        }
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio salvo com sucesso!</strong></div>');
            
            redirect(base_url('usuario/ficha/'.$this->input->post('id'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('usuario/ficha'));
            
        }
        
    }
    
    public function deletaViabilidade(){
        echo 'Deletar<br><br>';
        echo '<pre>'; print_r($_POST); exit();
    }
    
    /**
     * ura::operadoras()
     * 
     * Tela inicial de pesquisa da operadora
     * 
     * @return
     */
    public function node(){
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(260, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'ura/node/view_psq_node', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');   
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
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
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(261, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'ura/node/view_frm_node', $dados);
        
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
    public function pesqNode($nome = null, $status = null, $sort_by = 'nodeNro', $sort_order = 'asc', $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'nodeNro' => 'N&uacute;mero', 
                                'nodeDsc' => 'Descri&ccedil;&atilde;o',
                                'dataInicio' => 'Data in&iacute;cio',
                                'dataFim' => 'Data fim',
                                'origem' => 'Origem',
                                'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        /*switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'cd_ura_operadora';
                break;
            case 'status':
                $campoSortBy = 'status';
                break;
            default:
                $campoSortBy = 'nome';
        }*/
        $campoSortBy = $sort_by;
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNumero'] = ($this->input->post('numero') != '')? $this->input->post('numero') : $nome;
        $dados['postStatus'] = ($this->input->post('status') != '')? $this->input->post('status') : $status;
        
        $mostra_por_pagina = 30;
        $dados['dadosNode'] = $this->ura->pesquisaNode($dados['postNumero'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdNode'] = $this->ura->pesquisaQtdNode($dados['postNumero'], $dados['postStatus']); 
        
        $qtdRegistros = ($dados['qtdNode'][0]->total < $mostra_por_pagina)? $dados['qtdNode'][0]->total: $mostra_por_pagina;
        $dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('ura/pesqNode/'.$dados['postNumero'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdNode'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 7;
        $config['first_link'] = '&lsaquo; Primeiro';
        $config['last_link'] = '&Uacute;ltimo &rsaquo;';
        $config['full_tag_open'] = '<li>';
        $config['full_tag_close'] = '</li>';
        $config['first_tag_open']	= '';
       	$config['first_tag_close']	= '';
        $config['last_tag_open']		= '';
	    $config['last_tag_close']		= '';
	    $config['first_url']			= ''; // Alternative URL for the First Page.
	    $config['cur_tag_open']		= '<a id="paginacaoAtiva" class="active"><strong>';
	    $config['cur_tag_close']		= '</strong></a>';
	    $config['next_tag_open']		= '';
        $config['next_tag_close']		= '';
	    $config['prev_tag_open']		= '';
	    $config['prev_tag_close']		= '';
	    $config['num_tag_open']		= '';
		$this->pagination->initialize($config);
		$dados['paginacao'] = $this->pagination->create_links();
        
        $dados['postNumero'] = ($dados['postNumero'] == '0')? '': $dados['postNumero'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];

        $this->layout->region('html_header', 'view_html_header');

        $this->layout->region('menu_lateral', 'view_menu_lateral');
        if(in_array(260, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'ura/node/view_psq_node', $dados);
        
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
            redirect(base_url('ura/node'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao alterar status, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('ura/node'));
        
        }
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */