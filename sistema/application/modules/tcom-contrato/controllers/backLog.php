<?php 
include_once('contrato.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BackLog extends contrato {
     
     private $logDados;
     const modulo = 'tcom-contrato';
     const controller = 'BackLog';
     const pastaView = 'backlog';
     const tabela = 'tcom_contrato';
     const assunto = 'BackLog';
     const modelAssunto = 'backlog';
     const perModulo = 274;
     const perPesq = 347;
     const dirUpload = './files/telecom/contrato';
     const linkDownload = 'files/telecom/contrato';
     
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
        
        $this->load->model('tcom-contrato/backlog_model',self::modelAssunto);
        $this->load->model('tcom-viabilidade-resp-hist/viabilidade_resp_hist_model','viabHist');
        $this->load->model('tcom-viabilidade/viabilidade_model','viab');

        
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
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $dados);
        
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
        #echo '<pre>'; print_r($resultado); exit();
        $resultado['tabela'] = self::assunto;
        
        $postEncode = (!$post)? 0: $this->util->base64url_encode($post); 
        
        $crud = $this->crud->listarManual($resultado, $mostra_por_pagina, $postEncode, $sort_by, $sort_order, $pagina);
        $crud['permissor'] = $this->dadosBanco->unidade();
        $crud['historicoStatus'] = $this->viabHist->statusHistorico();
        $crud['viabTipo'] = $this->viab->tiposViabilidade();
        $crud['perVisualizarHistorico'] = self::perVisualizarHistorico;
        $crud['perImprimir'] = self::perImprimir;
        $crud['dirDownload'] = self::linkDownload;
        $crud['assunto'] = self::assunto;
        $crud['modulo'] = self::modulo;

        #$dados['menuLateral'] = $this->menuLateral;
        $this->layout->region('html_header', 'view_html_header');
        #$this->layout->region('menu_lateral', 'tcom/view_menu_lateral', $dados);
        $this->layout->region('menu_lateral', 'view_menu_lateral', false);
        
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
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */