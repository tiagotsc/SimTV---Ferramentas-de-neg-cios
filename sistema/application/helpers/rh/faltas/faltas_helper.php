<?php
    
    function vetorFaltas(){
        
        $vetorFaltas = null;
        
        for($i= 0;$i <> count($_POST['matricula']);$i++){
            $vetorFaltas[] = [
                'cd_usuario_cadastrante' => $this->session->userdata('cd'),
                'data_cadastro' => date('Y-m-d'),
                'cd_usuario_colaborador' => $this->beneficio->retornaIdUsuario($_POST['matricula'][$i]),
                'cd_unidade_colaborador' => (empty($_POST['regionalValue']))?$_POST['cd_unidade'][$i]:$_POST['regionalValue'],
                'mes_falta' => $_POST['mesFalta'].'-'.date('Y'),
                'qdt_acressimo' => $_POST['acrescimos'][$i],
                'qdt_descontos' => $_POST['descontos'][$i]
            ];
        }
        
        return $vetorFaltas;
        
    }