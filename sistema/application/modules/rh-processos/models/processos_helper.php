<?php

class processos_helper extends CI_Model{
    
    function __construct() {
        parent::__construct();
    }
    
    public function retornaInformacaoColaborador(){
        
        $this->db->select('nome_usuario, unidade.cd_unidade, unidade.nome as nome_unidade, departamento.cd_departamento, departamento.nome_departamento, cargo.cd_cargo, cargo.nome as nome_cargo');
        $this->db->join('adminti.unidade','adminti.usuario.cd_unidade = adminti.unidade.cd_unidade','left');
        $this->db->join('adminti.departamento','adminti.usuario.cd_departamento = adminti.departamento.cd_departamento','left');
        $this->db->join('adminti.cargo','adminti.usuario.cd_cargo = adminti.cargo.cd_cargo','left');
        $this->db->where('matricula_usuario',$_POST['matricula_colaborador']);
        return $this->db->get('adminti.usuario')->row();
        
    }
    
    public function retornaUnidades(){
        
        $this->db->select('cd_unidade, nome');
        $this->db->where('compraBeneficio', 'S');
        return $this->db->get('adminti.unidade')->result_array();
    }
    
    public function retornaDepartamentos(){
        
        $this->db->select('cd_departamento, nome_departamento');
        $this->db->where('status_departamento', 'A');
        return $this->db->get('adminti.departamento')->result_array();
    }
    
    public function retornaCargos(){
        
        $this->db->select('cd_cargo, nome');
        $this->db->where('status', 'A');
        return $this->db->get('adminti.cargo')->result_array();
    }
}