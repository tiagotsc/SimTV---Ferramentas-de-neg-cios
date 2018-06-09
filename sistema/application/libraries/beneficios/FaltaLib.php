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
        
        foreach ($_POST['colaboradores'] as $colaborador){
            
            $vetorFaltas[] = [
                'cd_usuario_cadastrante' => $this->ci->session->userdata('cd'),
                'data_cadastro' => date('Y-m-d'),
                'cd_usuario_colaborador' => $this->ci->beneficio->retornaIdUsuario($colaborador['matricula']),
                'cd_unidade_colaborador' => (empty($_POST['regionalValue']))?$colaborador['cd_unidade']:$_POST['regionalValue'],
                'mes_falta' => $_POST['mesCompraBeneficio'].'-'.date('Y'),
                'qdt_acressimo' => $colaborador['acrescimos'],
                'qdt_descontos' => $colaborador['descontos']
            ];  
        }

        return $vetorFaltas;
    }
    
    function diasUteis($cd_unidade, $mesCompraBeneficio, $matricula_usuario){
        
        $cd_usuario = $this->ci->beneficio->retornaIdUsuario($matricula_usuario);
        $feriados = $this->ci->beneficio->retornaFeriadosUnidade($cd_unidade, $mesCompraBeneficio);
        
        $feriados['total'] = count($feriados);
        $datas['mes'] = $mesCompraBeneficio;
        $datas['feriados'] = $feriados;

        $diasUteis = 0;
        $diasUteisFeriado = 0;
        $format = 'm-d';
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
        
        $diasUteisMes = $diasUteis - $diasUteisFeriado;
        
        $datas['ferias'] = $this->ci->beneficio->retornaPeriodoFeriasVale($cd_usuario, $mesCompraBeneficio);
        
        if($datas['ferias'] != NULL){
            return $diasUteisMes - $this->diasUteisFerias($datas);
        }else{
            return $diasUteisMes;
        }

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
                    if(date('m-d',$inicio) == $datas['feriados'][$i]['data']){
                        $diasUteisFeriado++;
                        $i++;  
                    }
                }
                else{
                    if(date('m-d',$inicio) == $datas['feriados'][$i]['data']){
                        $i++;
                    }
                }

                $inicio += $dia;
            }
            
            return $diasUteis - $diasUteisFeriado;

    }
    
}