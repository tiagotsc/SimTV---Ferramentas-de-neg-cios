<?php

class processos_model extends CI_Model{
    
    function __construct() {
        parent::__construct();
    }
    
    // ------------------ Consultas ------------------
    
        public function retornaProcessosPesquisa(){
            
            $this->db->select('id_processo, numero_processo, nome_colaborador, data_processo, unidade_colaborador, unidade.nome, motivo_processo, fase_processo');
            $this->db->join('adminti.unidade','adminti.rh_processo.unidade_colaborador = adminti.unidade.cd_unidade','left');
            return $this->db->get('adminti.rh_processo')->result_array();
            
        }
        
        public function retornaProcessoEdicao(){
            
//            $this->db->where('id_processo',$_POST['idProcesso']);
            return $this->db->get('adminti.rh_processo')->result_array();
        }


    // ------------------ Alteracoes ------------------
    
        public function cadastraProcesso(){

            $this->db->trans_begin();

            $this->db->insert('adminti.rh_processo',$_POST);

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