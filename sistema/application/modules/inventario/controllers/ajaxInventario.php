<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

class ajaxInventario extends Base{
    
    public function __construct() {
        parent::__construct();

        $this->load->model('maquina_model', 'maquina');
    }
    
    public function validaNumeroSeire(){
        
//        $_POST = 2;
        
        $return = $this->maquina->retornaNumeroSerie($_POST['numeroSerie']);
        
        $dados['dados']['numero'] = empty($return);
        
        $this->load->view('view_json',$dados);
    }
    
    public function retornaEquipamento(){
        
        $_POST['idEquipamento'] = 1;
        
        $dados['dados']['equipamento'] = $this->maquina->retornaMaquinaId($_POST['idEquipamento']);
        
        $this->load->view('view_json',$dados);
    }
}