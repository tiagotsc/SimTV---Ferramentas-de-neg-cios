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
class AnaliseFinanceira_model extends CI_Model{
    
    const tabela = 'tcom_analise_financ';
    
    /**
	 * Contrato_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        
        $this->load->model('tcom-contrato/log_contrato_model','contratoLog');
        
	}
    
    public function gravaPrimeiroEnvioAnalise($idContrato, $usuarios){
        
        $existe = $this->existeAnaliseEmail($idContrato);
        
        if(!$existe){
            
            $this->db->trans_begin();
            
            foreach($usuarios as $usu){
                $sql = "INSERT INTO ".BANCO_TELECOM.".".self::tabela."(idContrato,cd_usuario,cd_usuario_disparador) ";
                $sql .= "VALUES(".$idContrato.",".$usu->cd_usuario.",".$this->session->userdata('cd').")";
                $this->db->query($sql);
            }
            
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                return false;
            }
            else
            {
                $this->db->trans_commit();
                
                return $id;
            }
            
        }
         
    }
    
    public function existeAnaliseEmail($idContrato = false){
        
        if(!$idContrato){
            return true;
        }
        
        $this->db->where('idContrato', $idContrato);
        $this->db->from(BANCO_TELECOM.'.'.self::tabela);
        return $this->db->count_all_results();
        
    }
    
    public function responsaveisAprovacao($idContrato = false){
        
        if(!$idContrato){
            return false;
        }
        
        $this->db->select("
                            tcom_analise_financ.id,
                            tcom_analise_financ.cd_usuario,
                            usuario.nome_usuario,
                            nome AS cargo,
                            aprovado,
                            DATE_FORMAT(data_aprovacao, '%d/%m/%Y %H:%i:%s') AS data_aprovacao
                        ");
        
        $this->db->where('MD5(idContrato)', $idContrato);
        $this->db->join('adminti.usuario', 'usuario.cd_usuario = '.self::tabela.'.cd_usuario');
        $this->db->join('adminti.cargo', 'cargo.cd_cargo = usuario.cd_cargo', 'left');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();
        
    }
    
    public function salvarResposta($idContrato, $resposta){
        
        $sql = "UPDATE ".BANCO_TELECOM.".".self::tabela." SET aprovado = '".strtoupper($resposta)."', data_aprovacao = current_timestamp() ";
        $sql .= "\nWHERE MD5(idContrato) = '".$idContrato."' AND MD5(cd_usuario) = '".md5($this->session->userdata('cd'))."'";
        #echo '<pre>'; print_r($sql); exit();
        $status = $this->db->query($sql);
        
        if($status){
            return true;
        }else{
            return false;
        }
        
    }
    
}