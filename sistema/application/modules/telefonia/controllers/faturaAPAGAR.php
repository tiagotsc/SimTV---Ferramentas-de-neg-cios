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
class Fatura extends Base
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
    public function faturaArquivoMovel(){

        $this->session->set_userdata('menuLateral', $_SERVER['REDIRECT_QUERY_STRING']);
        
        $this->load->model('base/logarquivo_model','logArquivo'); 
        
        $dados['operadoras'] = $this->operadora->operadoras();
        
        $dados['ultimosArquivos'] = $this->logArquivo->dataUltimaAcao();
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        if(in_array(236, $this->session->userdata('permissoes'))){
        
            $this->layout->region('corpo', 'fatura/view_processa_fatura_movel', $dados);
        
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
