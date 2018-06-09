<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class anatel extends Base {
    
     private $logDados;
     
    /**
     * anatel::__construct()
     * 
     * Classe resposável por processar os relatórios
     * 
     * @return
     */
    public function __construct(){
        
		parent::__construct();
        
        $this->load->library('Xml', '', 'xml');
        #$this->load->library('email');
        
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = 'Anatel';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];

        /*
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        # Configurações do sistema
        include_once('configSistema.php');
        
        $this->load->library('Util', '', 'util'); 
        $this->load->library('Xml', '', 'xml'); 
		$this->load->helper('url');
        $this->load->library('table');
        $this->load->helper('text');
		$this->load->helper('form');
        $this->load->model('DadosBanco_model','dadosBanco');
        $this->load->model('AnatelForm_model','anatelForm');
        $this->load->library('email');
        
        if($this->anatelForm->verificaResponsavel()){ // Se é responsável por responder relatório da Anatel
            
            // Se a data corrente estiver dentro do período pega os formulários
            if((date('d/m/Y') >= $this->session->userdata('SATVA_INICIO') and date('d/m/Y') <= $this->session->userdata('SATVA_FIM'))){
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
    
    public function teste(){
        echo 1; exit();
    }
    
    /**
     * anatel::config()
     * 
     * Tela de configuração dos parâmetros do Satva
     * 
     */
    public function config(){
        
        $dados['dados'] = $this->anatelForm->config();
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(255, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'view_frm_config', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');   
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
                
    } 
    
    /**
     * anatel::config()
     * 
     * Salva as configurações dos parâmetros do Satva
     * 
     */
    public function salvaConfig(){
        
        $status = $this->anatelForm->salvaConfig();
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Configura&ccedil;&atilde;o salva com sucesso!</strong></div>');
            
            redirect(base_url('anatel/config')); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar configura&ccedil;&atilde;o, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('anatel/config'));
            
        }
        
    }
    
    /**
     * anatel::formularios()
     * 
     * Tela inicial de pesquisa
     * 
     */
    public function formularios(){
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $dados['departamento'] = $this->dadosBanco->departamento();
        $dados['tipos_frm_anatel'] = $this->anatelForm->tipos_frm();
        
	    #$dados['valores'] = $this->anatel->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_psq_fomulario', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * anatel::fichaForm()
     * 
     * Tela de cadastro e edição de formulários
     * 
     */
    public function fichaForm($cd = false){

        if($cd){
            
            #echo '<pre>'; print_r($parametros); exit();
            $dadosRetornados = $this->anatelForm->dadosFormulario($cd);
            
            foreach($dadosRetornados as $campo => $valor){
             
				$dados[$campo] = $valor; # ALIMENTA OS CAMPOS COM OS DADOS
                
			}
            
            $dados['perguntas'] = $this->anatelForm->perguntas($cd);
            $dados['regrasMeta'] = $this->anatelForm->regrasMeta($cd);
            $dados['indicador'] = $this->anatelForm->anatelIndDepartamento($dados['cd_departamento']);
            $dados['unidades'] = $this->dadosBanco->unidade();
            $dados['responsaveis'] = $this->anatelForm->responsavelIndicador($cd);
            $dados['grupos'] = $this->anatelForm->anatelGrupoIndicador();
            
            foreach($dados['responsaveis'] as $responsavel){
                
                $resp[$responsavel->cd_usuario] = $this->anatelForm->responsavelUnidades($cd, $responsavel->cd_usuario);
                #$dados['respUnidade'] = $this->anatelForm->responsavelIndicador($cd, $responsavel->cd_usuario);
            }
            $dados['respUnidade'] = $resp;
        }else{
            
            $campos = $this->anatelForm->camposFormulario($cd);
            
            foreach($campos as $campo){
                $dados[$campo] = '';
            }
            
            $dados['perguntas'] = false;
            $dados['regrasMeta'] = false;
            $dados['indicador'] = false;
            $dados['unidades'] = false;
            $dados['respUnidade'] = false;
            $dados['grupos'] = false;
        
        }
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
       
        $dados['departamento'] = $this->anatelForm->departamento();
        $dados['departamentoUser'] = $this->dadosBanco->departamento();
        #$dados['produtos'] = $this->anatelForm->produtos();
        $dados['tipos_frm_anatel'] = $this->anatelForm->tipos_frm();
        
        #echo 1; exit();
	    #$dados['valores'] = $this->anatel->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(130, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'view_frm_formulario', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');   
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * anatel::salvarForm()
     * 
     * Salva o formulário
     * 
     */
    public function salvarForm(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_anatel_frm')){
            
            try{
            
                $status = $this->anatelForm->atualizaForm();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->anatelForm->insereForm();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_anatel_frm'] = $status;
        }
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Formul&aacute;rio salvo com sucesso!</strong></div>');
            
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar formul&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm'));
            
        }
        
    }
    
    /**
     * anatel::apgUniUser()
     * 
     * Apaga unidade associado ao usuário
     * 
     */
    public function apgUniUser($indicador, $unidade, $usuario){
        
        $status = $this->anatelForm->apgUniUser($indicador, $unidade, $usuario);
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Unidade do usu&aacute;rio apagado com sucesso!</strong></div>');
            
            redirect(base_url('anatel/fichaForm/'.$indicador)); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar unidade do usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm'));
            
        }
        
    }
    
    /**
     * anatel::salvarRegraMeta()
     * 
     * Salva a regra de meta
     * 
     */
    public function salvarRegraMeta(){
        
        $status = $this->anatelForm->insereRegraMeta();
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Regra de meta salva com sucesso!</strong></div>');
            
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar regra de meta, caso o erro persista comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));
            
        }
        
    }
    
    /**
     * anatel::apagaQuestao()
     * 
     * Apaga determinada questão
     * 
     */
    public function apagaQuestao(){
        
        $status = $this->anatelForm->apagaQuestao();
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Quest&atilde;o apagada com sucesso!</strong></div>');
            
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar quest&atilde;o, caso o erro persista comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));
            
        }
        
    }
    
    /**
     * anatel::apagaRegraMeta()
     * 
     * Apaga determinada regra de meta
     * 
     */
    public function apagaRegraMeta(){
        
        $status = $this->anatelForm->apagaRegra();
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Regra de meta apagada com sucesso!</strong></div>');
            
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar regra de meta, caso o erro persista comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));
            
        }
        
        
    }
    
    /**
     * Perfil::pesquisar()
     * 
     * Pesquisa formulário
     * 
     * @param mixed $nome Nome para pesquisa
     * @param mixed $status Status para pesquisa
     * @param mixed $pagina Página corrente
     * @return
     */
    public function pesquisarForm($tipo_frm = null, $departamento = null, $status = null, $sort_by = 'tipo', $sort_order = 'asc', $pagina = null){
        
        $tipo_frm = ($tipo_frm == null)? '0': $tipo_frm;
        $departamento = ($departamento == null)? '0': $departamento;
        $status = ($status == null)? '0': $status;
        
        // Apelido do campo banco => Nome do campo mostrado na tabela HTML
        $dados['campos'] = array('tipo' => 'Tipo', 'departamento' => 'Departamento', 'status' => 'Status');
        
        // Traduz o apelido do campo informado ($sor_by) para o campo correspondente do banco
        switch ($sort_by) {
            case 'departamento':
                $campoSortBy = 'anatel_frm.cd_departamento';
                break;
            case 'status':
                $campoSortBy = 'status';
                break;
            /*case 'cd':
                $campoSortBy = 'anatel_frm.cd_anatel_frm';
                break;*/
            default:
                $campoSortBy = 'anatel_frm.cd_anatel_tipo_frm';
        }
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postTipoFrm'] = ($this->input->post('cd_tipo_frm_anatel') != '')? $this->input->post('cd_tipo_frm_anatel') : $tipo_frm;
        $dados['postDepartamento'] = ($this->input->post('cd_departamento') != '')? $this->input->post('cd_departamento') : $departamento;
        $dados['postStatus'] = ($this->input->post('status_frm_anatel') != '')? $this->input->post('status_frm_anatel') : $status;
        
        $mostra_por_pagina = 30;
        $dados['frmsTodos'] = $this->anatelForm->psqFrms($dados['postTipoFrm'], $dados['postDepartamento'], $dados['postStatus'], $mostra_por_pagina, $campoSortBy, $sort_order, $pagina);   
        $dados['qtdFrms'] = $this->anatelForm->psqQtdFrms($dados['postTipoFrm'], $dados['postDepartamento'], $dados['postStatus']);                     
        #echo '<pre>'; print_r($dados['frms']); exit();
        $dados['sort_by'] = $sort_by;
		$dados['sort_order'] = $sort_order;
        
        $dados['departamento'] = $this->dadosBanco->departamento();
        $dados['tipos_frm_anatel'] = $this->anatelForm->tipos_frm();
        
        $config['base_url'] = base_url('anatel/pesquisarForm/'.$dados['postTipoFrm'].'/'.$dados['postDepartamento'].'/'.$dados['postStatus'].'/'.$sort_by.'/'.$sort_order); 
		$config['total_rows'] = $dados['qtdFrms'][0]->total;
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
        
        $dados['postTipoFrm'] = ($dados['postTipoFrm'] == '0')? false: $dados['postTipoFrm'];
        $dados['postDepartamento'] = ($dados['postDepartamento'] == '0')? '': $dados['postDepartamento'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];
        
        #$menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu($this->session->userdata('permissoes')), $this->dadosBanco->paisMenu($this->session->userdata('permissoes')));
        
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_psq_fomulario', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Anatel::openForm()
     * 
     * Abre o formulário do indicador para resposta
     * 
     * @return
     */
    public function openForm($cdForm, $unidade){
        
        $dadosFrm = $this->anatelForm->pegaDadosFrm($cdForm, $unidade);
        $dados['dadosFrm'] = $dadosFrm;
	    #$dados['valores'] = $this->anatel->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if($dadosFrm){
            
            $dados['unidadeUsuario'] = $unidade;
            
            if(!$unidade){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning"><strong>Para responder &eacute; preciso ter uma unidade associada. Comunique ao administrador!</strong></div>');
            }
            
            $dados['regrasFrm'] = $this->anatelForm->pegaRegrasFrm($cdForm, $unidade);
            $dados['motivosJust'] = $this->anatelForm->motivos_just();
            $dados['contGrupos'] = $this->anatelForm->contGrupos($cdForm, $unidade);

            $this->layout->region('corpo', 'view_open_frm', $dados);
        }else{
            $this->layout->region('corpo', 'view_permissao', $dados);
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Anatel::salvarRespForm()
     * 
     * Salva o formulário do indicador
     * 
     * @return
     */
    public function salvarRespForm(){
        #echo '<pre>';
        #echo trim((float)$_POST['ilustracao'][5]);
        #print_r($_POST);
        #exit();
        $status = $this->anatelForm->gravaResposta();
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Formul&aacute;rio respondido com sucesso!</strong></div>');
            redirect(base_url('anatel/openForm/'.$this->input->post('cd_anatel_frm').'/'.$this->input->post('cd_unidade'))); 
             
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao responder formul&aacute;rio, caso o erro persista comunique o administrador!</div>');
            redirect(base_url('anatel/openForm/'.$this->input->post('cd_anatel_frm').'/'.$this->input->post('cd_unidade')));
            
        }
        
    }
    
    /**
     * Anatel::apagaFrmAnatel()
     * 
     * Apaga o indicador
     * 
     * @return
     */
    public function apagaFrmAnatel(){
        
        try{
        
            $status = $this->anatelForm->deleteFormulario();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Formul&aacute;rio apagado com sucesso!</strong></div>');
            redirect(base_url('anatel/formularios'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar formul&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('anatel/formularios'));
        
        }
    }
    
    /**
     * Anatel::xml()
     * 
     * Abre a tela de geração de xml
     * 
     * @return
     */
    public function xml(){
        
        $dados['tipos_frm_anatel'] = $this->anatelForm->tipos_frm();
        $dados['departamento'] = $this->dadosBanco->departamento();
        #$dados['tiposXml'] = $this->anatelForm->tiposXml();
        $dados['operadoras'] = $this->dadosBanco->idOperadoras();
        
	    #$dados['valores'] = $this->anatel->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_gera_xml', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
                
    }
    
    /**
     * Anatel::calcularIRS()
     * 
     * Abre a tela de calculo de IRS
     * 
     * @return
     */
    public function calcularIRS(){
        
        $dados['tipos_frm_anatel'] = $this->anatelForm->tipos_frm();
        $dados['departamento'] = $this->dadosBanco->departamento();
        #$dados['tiposXml'] = $this->anatelForm->tiposXml();
        $dados['operadoras'] = $this->dadosBanco->idOperadoras();
        
	    #$dados['valores'] = $this->anatel->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_calcular_irs', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
                
    }
    
    /**
     * Anatel::salvarCalculoIRS()
     * 
     * Salvar o calculo do IRS pro mês informado caso passe na verificação
     * 
     * @return
     */
    public function salvarCalculoIRS(){
        
        $verifica = $this->anatelForm->verificaImportIRS();
        
        $arrayStatus = array_column($verifica, 'status');
        
        # Se existe indicadores para serem preenchidos ou se nada foi encontrado
        /*if(in_array('VAZIO', $arrayStatus) or in_array('INCOMPLETO', $arrayStatus)){
            
            $msg = (in_array('VAZIO', $arrayStatus))? '<div class="alert alert-warning"><strong>Nada encontrado para o m&ecirc;s informado! Provavelmente os indicadores n&atilde;o foram respondidos ainda.</strong></div>' : '<div class="alert alert-warning"><strong>Verifique se os indicadores IREDC, IITS, ISRA, Focus, Base de Assinantes e Siga foram respondidos para as seguintes unidades:</strong></div>';
            
            if(in_array('INCOMPLETO', $arrayStatus)){
                foreach($verifica as $res){
                   $msg .= '- '.$res['nome'].'<br>';
                }
                $msg .= '<br>';
            }
        }*/
        if(count($verifica) > 0){
            
            $msg = '<div class="alert alert-warning"><strong>Verifique se os indicadores IREDC, IITS, ISRA, Focus, Base de Assinantes e Siga foram respondidos para as seguintes unidades:</strong></div>';
            foreach($verifica as $ver){
                $msg .= '- '.$ver['sigla'].' -> '.$ver['unidade'].' ('.$ver['email'].')<br>';
            }
            $msg .= '<br>';
            
        }else{ # Se passou na verificação
            
            $calcula = $this->anatelForm->calculaESalvaIRS();
            
            if($calcula){
            
                $msg = '<div class="alert alert-success"><strong>Calculo realizado e salvo com sucesso</strong></div>';
            
            }else{
                
                $msg = '<div class="alert alert-danger"><strong>Erro ao calcular IRS, caso o erro persista contate o administrador!</strong></div>';
                
            }
            
        }
        
        $this->logDados['descricao'] = 'Anatel - Calculo IRS';
        $this->logDados['acao'] = 'PROCESSANDO';
        $this->logGeral->grava($this->logDados);
        
        $this->session->set_flashdata('statusOperacao', $msg);

        redirect(base_url('anatel/calcularIRS'));
    }
    
    /**
     * Anatel::geraXml()
     * 
     * Gera o xml
     * 
     * @return
     */
    public function geraXml(){
        
        $verifica = $this->anatelForm->indicadoresRespondidos('NAO', $this->input->post('tipo_xml'));
        
        if(count($verifica) > 0){
            $msg = '<strong>Os seguintes indicadores ainda n&atilde;o foram respondidos (Responda-os para gerar o xml):</strong><br><br>';
            foreach($verifica as $ver){
                $msg .= $ver->indicador.' - '.htmlentities($ver->unidade).' ('.$ver->nome_usuario.', '.$ver->email_usuario.')<br>';
            }
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-warning">'.$msg.'</div>');
            redirect(base_url('anatel/xml'));
        }
        
        $this->logDados['descricao'] = 'Anatel - Gerando xml';
        $this->logDados['acao'] = 'PROCESSANDO';
        $this->logGeral->grava($this->logDados);
        
        $dados = $this->anatelForm->dadosIndicadores();
        $this->xml->satva($dados);
                
    }
    
    public function emailCobranca(){
        
        $dados['tipos_frm_anatel'] = $this->anatelForm->tipos_frm();
        $dados['departamento'] = $this->dadosBanco->departamento();
        
	    #$dados['valores'] = $this->anatel->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_email_cobranca', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
                
    }
    
    public function enviarEmail(){
        #echo '<pre>';
        #print_r($_POST);

        $msg = utf8_encode('Prezado(a),<br><br>Acesse a <a href="http://sistemas.simtv.com.br/sistema">Ferramentas de Negócios</a> com seu login e senha da rede para responder os indicadores da Anatel.<br><br>Observação: Use o navegador Chrome ou Firefox para acessar a ferramenta.<br><br>Att,<br><br>Sim TV');
        $titulo = utf8_encode("Anatel - Indicadores pendentes");

        try{
            
            foreach(array_unique($this->input->post('email')) as $email){

                $this->util->enviaEmail('Sim TV - Ferramenta de negocios', 'naoresponda@simtv.com.br',$email, utf8_decode($titulo), utf8_decode($msg));
    
            }
            
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        $this->logDados['descricao'] = 'Anatel - Envio de e-mail em massa';
        $this->logDados['acao'] = 'PROCESSANDO';
        $this->logGeral->grava($this->logDados);
        
        $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Os e-mails foram enviados com sucesso!</strong></div>');
        redirect(base_url('anatel/emailCobranca'));
        
    }
    
    public function associaResponsavel(){
        
        try{
        
            $status = $this->anatelForm->associaResponsavel();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio associado com sucesso!</strong></div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao associar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));
        
        }
        
    }
    
    public function apagaAssociacao(){
        
        try{
        
            $status = $this->anatelForm->apagaResponsavel();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Respons&aacute;vel apagado com sucesso!</strong></div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar respons&aacute;vel, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('anatel/fichaForm/'.$this->input->post('cd_anatel_frm')));
        
        }
        
    }
    
    public function envio(){
        
        $dados = $this->anatelForm->emailsSenhas();  
        
        foreach($dados as $d){

            $status = $this->util->enviaEmail('Sim TV - Ferramenta de negocios', 'naoresponda@simtv.com.br',$d->email, utf8_decode($titulo), utf8_decode($msg));
            
            if($status){
                echo 'Enviado<br><br>';
            }else{
                echo 'Nao enviado<br><br>';
            }
            exit();
            
        }
        
        
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */