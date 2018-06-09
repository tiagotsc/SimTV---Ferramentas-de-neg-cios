<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe responsável pela perfil
*/
class Perfil extends Base
{
    
	/**
	 * Perfil::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        $this->load->library('perfiltree', '', 'perfiltree');
        /*
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
        
        $this->load->library('Util', '', 'util');
        $this->load->model('dadosBanco_model','dadosBanco');
        $this->load->model('permissaoPerfil_model','permissaoPerfil');
        $this->load->helper('url');
        $this->load->library('pagination');
		$this->load->helper('form');
        $this->load->library('table');
        $this->load->model('AnatelForm_model','anatelForm');
        
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
    }
    
    function index()
    {
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
      	$this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    /**
     * Perfil::perfis()
     * 
     * Abre a tela de pesquisa de perfil
     * 
     * @return
     */
    public function perfis(){
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
       
	    #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'perfil/view_psq_perfil');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Perfil::fichaPerfil()
     * 
     * Abre a ficha do do perfil
     * 
     * @param bool $cd Quando informado carrega os dados do perfil
     * @return
     */
    public function fichaPerfil($cd = false){
        
        if($cd){
            
            $dados = $this->permissaoPerfil->dadosPerfil($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($cd);
            
            foreach($permissoesPerfil as $perPer){
                $permissoesDoPerfil[] = $perPer['cd_permissao'];
            }
            
        }else{
            
            $campos = $this->permissaoPerfil->camposPerfil();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
            
            $permissoesDoPerfil = false;
        
        }
        
        
        $paiPermissoes = $this->dadosBanco->paiPermissao();
        $permissoes = $this->dadosBanco->permissoes();
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        $info['permissoes'] = $this->util->montaPermissao($permissoes, $paiPermissoes, $permissoesDoPerfil);
       
	    #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(18, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'perfil/view_frm_perfil', $info);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function ficha($cd = false){
        
        if($cd){
            
            $dados = $this->permissaoPerfil->dadosPerfil($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){

				$dados[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
            $permissoesPerfil = $this->permissaoPerfil->permissoesDoPerfil($cd);
            
            foreach($permissoesPerfil as $perPer){
                $permissoesDoPerfil[] = $perPer['cd_permissao'];
            }
            
        }else{
            
            $campos = $this->permissaoPerfil->camposPerfil();
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            $permissoesDoPerfil = false;
        
        }
        
        $paiPermissoes = $this->dadosBanco->paiPermissao();
        $permissoes = $this->dadosBanco->permissoes();
      
        $dados['permissoes'] = $this->perfiltree->montaPermissaoAccordion($permissoes, $paiPermissoes, $permissoesDoPerfil);
        #echo '<pre>'; print_r($dados['permissoes']); exit();
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(18, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'perfil/view_frm', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Perfil::salvarPerfil()
     * 
     * Cadastra ou atualiza o perfil
     * 
     * @return
     */
    public function salvarPerfil(){
        
        if($this->input->post('cd_perfil')){
            
            try{
            
            $cd = $this->permissaoPerfil->atualizaPerfil($this->input->post('cd_perfil'));
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $cd = $this->permissaoPerfil->inserePerfil();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
        }
        
        if($cd){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Perfil salvo com sucesso!</strong></div>');
            redirect(base_url('perfil/fichaPerfil/'.$cd));
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao criar perfil, caso o erro persiste comunique o perfil!</div>');
            redirect(base_url('perfil/fichaPerfil/'));
        }
        
        
    }
    
    /**
     * Perfil::salvar()
     * 
     * Cadastra ou atualiza o perfil
     * 
     * @return
     */
    public function salvar(){
        
        if($this->input->post('cd_perfil')){
            
            try{
            
            $cd = $this->permissaoPerfil->atualizaPerfil($this->input->post('cd_perfil'));
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $cd = $this->permissaoPerfil->inserePerfil();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
        }
        
        if($cd){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Perfil salvo com sucesso!</strong></div>');
            redirect(base_url('perfil/ficha/'.$cd));
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao criar perfil, caso o erro persiste comunique o perfil!</div>');
            redirect(base_url('perfil/ficha/'));
        }
        
        
    }
    
    /**
     * Perfil::pesquisarPerfil()
     * 
     * Pesquisa o perfil
     * 
     * @param mixed $nome Nome para pesquisa
     * @param mixed $status Status para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesquisarPerfil($nome = null, $status = null, $sort_by = 'nome', $sort_order = 'asc', $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array('cd' => 'Cd', 'nome' => 'Nome', 'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'cd':
                $campoSortBy = 'cd_perfil';
                break;
            case 'status':
                $campoSortBy = 'status_perfil';
                break;
            default:
                $campoSortBy = 'nome_perfil';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNome'] = ($this->input->post('nome_perfil') != '')? $this->input->post('nome_perfil') : $nome;
        $dados['postStatus'] = ($this->input->post('status_perfil') != '')? $this->input->post('status_perfil') : $status;
        
        $mostra_por_pagina = 30;
        $dados['perfis'] = $this->permissaoPerfil->psqPerfis($dados['postNome'], $dados['postStatus'], $pagina, $campoSortBy, $sort_order, $mostra_por_pagina);   
        $dados['qtdPerfis'] = $this->permissaoPerfil->psqQtdPerfis($dados['postNome'], $dados['postStatus']);                     
        
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;
        
        $config['base_url'] = base_url('perfil/pesquisarPerfil/'.$dados['postNome'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdPerfis'][0]->total;
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
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'perfil/view_psq_perfil', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Perfil::copiarPerfil()
     * 
     * Cria cópia do perfil
     * 
     * @return
     */
    public function copiarPerfil(){
        
        $cd = $this->permissaoPerfil->copyPerfil();
        
        if($cd){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Perfil copiado com sucesso!</strong></div>');
            redirect(base_url('perfil/fichaPerfil/'.$cd));
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao copiar perfil, caso o erro persiste comunique o perfil!</div>');
            redirect(base_url('perfil/fichaPerfil/'));
        }
        
    }
    
    /**
     * Perfil::apagaPerfil()
     * 
     * Apaga o perfil
     * 
     * @return
     */
    public function apagaPerfil(){
        
        try{
        
            $status = $this->permissaoPerfil->deletePerfil();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Perfil apagado com sucesso!</strong></div>');
            redirect(base_url('perfil/perfis'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar perfil, o perfil deve estar associado a algum usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('perfil/perfis'));
        
        }
    }
                
}
