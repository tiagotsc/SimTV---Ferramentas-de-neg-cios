<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe respons?vel pela usu?rio
*/
class faturaproc extends Base
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
        
        # Configura??es do sistema
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
        
        if($this->anatelForm->verificaResponsavel()){ // Se ? respons?vel por responder relat?rio da Anatel
            
            // Se a data corrente estiver dentro do per?odo pega os formul?rios
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
        
        if($this->session->userdata('indexHabilita') == 'SIM'){
            $linkClicado = substr($_SERVER['PHP_SELF'],1);
            $link = 'sistema/index.php/telefonia';
        }else{
            $linkClicado = $_SERVER['REDIRECT_QUERY_STRING'];
            $link = 'telefonia';
        }        
              
        $this->layout->region('html_header', 'view_html_header');
      	#$this->layout->region('menu', 'view_menu', $menu);
        if($linkClicado == $link){
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
        
        // Ent?o chama o layout que ir? exibir as views parciais...
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
    
    public function faturasArquivos(){
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);
        
        $this->load->model('base/logarquivo_model','logArquivo'); 
        
        $dados['operadoras'] = $this->operadora->operadoras();
        
        $dados['ultimosArquivos'] = $this->logArquivo->dataUltimaAcao();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(236, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'fatura/view_fat_movel', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent?o chama o layout que ir? exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function uploadArquivos(){
        
        #echo '<pre>'; print_r($_POST);
        #echo '<br><br>';
        #print_r($_FILES); exit();
        
        $config['upload_path'] = './temp/movel';
        $config['allowed_types'] = 'txt';
		#$config['allowed_types'] = '*';
		$config['max_size'] = '50048'; # MB
		#$config['max_width'] = '0';
		#$config['max_height'] = '0';
		#$config['encrypt_name'] = true;
        $status = $this->util->uploadArquivo('file', $config); 
        if($status['status']){
            echo json_encode(array('status'=>'ok'));
        }else{
            echo json_encode(array('status'=>'erro'));
        }
    }
    
    public function removeArquivo(){
        #$_POST['arquivo'] = 'grupos.txt';
        $dir = './temp/movel/';
        
        $arquivo = str_replace(" ", "_", $this->input->post("arquivo"));

        $status = $this->util->apagaArquivo($dir.$arquivo);
        
        if($status === true){
            echo json_encode(array('status'=>'ok'));
        }else{
            echo json_encode(array('status'=>'erro'));
        }
        exit();
    }
    
    /*public function faturas(){
        
        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);
        
        $this->load->model('base/logarquivo_model','logArquivo'); 
        
        $dados['operadoras'] = $this->operadora->operadoras();
        
        $dados['ultimosArquivos'] = $this->logArquivo->dataUltimaAcao();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(236, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'fatura/view_fat_movel_fatura', $dados);
        
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent?o chama o layout que ir? exibir as views parciais...
      	$this->layout->show('layout');          
        
    }*/
    
    /**
     * Telefonia::processaFatura()
     * 
     * Processa os arquivos de faturas
     * 
     * @return
     */
    public function processaFatura(){

        #$_POST['processar'] = 'sim';
        #$_POST['tipo'] = 8;
        if($this->input->post("processar") == 'sim'){
             
            $this->load->library('telefonia/Fatura', '', 'fatura');
            $this->load->model('base/logArquivo_model','logArquivo');  
            try
            {

                $status = $this->fatura->processarArquivoMovel();
                $this->session->set_flashdata('statusOperacao', implode('', $status));
                
                $this->logDados['descricao'] = 'Telefonia - Processa arquivo fatura('.$this->input->post('tipo').')';
                $this->logDados['acao'] = 'INSERT'; 
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }
        
        $this->logGeral->grava($this->logDados);

        
    }
    
}    