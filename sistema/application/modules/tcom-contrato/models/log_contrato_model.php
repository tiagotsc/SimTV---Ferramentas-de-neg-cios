<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo de contrato
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class Log_contrato_model extends CI_Model{
	
    const tabela = 'tcom_log_contrato';
    
	/**
	 * Contrato_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function gravaLogGeracao($acao, $posts, $querySql){
		
        foreach($posts as $campoTb => $post){
            
            if(!in_array($campoTb, array('cd_usuario_cadastro'))){
            
    			$valorFormatado = trim($this->util->removeAcentos($this->input->post($post)));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    			
                $campoTb = str_replace(array('anexo_', 'alt_'),'',$campoTb);
                
    			$campo[] = $campoTb;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
        $campo[] = 'query_circuito';
        $valor[] = "'".addslashes($this->getQueryInsereCircuito())."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }
    
    public function getQueryInsereCircuito(){
        
        $posts = array('designacao','idInterface','idTaxaDigital');
        foreach($posts as $p){
			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			$campo[] = $p;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_circuito (".$campos.")\n VALUES(".$valores.");";
        
        return $sql;
        
    }
    
    public function gravaLogNumero($acao, $querySql){
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('id');
        $campo[] = 'numero';
		$valor[] = "'".$this->input->post('numero')."'";
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        
    }
    
    /*public function gravaLogStatusVigencia($acao, $querySql){
        
        $dataFim = $this->util->formaValorBanco($this->input->post('alt_data_fim'));
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('alt_id');
        if($this->input->post('duracao_mes')){
        $campo[] = 'duracao_mes';
        $valor[] = $this->input->post('duracao_mes');
        }
        $campo[] = 'data_fim';
		$valor[] = $dataFim;
        $campo[] = 'status';
		$valor[] = "'".$this->input->post('alt_status')."'";
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }*/
    
    public function gravaLogAnexo($acao, $querySql){
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('id');
        $campo[] = 'anexo';
		$valor[] = "'".$this->input->post('anexo')."'";
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }
    
    public function gravaLogDeleta($acao, $sqlContrato, $sqlCircuito){
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('apg_id');
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($sqlContrato)."'";
        $campo[] = 'query_circuito';
        $valor[] = "'".addslashes($sqlCircuito)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }
    
    public function gravaLogExecAtiv($acao, $querySql, $queryEquip = false, $queryCircuito = false){
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('idContrato');
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'data_inicio';
        $valor[] = 'CURDATE()';
        $campo[] = 'status';
        $valor[] = "'A'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
        if($queryEquip != ''){
            $campo[] = 'query_equip';
            $valor[] = "'".addslashes($queryEquip)."'";
        }
        if($queryCircuito != ''){
            $campo[] = 'query_circuito';
            $valor[] = "'".addslashes($queryCircuito)."'";
        }
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }
    
    public function gravaLogVigencia($acao, $querySql){
        
        $dataInicio = $this->util->formaValorBanco($this->input->post('data_inicio'));
        $dataFim = $this->util->formaValorBanco($this->input->post('data_fim'));
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('id');
        $campo[] = 'duracao_mes';
        $valor[] = $this->input->post('duracao_mes');
        $campo[] = 'data_inicio';
		$valor[] = $dataInicio;
        $campo[] = 'data_fim';
		$valor[] = $dataFim;
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }   
    
    public function gravaLogStatus($acao, $querySql){
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('id');
        $campo[] = 'status';
		$valor[] = "'".$this->input->post('status')."'";
        
        if($this->input->post('status') == 'C'){
            if($this->input->post('data_solic_canc')){
                $campo[] = 'data_solic_canc';
                $valor[] = $this->util->formaValorBanco($this->input->post('data_solic_canc'));
            }
            if($this->input->post('data_canc')){
                $dataCanc = $this->util->formaValorBanco($this->input->post('data_canc'));
            }else{
                $dataCanc = "'".date('Y-m-d')."'";
            }
            $campo[] = 'data_canc';
            $valor[] = $dataCanc;
        }
        
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        
    } 
    
    public function gravaLogServicos($acao, $querySql){

        $campo[] = 'idContrato';
        $valor[] = $this->input->post('id');
        
        if($this->input->post('idServicoTipo')){
            $campo[] = 'idServicoTipo';
    		$valor[] = $this->input->post('idServicoTipo');
        }
        
        if($this->input->post('idServico')){
            $campo[] = 'idServico';
    		$valor[] = $this->input->post('idServico');
        }
        
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_sql';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }        
    
    public function gravaLogCircuito($acao, $querySql){
        
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('idContrato');
        $campo[] = 'status';
		$valor[] = "'".$this->input->post('status')."'";
        $campo[] = 'acao';
        $valor[] = "'".$acao."'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'query_circuito';
        $valor[] = "'".addslashes($querySql)."'";
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        
    }
    
    public function gravaLogExecUpgrDown(){
        
    }
    
    public function gravaLogExecMdEnd(){
        
    }

}