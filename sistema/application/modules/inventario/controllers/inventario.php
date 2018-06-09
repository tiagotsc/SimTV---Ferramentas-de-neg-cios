<?php 
include_once(APPPATH.'modules/base/controllers/base.php');
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario extends Base {
    
    private $logDados;
    const modulo = 'Inventario';
    const controller = 'inventario';
    const tabela = '';
    const assunto = 'Inventario';
    const modelAssunto = 'inventario';
    const perModulo = 416;
    const perDesenv = 428;
    
    public function __construct(){
        
        parent::__construct();
        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = "Ferramenta de negocio";
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];
        
        $this->load->library('Crud', '', 'crud');
        
        $this->load->model('helpers_model', 'helper');
        $this->load->model('maquina_model', 'maquina');
        $this->load->model('emprestimo_model','emprestimo');
        $this->load->model('rh-usuario/usuario_model', 'usuario');
        
        $this->util->setPositionMenu('');
        $this->menuLateral = $this->util->montaMenuLateral($this->dadosBanco->menuLateralDropDown('INVENTARIO', $this->session->userdata('permissoes')), $this->dadosBanco->paisMenuLateralDropDown('INVENTARIO', $this->session->userdata('permissoes')));
        
    }
    
    // ------------------ Controle de paginas ------------------
    
    public function index(){
        $this->layout->region('html_header', 'view_html_header');

        $dados['menuLateral'] = $this->menuLateral;
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $dados['menuLateral'] = $this->menuLateral;
            $this->layout->region('corpo', 'base/view_principal');
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->laoyut->region('corpo', 'base/view_permissao');
            
        }
        
        $this->layout->region('menu_lateral', 'base/view_menu_lateral', $dados);
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function pesquisa(){
        
        $this->layout->region('html_header', 'view_html_header');

        $menuLateral['menuLateral'] = $this->menuLateral;
        
        $dados['maquinas'] = $this->maquina->retornaMaquinas();
        $dados['unidades'] = $this->helper->retornaUnidade();
        $dados['departamentos'] = $this->helper->retornaDepartamento();
        
//        $this->imprimeVetor($dados);
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $this->layout->region('corpo', 'hardware/maquinas/pesquisaEquipamento_view',$dados);
            $this->layout->region('menu_lateral', 'base/view_menu_lateral', $menuLateral);
        
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    public function cadastro(){
        
        $dados['unidades'] = $this->helper->retornaUnidade();
        $dados['departamentos'] = $this->helper->retornaDepartamento();
        $dados['marca'] = $this->helper->retornaMarca();
        $dados['modelo'] = $this->helper->retornaModelo();
        $dados['tipoEquipamento'] = $this->helper->retornaTipoEquipamento();
        
//        $this->imprimeVetor($dados);
        
        $this->layout->region('html_header', 'view_html_header');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            $this->layout->region('corpo', 'hardware/maquinas/cadastraEquipamento_view',$dados);
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
//        $this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function emprestimo(){
        $this->layout->region('html_header', 'view_html_header');
        
        $dados['equipamentos'] = $this->maquina->retornaMaquinas();
        $dados['usuarios'] = $this->usuario->retornaUsuarioEmprestimo();
        $menuLateral['menuLateral'] = $this->menuLateral;
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            $this->layout->region('corpo', 'emprestimo/pesquisaEmprestimo_view',$dados);
            $this->layout->region('menu_lateral', 'base/view_menu_lateral', $menuLateral);
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    public function cadastraEmprestimo(){
        $this->layout->region('html_header', 'view_html_header');
        
        $menuLateral['menuLateral'] = $this->menuLateral;
        
        $dados['equipamentos'] = $this->maquina->retornaMaquinas();
        $dados['usuarios'] = $this->usuario->retornaUsuarioEmprestimo();
        
//        $this->imprimeVetor($dados);
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            $this->layout->region('corpo', 'emprestimo/cadastraEmprestimo_view',$dados);
//            $this->layout->region('menu_lateral', 'base/view_menu_lateral', $menuLateral);
        }else{
            
            $dados['menuLateral'] = false;
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
    }
    
    // ------------------ Fim ------------------
    
    public function salvarMaquina(){
        
        $return = $this->maquina->salvarMaquina();
        
        if($return === true){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Equipamento cadastrado com sucesso!</strong></div>');
            redirect(base_url('inventario/cadastro'));
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Falha ao cadastrar equipamento</div>');
            redirect(base_url('inventario/cadastro'));
        }
        
        
    }
    
    public function salvaEmprestimo(){
        
        echo date('Y-m-d',strtotime($_POST['data_inicio']));
        exit();
        
        $this->imprimeVetor($_POST);
        
        $return = $this->emprestimo->salvaEmprestimo();
        
        if($return === true){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Emprestimo cadastrado com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Falha ao cadastrar emprestimo</div>');
        }
        redirect(base_url('inventario/cadastraEmprestimo'));
    }
    
    function imprimeVetor($vetor){
        echo '<pre>';
        print_r($vetor);
        echo '</pre>';
        exit();
    }
}