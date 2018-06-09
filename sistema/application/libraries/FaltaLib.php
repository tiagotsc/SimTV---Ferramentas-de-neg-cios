<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
setlocale(LC_ALL, 'pt_BR.UTF-8');

class FaltaLib{
    
    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('rh-beneficio/beneficio_model', 'beneficio');
    }
    
    
    function vetorFalta(){
        
        $vetorFaltas = null;
        for($i= 0;$i <> count($_POST['matricula']);$i++){
            $vetorFaltas[] = [
                'cd_usuario_cadastrante' => $this->ci->session->userdata('cd'),
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
    
    function diasUteis($datas){
        
        $diasUteis = 0;
        $diasUteisFeriado = 0;
        $format = 'Y-m-d';
        $i = 0;
        // Obtém o número de dias no mês 
        $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $datas['mes'], date('Y'));


        for($dia = 1; $dia <= $dias_no_mes; $dia++){

            // Obtém o timestamp
            $timestamp = mktime(0, 0, 0, $datas['mes'], $dia, date('Y'));            
            $diaTeste  = date("N", $timestamp);
            
            if($diaTeste < 6){
                
                $diasUteis++;
                if(date($format,$timestamp) == $datas['feriados'][$i]['data']){
                    $diasUteisFeriado++;
                    $i++;
                }
            }else{
                if(date($format,$timestamp) == $datas['feriados'][$i]['data']){
                    $i++;
                }
            }

        }
        
        return $diasUteis - $diasUteisFeriado;

    }
    
    function diasUteisFerias($datas){
        
        $inicio = strtotime($datas['ferias']['inicio']);
        $fim = strtotime($datas['ferias']['fim']);
        $format = 'Y-m-d';
        $dia = 86400;// 1 dia em segundos
        $diasUteis = 0;
        $diasUteisFeriado = 0;
        $i = 0;
        

        $inicioArray = explode('-',$datas['ferias']['inicio']);
        $fimArray = explode('-',$datas['ferias']['fim']);

        if($inicioArray[1] != $fimArray[1]){
            
            if($datas['mes'] == $inicioArray[1]){
                $fim = date('Y').'-'.$datas['mes'].'-'.cal_days_in_month(CAL_GREGORIAN, $datas['mes'], date('Y'));
                $fim = strtotime($fim);
            }else{
                $inicio = date('Y').'-'.$datas['mes'].'-'.'01';
                $inicio = strtotime($inicio);
            }
            
        }
        
            while(date($format,$inicio) <= date($format,$fim)){
                $diaTeste = date('N',$inicio);
                if($diaTeste < 6){
                    $diasUteis++;
                    if(date($format,$inicio) == $datas['feriados'][$i]['data']){
                        $diasUteisFeriado++;
                        $i++;  
                    }
                }else{
                    if(date($format,$inicio) == $datas['feriados'][$i]['data']){
                        $i++;
                    }
                }

                $inicio += $dia;
            }
            
            return $diasUteis - $diasUteisFeriado;

    }
    
}