<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

class AjaxAdministracao extends Base{
    
    public function __construct() {
        parent::__construct();
        
        $this->load->model('administracao_model', 'administracao');
    }
    
    
    public function dados(){
        
        $_POST['cd_unidade'] = 15;
        
        $dados['dados']['passagens'] = $this->administracao->retornaPassagens($_POST['cd_unidade']);
        $dados['dados']['unidade'] = $_POST['cd_unidade'];
                
        $this->load->view('view_json',$dados);
    }
    
    public function retornaPassagensInativas(){
        
//        $_POST['idPassagem'] = 1;
        
        $dados['dados']['passagens'] = $this->administracao->retornaPassagensInativas($_POST['idPassagem']);;
        
        $this->load->view('view_json',$dados);
    }
}