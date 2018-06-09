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
    const tabelaFaltaAlelo = 'adminti.rh_faltas_alelo';
    const logAlelo = 'adminti.log_compra_alelo';
    const logValeTransporte = 'adminti.log_compra_vale_transporte';
    
    
    public function __construct(){

        parent::__construct();

        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->helper("transporte");
        $this->load->helper('download');
        
        $this->load->library('Crud', '', 'crud');
        $this->load->library('Util', '', 'util');
        $this->load->library('beneficios/alelo','','alelo');
        $this->load->library('beneficios/VT','','transport');
        $this->load->library('beneficios/FaltaLib','','faltaLib');
        $this->load->library('beneficios/LogBeneficio','','logBeneficio');
        
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('rh-usuario/faltas_model', 'faltas');
        $this->load->model('rh-usuario/rhFerias_model','ferias');
        $this->load->model('beneficio_model', self::modelAssunto);
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
    }
    
    //------------------ CONTROLE DE PAGINAS - INICIO ------------------
    
    public function teste(){
        
        $_POST['cd_unidade'] = 15;
        $_POST['mesCompraBeneficio'] = "02";
        
        
//        echo date('Y')."-"."02"."-%";
//        exit();
        
        $d = $this->beneficio->retornaFeriadosUnidade(15,"03");
        
//        $t = $this->transport->montaCompraBeneficio($d);
        
        echo '<pre>';
        print_r($d);
        echo '</pre>';
        exit();
        
    }
    
    
    public function compraValeTransporte(){
        
        $dados['unidade'] = carregaUnidade($this->beneficio->retornaUnidade());

        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            $this->layout->region('corpo', self::pastaView.'/vt/compra_vale_transporte_view',$dados);
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
            $this->layout->region('corpo', self::pastaView.'/vt/cadastra_lote_view',$link);
        }else{
            $this->layout->region('corpo', 'view_permissao');
        }
  
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    public function compraValeAlelo(){
        
        $dados['unidades'] = $this->beneficio->retornaRazaoSocial();
        $dados['valorBeneficio'] = $this->beneficio->retornaValorBeneficio();
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            $this->layout->region('corpo', self::pastaView.'/alelo/compra_vale_alelo_view',$dados);
        }else{
            $this->layout->region('corpo', 'view_permissao');
        }
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    //------------------ CONTROLE DE PAGINAS - FIM    ------------------
    
    public function geraArquivoVt(){
        
        
//        $x = $this->transport->geraRegistroJuizFora();
//        echo '<pre>';
//        print_r($_POST);
//        echo '</pre>';
//        exit();
        
        
        
//        $t = $this->transport->geraArray();
//        imprimeVetor($t);
//        $this->transport->testaUnidade();
        
        
        if($_POST != NULL){
            
            $_POST['cd_usuario'] = $this->session->userdata('cd');
            $_POST['data'] = date('Y-m-d H:i:s');
            
//            $opc = $this->faltas->salvaFalta($this->faltaLib->vetorFalta());
            
            $log = $this->transport->logCompraValeTransporte();
//            imprimeVetor($log);
//            $t = $this->logBeneficio->logCompraValeTransporte();
            
            
            $this->beneficio->logBeneficio($log,self::logValeTransporte);
            $this->transport->testaUnidade();
            
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">O arquivo nao foi gerado por falta de informacoes</div>');
            redirect(base_url('rh-beneficio/beneficio/compraValeTransporte'));
        }
    }
    
    public function geraArquivoAlelo(){
        
//        $infos = $this->alelo->montaCompraBeneficio($this->beneficio->retornaBeneficioCompra($_POST['razaoSocial'], $_POST['opcBeneficio']));
        $infos = $this->beneficio->retornaBeneficioCompra($_POST['razaoSocial'], $_POST['opcBeneficio']);
        
//        echo '<pre>';
//        print_r($_POST); 
//        print_r($infos);
//        echo '</pre>';
//        exit();
        
//        $t = $this->alelo->geraArray();
//        imprimeVetor($t);
        
        
//        $this->alelo->geraArquivo();
        
//        $x = $this->transport->geraRegistroJuizFora();
//        imprimeVetor($x);
        
//        $this->transport->testaUnidade();
        
        if($_POST != NULL){
//            echo 1;exit();
            
//            echo '<pre>';
//            print_r($_POST);
//            echo '</pre>';
//            exit();
            
            $_POST['cd_usuario'] = $this->session->userdata('cd');
            $_POST['data'] = date('Y-m-d H:i:s');
            
            $t = $this->faltaLib->vetorFalta();
            
//            echo '<pre>';
//            print_r($t);
//            echo '</pre>';
//            exit();
            
            $opc = $this->faltas->salvaFalta($this->faltaLib->vetorFalta(),self::tabelaFaltaAlelo);
            
            
            $this->logBeneficio->logCompraAlelo(self::logAlelo);
            
            
//            $this->beneficio->logBeneficio($log);
            
            $this->alelo->geraArquivo();
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">O arquivo nao foi gerado por falta de informacoes</div>');
            redirect(base_url('rh-beneficio/beneficio/compraValeTransporte'));
        }
    }
    
    
    
    //depreciate
    public function cadastraBeneficio(){
        
        /*$cd = $_POST['cd_usuario'];
        
        
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
        
        $beneficios = ['cd_usuario' => $_POST['cd_usuario'],'id_passagem'=>$_POST['id_passagem']];
        
        
        
        //------------------ Monta array para salvar informacoes no banco - FIM ------------------
        
        //------------------ Testa se a acao e INSERT ou UPDATE - INICIO ------------------
        
        $condicao = $this->beneficio->retornaValeTransporte($_POST['cd_usuario']);
        
        
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

//            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>F&eacute;rias do(a) '.$this->input->post('ferias-nome').' salva com sucesso!</strong></div>');
        }else{
            
//            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar f&eacute;rias, caso o erro persiste comunique o administrador!</div>');
        }
        
//        redirect(base_url('rh-usuario/usuario/ficha/'.$_POST['cd_usuario']));*/
        
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