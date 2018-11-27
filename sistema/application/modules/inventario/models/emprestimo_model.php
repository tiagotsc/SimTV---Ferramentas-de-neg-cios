<?php

class emprestimo_model extends CI_Model{
    
    const tabelaEmprestimo = 'adminti.inventario_emprestimo';
    
    // ------------------ Selecao ------------------
    
    public function salvaEmprestimo(){
        
        $this->db->trans_begin();
            
        $this->db->insert(self::tabelaEmprestimo, $_POST);

        if ($this->db->trans_status() === FALSE){

            $this->db->trans_rollback();
            return false;
        }
        else{

            $this->db->trans_commit();
            return true;
        }
        
    }
    
    // ------------------ Alteracao ------------------
}

        