<?php

/**
 * RegistroTelecom_model
 * 
 * Classe que realiza consultas gen�ricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class RegistroTelecom_model extends CI_Model{
	
	function __construct(){
		parent::__construct();
        $this->load->library('Util', '', 'util');
	}
	
    /**
    * Fun��o que pega os bancos de cobran�as
    * @return Retorna todos os banco ativos
    */
	public function registros(){
	       
        $this->db->join('status', 'sigla_status = status_registro_telecom');
		return $this->db->get('registro_telecom')->result();
        
	}
    
    /**
    * Fun��o que realiza a inser��o dos dados de registro de telecom
    * @return O n�mero de linhas afetadas pela opera��o
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
		foreach($_POST as $c => $v){
			
			$valorFormatado = $this->util->removeAcentos($this->input->post($c));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
			$campo[] = $c;
			$valor[] = $valorFormatado;
		}
		
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
		$sql = "INSERT INTO registro_telecom (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
		#return $this->db->affected_rows(); # RETORNA O N�MERO DE LINHAS AFETADAS
        
        return $this->db->insert_id();
	}
    
    /**
    * Fun��o que realiza a atualiza��o dos dados do paciente na base de dados
    * @return O n�mero de linhas afetadas pela opera��o
    */
	public function atualiza(){
		
		foreach($_POST as $c => $v){
			
			if($c != 'cd_registro_telecom'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
		$sql = "UPDATE registro_telecom SET ".$camposValores." WHERE cd_registro_telecom = ".$this->input->post('cd_registro_telecom');
		
		return $this->db->query($sql); # RETORNA O N�MERO DE LINHAS AFETADAS
		
	}
    
    /**
    * Fun��o que pega os dados solicitados de acordo com o par�metro
    * @return Os dados
    */
    public function dadosRegistroTelecom($cd){
	
		$this->db->where('cd_registro_telecom', $cd);
		$paciente = $this->db->get('registro_telecom')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $paciente[0];
	}
    
    /**
    * Fun��o que pega os nomes dos campos da tabela
    * @return Os campos
    */
	public function camposRegistroTelecom(){
		
		$campos = $this->db->get('registro_telecom')->list_fields();
		
		return $campos;
		
	}
    
    /**
    * Fun��o que apaga o registro
    * @return O n�mero de linhas afetadas pela opera��o
    */
    public function deletarRegistro(){
        
        $id = $this->input->post('excluir_cd_registro');
        
        $this->db->where('cd_registro_telecom', $id);
         
        $this->db->delete('registro_telecom'); 
        
        return $this->db->affected_rows();
    }

}