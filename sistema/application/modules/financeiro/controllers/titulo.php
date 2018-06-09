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
class Titulo extends Base
{
    
    private $logDados;
    
	/**
	 * Titulo::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();

        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = 'Titulo';
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        #$this->load->model('titulo_model','operadora');
  
        
    }
    
    /**
     * Titulo::index()
     * 
     * Tela inicial da telefonia
     * 
     * @return
     */
    function index()
    { 
        
        
    }
    
    /**
     * Titulo::operadoras()
     * 
     * Tela inicial de pesquisa da operadora
     * 
     * @return
     */
    public function impressaoTitulo($tipo = ''){
        
        /*
        if($tipo == 'paga'){
            $url = 'http://sistemas.simtv.com.br/ws/titulo/segundaViaPdfPaga';
        }else{
            $url = 'http://sistemas.simtv.com.br/ws/titulo/segundaViaPdf';
        }
        */
        $url = 'http://sistemas.simtv.com.br/ws/titulo/segundaViaPdf';
        $dados['permissor'] = $this->dadosBanco->unidade();
        $dados['url'] = $url;
        $dados['tipo'] = $tipo;
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'titulo/view_segunda_via', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
                
}
