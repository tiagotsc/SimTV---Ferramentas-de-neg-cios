<?php

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
    
    function imprimeVetor($vetor){
        
        echo '<pre>';
        pritn_r($vetor);
        echo '</pre>';
        exit();
        
    }