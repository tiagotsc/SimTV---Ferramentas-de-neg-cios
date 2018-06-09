<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza consultas gen�ricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class Dashtelecom_model extends CI_Model{
	
	/**
	 * Dashboard_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function unidadesContratos($status = 'A'){
        
        $sql = "SELECT 
                DISTINCT
                unidade.cd_unidade,
                unidade.nome
                FROM ".BANCO_TELECOM.".tcom_contrato
                INNER JOIN adminti.unidade ON unidade.cd_unidade = tcom_contrato.cd_unidade
                WHERE tcom_contrato.status = 'P'
                ORDER BY unidade.nome
                ";
                
        return $this->db->query($sql)->result();
        
    }
    
    public function historicoPendencias($unidade = 'todas'){ 

        $sql = "SELECT
                CASE 
                	WHEN tcom_status_hist.id IS NULL
                		THEN 1000
                ELSE tcom_status_hist.id END AS idStatus,
                CASE 
                	WHEN tcom_status_hist.id IS NULL
                		THEN 'Sem Histórico'
                ELSE tcom_status_hist.nome END AS historico,
                #unidade.nome AS unidade,
                COUNT(*) AS qtd
                FROM ".BANCO_TELECOM.".tcom_viab_resp
                LEFT JOIN ".BANCO_TELECOM.".tcom_status_hist ON tcom_viab_resp.idStatusHist = tcom_status_hist.id
                LEFT JOIN ".BANCO_TELECOM.".tcom_contrato ON tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END
                INNER JOIN adminti.unidade ON unidade.cd_unidade = tcom_contrato.cd_unidade
                WHERE tcom_contrato.status = 'P' ";
        if($unidade != 'todas'){
            $sql .= "AND tcom_contrato.cd_unidade = ".$unidade." ";
        }
        $sql .= "GROUP BY tcom_status_hist.id, tcom_status_hist.nome#,unidade.nome";
                    
        return $this->db->query($sql)->result();
        
    }
    
    public function qtdContHistPend($unidade = 'todas'){
        
        $sql = "SELECT
                    COUNT(*) AS qtd
                FROM ".BANCO_TELECOM.".tcom_viab_resp
                LEFT JOIN ".BANCO_TELECOM.".tcom_contrato ON tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END
                WHERE tcom_contrato.status = 'P' ";       
        if($unidade != 'todas'){
            $sql .= "AND tcom_contrato.cd_unidade = ".$unidade;
        }
        return $this->db->query($sql)->row()->qtd;

    }

}