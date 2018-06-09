<?php

error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

/**
* Classe responsavel pelo cadastro de beneficios
*/
class Administracao extends Base{
    
    private $logDados;
    const modulo = 'rh-beneficio';
    const controller = 'administracao';
    const pastaView = 'administracao';
    const assunto = 'Administracao';
    const modelAssunto = 'administracao';
    const perModulo = '359';
    
    public function __construct(){

        parent::__construct();

        $this->logDados['usuario'] = $this->session->userdata('cd');
        $this->logDados['aplicacao'] = SISTEMA;
        $this->logDados['modulo'] = ucfirst(self::modulo).' - '.self::assunto;
        $this->logDados['funcao'] = $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->helper("transporte");
        $this->load->library('Crud', '', 'crud');
        $this->load->library('Util', '', 'util');
        $this->load->model('base/crud_model','crudModel');
        $this->load->model('administrador/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('administracao_model', self::modelAssunto);
        $this->load->model('beneficio_model', 'beneficio');
    }
    
    
    
    
//    public function teste(){}
    
    public function passagem(){
        
        $dados['passagens'] = $this->administracao->retornaArrayUnidadePassagem();
//        $dados['unidades'] = $this->administracao->retornaUnidade();
        $dados['unidade'] = carregaUnidade($this->beneficio->retornaUnidade());
        
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            $this->layout->region('corpo', self::pastaView.'/passagem_view',$dados);
        }else{
            $this->layout->region('corpo', 'view_permissao');
        }
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
        
    }
    
    public function cadastraPassagem(){
        $result;
        $baseData = array(
                'status' => 'I',
                'data_cadastro' => date('Y-m-d'),
                'data_desativacao' => NULL,
                'cd_unidade' => $_POST['cd_unidade']
            );

        for($i = 0;count($_POST)-1>$i;$i++){
            if($_POST['passagem_'.$i]['bilheteUnico']){
                $data[] = array('passagens' => null,'valor'=>$_POST['passagem_'.$i]['valorPassagem'],'bilhete_unico'=>'S')+$baseData;
            }else{
                $data[] = array('passagens' => $_POST['passagem_'.$i]['qdtPassagem'],'valor'=>$_POST['passagem_'.$i]['valorPassagem'],'bilhete_unico'=>'N')+$baseData;
            }
        }

        $resultado = $this->administracao->cadastraPassagem($data);
        
        if($resultado){
            $this->session->set_flashdata('statusOperacao', '<div class="col-md-offset-1 col-md-10 alert alert-success"><strong>Passagem cadastrada com sucesso!</strong></div>');
        }else{
            $this->session->set_flashdata('statusOperacao', '<div class="col-md-offset-1 col-md-10 alert alert-danger">Erro ao cadastrar passagem, caso o erro persiste comunique o administrador!</div>');
        }
        
        redirect(base_url('/rh-beneficio/administracao/passagem'));

    }
    
    public function vales() {
        
        $this->layout->region('html_header', 'view_html_header');
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        
        if(in_array(self::perModulo, $this->session->userdata('permissoes'))){
            
            $this->layout->region('corpo', self::pastaView.'/vales_view',$dados);
            
        }else{
            
            $this->layout->region('corpo', 'view_permissao');
            
        }
        
        $this->layout->region('rodape', 'view_rodape');
        $this->layout->region('html_footer', 'view_html_footer');
        
        
        // Ent�o chama o layout que ir� exibir as views parciais...
        $this->layout->show('layout');
    }
}