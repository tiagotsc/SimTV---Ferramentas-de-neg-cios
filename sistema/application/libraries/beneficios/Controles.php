<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controles extends Base{
    
    
    
    
    
    public function __construct(){

        parent::__construct();
        
        $this->load->model('beneficio/beneficio_model','beneficio');
        $this->load->model('base/logArquivo_model','logArquivo');
        
        
    }
    
    
    
    
    public function processaArquivo($arquivo){
        

        
        $handle = file($arquivo['arquivo']['full_path']);
        
//        echo '<pre>';
//        print_r($handle);
//        echo '</pre>';
//        exit();
        
            $log['nome'] = basename($arquivo);
            $log['localizacao'] = $arquivo;
            $log['md5file'] = md5_file($arquivo);
            $log['fonte'] = 'Vale Transporte';
        

//        echo '3';
//        exit();
        
        $this->processaLote($handle);
        
        echo '4';
        exit();
        
        
//        if($handle){
//            return $handle;
//        }else{
//            return false;
//        }
        
    }
    
    public function processaLote($arquivo = false){
        
        if($arquivo){
            
            
            if($this->logArquivo->existenciaArquivo(md5_file($arquivo))){
                
                $msg = '<div class="alert alert-warning"><strong>O arquivo "'.basename($handle).'" j&aacute; foi processado!</strong></div>';
                
                return $msg;
            }
            
            $log['nome'] = $arquivo['arquivo']['file_name'];
            $log['localizacao'] = $arquivo['arquivo']['file_path'];
            $log['md5file'] = md5_file($arquivo['arquivo']['full_path']);
            $log['fonte'] = 'Vale Transporte';
            
            $logArquivo = $this->logArquivo->grava($log);
            
            $handle = file($arquivo['arquivo']['full_path']);

            foreach ($handle as $ha){
                $d = explode(',', $ha);

                $dados[] = array(
                    'cd_usuario' => $this->beneficio->retornaIdUsuario($d[0]),
                    'id_passagem' => $d[1]
//                    'numero_vale_transporte' => $d[2]
                );

            }

            $status = $this->beneficio->cadastraValeEmLote($dados);
        }else{
            $status = false;
        }
            
            
        if($status){

            return $msg = '<div class="alert alert-success"><strong>Usu&aacute;rio cadastrados com sucesso!</strong></div>';

//            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio cadastrados com sucesso!</strong></div>');
//            redirect(base_url('rh-beneficio/beneficio/importaEmLote'));

        }else{
            
            return $msg = '<div class="alert alert-danger">Erro ao cadastrar usu&aacute;rio, verifique o arquivo e tente novamente!</div>';
            
//            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao cadastrar usu&aacute;rio, verifique o arquivo e tente novamente!</div>');
//            redirect(base_url('rh-beneficio/beneficio/importaEmLote'));

        }
            
    }

        
        
        
        
    
    
    
    
}