<?php
#error_reporting(0);
include_once(APPPATH.'modules/base/controllers/base.php');
#include_once(APPPATH.'controllers/base.php');
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');

class ajaxUsuario extends Base{
    
    
    public function __construct() {
        parent::__construct();

        $this->load->model('faltas_model', 'faltas');
        $this->load->model('rh-beneficio/beneficio_model', 'beneficio');
        $this->load->library('faltaLib','','falta');
    }
    
    public function retornaFaltas(){
        
        $dados['dados']['faltas'] = $this->faltas->consultaFalta($_POST['cd_usuario']);
        
        $this->load->view('view_json',$dados);
        
    }
    
    public function retornaUsuarios(){
        
        $dados['dados']['usuarios'] = $this->cadastraFaltas($this->faltas->retornaUsuarioUnidade($_POST['unidade']));
        
        $this->load->view('view_json',$dados);
        
    }
    
    public function cadastraFaltas($colaboradores){ 
        
        $compra;
        $feriados = $this->beneficio->retornaFeriadosUnidade($_POST['unidade'],$_POST['mesCompraBeneficio']);
        $feriados['total'] = count($feriados);
        $datas['mes'] = $_POST['mesCompraBeneficio'];
        $datas['feriados'] = $feriados;
        $diasUteisMes = $this->falta->diasUteis($datas);
        $diasCompraPassagem = '';
        
        foreach($colaboradores as $colaborador){
            
            $ferias = $this->beneficio->retornaPeriodoFeriasVale($colaborador['cd_usuario'],$_POST['mesCompraBeneficio']);
            
            if($ferias != NULL){
                $datas['ferias'] = $ferias;
                $diasCompraPassagem = $diasUteisMes - $this->falta->diasUteisFerias($datas);
            }else{
                $diasCompraPassagem = $diasUteisMes;
            }
            
            $infoTela = $this->faltas->consultaFaltaCadastro($colaborador['cd_usuario'],$_POST['mesCompraBeneficio'].'-'.date('Y'));
            
            $compra[] = array(
                'cd_usuario' => $colaborador['cd_usuario'],
                'matricula_usuario' => $colaborador['matricula_usuario'],
                'nome_usuario' => $colaborador['nome_usuario'],
                'diasUteis' => $diasCompraPassagem,
                'qdt_acressimo' => (is_null($infoTela['qdt_acressimo']))?0:$infoTela['qdt_acressimo'],
                'qdt_descontos' => (is_null($infoTela['qdt_descontos']))?0:$infoTela['qdt_descontos']
            );
            
        }
        
        return $compra;
    }
    
}