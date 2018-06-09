<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class rhProcessos extends Base {
    
    private $logDados;
    const modulo = 'rh_processos_trabalhista';
    const controller = 'rh_processos';
    const tabela = 'rh_';
    const assunto = 'processos_trabalhista';
    const modelAssunto = 'processos_trabalhista';
    const perModulo = 401;
    
    public function __construct(){
        
        parent::__construct();
        
        $this->load->library('Crud', '', 'crud');
        $this->load->model('rh-processos/processos_helper','helper');
        $this->load->model('rh-processos/processos_model','processos');
    }
    
    public function pesquisar(){
        
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        
        $dados['processos'] = $this->processos->retornaProcessosPesquisa();
        
//        $this->imprimeVetor($dados);
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'pesquisaProcessos_view',$dados);
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function cadastrar(){
        
        $this->layout->region('html_header', 'view_html_header');

        $menu['menuLateral'] = $this->menuLateral;
        $dados['cargos'] = $this->helper->retornaCargos();
        $dados['setores'] = $this->helper->retornaDepartamentos();
        $dados['unidades'] = $this->helper->retornaUnidades();
        
//        echo '<pre>';
//        print_r($dados);
//        echo '</pre>';
//        exit();
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'cadastraProcesso_view',$dados);
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'view_menu_lateral', $menu);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function cadastrarProcessos(){
        
//        echo '<pre>';
//        print_r($_POST);
//        echo '</pre>';
//        exit();
        
        $result = $this->processos->cadastraProcesso();
        
        if($result == true){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">Processo cadastrado com sucesso</div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Falha ao cadastrar processo</div>');
        }
        redirect(base_url('rh-processos/rhProcessos/cadastrar'));
        
    }
    
    function imprimeVetor($dados){
        echo '<pre>';
        print_r($dados);
        echo '</pre>';
        exit();
    }
    
}