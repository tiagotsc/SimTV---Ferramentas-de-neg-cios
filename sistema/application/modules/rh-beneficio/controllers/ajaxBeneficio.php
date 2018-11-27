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
        
        $this->load->helper("transporte");
        $this->load->library('beneficios/alelo', '','alelo');
        $this->load->library('beneficios/VT', '','transporte');
        $this->load->model('rh-beneficio/beneficio_model', 'beneficio');
    }
    
    public function dadosPassagem(){
        
        $dados['dados']['compraValeTransporte'] = $this->transporte->montaCompraBeneficio($this->beneficio->infoVale($_POST['cd_unidade']));
        $dados['dados']['feriado'] = formataData($this->beneficio->retornaFeriadosUnidade($_POST['cd_unidade'],$_POST['mesCompraBeneficio']));
        $dados['dados']['unidade'] = $_POST['cd_unidade'];
                
        $this->load->view('view_json',$dados);
        
    }
    
    public function passagem(){
        
        $dados['dados']['passagem'] = $this->beneficio->retornaPassagem($_POST['id_passagem']);
        
        $this->load->view('view_json',$dados);
    }
    
    public function dadosUnidade(){
        
        $dados['dados']['unidade'] = $this->beneficio->retornaUnidadeRazaoSocial($_POST['razaoSocial']);
        
        $this->load->view('view_json',$dados);
    }
    
    public function dadosAlelo(){        
        
//        $_POST['mesCompraBeneficio'] = '02';
//        $_POST['opcBeneficio'] = 1;
//        $_POST['razao_social'] = 1;
//        $_POST['nomeDaTabela'] = 'rh_faltas_alelo';
        
        $dados['dados']['infoBeneficio'] = $this->alelo->montaCompraBeneficio($this->beneficio->retornaBeneficioCompra($_POST['razao_social'], $_POST['opcBeneficio']));
        $dados['dados']['razao_social'] = $_POST['razao_social'];
        $dados['dados']['mesCompraBeneficio'] = $_POST['mesCompraBeneficio'];
        
        $this->load->view('view_json',$dados);
    }
    
}