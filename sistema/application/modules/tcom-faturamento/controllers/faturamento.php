<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class faturamento extends Base {
     
    private $logDados;
    const modulo = 'tcom-faturamento';
    const controller = 'faturamento';
    const pastaView = 'faturamento';
    const tabela = 'tcom_faturamento';
    const assunto = 'Faturamento';
    const modelAssunto = 'fatModel';
    const perModulo = 274;
    const perPesq = 413;
    const perPesqContFat = 433;
    const perNotaCredDeb = 434;
    const perCadEdit = 413;
    const perDeletar = 413;
    const perAcao = 415;
    const perValores = 364;
    const linkAcaoDownload = 'files/telecom/faturamento/';
     
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
        $this->load->model('faturamento_model',self::modelAssunto);
        $this->load->model('delin_model','delin');
        $this->load->model('tcom-contrato/contrato_model','contrato');
        $this->load->model('tcom-contrato/contratoValor_model','contratoValor');
        $this->load->model('tcom-operadora/operadora_model','operadora');
        $this->load->model('tcom-faturamento/notafiscal_model','notafiscal');
        $this->load->model('tcom-faturamento/nota_debcred_model','notaDebCred');
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('TELECOM', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('TELECOM', $this->session->userdata('permissoes')));
        
	} 
    
    public function teste(){
        
        #print_r($this->fatModel->testeFat());
        exit();
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
    
    public function realizarAcao(){
        
        $mostra_por_pagina = 30;
        $modelAssunto = self::modelAssunto;
        
        $valores['pasta'] = self::modulo;
        $valores['controller'] = self::controller;
        $valores['titulo'] = self::assunto;
        $valores['assunto'] = 'Realizar '.self::assunto;
        $valores['ano_acao'] = $this->delin->delinAno();
        $valores['linkAcaoDownload'] = self::linkAcaoDownload;
 
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        
        
        if(in_array(self::perAcao, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', self::pastaView.'/view_acao', $valores);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function gerar(){
        
        if($this->input->post('tipo_acao') == 'delin'){
            $this->geraDelin();                     
        }elseif($this->input->post('tipo_acao') == 'faturamento'){        
            $this->geraFaturamento();
        }elseif($this->input->post('tipo_acao') == 'nota_fiscal'){        
            $this->geraNF();
        }

    }
    
    public function geraDelin(){
        $arquivo = str_replace('/','_',$this->input->post('data')).'-'.date('H_i_s').".xlsx";
        $link = str_replace('/','_',$this->input->post('data')).'-'.date('H_i_s').".xlsx";
        
        $informacoes = $this->delin->dadosDelin($arquivo,$link);
            
        if($informacoes){
            
            $dados['valores'] = ($informacoes)? $informacoes: '';
            $dados['campos'] = ($informacoes)? array_keys($dados['valores'][0]): '';
            $dados['link'] = $link;
            $dados['modulo'] = self::modulo;
            $dados['controller'] = self::controller;
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Delin gerado para competencia '.$this->input->post('data').' com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao gerar delin para competencia '.$this->input->post('data').', caso o erro persista comunique o administrador!</strong></div>');
        }
        
        $this->load->view(self::pastaView.'/view_gera_aba', $dados); 
               
    }           
     
    
    public function geraFaturamento(){
        
        $modelAssunto = self::modelAssunto;
        $informacoes = $this->$modelAssunto->gravaFaturamento();
                        
        if($informacoes){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Faturamento gerado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao gerar faturamento, caso o erro persista comunique o administrador!</strong></div>');
        }
        
        redirect(base_url(self::modulo.'/'.self::controller.'/realizarAcao'));
            
    }  
    
    public function geraNF(){
        
        $informacoes = $this->notafiscal->gravaNF();
                        
        if($informacoes){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Nota fiscal gerado para competencia '.$this->input->post('data').' com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Erro ao gerar nota fiscal para competencia '.$this->input->post('data').', caso o erro persista comunique o administrador!</strong></div>');
        }
        
        redirect(base_url(self::modulo.'/'.self::controller.'/realizarAcao'));
            
    } 
    
    public function pesqContr($numeroContrato = false){
        
        $mostra_por_pagina = 30;
        $modelAssunto = 'contrato';
        
        if($numeroContrato){
            $_POST['numero'] = $numeroContrato;
        }
        
        $this->crud->ini();
        $par = $this->crud->getParMetodo();
        list($post, $sort_by, $sort_order, $pagina) = $par;
        
        $resultado = $this->$modelAssunto->pesquisa($post, $mostra_por_pagina, $sort_by, $sort_order, $pagina);
        $resultado['tabela'] = self::assunto;
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['permissor'] = $this->dadosBanco->unidade();
        $crud['indiceReajuste'] = $this->$modelAssunto->indiceReajuste();
        $crud['regrasReajustes'] = $this->contratoValor->regrasReajustes();
        $crud['perValores'] = self::perValores;
        $crud['perNotaCredDeb'] = self::perNotaCredDeb;
        $crud['assunto'] = self::assunto;
        $crud['modulo'] = self::modulo;
        $crud['operadorasPai'] = $this->operadora->pais();
        $crud['operadoras'] = $this->operadora->operadorasFaturamento();
        $crud['notaMotivos'] = $this->notaDebCred->motivos(); 
        $dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        
        
        if(in_array(self::perPesqContFat, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'contrato/view_psq', $crud);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
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
        $crud['gruposFaturados'] = $this->$modelAssunto->gruposFaturados();
        $crud['competenciasFaturadas'] = $this->$modelAssunto->competenciasFaturadas();

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
    
    
    public function ficha($idPai, $competencia = false){
        
        $modelAssunto = self::modelAssunto;
        
        $dados['assunto'] = self::assunto;
        $dados['modulo'] = self::modulo; 
        $dados['controller'] = self::controller; 
        $dados['grupo'] = $this->$modelAssunto->GrupoOperadora($idPai); 
        $dados['abertos'] = $this->$modelAssunto->dadosFaturamento($idPai,'ABERTO');   
        $dados['pagos'] = $this->$modelAssunto->dadosFaturamento($idPai,'PAGO');
        
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