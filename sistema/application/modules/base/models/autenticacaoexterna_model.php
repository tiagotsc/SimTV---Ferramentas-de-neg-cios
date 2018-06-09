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
class AutenticacaoExterna_model extends CI_Model{
	
	/**
	 * DadosBanco_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Usuario_model::dadosUsuario()
    * 
    * Fun��o que monta um array com todos os dados do usu�rio
    * @param $cd Cd do usu�rio para recupera��o de dados
    * @return Retorna todos os dados do usu�rio
    */
	public function dadosUsuario($identificacao){
        
        $this->db->select('
                            usuario.cd_usuario, 
                            login_usuario, 
                            matricula_usuario, 
                            nome_usuario, 
                            rg_usuario, 
                            cpf_usuario, 
                            email_usuario, 
                            nome_usuario, 
                            cd_cargo, 
                            cd_departamento, 
                            cd_perfil, 
                            status_config_usuario, 
                            cd_estado, 
                            cd_unidade, 
                            tipo_usuario, 
                            index_php_usuario
                            ');
		$this->db->where('MD5(usuario.cd_usuario)', $identificacao);
        $this->db->or_where('MD5(usuario.email_usuario)', $identificacao); 
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
		return $this->db->get('adminti.usuario')->row(); 
		
	}

}