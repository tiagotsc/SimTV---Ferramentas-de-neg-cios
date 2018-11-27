<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class helpers_model extends CI_Model{
    
    const tabelaDepartamento = 'adminti.departamento';
    const tabelaUnidade = 'adminti.unidade';
    const tabelaMarca = 'adminti.inventario_marca';
    const tabelaModelo = 'adminti.inventario_modelo';
    const tabelaTipoEquipamento = 'adminti.inventario_tipo_equipamento';
    
    function __construct(){
        parent::__construct();
    }
    
    public function retornaDepartamento(){
        
        $this->db->select('cd_departamento,nome_departamento');
        $this->db->where('status_departamento','A');
        return $this->db->get(self::tabelaDepartamento)->result();
        
    }
    
    public function retornaUnidade(){
        $this->db->select('cd_unidade,nome');
        $this->db->where('status','A');
        return $this->db->get(self::tabelaUnidade)->result();
    }
    
    public function retornaModelo(){
        
        $this->db->select('id_modelo, modelo');
        return $this->db->get(self::tabelaModelo)->result();
    }
    
    public function retornaMarca(){
        
        $this->db->select('id_marca, nome');
        return $this->db->get(self::tabelaMarca)->result();
    }
    
    public function retornaTipoEquipamento(){
        
        $this->db->select('id_tipo_equipamento, tipo_equipamento');
        return $this->db->get(self::tabelaTipoEquipamento)->result();
    }
    
}