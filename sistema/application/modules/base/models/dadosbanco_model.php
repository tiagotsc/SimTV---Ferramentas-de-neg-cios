<?php

/**
 * DadosBanco_model
 * 
 * Classe que realiza consultas genéricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class DadosBanco_model extends CI_Model{
	
	/**
	 * DadosBanco_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * DadosBanco_model::status()
    * 
    * Função que pega o status
    * @return Retorna os status existentes
    */
	public function status(){
		return $this->db->get('status')->result();
	}
    
    /**
    * DadosBanco_model::menu()
    * 
    * Função que pega os dados do menu
    * @return Retorna o menu
    */
	public function menu($permitidos = false){
	   
       if($permitidos){
            $this->db->where("cd_permissao IN (".implode(',',$permitidos).")");
        }
       
        $this->db->where("status_menu =  'A'");
        $this->db->order_by("ordem_menu", "asc"); 
		return $this->db->get('menu')->result();
	}
    
    /**
    * DadosBanco_model::paisMenu()
    * 
    * Função que pega o id dos menus pai
    * @return Retorna todos os dados do menu
    */
	public function paisMenu($permitidos = false){
	
        if($permitidos){
            $this->db->where("cd_permissao IN (".implode(',',$permitidos).")");
        }
    
        $this->db->distinct();
        $this->db->select('pai_menu');
		$this->db->where('pai_menu <> 0');
        $this->db->where("status_menu =  'A'");
		$paisMenu = $this->db->get('menu')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $paisMenu;
	}
    
    /**
    * DadosBanco_model::menu()
    * 
    * Função que pega os dados do menu
    * @return Retorna o menu
    */
	public function menuLateralDropDown($modulo, $permitidos = false){
	   
       if($permitidos){
            $this->db->where("cd_permissao IN (".implode(',',$permitidos).")");
        }
        
        $this->db->where("modulo", $modulo);
        $this->db->where("status = 'A'");
        $this->db->order_by("ordem", "asc"); 
		return $this->db->get('menu_lateral')->result();
	}
    
    /**
    * DadosBanco_model::paisMenu()
    * 
    * Função que pega o id dos menus pai
    * @return Retorna todos os dados do menu
    */
	public function paisMenuLateralDropDown($modulo, $permitidos = false){
	
        if($permitidos){
            $this->db->where("cd_permissao IN (".implode(',',$permitidos).")");
        }
    
        $this->db->distinct();
        $this->db->select('pai');
        $this->db->where("modulo", $modulo);
		$this->db->where('pai <> 0');
        $this->db->where("status =  'A'");
		$paisMenu = $this->db->get('menu_lateral')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $paisMenu;
	}
    
    /**
    * DadosBanco_model::permissoes()
    * 
    * Função que pega os dados das permissões
    * @return Retorna as permissões
    */
	public function permissoes(){
	   
        $this->db->where("status_permissao =  'A'");
        #$this->db->order_by("nome_permissao", "asc"); 
        $this->db->order_by("ordem_permissao", "asc"); 
		return $this->db->get('permissao')->result();
	}
    
    /**
    * DadosBanco_model::paiPermissao()
    * 
    * Função que pega o id das permissões pai
    * @return Retorna todos os dados das permissões
    */
	public function paiPermissao(){
	
        $this->db->distinct();
        $this->db->select('pai_permissao');
		$this->db->where('pai_permissao <> 0');
        $this->db->where("status_permissao =  'A'");
		$paisPemissao = $this->db->get('permissao')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $paisPemissao;
	}
    
    /**
    * DadosBanco_model::departamento()
    * 
    * Função que pega os departamento
    * @return Retorna os departamentos ativos
    */
	public function departamento(){
	   
        $this->db->where("status_departamento =  'A'");
        $this->db->order_by("nome_departamento", "asc"); 
        
		return $this->db->get('adminti.departamento')->result();
        
	}
    
    /**
    * DadosBanco_model::operadora()
    * 
    * Função que pega as operadoras
    * @return Retorna as operadoras ativos
    */
	public function operadora($idOperadora = null){
	   
        if($idOperadora){
            $this->db->where("id_operadora", $idOperadora);
        }
       
        $this->db->where("status =  'A'");
        $this->db->order_by("nome", "asc"); 
        
		return $this->db->get('adminti.operadora')->result();
        
	}
    
    /**
    * DadosBanco_model::estado()
    * 
    * Função que pega os estado
    * @return Retorna os estado
    */
	public function estado(){
	   
        $this->db->order_by("nome_estado", "asc"); 
		return $this->db->get('adminti.estado')->result();
	}
    
    /**
    * DadosBanco_model::parametro()
    * 
    * Função que pega os parâmetros
    * @return Retorna os parâmetros
    */
	public function parametro(){
	   
        $this->db->where("status_parametro =  'A'");
        #$this->db->order_by("nome_departamento", "asc"); 
        
		return $this->db->get('parametro')->result();
        
	}

    /**
    * DadosBanco_model::unidade()
    * 
    * Função que pega as unidades
    * @return Retorna as unidades
    */
	public function unidade($cd_unidade = null){
        
        if($cd_unidade != null){
            $this->db->where("permissor", $cd_unidade);
        }
        
        $this->db->where("status =  'A'");
        $this->db->order_by("nome", "asc"); 
        
		return $this->db->get('adminti.unidade')->result();
        
	}
    
    /**
    * DadosBanco_model::idOperadoras()
    * 
    * Função que pega os ids das operadoras
    * @return Retorna os ids
    */
    public function idOperadoras(){
        
        $this->db->distinct();
        $this->db->select('adminti.unidade.id_operadora,adminti.operadora.nome AS empresa');
        $this->db->where("adminti.unidade.id_operadora IS NOT NULL");
        $this->db->join('adminti.operadora', 'adminti.operadora.id_operadora = adminti.unidade.id_operadora');  
        $this->db->order_by("adminti.unidade.id_operadora", "asc");
        return $this->db->get('adminti.unidade')->result();
        
    }
    
    /**
    * DadosBanco_model::ddd()
    * 
    * Função que pega os ddds
    * @return Retorna os ddds
    */
	public function ddd(){
	   
        $this->db->select("cd_telefonia_ddd, CONCAT(ddd,' - ',estado) AS descricao");
        $this->db->where("status =  'A'");
        $this->db->order_by("ddd", "asc"); 
        
		return $this->db->get('adminti.telefonia_ddd')->result();
        
	}
    
    /**
    * DadosBanco_model::telefoniaOferta()
    * 
    * Função que pega as ofertas
    * @return Retorna as ofertas
    */
	/*public function telefoniaOferta(){
	   
        $this->db->where("status =  'A'");
        $this->db->order_by("nome", "asc"); 
        
		return $this->db->get('adminti.telefonia_oferta')->result();
        
	}*/
    
    /**
    * DadosBanco_model::telefoniaMarca()
    * 
    * Função que pega as marcas dos telefones
    * @return Retorna as marcas
    */
	public function telefoniaMarca(){
	   
        $this->db->where("status =  'A'");
        $this->db->order_by("nome", "asc"); 
        
		return $this->db->get('adminti.telefonia_marca')->result();
        
	}
    
    /**
    * DadosBanco_model::cargos()
    * 
    * Função que pega todos os cargos
    * @return Retorna os cargos
    */
	public function cargos(){
	   
        $this->db->where("status =  'A'");
        $this->db->order_by("nome", "asc"); 
        
		return $this->db->get('adminti.cargo')->result();
        
	}
    
    public function menuLateral($modulo, $permissoes = false){
        
        if($permissoes){
            $this->db->where("cd_permissao IN (".implode(',',$permissoes).")");
        }
        
        $this->db->where("modulo", $modulo);
        $this->db->where("status =  'A'");
        $this->db->order_by("ordem", "asc"); 
        
		return $this->db->get('sistema.menu_lateral')->result();
        
    }
    
    function get_field_types_basic_table($table)
    {
    	$db_field_types = array();
    	foreach($this->db->query("SHOW COLUMNS FROM `".$table."`")->result() as $db_field_type)
    	{
    		$type = explode("(",$db_field_type->Type);
    		$db_type = $type[0];

    		if(isset($type[1]))
    		{
    			if(substr($type[1],-1) == ')')
    			{
    				$length = substr($type[1],0,-1);
    			}
    			else
    			{
    				list($length) = explode(" ",$type[1]);
    				$length = substr($length,0,-1);
    			}
    		}
    		else
    		{
    			$length = '';
    		}
    		$db_field_types[$db_field_type->Field]['db_max_length'] = $length;
    		$db_field_types[$db_field_type->Field]['db_type'] = $db_type;
    		$db_field_types[$db_field_type->Field]['db_null'] = $db_field_type->Null == 'YES' ? true : false;
    		$db_field_types[$db_field_type->Field]['db_extra'] = $db_field_type->Extra;
    	}

    	$results = $this->db->field_data($table);
    	foreach($results as $num => $row)
    	{
    		$row = (array)$row;
    		$results[$num] = (object)( array_merge($row, $db_field_types[$row['name']])  );
    	}

    	return $results;
    }

    function get_field_types($table_name)
    {
    	$results = $this->db->field_data($table_name);

    	return $results;
    }

}