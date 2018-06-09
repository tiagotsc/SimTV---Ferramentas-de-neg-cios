<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

class AjaxBeneficio extends Base{
    
    public function __construct() {
        parent::__construct();
        
        $this->load->model('rh-beneficio/beneficio_model', 'beneficio');
        $this->load->library('beneficios/Transporte', '','transporte');
        $this->load->helper("transporte");
    }
    
    public function dados(){
        
        $dados['dados']['compraValeTransporte'] = $this->transporte->montaCompraBeneficio($this->beneficio->infoVale($_POST['cd_unidade']));
        $dados['dados']['feriado'] = formataData($this->beneficio->retornaFeriadosUnidade($_POST['cd_unidade'],$_POST['mesCompraBeneficio']));
        
        
        $this->load->view('view_json',$dados);
        
    }
    
    public function passagem(){
        
        $dados['dados']['passagem'] = $this->beneficio->retornaPassagem($_POST['id_passagem']);
        
        $this->load->view('view_json',$dados);
    }
}