<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class faltas_model extends CI_Model{
    
    function __construct(){
        
        parent::__construct();
        
    }
    
    public function salvaFalta($faltasColaboradores, $nomeTabela){
        
        $this->db->trans_begin();
        
        foreach($faltasColaboradores as $falta){
            
            $dias = $this->consultaFaltaCadastro($falta['cd_usuario_colaborador'],$falta['mes_falta'], $nomeTabela);
            
            if( empty($dias) ){
                $this->db->insert($nomeTabela, $falta);
            }else{
                $this->db->where('id_falta', $dias['id_falta']);
                $this->db->update($nomeTabela, $falta);
            }
        }
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return false;
        }
        else{   
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function consultaFaltaCadastro($cd_usuario,$data,$nomeTabela){
        
        $where = "cd_usuario_colaborador = ".$cd_usuario." AND mes_falta = '".$data."'";
        
        $this->db->select("id_falta, qdt_acressimo, qdt_descontos");
        $this->db->where($where);
        return $this->db->get($nomeTabela)->row_array();
        
    }
    
    public function consultaFalta($cd_usuario){
        
        $where = "cd_usuario_colaborador = ".$cd_usuario." AND mes_falta = '".$data."'";
        
        $this->db->select('data_falta,cd_usuario');
        $this->db->where($where);
        return $this->db->get('adminti.rh_faltas')->result_array();
        
    }
    
    public function consultaFaltaCompra($cd_usuario, $mesFalta){
        
        $where = "cd_usuario = ".$cd_usuario." AND data_falta like '%-%-".$mesFalta."' AND status = 'A'";
        
        $this->db->select('data_falta,cd_usuario');
        $this->db->where($where);
        return $this->db->get('adminti.rh_faltas')->result_array();
        
    }
    
    public function retornaUsuarioUnidade($cd_unidade){
        $where = "cd_unidade = ".$cd_unidade." AND matricula_usuario IS NOT NULL";
        
        $this->db->select('cd_usuario, matricula_usuario, nome_usuario');
        $this->db->where($where);
        $this->db->order_by('nome_usuario','asc');
        return $t = $this->db->get('adminti.usuario')->result_array();
        
    }
}