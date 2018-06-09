<?php

/**
 * DadosBanco_model
 * 
 * Classe que realiza consultas gen�ricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class Financeiro_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
    /**
    * Fun��o que pega os bancos de cobran�as
    * @return Retorna todos os banco ativos
    */
	public function banco($cdBanco = null){
	   
       if(!empty($cdBanco)){
        $this->db->where('cd_banco', $cdBanco);
       }
        $this->db->where('status_banco', 'A');
        $this->db->order_by("nome_banco", "asc");
		return $this->db->get('banco')->result();
        
	}

}