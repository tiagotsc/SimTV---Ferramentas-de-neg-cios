<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class administracao_model extends CI_Model{
    
    const tabelaPassagem = 'adminti.passagem';
    const tabelaValeTransporte = 'adminti.vale_transporte';
    
    
    function __construct(){
        parent::__construct();

    }
    
    
    // ----------------------- consultas -------------------------
    
        public function retornaPassagens($cd_unidade){
            $this->db->select('passagens,valor,passagem.cd_unidade,unidade.nome, passagem.status, passagem.data_cadastro, passagem.data_desativacao');
            $this->db->where('passagem.cd_unidade', $cd_unidade);
            $this->db->join('adminti.unidade',"adminti.passagem.cd_unidade = adminti.unidade.cd_unidade",'left');
            return $this->db->get('adminti.passagem')->result_array();
            
        }
        public function retornaUnidade(){
            $this->db->select('passagem.cd_unidade, unidade.nome');
            $this->db->distinct();
            $this->db->where('passagem.status', 'A');
            $this->db->join('adminti.unidade',"adminti.passagem.cd_unidade = adminti.unidade.cd_unidade",'left');
            return $this->db->get('adminti.passagem')->result_array();
        }
        public function retornaArrayUnidadePassagem(){
        
            $x1 = $this->administracao->retornaUnidade();
            $y;

            foreach($x1 as $x){
                $y[] = array('cd_unidade' => $x['cd_unidade'],'unidade' => $x['nome'],'passagemInfo' => $this->administracao->retornaPassagens($x['cd_unidade']));
            }

            return $y;
        }
 
        
    
    
    //------------------------ alteracoes ------------------------
    
    public function cadastraPassagem($passagem){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch('adminti.passagem', $passagem);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
        
        
    }
    
}