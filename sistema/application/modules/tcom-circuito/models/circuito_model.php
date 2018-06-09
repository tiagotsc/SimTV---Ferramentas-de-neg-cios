<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de circuito
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Circuito_model extends CI_Model{
	
    const tabela = 'tcom_circuito';
    
	/**
	 * Contrato_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        $this->load->model('tcom-contrato/log_contrato_model','contratoLog');
	}
    
    public function circuitos($id = false){
        
        if($id){
            $this->db->where('id', $id);
            return $this->db->get(BANCO_TELECOM.'.tcom_circuito')->row();
        }else{
            return $this->db->get(BANCO_TELECOM.'.tcom_circuito')->result();
        }
        
    }
    /*
    public function proximaDesignacao($cd_unidade, $idOperadora){
        
        $this->db->where('cd_unidade',$cd_unidade); 
        $this->db->where('idOper',$idOperadora);
        $qtd = $this->db->count_all_results(BANCO_TELECOM.'.tcom_contrato');
        $numero = $qtd + 1;
        
        $this->db->where('cd_unidade', $cd_unidade);
        $siglaUnidade = $this->db->get('adminti.unidade')->row()->sigla;
        
        $this->db->where('id', $idOperadora);
        $tituloOperadora = str_replace(" - ", "-", $this->db->get(BANCO_TELECOM.'.tcom_oper')->row()->titulo);
        
        return $siglaUnidade.'-LDF-'.str_pad($numero, 5, "0", STR_PAD_LEFT).'-'.$tituloOperadora;
        
    }
    */
    public function proximaDesignacao($cd_unidade, $idOperadora){
        
        $this->db->where('cd_unidade', $cd_unidade);
        $siglaUnidade = $this->db->get('adminti.unidade')->row()->sigla;
        
        $this->db->where('id', $idOperadora);
        $tituloOperadora = str_replace(" ", "", $this->db->get(BANCO_TELECOM.'.tcom_oper')->row()->titulo);
        
        $sql = "SELECT
                MAX(SUBSTR(designacao, 9, 5)) AS numero
                FROM ".BANCO_TELECOM.".tcom_circuito WHERE designacao LIKE '".$siglaUnidade."-LDF-%-".$tituloOperadora."'";
        $numero = $this->db->query($sql)->row()->numero + 1;
        
        return $siglaUnidade.'-LDF-'.str_pad($numero, 5, "0", STR_PAD_LEFT).'-'.$tituloOperadora;
        #echo $numero; echo '<br>';
        #echo $siglaUnidade.'-LDF-'.str_pad($numero, 5, "0", STR_PAD_LEFT).'-'.$tituloOperadora;
        #exit();
        
    }
    
    public function insere($designacao = false){
        
        $posts = array('idInterface','idTaxaDigital');
        
        if(!$designacao){
            $designacao = $this->proximaDesignacao($this->input->post('idUnidade'),$this->input->post('idOperadora'));
        }
        
        $campo[] = 'designacao';
		$valor[] = "'".$designacao."'";
        $campo[] = 'cd_usuario_cadastro';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'data_cadastro';
		$valor[] = "'".date('Y-m-d H:i:s')."'";

		foreach($posts as $p){

			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_circuito (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
        $_POST['designacao'] = $designacao;
        
        return $this->db->insert_id();
        
    }
    
    public function atualizaInterfaceVelocidade(){
        
        $posts = array('idInterface','idTaxaDigital');
        
        $campoValor[] = 'cd_usuario_atualizacao = '.$this->session->userdata('cd');

		foreach($posts as $p){

			$campoValor[] = $p."=".$this->input->post($p);
            
		}

		$resultado = implode(', ', $campoValor);
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_circuito SET ".$resultado."\n WHERE id=".$this->input->post('idCircuito');
		$status = $this->db->query($sql);
        $this->contratoLog->gravaLogCircuito('UPDATE_CIRCUITO', $sql);
        
        if($status){
            return $sql;
        }else{
            return false;
        }
        
    }

}