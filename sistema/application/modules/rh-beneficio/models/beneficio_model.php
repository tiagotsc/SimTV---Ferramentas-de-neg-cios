<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Beneficio_model extends CI_Model{
    
    const tabelaPassagem = 'adminti.passagem';
    const tabelaValeTransporte = 'adminti.vale_transporte';
	
    /**
     * Usuario_model::__construct()
     * 
     * @return
     */
    function __construct(){
        parent::__construct();

    }
    
    //------------------ Inicio Consultas ------------------
    
    
    public function retornaPassagem($id = NULL){
        
        if($id == NULL){
            $this->db->select('id, passagens, valor');
            $this->db->where('status', 'A');
            return $this->db->get(self::tabelaPassagem)->result_array();
            
        }else{
            
            $this->db->select('valor');
            $this->db->where('id', $id);
            $this->db->where('status', 'A');
            return $this->db->get(self::tabelaPassagem)->result_array();
        }
        
        
    }
    
    public function retornaValeTransporte($cd_usuario){
            
        $this->db->select('id_passagem, numero_vale_transporte, passagens, valor');
        $this->db->where('cd_usuario',$cd_usuario);
        $this->db->join('adminti.passagem', 'adminti.vale_transporte.id_passagem = adminti.passagem.id', 'left');
        return $this->db->get('adminti.vale_transporte')->row_array();

    }
    
    public function retornaIdUsuario($matricula){
        
        $this->db->select('cd_usuario');
        $this->db->where('matricula_usuario',$matricula);
        $va = $this->db->get('adminti.usuario')->row_array();
        
        return $va['cd_usuario'];
        
    }
    
    public function informacoesValeTransporte($cd){
        
        $this->db->select('valor');
        $this->db->where('cd_usuario',$cd);
        $this->db->join('adminti.passagem','adminti.passagem.id = adminti.vale_transporte.id_passagem','left');
        return $this->db->get('adminti.vale_transporte')->row();
    }
    
    public function retornaFuncionarioValeTransporte(){
        $this->db->select('matricula_usuario');
//        $this->db->where('vale_transporte.cd_usuario',$cd);
        $this->db->join('adminti.usuario','adminti.usuario.cd_usuario = adminti.vale_transporte.cd_usuario','left');
        return $this->db->get('adminti.vale_transporte')->result_array();
    }
    
    public function retornaPeriodoFerias($cd_usuario){
        
        $this->db->select('inicio, fim');
        $this->db->where('cd_usuario', $cd_usuario);
        $this->db->where('status', 'A');
        return $this->db->get('adminti.ferias')->row_array();
        
    }
    
    public function retornaPeriodoFeriasVale($cd_usuario, $mes){
        
//        echo '10';
//        exit();
        
        $where = "cd_usuario = ". $cd_usuario ." AND inicio LIKE '%-".$mes."-%'";
        
        $this->db->select('inicio, fim');
        $this->db->where($where);
//        $this->db->where('status', 'A');
        return $this->db->get('adminti.ferias')->row_array();
        
    }
    
    public function infoVale($cd_unidade){
        $this->db->select('matricula_usuario, nome_usuario, valor');
        $this->db->join('adminti.usuario','adminti.usuario.cd_usuario = adminti.vale_transporte.cd_usuario','left');
        $this->db->join('adminti.passagem','adminti.passagem.id = adminti.vale_transporte.id_passagem','left');
        $this->db->where('cd_unidade', $cd_unidade);
        $this->db->where('usuario.status_usuario', 'A');
        return $this->db->get('adminti.vale_transporte')->result_array();
        
    }
    
    public function infoValeT(){
        $this->db->select('matricula_usuario, nome_usuario, valor');
        $this->db->join('adminti.usuario','adminti.usuario.cd_usuario = adminti.vale_transporte.cd_usuario','left');
        $this->db->join('adminti.passagem','adminti.passagem.id = adminti.vale_transporte.id_passagem','left');
//        $this->db->where('cd_unidade', $cd_unidade);
        return $this->db->get('adminti.vale_transporte')->result_array();
        
    }
    
    public function retornaUnidade(){
        $this->db->select('cd_unidade, sigla, nome');
        $this->db->where('permissor !=', 'NULL');
        return $this->db->get('adminti.unidade')->result();
    }
    
    public function retornaFeriadosUnidade($cd_unidade, $mesCompraBeneficio){
                
        $where = "cd_unidade = ". $cd_unidade ." AND data LIKE '%-".$mesCompraBeneficio."-%' ";

        $this->db->select('data, descricao');
        $this->db->where($where);
        $this->db->order_by('data', "asc"); 
        return $this->db->get('adminti.feriado')->result_array();
        
    }
    
    public function verificaGrupoFetranspor($cd_usuario){
        
        $this->db->select('matricula_fetranspor');
        $this->db->where('cd_usuario',$cd_usuario);
        return $this->db->get('adminti.usuario')->row_array();
    }

    
    //------------------ Fim Consultas ------------------
    
    
    
    //------------------ Inicio Alteracoes ------------------
    
    public function salvaValeTransporte($beneficios){
        
        array_pop($beneficios);
        
        $this->db->trans_begin();
        
        $this->db->insert('adminti.vale_transporte', $beneficios);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
        
    }
    
    public function atualizaValeTransporte($beneficios){
        
        array_pop($beneficios);
        
        $this->db->trans_begin(); 
        
        $this->db->where('cd_usuario',$beneficios['cd_usuario']);
        $this->db->update('adminti.vale_transporte', $beneficios);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function cadastraValeEmLote($usuarios){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch('adminti.vale_transporte', $usuarios);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
        
    }
    
    public function logBeneficio($log){
        
        $this->db->trans_begin();
        
        $this->db->insert_batch('adminti.log_compra_vale_transporte', $log);
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function deletaValeTransporte($id){
        
        $this->db->trans_begin();
        
        $this->db->where('cd_usuario',$id);
        $this->db->delete('adminti.vale_transporte');
        
        if ($this->db->trans_status() === FALSE){
        
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
    }
    
    
    //------------------ Fim Alteracoes ------------------
    

}