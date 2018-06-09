<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
#error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');
class Faltas{

    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('rh-beneficio/beneficio_model','beneficio');
        $this->ci->load->library('Util', '', 'util');
        
    }
    
    function vetorFaltas(){
        echo 1;exit();
        $vetorFaltas = null;
        
        for($i= 0;$i <> count($_POST['matricula']);$i++){
            $vetorFaltas[] = [
                'cd_usuario_cadastrante' => $this->session->userdata('cd'),
                'data_cadastro' => date('Y-m-d'),
                'cd_usuario_colaborador' => $this->ci->beneficio->retornaIdUsuario($_POST['matricula'][$i]),
                'cd_unidade_colaborador' => (empty($_POST['regionalValue']))?$_POST['cd_unidade'][$i]:$_POST['regionalValue'],
                'mes_falta' => $_POST['mesFalta'].'-'.date('Y'),
                'qdt_acressimo' => $_POST['acrescimos'][$i],
                'qdt_descontos' => $_POST['descontos'][$i]
            ];
        }
        
        return $vetorFaltas;
        
    }
}