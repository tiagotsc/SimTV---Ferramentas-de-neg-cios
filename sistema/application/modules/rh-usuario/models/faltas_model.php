<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Faltas_model extends CI_Model{
    
    function __construct(){
        
        parent::__construct();
        
    }
    
    public function salvaFalta(){
        
        $data_falta = $this->util->formaValorBanco($this->input->post('data_falta'));
                
        if($this->existeFerias($this->input->post('cd_usuario'))){
            $sql = "UPDATE adminti.ferias SET inicio = ".$inicio.", fim = ".$fim." WHERE cd_usuario = ".$this->input->post('fer_cd_usuario');
        }else{
            $sql = "INSERT INTO adminti.ferias(inicio, fim, cd_usuario) VALUES(".$inicio.",".$fim.", ".$this->input->post('fer_cd_usuario').")";
        }
        
        
        $sql = "INSERT INTO adminti.faltas(data_falta, cd_usuario) VALUES(".$data_falta.", ".$this->input->post('cd_usuario').")";
        
        
        $this->db->trans_begin();
        
//        $this->db->insert('adminti.faltas',$data);
        $this->db->query($sql);
        
        if($this->db->trans_status() === FALSE){
            
            $this->db->trans_rollback();
            return false;
        }
        else{
            
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function consultaFalta(){
        
        
        
    }
}