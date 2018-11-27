<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do m�dulo de interface
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2016
 * @access public
 */
class nota_debcred_model extends CI_Model{
	
    const tabela = 'tcom_debcred';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
        
		parent::__construct();
	}
    
    public function motivos($id){
        if($id){
            $this->db->where('id', $id);
        }
        $this->db->where('status', 'A');
        $this->db->order_by('nome', 'asc');
        if($id){
            return $this->db->get(BANCO_TELECOM.'.tcom_debcred_motivo')->row();
        }else{
            return $this->db->get(BANCO_TELECOM.'.tcom_debcred_motivo')->result();
        }
    }
    
    public function insere(){
        
        $this->db->trans_begin();
    
        foreach($_POST as $c => $v){
    
            if( !in_array($c,array('nome_contrato','nota_id')) ){
    
                $valorFormatado = $this->util->formaValorBanco( addslashes ( str_replace(",", ".", str_replace(".", "", trim($this->input->post($c)))) ));
    
                $campo[] = $c;
                $valor[] = $valorFormatado;
    
            }
    
        }
        
        $campo[] = 'idUsuarioInsere';
        $valor[] = $this->session->userdata('cd');
    
        $campos = implode(', ', $campo);
        $valores = str_replace('%','',implode(', ', $valor));
    
        $sql = "INSERT INTO ".BANCO_TELECOM.".".self::tabela." (".$campos.")\n VALUES(".$valores.");";
        $this->db->query($sql);
        $id = $this->db->insert_id();
        
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
    
    public function atualiza(){
        
         $this->db->trans_begin();
        
            foreach($_POST as $c => $v){
                
                if( !in_array($c,array('nome_contrato','nota_id')) ){

                    $valorFormatado = $this->util->formaValorBanco( addslashes ( str_replace(",", ".", str_replace(".", "", trim($this->input->post($c)))) ));
    
                    $campoValor[] = $c.' = '.$valorFormatado;
                
                }

            }
            
            $campoValor[] = 'IdUsuarioAtualiza = '.$this->session->userdata('cd');
            $campoValor[] = "dataAtualizacao = '".date('Y-m-d H:i:s')."'";

            $camposValores = str_replace('%','',implode(', ', $campoValor));

            $sql = "UPDATE ".BANCO_TELECOM.".".self::tabela." SET ".$camposValores." WHERE id = ".$this->input->post('nota_id').";";
            $this->db->query($sql);

        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
        
    }
    
    public function dadosNotas($idContrato){
        
        if($idContrato){
            $this->db->where('idContrato', $idContrato);
        }
        
        $this->db->select("tcom_debcred.id, 
                            format(valor,2,'de_DE') AS valor, 
                            tipo, 
                            idDebCredMotivo, 
                            tcom_debcred.status, 
                            nome_usuario, 
                            faturado,
                            tcom_debcred_motivo.nome AS nome,
                            DATE_FORMAT(CASE WHEN tcom_debcred.dataAtualizacao IS NOT NULL THEN tcom_debcred.dataAtualizacao ELSE tcom_debcred.dataCadastro END, '%d/%m/%Y %H:%i:%s') AS dataCadastro");
        $this->db->order_by('dataCadastro', 'desc');
        
        $this->db->join(BANCO_TELECOM.'.tcom_debcred_motivo', 'tcom_debcred_motivo.id = idDebCredMotivo');
        $this->db->join('adminti.usuario', 'CASE WHEN IdUsuarioAtualiza IS NOT NULL THEN IdUsuarioAtualiza ELSE idUsuarioInsere END = cd_usuario');                
        return $this->db->get(BANCO_TELECOM.'.tcom_debcred')->result();
        
    }
    
}