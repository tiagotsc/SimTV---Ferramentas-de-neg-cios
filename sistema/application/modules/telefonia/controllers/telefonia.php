<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe responsável pela usuário
*/
class Telefonia extends Base
{
    
    private $logDados;
    
	/**
	 * Telefonia::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        /*
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
        */
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = 'Telefonia';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Termo', '', 'termo');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('operadora_model','operadora');
        $this->load->model('plano_model','plano');
        $this->load->model('linha_model','linha');
        $this->load->model('aparelho_model','aparelho');
        $this->load->model('emprestimo_model','emprestimo');
        $this->load->model('servico_model','servico');
        #$this->load->library('email');
        /*
        $this->load->library('Util', '', 'util');
        $this->load->library('Termo', '', 'termo');
        $this->load->model('dadosBanco_model','dadosBanco');
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        $this->load->model('operadora_model','operadora');
        $this->load->model('plano_model','plano');
        $this->load->model('linha_model','linha');
        $this->load->model('aparelho_model','aparelho');
        $this->load->model('emprestimo_model','emprestimo');
        $this->load->model('servico_model','servico');
        $this->load->model('log_model','logGeral');
        $this->load->helper('url');
        $this->load->library('pagination');
		$this->load->helper('form');
        $this->load->library('table');
        $this->load->model('AnatelForm_model','anatelForm');
        $this->load->library('email');
        
        if($this->anatelForm->verificaResponsavel()){ // Se é responsável por responder relatório da Anatel
            
            // Se a data corrente estiver dentro do período pega os formulários
            if(date('d/m/Y') >= $this->session->userdata('SATVA_INICIO') and date('d/m/Y') <= $this->session->userdata('SATVA_FIM')){
                $this->util->setMenuCompleto('');
                $this->util->setPositionMenu('left');
                $menu['menu_satva'] = $this->util->montaMenu($this->anatelForm->menuIndicadoresUnidades(), $this->anatelForm->menuIndicadores());
            }else{
                $menu['menu_satva'] = false;
            }
            
        }else{
            $menu['menu_satva'] = false;
        }
        $this->util->setMenuCompleto('');
        $this->util->setPositionMenu('right');
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        $this->layout->region('menu', 'view_menu', $menu);
        
        */
        $this->menuLateral = $this->dadosBanco->menuLateral('TELEFONIA', $this->session->userdata('permissoes'));
        
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
      	#$this->layout->region('menu', 'view_menu', $menu);
        if($_SERVER['REDIRECT_QUERY_STRING'] == 'telefonia'){
            $this->session->unset_userdata('telefonia');
            $this->session->unset_userdata('menuLateral');
            $dados['termo'] = $this->emprestimo->termoUsuario($this->session->userdata('cd'));
        }
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
    
    /**
     * Telefonia::movel()
     * 
     * Define o menu MOVEL pra abertura
     * 
     * @return
     */
    public function movel(){
        
        $this->session->set_userdata('telefonia', 'movel');
        $this->index();
    }
    
    /**
     * Telefonia::fixo()
     * 
     * Define o menu FIXO pra abertura
     * 
     * @return
     */
    public function fixo(){
        
        $this->session->set_userdata('telefonia', 'fixo');
        $this->index();
        
    }
    
    /**
     * Telefonia::pesquisar()
     * 
     * Tela de pesquisa da telefonia
     * 
     * @return
     */
    public function pesquisar(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);
        
        $this->layout->region('html_header', 'view_html_header');

