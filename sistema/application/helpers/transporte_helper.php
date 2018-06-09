<?php
    
    function geraArray(){
        
        $dados;
        $i = 0;
        
        
        if($_POST != NULL){
        
            while ($i < count($_POST['matricula'])){

                $dados['valorTotalArquivo'] += $_POST['total'][$i];
                
                $dados['matricula'][$i] = array(
                    'matricula' => $_POST['matricula'][$i],
                    'nome' => $_POST['nome'][$i],
                    'dias' => $_POST['dias'][$i],
                    'acrescimos' => intval($_POST['acrescimos'][$i]),
                    'descontos' => intval($_POST['descontos'][$i]),
                    'valorPassagem' => calculaValorPassagem($_POST['valorPassagem'][$i]),
                    'valorTotalPassagem' =>$_POST['total'][$i],
                );

                $i++;
            }
            
            return $dados;
        }else{
            echo 'deu ruim';
            exit();
        }
        
    }
    
    function imprimeVetor($vetor){
        echo '<pre>';
        print_r($vetor);
        echo '</pre>';
        exit();
    }
    
    function carregaUnidade($dados){
        
        $data;
        
        foreach($dados as $dado){
            if($dado->sigla != NULL){
                $data[] = (object) array(
                    'cd_unidade' => $dado->cd_unidade,
                    'sigla' => $dado->sigla,
                    'nome' => $dado->nome
                );
            }
        }
        
        return $data;
        
    }
    
    function formataData($dados){
        
        
        $feriados;
        
        foreach ($dados as $dado){
            
            $feriados[] = array(
                'data' => date('d-m-Y',strtotime($dado['data'])),
                'descricao' => $dado['descricao']
            );
            
        }
        
        return $feriados;
    }