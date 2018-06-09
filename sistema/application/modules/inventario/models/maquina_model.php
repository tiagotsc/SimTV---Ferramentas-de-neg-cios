<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class maquina_model extends CI_Model{
    
    //nome das tabelas utilizadas para o modulo de cadastro de equipamentos
        const tabelaEquipamento = 'adminti.inventario_equipamento';
        
    
    function __construct(){
        parent::__construct();
    }
    
    //---------------- Pesquisas - INICIO ----------------
        
        public function retornaMaquinas(){
            $this->db->select('unidade.nome as unidade_nome, nome_departamento, numero_serie, id_equipamento, FK_tipo_equipamento, inventario_tipo_equipamento.tipo_equipamento, FK_fabricante, inventario_marca.nome as nome_marca, FK_modelo, inventario_modelo.modelo, inventario_equipamento.status');
            $this->db->join('adminti.unidade', 'adminti.inventario_equipamento.FK_cd_localidade = adminti.unidade.cd_unidade','left');
            $this->db->join('adminti.departamento','adminti.inventario_equipamento.FK_cd_setor = adminti.departamento.cd_departamento','left');
            $this->db->join('adminti.inventario_marca','adminti.inventario_equipamento.FK_fabricante = adminti.inventario_marca.id_marca', 'left');
            $this->db->join('adminti.inventario_modelo','adminti.inventario_equipamento.FK_modelo = adminti.inventario_modelo.id_modelo', 'left');
            $this->db->join('adminti.inventario_tipo_equipamento','adminti.inventario_equipamento.FK_tipo_equipamento = adminti.inventario_tipo_equipamento.id_tipo_equipamento', 'left');
            
            return $this->db->get(self::tabelaEquipamento)->result_array();
        }
    
        public function retornaNumeroSerie($numeroSerie){
            
            $this->db->select('numero_serie');
            $this->db->where('numero_serie',$numeroSerie);
            return $this->db->get(self::tabelaEquipamento)->row();
        }
        
        public function retornaMaquinaId($idEquipamento = null){
            $this->db->select('FK_cd_localidade, FK_cd_setor, numero_serie, id_equipamento, FK_tipo_equipamento, FK_fabricante, FK_modelo, inventario_equipamento.status');
            $this->db->where('id_equipamento',$idEquipamento);
            return $this->db->get(self::tabelaEquipamento)->row();
            
        }
        
    
    //----------------   Pesquisas - FIM  ----------------
    
    //---------------- Alteracoes - INICIO ----------------
    
        public function salvarMaquina(){
            
            $this->db->trans_begin();
            
            $this->db->insert(self::tabelaEquipamento, $_POST);
            
            if ($this->db->trans_status() === FALSE){
        
                $this->db->trans_rollback();
                return false;
            }
            else{

                $this->db->trans_commit();
                return true;
            }
        }
    
    //----------------   Alteracoes - FIM  ----------------
    
    
    
}