        $lateral['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $lateral);
        if(in_array(244, $this->session->userdata('permissoes'))){
            
            if($this->input->post('pesquisar') == 'sim'){
            
                $dados['dados'] = $this->emprestimo->pesqVisualizar();
                $dados['postLinha'] = $this->input->post('linha');
                $dados['postImei'] = $this->input->post('imei');
                $dados['postUser'] = $this->input->post('user');
                $dados['pesquisar'] = 'sim';
            
            }else{
                
                $dados['dados'] = false;
                $dados['postLinha'] = '';
                $dados['postImei'] = '';
                $dados['postUser'] = '';
                $dados['pesquisar'] = 'nao';
                
            }
        
            $this->layout->region('corpo', 'view_psq', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::operadoras()
     * 
     * Tela inicial de pesquisa da operadora
     * 
     * @return
     */
    public function operadoras(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);

        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        if(in_array(202, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'operadora/view_psq_operadora');
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::fichaOperadora()
     * 
     * Exibe a ficha para cadastro e atualização da operadora
     * 
     * @param bool $cd Cd da operadora que quando informado carrega os dados da operadora
     * @return
     */
    public function fichaOperadora($cd = false){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        if($cd){
            
            $dados = $this->operadora->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->operadora->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
        
        }

        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(203, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'operadora/view_frm_operadora', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::salvaOperadora()
     * 
     * Cadastra ou atualiza a operadora
     * 
     * @return
     */
    public function salvaOperadora(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_telefonia_operadora')){
            
            try{
            
                $status = $this->operadora->atualiza();
                $this->logDados['descricao'] = 'Telefonia - Atualiza operadora';
                $this->logDados['acao'] = 'UPDATE';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->operadora->insere();
                $this->logDados['descricao'] = 'Telefonia - Cadastra operadora';
                $this->logDados['acao'] = 'INSERT';
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_telefonia_operadora'] = $status;
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Operadora gravada com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaOperadora/'.$this->input->post('cd_telefonia_operadora'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaOperadora'));
            
        }
        
    }
    
    /**
     * Telefonia::pesqOperadora()
     * 
     * Pesquisa a operadora
     * 
     * @param mixed $nome Nome da operadora para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesqOperadora($nome = null, $status = null, $sort_by = 'nome', $sort_order = 'asc', $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'nome' => 'Nome', 
                                'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'cd_telefonia_operadora';
                break;
            case 'status':
                $campoSortBy = 'status';
                break;
            default:
                $campoSortBy = 'nome';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNome'] = ($this->input->post('nome') != '')? $this->input->post('nome') : $nome;
        $dados['postStatus'] = ($this->input->post('status') != '')? $this->input->post('status') : $status;
        
        $mostra_por_pagina = 30;
        $dados['dados'] = $this->operadora->pesquisa($dados['postNome'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdDados'] = $this->operadora->pesquisaQtd($dados['postNome'], $dados['postStatus']); 
        
        $qtdRegistros = ($dados['qtdDados'][0]->total < $mostra_por_pagina)? $dados['qtdDados'][0]->total: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $dados['qtdDados'][0]->total){
            $restante = $dados['qtdDados'][0]->total - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
        
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('telefonia/pesqOperadora/'.$dados['postNome'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdOperadora'][0]->total;
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
        
        $dados['postNome'] = ($dados['postNome'] == '0')? '': $dados['postNome'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];

        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(202, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'operadora/view_psq_operadora', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::apagaOperadora()
     * 
     * Apaga a operadora
     * 
     * @return
     */
    public function apagaOperadora(){
        
        try{
        
            $status = $this->operadora->delete(); 
            $this->logDados['descricao'] = 'Telefonia - Apaga operadora';
            $this->logDados['acao'] = 'DELETE'; 
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Operadora apagada com sucesso!</strong></div>');
            redirect(base_url('telefonia/operadoras'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar operadora, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/operadoras'));
        
        }
    }
    
    /**
     * Telefonia::planos()
     * 
     * Tela inicial de pesquisa dos planos
     * 
     * @return
     */
    public function planos(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);

        $dados['operadoras'] = $this->operadora->operadoras();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(217, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'plano/view_psq_plano', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::fichaPlano()
     * 
     * Exibe a ficha para cadastro e atualização do plano
     * 
     * @param bool $cd Cd do plano que quando informado carrega os dados do plano
     * @return
     */
    public function fichaPlano($cd = false){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        if($cd){
            
            $dados = $this->plano->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $dados['tarifas'] = $this->plano->tarifasPlano($cd);
            
        }else{
            
            $campos = $this->plano->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
            
            $dados['tarifas'] = false;
        
        }

        $dados['operadoras'] = $this->operadora->operadoras();
        
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(218, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'plano/view_frm_plano', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::salvaPlano()
     * 
     * Cadastra ou atualiza do plano
     * 
     * @return
     */
    public function salvaPlano(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_telefonia_plano')){
            
            try{
            
                $status = $this->plano->atualiza();
                
                $this->logDados['descricao'] = 'Telefonia - Atualiza plano';
                $this->logDados['acao'] = 'UPDATE'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->plano->insere();
                
                $this->logDados['descricao'] = 'Telefonia - Cadastra plano';
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_telefonia_plano'] = $status;
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Plano gravado com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaPlano/'.$this->input->post('cd_telefonia_plano'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar plano, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaPlano'));
            
        }
        
    }
    
    /**
     * Telefonia::pesqPlano()
     * 
     * Pesquisa a plano
     * 
     * @param mixed $nome Nome do plano para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesqPlano($nome = null, $operadora = null, $status = null, $sort_by = 'nome', $sort_order = 'asc', $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $operadora = ($operadora == null)? '0': $operadora;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'nome' => 'Nome',
                                'operadora' => 'Operadora', 
                                'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'tplano.cd_telefonia_plano';
                break;
            case 'operadora':
                $campoSortBy = 'tplano.cd_telefonia_operadora';
                break;
            case 'status':
                $campoSortBy = 'tplano.status';
                break;
            default:
                $campoSortBy = 'tplano.nome';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNome'] = ($this->input->post('nome') != '')? $this->input->post('nome') : $nome;
        $dados['postOperadora'] = ($this->input->post('operadora') != '')? $this->input->post('operadora') : $operadora;
        $dados['postStatus'] = ($this->input->post('status') != '')? $this->input->post('status') : $status;
        
        $mostra_por_pagina = 30;
        $dados['dados'] = $this->plano->pesquisa($dados['postNome'], $dados['postOperadora'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdDados'] = $this->plano->pesquisaQtd($dados['postNome'], $dados['postOperadora'], $dados['postStatus']); 
        
        $qtdRegistros = ($dados['qtdDados'][0]->total < $mostra_por_pagina)? $dados['qtdDados'][0]->total: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $dados['qtdDados'][0]->total){
            $restante = $dados['qtdDados'][0]->total - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
        
        $dados['operadoras'] = $this->operadora->operadoras();
                
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('telefonia/pesqPlano/'.$dados['postNome'].'/'.$dados['postOperadora'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdDados'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 8;
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
        
        $dados['postNome'] = ($dados['postNome'] == '0')? '': $dados['postNome'];
        $dados['postOperadora'] = ($dados['postOperadora'] == '0')? '': $dados['postOperadora'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];

        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(217, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'plano/view_psq_plano', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::apagaPlano()
     * 
     * Apaga o plano
     * 
     * @return
     */
    public function apagaPlano(){
        
        try{
        
            $status = $this->plano->delete(); 
            
            $this->logDados['descricao'] = 'Telefonia - Apaga plano';
            $this->logDados['acao'] = 'DELETE';  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Plano apagado com sucesso!</strong></div>');
            redirect(base_url('telefonia/planos'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar plano, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/planos'));
        
        }
    }
    
    /**
     * Telefonia::servicos()
     * 
     * Tela inicial de pesquisa dos serviços
     * 
     * @return
     */
    public function servicos(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);

        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(230, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'servico/view_psq_servico');
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::fichaServico()
     * 
     * Exibe a ficha para cadastro e atualização do serviço
     * 
     * @param bool $cd Cd do plano que quando informado carrega os dados do serviço
     * @return
     */
    public function fichaServico($cd = false){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        if($cd){
            
            $dados = $this->servico->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->servico->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
        
        }
        
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(231, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'servico/view_frm_servico', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::salvaServico()
     * 
     * Cadastra ou atualiza do serviço
     * 
     * @return
     */
    public function salvaServico(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_telefonia_servico')){
            
            try{
            
                $status = $this->servico->atualiza();
                
                $this->logDados['descricao'] = utf8_encode('Telefonia - Atualiza serviço');
                $this->logDados['acao'] = 'UPDATE'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->servico->insere();
                
                $this->logDados['descricao'] = utf8_encode('Telefonia - Cadastra serviço');
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_telefonia_servico'] = $status;
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Servi&ccedil;o gravado com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaServico/'.$this->input->post('cd_telefonia_servico'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar servi&ccedil;o, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaServico'));
            
        }
        
    }
    
    /**
     * Telefonia::pesqServico()
     * 
     * Pesquisa o serviço
     * 
     * @param mixed $nome Nome do servico para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesqServico($nome = null, $status = null, $sort_by = 'nome', $sort_order = 'asc', $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'nome' => 'Nome', 
                                'qtd' => 'Quantidade', 
                                'valor' => 'Valor', 
                                'data_inicio' => 'Data In&iacute;cio', 
                                'data_fim' => 'Data Fim',
                                'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'cd_telefonia_servico';
                break;
            case 'nome':
                $campoSortBy = 'nome';
                break;
            case 'qtd':
                $campoSortBy = 'qtd';
                break;
            case 'valor':
                $campoSortBy = 'valor';
                break;
            case 'data_inicio':
                $campoSortBy = 'data_inicio';
                break;
            case 'data_fim':
                $campoSortBy = 'data_fim';
                break;
            default:
                $campoSortBy = 'status';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNome'] = ($this->input->post('nome') != '')? $this->input->post('nome') : $nome;
        $dados['postStatus'] = ($this->input->post('status') != '')? $this->input->post('status') : $status;
        
        $mostra_por_pagina = 30;
        $dados['dados'] = $this->servico->pesquisa($dados['postNome'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdDados'] = $this->servico->pesquisaQtd($dados['postNome'], $dados['postStatus']); 
        
        $qtdRegistros = ($dados['qtdDados'][0]->total < $mostra_por_pagina)? $dados['qtdDados'][0]->total: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $dados['qtdDados'][0]->total){
            $restante = $dados['qtdDados'][0]->total - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        } 
                
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('telefonia/pesqServico/'.$dados['postNome'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdDados'][0]->total;
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
        
        $dados['postNome'] = ($dados['postNome'] == '0')? '': $dados['postNome'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];

        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(230, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'servico/view_psq_servico', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::apagaServico()
     * 
     * Apaga o serviço
     * 
     * @return
     */
    public function apagaServico(){
        
        try{
        
            $status = $this->servico->delete();  
            
            $this->logDados['descricao'] = utf8_encode('Telefonia - Apaga serviço');
            $this->logDados['acao'] = 'DELETE'; 
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Servi&ccedil;o apagado com sucesso!</strong></div>');
            redirect(base_url('telefonia/servicos'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar servi&ccedil;o, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/servicos'));
        
        }
    }
    
    /**
     * Telefonia::linhas()
     * 
     * Tela inicial de pesquisa das Linhas
     * 
     * @return
     */
    public function linhas(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);

    	$dados['ddds'] = $this->dadosBanco->ddd();
        $dados['planos'] = $this->plano->planos();
        $dados['operadoras'] = $this->operadora->operadoras();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(220, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'linha/view_psq_linha', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::fichaLinha()
     * 
     * Exibe a ficha para cadastro e atualização da linha
     * 
     * @param bool $cd Cd da ficha que quando informado carrega os dados da linha
     * @return
     */
    public function fichaLinha($cd = false, $visualizar = false){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        if($cd){
            
            $dados = $this->linha->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $dados['servicosAtivos'] = $this->linha->servicosLinha($cd, 'ATIVO');
            $dados['servicosInativos'] = $this->linha->servicosLinha($cd, 'INATIVO');
            
        }else{
            
            $campos = $this->linha->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
            
            $dados['servicosAtivos'] = array();
            $dados['servicosInativos'] = array();
        }
        
        $dados['ddds'] = $this->dadosBanco->ddd();
        $dados['planos'] = $this->plano->planos();
        $dados['operadoras'] = $this->operadora->operadoras();
        $dados['todosServicos'] = $this->servico->servicos();
        
        if($visualizar){
        
        $dados['disabled'] = 'disabled="true"';
        $dados['readonly'] = 'readonly';
        $dados['classData'] = '';
        
        }else{
            
        $dados['disabled'] = '';
        $dados['readonly'] = '';
        $dados['classData'] = 'data';
        
        }
        
        $dados['visualizar'] = $visualizar;
        
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(221, $this->session->userdata('permissoes')) or in_array(238, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'linha/view_frm_linha', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::visualizarLinha()
     * 
     * Restringe a tela de edição somente para exibição das informações
     * 
     * @param $cd Cd da linha para abrir a ficha
     * @return
     */
    public function visualizarLinha($cd = false){
        
        $this->fichaLinha($cd, true);
        
    }
    
    /**
     * Telefonia::salvaLinha()
     * 
     * Cadastra ou atualiza da linha
     * 
     * @return
     */
    public function salvaLinha(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_telefonia_linha')){
            
            try{
            
                $status = $this->linha->atualiza();
                
                $this->logDados['descricao'] = 'Telefonia - Atualiza linha';
                $this->logDados['acao'] = 'UPDATE'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->linha->insere();
                
                $this->logDados['descricao'] = 'Telefonia - Cadastra linha';
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_telefonia_linha'] = $status;
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Linha gravado com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaLinha/'.$this->input->post('cd_telefonia_linha'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar linha, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaLinha'));
            
        }
        
    }
    
    /**
     * Telefonia::pesqLinha()
     * 
     * Pesquisa a linha
     * 
     * @param mixed $nome Nome da linha para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesqLinha($ddd = null, $identificacao = null, $numero = null, $operadora = null, $plano = null, $status = null, $sort_by = 'ddd', $sort_order = 'asc', $pagina = null){
        
        $ddd = ($ddd == null)? '0': $ddd;
        $identificacao = ($identificacao == null)? '0': $identificacao;
        $numero = ($numero == null)? '0': $numero;
        $operadora = ($operadora == null)? '0': $operadora;
        $plano = ($plano == null)? '0': $plano;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'ddd' => 'DDD',
                                'numero' => 'Numero',
                                #'operadora' => 'Operadora', 
                                #'plano' => 'Plano', 
                                'dados' => 'Dados',
                                'tz41' => 'TZ 41',
                                'sms' => 'SMS',
                                'pct_minutos' => 'Pct. Min.',
                                'status' => 'Status'
                                );
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'ddd':
                $campoSortBy = 'tddd.ddd';
                break;
            case 'numero':
                $campoSortBy = 'tlinha.numero';
                break;
            case 'operadora':
                $campoSortBy = 'toperadora.nome';
                break;
            case 'plano':
                $campoSortBy = 'tplano.nome';
                break;
            default:
                $campoSortBy = 'tlinha.status';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postDDD'] = ($this->input->post('ddd') != '')? $this->input->post('ddd') : $ddd;
        $dados['postIdentificacao'] = ($this->input->post('identificacao') != '')? $this->input->post('identificacao') : $identificacao;
        $dados['postNumero'] = ($this->input->post('numero') != '')? $this->input->post('numero') : $numero;
        $dados['postOperadora'] = ($this->input->post('operadora') != '')? $this->input->post('operadora') : $operadora;
        $dados['postPlano'] = ($this->input->post('plano') != '')? $this->input->post('plano') : $plano;
        $dados['postStatus'] = ($this->input->post('status') != '')? $this->input->post('status') : $status;
        
        $mostra_por_pagina = 30;
        $dados['dados'] = $this->linha->pesquisa($dados['postDDD'], $dados['postIdentificacao'], $dados['postNumero'], $dados['postOperadora'], $dados['postPlano'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdDados'] = $this->linha->pesquisaQtd($dados['postDDD'], $dados['postIdentificacao'], $dados['postNumero'], $dados['postOperadora'], $dados['postPlano'], $dados['postStatus']); 
        
        $qtdRegistros = ($dados['qtdDados'][0]->total < $mostra_por_pagina)? $dados['qtdDados'][0]->total: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $dados['qtdDados'][0]->total){
            $restante = $dados['qtdDados'][0]->total - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
        
        $dados['ddds'] = $this->dadosBanco->ddd();
        $dados['planos'] = $this->plano->planos();
        $dados['operadoras'] = $this->operadora->operadoras();
                
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('telefonia/pesqLinha/'.$dados['postDDD'].'/'.$dados['postIdentificacao'].'/'.$dados['postNumero'].'/'.$dados['postOperadora'].'/'.$dados['postPlano'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdDados'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 11;
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
        
        $dados['postDDD'] = ($dados['postDDD'] == '0')? '': $dados['postDDD'];
        $dados['postIdentificacao'] = ($dados['postIdentificacao'] == '0')? '': $dados['postIdentificacao'];
        $dados['postNumero'] = ($dados['postNumero'] == '0')? '': $dados['postNumero'];
        $dados['postOperadora'] = ($dados['postOperadora'] == '0')? '': $dados['postOperadora'];
        $dados['postPlano'] = ($dados['postPlano'] == '0')? '': $dados['postPlano'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];

        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(220, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'linha/view_psq_linha', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::apagaLinha()
     * 
     * Apaga a linha
     * 
     * @return
     */
    public function apagaLinha(){
        
        try{
        
            $status = $this->linha->delete(); 
            
            $this->logDados['descricao'] = 'Telefonia - Apaga linha';
            $this->logDados['acao'] = 'DELETE'; 
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Linha apagada com sucesso!</strong></div>');
            redirect(base_url('linhas'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar linha, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('linhas'));
        
        }
    }
    
    /**
     * Telefonia::Aparelhos()
     * 
     * Tela inicial de pesquisa dos aparelhos
     * 
     * @return
     */
    public function Aparelhos(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);

    	$dados['marcas'] = $this->dadosBanco->telefoniaMarca();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(223, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'aparelho/view_psq_aparelho', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::fichaAparelho()
     * 
     * Exibe a ficha para cadastro e atualização do aparelho
     * 
     * @param bool $cd Cd do aparelho que quando informado carrega os dados do aparelho
     * @return
     */
    public function fichaAparelho($cd = false, $visualizar = false){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        if($cd){
            
            $dados = $this->aparelho->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $dados['ddds'] = $this->linha->dddsAssociadosLinhas();
            $dados['imeis'] = $this->aparelho->imeiAparelho($cd);
            
        }else{
            
            $campos = $this->aparelho->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
            
            $dados['imeis'] = false;
        
        }
        
        $dados['marcas'] = $this->dadosBanco->telefoniaMarca();
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $menu['menuLateral'] = $this->menuLateral;
        
        if($visualizar){
        
        $dados['disabled'] = 'disabled="true"';
        $dados['readonly'] = 'readonly';
        $dados['classData'] = '';
        
        }else{
            
        $dados['disabled'] = '';
        $dados['readonly'] = '';
        $dados['classData'] = 'data';
        
        }
        
        $dados['visualizar'] = $visualizar;
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(224, $this->session->userdata('permissoes'))  or in_array(243, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'aparelho/view_frm_aparelho', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::visualizarLinha()
     * 
     * Restringe a tela de edição somente para exibição das informações
     * 
     * @param $cd Cd da aparelho para abrir a ficha
     * @return
     */
    public function visualizarAparelho($cd = false){
        
        $this->fichaAparelho($cd, true);
        
    }
    
    /**
     * Telefonia::salvaAparelho()
     * 
     * Cadastra ou atualiza do aparelho
     * 
     * @return
     */
    public function salvaAparelho(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_telefonia_aparelho')){
            
            try{
            
                $status = $this->aparelho->atualiza();
                
                $this->logDados['descricao'] = 'Telefonia - Atualiza aparelho';
                $this->logDados['acao'] = 'UPDATE'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->aparelho->insere();
                
                $this->logDados['descricao'] = 'Telefonia - Cadastra aparelho';
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_telefonia_aparelho'] = $status;
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Aparelho gravado com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaAparelho/'.$this->input->post('cd_telefonia_aparelho'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar aparelho, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaAparelho'));
            
        }
        
    }
    
    /**
     * Telefonia::pesqAparelho()
     * 
     * Pesquisa o aparelho
     * 
     * @param mixed $nome Nome do aparelho para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesqAparelho($linha = null, $imei = null, $marca = null, $modelo = null, $tipo = null, $status = null, $sort_by = 'modelo', $sort_order = 'asc', $pagina = null){
        
        $linha = ($linha == null)? '0': $linha;
        $imei = ($imei == null)? '0': $imei;
        $marca = ($marca == null)? '0': $marca;
        $modelo = str_replace('%20',' ',($modelo == null)? '0': $modelo);
        $tipo = ($tipo == null)? '0': $tipo;
        $status = ($status == null)? '0': $status;

        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                #'imei' => 'IMEI',
                                'marca' => 'Marca',
                                'modelo' => 'Modelo',
                                'imei' => 'Imei',
                                'usuario' => 'Usu&aacute;rio', 
                                'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'taparelho.cd_telefonia_aparelho';
                break;
            case 'imei':
                $campoSortBy = 'timei.imei';
                break;
            case 'marca':
                $campoSortBy = 'tmarca.cd_telefonia_marca';
                break;
            case 'modelo':
                $campoSortBy = 'taparelho.modelo';
                break;
            case 'tipo':
                $campoSortBy = 'taparelho.tipo';
                break;
            case 'usuario':
                $campoSortBy = 'nome_usuario';
                break;
            default:
                $campoSortBy = 'taparelho.status';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postLinha'] = ($this->input->post('linha') != '')? $this->input->post('linha') : $linha;
        $dados['postImei'] = ($this->input->post('imei') != '')? $this->input->post('imei') : $imei;
        $dados['postMarca'] = ($this->input->post('marca') != '')? $this->input->post('marca') : $marca;
        $dados['postModelo'] = ($this->input->post('modelo') != '')? $this->input->post('modelo') : $modelo;
        $dados['postTipo'] = ($this->input->post('tipo') != '')? $this->input->post('tipo') : $tipo;
        $dados['postStatus'] = ($this->input->post('status') != '')? $this->input->post('status') : $status;
        $mostra_por_pagina = 30;
        $dados['dados'] = $this->aparelho->pesquisa($dados['postLinha'], $dados['postImei'], $dados['postMarca'], $dados['postModelo'], $dados['postTipo'], $dados['postStatus'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdDados'] = $this->aparelho->pesquisaQtd($dados['postLinha'], $dados['postImei'], $dados['postMarca'], $dados['postModelo'], $dados['postTipo'], $dados['postStatus']); 
        
        $qtdRegistros = ($dados['qtdDados'][0]->total < $mostra_por_pagina)? $dados['qtdDados'][0]->total: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $dados['qtdDados'][0]->total){
            $restante = $dados['qtdDados'][0]->total - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
        
        $dados['marcas'] = $this->dadosBanco->telefoniaMarca();     
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('telefonia/pesqAparelho/'.$dados['postLinha'].'/'.$dados['postImei'].'/'.$dados['postMarca'].'/'.$dados['postModelo'].'/'.$dados['postTipo'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdDados'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 11;
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
        
        $dados['postLinha'] = ($dados['postLinha'] == '0')? '': $dados['postLinha'];
        $dados['postImei'] = ($dados['postImei'] == '0')? '': $dados['postImei'];
        $dados['postMarca'] = ($dados['postMarca'] == '0')? '': $dados['postMarca'];
        $dados['postModelo'] = ($dados['postModelo'] == '0')? '': $dados['postModelo'];
        $dados['postTipo'] = ($dados['postTipo'] == '0')? '': $dados['postTipo'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];
        
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(223, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'aparelho/view_psq_aparelho', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::apagaAparelho()
     * 
     * Apaga o aparelho
     * 
     * @return
     */
    public function apagaAparelho(){
        
        try{
        
            $status = $this->aparelho->delete();  
            
            $this->logDados['descricao'] = 'Telefonia - Apaga aparelho';
            $this->logDados['acao'] = 'DELETE';
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Aparelho apagado com sucesso!</strong></div>');
            redirect(base_url('telefonia/aparelhos'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar aparelho, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/aparelhos'));
        
        }
    }
    
    /**
     * Telefonia::Emprestimos()
     * 
     * Tela inicial de pesquisa dos emprestimos
     * 
     * @return
     */
    public function emprestimos(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);
        
        $this->layout->region('html_header', 'view_html_header');
      	
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(226, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'emprestimo/view_psq_emprestimo');
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::fichaEmprestimo()
     * 
     * Exibe a ficha para cadastro e atualização do emprestimo
     * 
     * @param bool $cd Cd do emprestimo que quando informado carrega os dados do emprestimo
     * @return
     */
    public function fichaEmprestimo($cd = false){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia'));
		}
        
        if($cd){
            
            $dados = $this->emprestimo->dados($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $dados['ddds'] = $this->linha->dddsAssociadosLinhas();
            $dados['linhasEmprestimo'] = $this->linha->linhasAssociadasEmprestimo($cd);
            $dados['aparelhos'] = $this->aparelho->aparelhos('assoEmpNao', $dados['cd_telefonia_aparelho']);
            
        }else{
            
            $campos = $this->aparelho->campos();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
            
            $dados['aparelhos'] = $this->aparelho->aparelhos('nao');
            
        }
        if($dados['cd_telefonia_emprestimo'] and !$dados['linhasEmprestimo']){
            #$this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>N&atilde;o esque&ccedil;a de associar a linha!</strong></div>');
            $dados['observacao'] = '<div class="alert alert-warning"><strong>N&atilde;o esque&ccedil;a de associar a linha!</strong></div>';
        }else{
            $dados['observacao'] = '';
        }
        $dados['usuarios'] = $this->emprestimo->listaUsuarios();
     
        $this->layout->region('html_header', 'view_html_header');
      	
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(227, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'emprestimo/view_frm_emprestimo', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::salvaEmprestimo()
     * 
     * Cadastra ou atualiza do emprestimo
     * 
     * @return
     */
    public function salvaEmprestimo(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_telefonia_emprestimo')){
            
            try{
            
                $status = $this->emprestimo->atualiza();
                
                $this->logDados['descricao'] = utf8_encode('Telefonia - Atualiza empréstimo');
                $this->logDados['acao'] = 'UPDATE'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->emprestimo->insere();
                
                $this->logDados['descricao'] = utf8_encode('Telefonia - Cadastra empréstimo');
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_telefonia_emprestimo'] = $status;
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Emprestimo gravado com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaEmprestimo/'.$this->input->post('cd_telefonia_emprestimo'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar emprestimo, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaEmprestimo'));
            
        }
        
    }
    
    /**
     * Telefonia::novoServico()
     * 
     * Grava novo serviço e associa a linha
     * 
     * @return
     */
    public function novoServico(){
        
        $status = $this->linha->gravaNovoServico();  
        
        $cd = ($this->input->post('cd_linha'))? $this->input->post('cd_linha'): '';
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Servi&ccedil;o gravado e associado com sucesso!</strong></div>');
            
            redirect(base_url('telefonia/fichaLinha/'.$cd)); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao gravar servi&ccedil;o, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/fichaLinha/'.$cd));
            
        }      
        
    }
    
    /**
     * Telefonia::pesqEmprestimo()
     * 
     * Pesquisa o emprestimo
     * 
     * @param mixed $nome Nome do emprestimo para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesqEmprestimo($linha = null, $imei = null, $user = null, $sort_by = 'user', $sort_order = 'asc', $pagina = null){
        
        $linha = ($linha == null)? '0': $linha;
        $imei = ($imei == null)? '0': $imei;
        $user = ($user == null)? '0': str_replace("%20", " ", $user);
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array(
                                #'cd' => 'Cd', 
                                'tddd' => 'DDD',
                                'linha' => 'Linha',
                                'imei' => 'IMEI',
                                'user' => 'Usu&aacute;rio',
                                #'dataInicio' => 'In&iacute;cio',
                                #'dataTermino' => 'T&eacute;rmino',
                                #'parcelas' => 'Parcelas Restantes',
                                #'valorFidelizado' => 'Valor Fidelizado',
                                #'valorMulta' => 'Valor Multa'
                                );
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'temprestimo.cd_telefonia_emprestimo';
                break;
            case 'tddd':
                $campoSortBy = 'tddd.ddd';
                break;
            case 'linha':
                $campoSortBy = 'tlinha.numero';
                break;
            case 'user':
                $campoSortBy = 'usuario.nome_usuario';
                break;
            case 'dataInicio':
                $campoSortBy = 'temprestimo.data_inicio';
                break;
            case 'dataTermino':
                $campoSortBy = 'temprestimo.data_termino';
                break;
            case 'parcelas':
                $campoSortBy = 'temprestimo.parcelas';
                break;
            case 'valorFidelizado':
                $campoSortBy = 'temprestimo.fidelizado';
                break;
            default:
                $campoSortBy = 'temprestimo.multa';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postLinha'] = ($this->input->post('linha') != '')? $this->input->post('linha') : $linha;
        $dados['postImei'] = ($this->input->post('imei') != '')? $this->input->post('imei') : $imei;
        $dados['postUser'] = ($this->input->post('user') != '')? $this->input->post('user') : $user;
        
        $mostra_por_pagina = 30;
        $dados['dados'] = $this->emprestimo->pesquisa($dados['postLinha'], $dados['postImei'], $dados['postUser'], $pagina, $mostra_por_pagina, $campoSortBy, $sort_order);   
        $dados['qtdDados'] = $this->emprestimo->pesquisaQtd($dados['postLinha'], $dados['postImei'], $dados['postUser']); 
        
        $qtdRegistros = ($dados['qtdDados'][0]->total < $mostra_por_pagina)? $dados['qtdDados'][0]->total: $mostra_por_pagina;
        #$dados['qtdDadosCorrente'] = ($pagina == null)? $qtdRegistros: $mostra_por_pagina + $pagina;
        if($pagina == null){
            $dados['qtdDadosCorrente'] = $qtdRegistros;
        }elseif(($mostra_por_pagina + $pagina) > $dados['qtdDados'][0]->total){
            $restante = $dados['qtdDados'][0]->total - $pagina;
            $dados['qtdDadosCorrente'] = $pagina + $restante;
        }else{
            $dados['qtdDadosCorrente'] = $mostra_por_pagina + $pagina;
        }
                
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;                    
        
        $config['base_url'] = base_url('telefonia/pesqEmprestimo/'.$dados['postLinha'].'/'.$dados['postImei'].'/'.$dados['postUser'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdDados'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 8;
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
        
        $dados['postLinha'] = ($dados['postLinha'] == '0')? '': $dados['postLinha'];
        $dados['postImei'] = ($dados['postImei'] == '0')? '': $dados['postImei'];
        $dados['postUser'] = ($dados['postUser'] == '0')? '': $dados['postUser'];
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(226, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'emprestimo/view_psq_emprestimo', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Telefonia::apagaEmprestimo()
     * 
     * Apaga o emprestimo
     * 
     * @return
     */
    public function apagaEmprestimo(){
        
        try{
        
            $status = $this->emprestimo->delete();
            
            $this->logDados['descricao'] = utf8_encode('Telefonia - Apaga empréstimo');
            $this->logDados['acao'] = 'DELETE';   
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Empr&eacute;stimo apagado com sucesso!</strong></div>');
            redirect(base_url('telefonia/emprestimos'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar empr&eacute;stimo, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/emprestimos'));
        
        }
    }
    
    public function servicoMassa(){
        
        $dados['servicos'] = $this->servico->servicos();
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
        if(in_array(251, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'linha/view_servico_massa', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');        
        
    }
    
    public function salvaServicoMassa(){
        
        $status = $this->linha->salvaServicoMassa();
        
        if($status){
            
            $acoes = array();
            
            if($this->input->post('remover')){
                $acoes[] = 'removido';
            }
            
            if($this->input->post('adicionar')){
                $acoes[] = 'adicionado';
            }
            
            $acoes = implode(' / ', $acoes);
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>O servi&ccedil;o foi '.$acoes.' nas '.$status.' linhas informadas com sucesso!</strong></div>');
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Nada foi alterado!</div>');
            
        }
        
        redirect(base_url('telefonia/servicoMassa'));
        
    }
    
    /**
     * Telefonia::salvaTermo()
     * 
     * Gera e salva o termo
     * 
     * @return
     */
    public function salvaTermo(){
        
        try{
        
            $status = $this->emprestimo->salvaTermo();  
            
            $this->logDados['descricao'] = 'Telefonia - Gera/Salva termo';
            $this->logDados['acao'] = 'INSERT'; 
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        if($status){
            
            if($this->input->post('email')){
                
                $nomeDe = 'Sim TV - Ferramentas de Negócios';
                $emailDe = 'naoresponda@simtv.com.br';
                $para = $this->input->post('email_usuario');
                #$para = 'tiago.costa@simtv.com.br';
                $titulo = 'Termo de Telefonia';
                $msg = 'Prezado(a),<br><br>
                        Seu termo de telefonia foi gerado.<br>
                        Para acessar o termo acesse o sistema através do link:<br>
                        <a target="_blank" href="http://sistemas.simtv.com.br/sistema">Ferramenta de negócios</a><br>
                        Logando com o seu login e senha da rede.<br><br>-Dentro do sistema acesse menu "Telefonia".
                        <br>-No quadro que aparece, clique no ícone no canto direito da tabela para visualizar o termo.<br><br><br>
                        Att,<br>Equipe sistemas';
                
                $this->util->enviaEmail($nomeDe, $emailDe, $para, $titulo, $msg);
                    
            }
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Termo salvo com sucesso!</strong></div>');
            redirect(base_url('telefonia/emprestimos'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar termo, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/emprestimos'));
        
        }
        
    }
    
    /**
     * Telefonia::termoSalvaResposta()
     * 
     * Salva a resposta do termo
     * 
     * @return
     */
    public function termoSalvaResposta(){
        
        try{
        
            $status = $this->emprestimo->salvaRespostaTermo();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
            
            $nomeDe = $this->session->userdata('nome');
            $emailDe = $this->session->userdata('email');
            $para = 'equipe.sistemas@simtv.com.br';
            #$para = 'tiago.costa@simtv.com.br';
            $titulo = 'Termo de Telefonia - Respondido';
            $msg = 'O colaborador(a),<br><br><b>'.$this->session->userdata('matricula').' - '.$this->session->userdata('nome').'</b> 
                    respondeu o termo de telefonia.<br><br><br><br>Att,<br>Equipe sistemas';
            
            $this->util->enviaEmail($nomeDe, $emailDe, $para, $titulo, $msg);            
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Termo respondido com sucesso!</strong></div>');
            redirect(base_url('telefonia/telefonia'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao responder termo, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('telefonia/telefonia'));
        
        }
        
    }
    
    public function faturas(){
        
        if(!$this->session->userdata('telefonia')){
			redirect(base_url('telefonia/telefonia'));
		}
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);

        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        $dados['linhas'] = $this->emprestimo->linhasAssociadas();
        $dados['usuarios'] = $this->emprestimo->usuariosAssociados();
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        if(in_array(202, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'fatura/view_psq_fatura');
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');        
        
    }
    
    /**
     * Telefonia::faturasArquivos()
     * 
     * Tela de processamento de arquivos de futuras telefônicas
     * 
     * @return
     */
    public function faturasArquivos(){

        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);
        
        $this->load->model('base/logarquivo_model','logArquivo'); 
        
        $dados['operadoras'] = $this->operadora->operadoras();
        
        $dados['ultimosArquivos'] = $this->logArquivo->dataUltimaAcao();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(236, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'fatura/view_processa_fatura', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');        
        
    }
    
    /**
     * Telefonia::processaFatura()
     * 
     * Processa os arquivos de faturas
     * 
     * @return
     */
    public function processaFatura(){
        #echo 1; exit();
        
        #$_POST['processar'] = 'sim';
        #$_POST['tipo'] = 8;
        if($this->input->post("processar") == 'sim'){
            
            $this->load->library('telefonia/Fatura', '', 'fatura');
            $this->load->model('base/logArquivo_model','logArquivo'); 
            
            $fonteUpload = array('CALLCENTER - ATIVO', 'CALLCENTER - RECEPTIVO - 0800', 'CALLCENTER - RECEPTIVO - 4004');
                        
            try
            {
            
                if(in_array($this->input->post('tipo'), $fonteUpload)){
                
                    $md5file = md5_file($_FILES['userfile']['tmp_name']);
                
                    if($this->logArquivo->existenciaArquivo($md5file)){
                        $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>O arquivo j&aacute; foi processado!</strong></div>');
                    }else{
                        
                        $status = $this->util->uploadArquivo();
                        if($status['status']){
                            
                            $this->fatura->processaArquivoCallCenter($status);
                            $this->util->apagaArquivo($status['arquivo']['full_path']);
                        }
                        $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>O arquivo foi processado com sucesso!</strong></div>');
                    }    
                    
                }else{

                    $status = $this->fatura->processarArquivoMovel();
                    $this->session->set_flashdata('statusOperacao', implode('', $status));
                    
                }
                
                $this->logDados['descricao'] = 'Telefonia - Processa arquivo fatura('.$this->input->post('tipo').')';
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }
        
        $this->logGeral->grava($this->logDados);
        
        redirect(base_url('telefonia/faturasArquivos'));
        
    }
                
}
