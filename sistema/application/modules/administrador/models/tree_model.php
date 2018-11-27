<?php
/**
* Classe que realiza todas as intera��es com a entidade agenda
*/
class Tree_model extends CI_Model{
	
	/**
	 * PermissaoPerfil_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){ 
		parent::__construct();
		$this->load->library('Util', '', 'util');
	}
    
    function tree_all() { 
    $result = $this->db->query("SELECT  id, name,name as text, 'glyphicon glyphicon-circle-arrow-right' AS icon, parent_id FROM categories  ")->result_array();
        foreach ($result as $row) {
    	$data[] = $row;
        }
        return $data;
    }
    
    function tqTree_all() { 
    #$result = $this->db->query("SELECT  id, name, parent_id FROM categories  ")->result_array();
    $result = $this->db->query("SELECT 
        	cd_permissao AS id, 
        	nome_permissao AS name, 
        	CASE WHEN pai_permissao = '0' THEN NULL ELSE pai_permissao END AS parent_id 
        FROM sistema.permissao 
        WHERE status_permissao = 'A' 
        ORDER BY ordem_permissao")->result_array();
        foreach ($result as $row) {
    	$data[] = $row;
        }
        return $data;
    }
    
 }
    