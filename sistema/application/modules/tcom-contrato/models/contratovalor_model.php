<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do módulo da resposta da viabilidade
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class ContratoValor_model extends CI_Model{
    
    const tabela = 'tcom_contrato_valor';
    
	/**
	 * ViabilidadeRes_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){ 
		parent::__construct();
	}
    
    public function existeContratoValor($idContrato){
        
        $this->db->where('idContrato', $idContrato);
        $this->db->from(BANCO_TELECOM.'.'.self::tabela);
        $count = $this->db->count_all_results();

        return $count;
        
    }
    
    public function regraAplicaCamposAtivacao($idContrato){
        $diaProRataPriMes = 30-date('d')+1;
        $dataPriFatura = date('Y-m-d', strtotime("+1 months",strtotime(date('Y-m-d'))));
        
        if($this->existeContratoValor($idContrato)){
            $sql = "UPDATE ".BANCO_TELECOM.'.'.self::tabela." SET ";
            $sql .= "dia_pro_rata_pri_mes='".$diaProRataPriMes."', ";
            $sql .= "data_pri_fatura='".$dataPriFatura."' ";
            $sql .= "WHERE idContrato=".$idContrato;
        }else{
            $sql = "INSERT ".BANCO_TELECOM.'.'.self::tabela."(dia_pro_rata_pri_mes, data_pri_fatura, idContrato) VALUES('".$diaProRataPriMes."','".$dataPriFatura."',".$idContrato.")";
        }
        $this->db->query($sql);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato_valor(dia_pro_rata_pri_mes, data_pri_fatura, idContrato) VALUES('".$diaProRataPriMes."','".$dataPriFatura."',".$idContrato.")";
        $this->db->query($sql);
    }
    
    public function regrasReajustes(){
        
        $this->db->order_by('nome', 'asc');
        return $this->db->get(BANCO_TELECOM.'.tcom_regra_reajuste')->result();

    }

}