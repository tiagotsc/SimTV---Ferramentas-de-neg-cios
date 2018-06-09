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
class Ativacao_model extends CI_Model{
    
	/**
	 * ViabilidadeRes_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){ 
		parent::__construct();
	}
    
    public function pendenciaAtivacao(){
        
        $this->db->select('tcom_viab.id,
                            tcom_viab.n_solicitacao');
        
        $this->db->where('tcom_viab_resp.ativou', 'N');                    
        $this->db->where('tcom_viab_resp.aprovacao', 'S');
        $this->db->where("tcom_contrato.status = 'P'");
        #$this->db->where('tcom_viab_resp.idStatusHist IS NOT NULL');
        #$this->db->where('tcom_contrato.status', 'P');
        #$this->db->or_where('idStatusHist NOT IN (8)');
        
        $this->db->join(BANCO_TELECOM.'.tcom_viab', 'tcom_viab.id = tcom_viab_resp.idViab');
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN idContratoAtual IS NOT NULL THEN idContratoAtual ELSE tcom_viab_resp.idContrato END');
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp')->result();
        
    }
    
    public function aplicaAtivacao(){
        
        $this->db->trans_begin();
        
        $this->salvaAtivacao();
        
        $this->contrato->execAtivacao();
        
        $this->salvaMsgAtivacao();
        
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
    
    public function salvaMsgAtivacao(){

        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_viab_resp_hist(idViabResp, observacao, anexo_label, anexo, idStatusHist, cd_usuario) ";
        $obs = utf8_encode(":: CONCLUSÃO ATIVAÇÃO")."\n\n";
        if($this->input->post('equip-nome')){
            
            $obs .= "Equipamentos usados:\n"; 
            foreach($this->input->post('equip-nome') as $equipNome){
                $obs .= utf8_encode($equipNome."\n");
            }
            $obs .="\n";
        }
        $obs .= addslashes($this->input->post('obs_ativacao'));
        
        $sql .= "VALUES(".$this->input->post('idResposta').",'".$obs."','".$this->input->post('anexo_label')."','".$this->input->post('anexo')."',".$this->input->post('idStatusHist').",".$this->session->userdata('cd').")";

        $this->db->query($sql);
        
    }
    
    public function aplicaUpgradeDowngrade(){
        
        $this->db->trans_begin();
        
        $this->salvaAtivacao();
        
        if($this->input->post('idContratoAnterior')){
            $this->contrato->inativaContratoAnterior($this->input->post('idContratoAnterior'));
            $this->salvaMsgInativacao($this->input->post('idContratoAnterior'));
        }
        
        #$queryCircuito = $this->circuito->atualizaInterfaceVelocidade();
        #$this->contrato->execAtivacaoUpgradeDowngrade($queryCircuito);
        $this->contrato->execAtivacao();
        
        $this->salvaMsgAtivacao();
        
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
    
    public function salvaAtivacao(){
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_viab_resp SET ativou = 'S', ";
        $sql .= "data_ativacao = CURRENT_TIMESTAMP(), ";
        $sql .= "cd_usuario_ativacao = ".$this->session->userdata('cd').", ";
        $sql .= "idStatusHist=".$this->input->post('idStatusHist')." ";
        $sql .= "WHERE id = ".$this->input->post('idResposta');
        $this->db->query($sql);
        
    }
    
    public function aplicaMudancaEndereco(){
        
        $this->db->trans_begin();
        
        $this->salvaAtivacao();
        
        if($this->input->post('idContratoAnterior')){
            $this->contrato->inativaContratoAnterior($this->input->post('idContratoAnterior'));
            $this->salvaMsgInativacao($this->input->post('idContratoAnterior'));
        }
        
        #$this->cliente->execAtivacaoMdEndereco();
        $this->contrato->execAtivacao();
        $this->salvaMsgAtivacao();
        
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
    
    public function salvaMsgInativacao($idContrato){

        switch ($this->input->post('idTipo')) {
            case 1:
                $tipo = "ATIVAÇÃO";
                break;
            case 2:
                $tipo = "UPGRADE";
                break;
            case 3:
                $tipo = "DOWNGRADE";
                break;
            case 4:
                $tipo = "DARKFIBER";
                break;
            case 5:
                $tipo = "MUD. DE END.";
                break;
        }
        
        $viabResp = $this->dadosRespTecnicaViab($idContrato);
        #echo '<pre>'; print_r($viabResp); exit();
        $cont = count($viabResp) - 1;
        #echo '<pre>'; print_r($viabResp[$cont]->id); exit();
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_viab_resp_hist(idViabResp, observacao, idStatusHist) ";
        $obs = utf8_encode(":: INATIVAÇÃO POR ".$tipo)."\n\n";
        $obs .= "EM ".date('d/m/Y H:i:s');
        
        $sql .= "VALUES(".$viabResp[$cont]->id.",'".$obs."',5)";

        $this->db->query($sql);
    }
    
    public function dadosRespTecnicaViab($idContrato){

        $this->db->select("tcom_viab_resp.*");
        #$this->db->where("ativou = 'S' AND AND tcom_contrato.status = 'A' CASE WHEN idContratoAtual IS NOT NULL THEN idContratoAtual = ".$idContrato." ELSE tcom_viab_resp.idContrato = ".$idContrato." END");
        $this->db->where("ativou = 'S' AND CASE WHEN idContratoAtual IS NOT NULL THEN idContratoAtual = ".$idContrato." ELSE tcom_viab_resp.idContrato = ".$idContrato." END");
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN idContratoAtual IS NOT NULL THEN idContratoAtual ELSE tcom_viab_resp.idContrato END');
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp')->result();

    }

}