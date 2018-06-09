<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class administracao_model extends CI_Model{
    
    const tabelaUnidade = 'adminti.unidade';
    const tabelaVale = 'adminti.rh_passagem';
    const tabelaPassagem = 'adminti.rh_passagem';
    const tabelaValeTransporte = 'adminti.rh_vale_transporte';
    const tabelaBeneficioValorAlelo = 'adminti.rh_beneficio_valor_alelo';
    
    function __construct(){
        parent::__construct();

    }
    
    
    // ----------------------- consultas -------------------------
    
        public function retornaPassagens($cd_unidade){
            $this->db->select('id, passagens, valor, '.self::tabelaPassagem.'.cd_unidade, '.self::tabelaUnidade.'.nome, '.self::tabelaPassagem.'.status, '.self::tabelaPassagem.'.data_cadastro, '.self::tabelaPassagem.'.data_desativacao');
            $this->db->where(''.self::tabelaPassagem.'.cd_unidade', $cd_unidade);
            $this->db->where(self::tabelaPassagem.'.status', 'A');
            $this->db->join(self::tabelaUnidade, self::tabelaPassagem.'.cd_unidade = '.self::tabelaUnidade.'.cd_unidade','left');
            return $this->db->get(self::tabelaPassagem)->result_array();
            
        }
        public function retornaUnidade(){
            $this->db->select(self::tabelaPassagem.'.cd_unidade, unidade.nome');
            $this->db->distinct();
            $this->db->where(self::tabelaPassagem.'.status', 'A');
            $this->db->join(self::tabelaUnidade, self::tabelaPassagem.'.cd_unidade = '.self::tabelaUnidade.'.cd_unidade','left');
            return $this->db->get(self::tabelaPassagem)->result_array();
        }
        public function retornaArrayUnidadePassagem(){
        
            $x1 = $this->administracao->retornaUnidade();
            $y;

            foreach($x1 as $x){
                $y[] = array('cd_unidade' => $x['cd_unidade'],'unidade' => $x['nome'],'passagemInfo' => $this->administracao->retornaPassagens($x['cd_unidade']));
            }

            return $y;
        }
        public function retornaVale(){
            return $this->db->get(self::tabelaBeneficioValorAlelo)->result_array();
        }
        public function retornaPassagensInativas(){
            
            $unidade = $this->retornaPassagemUnidade($_POST['idPassagem']);

            
            $where = ['cd_unidade' => $unidade['cd_unidade'], 'status' => 'I', 'id != ' => $_POST['idPassagem']];
            
            $this->db->select('id, passagens, valor');
            $this->db->where($where);
            return $this->db->get(self::tabelaPassagem)->result_array();
            
            
        }
        function retornaPassagemUnidade($passagemUnidade){
            
            $this->db->select('cd_unidade');
            $this->db->where('id', $passagemUnidade);
            return $this->db->get(self::tabelaPassagem)->row_array();
            
        }
    
    
    //------------------------ alteracoes ------------------------
    
    public function cadastraPassagem($passagem){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch(self::tabelaPassagem, $passagem);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{    
            $this->db->trans_commit();
            return true;
        }
    }
    public function cadastraVale($dadosParaCadastro){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch(self::tabelaPassagem, $dadosParaCadastro);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{    
            $this->db->trans_commit();
            return true;
        }
    }
    public function alteraPassagem(){
        
        $this->db->trans_begin();
        
        $set = ['id_passagem' => $_POST['idPassagemNova']];
        
        $this->db->where('id_passagem', $_POST['idPassagemAntiga']);
        $this->db->update(self::tabelaValeTransporte, $set);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{    
            $this->db->trans_commit();
            $this->ativaPassagem($_POST['idPassagemNova']);
            return true;
        }
        
    }
    public function inativaPassagem($idPassagem){
        
        $set = ['status' => 'D','data_desativacao' => date('Y-m-d')];
        $this->db->update(self::tabelaPassagem, $set, 'id = '.$idPassagem);
        
    }
    public function ativaPassagem($idPassagem){
        $set = ['status' => 'A'];
        $this->db->update(self::tabelaPassagem, $set, 'id = '.$idPassagem);
    }
    
}