<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class rh extends Base {
    
    private $logDados;
    const modulo = 'rh';
    const controller = 'rh';
    const tabela = 'sistema.tcom_interface';
    const assunto = 'Interface';
    const modelAssunto = 'tinterface';
    const perModulo = 359;
    
    public function __construct(){
        
        parent::__construct();
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('RH', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('RH', $this->session->userdata('permissoes')));
        
//        echo '<pre>';
//        print_r($this->menuLateral);
//        echo '</pre>';
//        exit();
        
    }
    
    function index(){
        
        
        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        
//        echo '<pre>';
//        print_r($dados);
//        echo '</pre>';
//        exit();
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'view_principal');
        
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
    
}