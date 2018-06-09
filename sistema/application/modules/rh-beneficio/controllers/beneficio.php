<?php

#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

/**
* Classe responsavel pelo cadastro de beneficios
*/
class Beneficio extends Base{
    
    private $logDados;
    const modulo = 'rh-beneficio';
    const controller = 'beneficio';
    const pastaView = 'beneficio';
    const tabela = 'adminti.beneficio_usuario';
    const assunto = 'Beneficio';
    const modelAssunto = 'beneficio';
    const perModulo = '359';
    
    public function __construct(){

        parent::__construct();

        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->helper("transporte");
        $this->load->helper('download');
        $this->load->library('beneficios/Transporte','','transport');
        $this->load->library('Crud', '', 'crud');
        $this->load->library('Util', '', 'util');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('beneficio_model', self::modelAssunto);
        $this->load->model('rh-usuario/rhferias_model','ferias');
    }
    
    //------------------ CONTROLE DE PAGINAS - INICIO ------------------
    
    public function teste(){
       
        imprimeVetor($_SESSION);
        
    }
    
    public function compraValeTransporte(){
        
        $dados['unidade'] = carregaUnidade($this->beneficio->retornaUnidade());

        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $this->layout->region('corpo', self::pastaView.'/view_compra_vale_transporte',$dados);
            
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    public function cadastraValeLote(){
        
        $link['url_model'] = 'rh-beneficio/beneficio/lote';
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $this->layout->region('corpo', self::pastaView.'/view_cadastra_lote',$link);
            
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
  
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    //------------------ CONTROLE DE PAGINAS - FIM    ------------------
    
    public function geraArquivo(){
        
        if($_POST != NULL){
            
            $_POST['cd_usuario'] = $this->session->userdata('cd');
            $_POST['data'] = date('Y-m-d H:i:s');
            
            $log = $this->transport->logCompraValeTransporte();
            
            $this->beneficio->logBeneficio($log);
            
            $this->transport->geraArquivo();
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">O arquivo nao foi gerado por falta de informacoes</div>');
            redirect(base_url('rh-beneficio/beneficio/compraValeTransporte'));
        }
    }
    
    public function cadastraBeneficio(){
        
        $cd = $_POST['cd_usuario'];
        
        
        //------------------ Monta array para salvar informacoes no banco - INICIO ------------------
        
        foreach($_POST as $c => $v){

            if($c <> 'btn_cadastro'){

                if ($c == 'numero_vale_transporte'){
                    
                    $chave[] = $c;
                    $valor[] = $this->formataValor($v);
                    
                }else{
                    
                    $chave[] = $c;
                    $valor[] = $v;
                    
                }
                
            }                
        }
        
        $beneficios = array_combine($chave, $valor);
        
        //------------------ Monta array para salvar informacoes no banco - FIM ------------------
        
        //------------------ Testa se a acao e INSERT ou UPDATE - INICIO ------------------
        
        $condicao = $this->beneficio->retornaValeTransporte($cd);
        
        
        if( $condicao == NULL){
            
            $status = $this->beneficio->salvaValeTransporte($beneficios);
            
        }else{
            
            $status = $this->beneficio->atualizaValeTransporte($beneficios);
            
        }
        
        //------------------ Testa se a acao e INSERT ou UPDATE - FIM ------------------
        
        //------------------ Gera log da acao e retorna feedback da acao - INICIO------------------
        
        if($status){

            $this->logDados['descricao'] = utf8_encode('Beneficios - Salva beneficio');
            $this->logDados['acao'] = 'UPDATE';
            $this->logGeral->grava($this->logDados);

            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>F&eacute;rias do(a) '.$this->input->post('ferias-nome').' salva com sucesso!</strong></div>');
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar f&eacute;rias, caso o erro persiste comunique o administrador!</div>');
        }
        
        redirect(base_url('rh-usuario/usuario/ficha/'.$_POST['cd_usuario']));
        
        //------------------ Gera log da acao e retorna feedback da acao - FIM ------------------
 
    }
    
    public function deletaBeneficio(){

        
        try{

            $status = $this->beneficio->deletaValeTransporte($_POST['cd_usuario']);

        }catch( Exception $e ){

            log_message('error', $e->getMessage());

        }

        if($status){

            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Vale transporte apagado com sucesso!</strong></div>');
            redirect(base_url('rh-usuario/usuario/ficha').'/'.$_POST['cd_usuario']);

        }else{

            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar vale transporte, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('rh-usuario/usuario/ficha').'/'.$_POST['cd_usuario']);

        }
    }
    
    public function formataValor($valor){
        
        $substrituir = array('.','-');
        return str_replace($substrituir, '', $valor);
        
    } 

    public function lote(){
       
        $this->load->library('beneficios/Controles', '', 'controles');
//        echo 1;
//        exit();
        $md5file = md5_file($_FILES['userfile']['tmp_name']);
                
        if($this->logArquivo->existenciaArquivo($md5file)){
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>O arquivo j&aacute; foi processado!</strong></div>');
        }else{
        
            $status = $this->util->uploadArquivo();
            
            if($status['status']){

                $msg = $this->controles->processaLote($status);
                $this->util->apagaArquivo($status['arquivo']['full_path']);

            }
            $this->session->set_flashdata('statusOperacao', $msg);
        }
        
        redirect(base_url('rh-beneficio/beneficio/cadastraValeLote'));
        
    }
    
        
    
}