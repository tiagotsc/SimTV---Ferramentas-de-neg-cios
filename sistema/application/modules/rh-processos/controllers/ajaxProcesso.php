<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

class AjaxProcesso extends Base{
    
    public function __construct(){
        parent::__construct();
        
        $this->load->model('rh-processos/processos_helper','helper');
        $this->load->model('rh-processos/processos_model','processo');
    }
    
    public function retornaInformacoesColaborador(){
        
//        $_POST['matricula_colaborador'] = 755249;
        
        $dados['dados']['info'] = $this->helper->retornaInformacaoColaborador();
        
        $this->load->view('view_json',$dados);
    }
    
    public function editaProcesso(){
        
//        echo 1;exit();
        
        $_POST['idProcesso'] = 1;
        
        $dados['dados']['processo'] = $this->processo->retornaProcessoEdicao();
        
        $this->load->view('view_json',$dados);
        
    }
    
}