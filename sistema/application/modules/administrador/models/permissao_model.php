<?php
/**
* Classe que realiza todas as intera��es com a entidade agenda
*/
class Permissao_model extends CI_Model{
	
	/**
	 * PermissaoPerfil_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
		$this->load->library('Util', '', 'util');
	}
    
    /**
    * Permissao_model::permissoes()
    * 
    * Função que pega os dados das permissões
    * @return Retorna as permissões
    */
	public function permissoes(){
	   
        $this->db->where("status_permissao =  'A'");
        #$this->db->order_by("nome_permissao", "asc"); 
        $this->db->order_by("ordem_permissao", "asc"); 
		return $this->db->get('permissao')->result_array();
	}
    
 }