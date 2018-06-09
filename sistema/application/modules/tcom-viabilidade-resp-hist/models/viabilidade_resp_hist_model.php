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
class Viabilidade_resp_hist_model extends CI_Model{
	
    const tabela = 'tcom_viab_resp_hist';
    
	/**
	 * ViabilidadeRes_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){ 
		parent::__construct();
	}
    
    public function historicoResposta($id = false){
        
        if($id == false){
            return false;
        }
        
        $this->db->select("
                            tcom_viab_resp_hist.id,
                            tcom_viab_resp_hist.anexo_label,
                            tcom_viab_resp_hist.anexo,
                            tcom_viab_resp_hist.observacao,
                            tcom_viab_resp_hist.data_cadastro,
                            tcom_status_hist.nome,
                            usuario.nome_usuario,
                            usuario.cd_usuario");
        
        $this->db->where('idViabResp', $id); 
        $this->db->join('adminti.usuario', 'usuario.cd_usuario = tcom_viab_resp_hist.cd_usuario', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_status_hist.id = tcom_viab_resp_hist.idStatusHist'); 
        $this->db->order_by('tcom_viab_resp_hist.data_cadastro', 'desc');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();
    }
    
    public function dadosViabilidade($id){
        
        if(!$id){
            return false;
        }
        
        $this->db->select("controle, n_solicitacao, numero, unidade.cd_unidade, unidade.nome AS unidade, tcom_viab_tipo.nome AS tipo, permissor");
        $this->db->where('tcom_viab_resp.id', $id);
        $this->db->join(BANCO_TELECOM.'.tcom_viab_resp', 'tcom_viab_resp.idViab = tcom_viab.id'); 
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo'); 
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_viab.cd_unidade');
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END', 'left'); 
        return $this->db->get(BANCO_TELECOM.'.tcom_viab')->row();
        
    }
    
    public function dadosViabilidadeHist($id){
        
        if(!$id){
            return false;
        }
        
        $this->db->where('tcom_viab_resp_hist.id', $id);
        $this->db->join('adminti.usuario', 'usuario.cd_usuario = tcom_viab_resp_hist.cd_usuario', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_status_hist.id = tcom_viab_resp_hist.idStatusHist');
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp_hist')->row();
        
    }
    
    /**
     * Usuario_model::autenticaUsuario()
     * 
     * Autentica o usuário
     * 
     * @return Retorna os dados do usuário caso ele exista
     */
    public function autenticaUsuario($login){
        
        $this->db->select('usuario.cd_usuario, login_usuario, matricula_usuario, nome_usuario, email_usuario, cd_departamento, cd_perfil, status_usuario AS status_pai, status_config_usuario AS status_filho, status_chat_usuario, index_php_usuario, data_chat_usuario, CURRENT_TIMESTAMP() AS data_hora_atual');
        $this->db->where('login_usuario', $login);
        $this->db->where('status_usuario', 'A');
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        return $this->db->get('adminti.usuario')->row();
        
    }
    
    public function statusHistorico($condicao = false){

        $this->db->where('status', 'A');
        
        if($condicao == 'FINAL_NAO'){
            $this->db->where('final', 'N');
        }
        
        $this->db->order_by('nome', 'asc');
        #$this->db->limit(5);
        return $this->db->get(BANCO_TELECOM.'.tcom_status_hist')->result();
    }
    
    public function pegaIdResposta($id){
        
        $this->db->where('id', $id);
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp_hist')->row()->idViabResp;
        
    }
    
    public function pegaIdEmailEnvia($idStatusHist = false){
        
        if(!$idStatusHist){
            return false;
        }
        
        $this->db->select("idEmailEnvia");
        $this->db->where('id', $idStatusHist);
        return $this->db->get(BANCO_TELECOM.'.tcom_status_hist')->row()->idEmailEnvia;
        
    }
    
    /**
     * 
     * Pega o último status do histórico
     * @param Id para filtrar o histórico
     * 
     * @return A quantidade de linhas
     */
    public function eStatusFinal($id){
        
        $this->db->select('tcom_status_hist.final');
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_status_hist.id = tcom_viab_resp_hist.idStatusHist');
        $this->db->order_by('tcom_viab_resp_hist.data_cadastro', 'DESC');
        $this->db->where('tcom_viab_resp_hist.idViabResp', $id);
        #$this->db->limit(1);
        
        $resultado = $this->db->get(BANCO_TELECOM.'.'.self::tabela)->row();
        
        $statusTipo = isset($resultado->final)? $resultado->final: '';
        
        if($statusTipo == 'N' or $statusTipo == ''){
            return false;
        }else{
            return true;
        }
        
    }
    
    public function tipoStatus($id = false){
        
        if(!$id){
            return false;
        }
        
        $this->db->where('id', $id);
        return $this->db->get(BANCO_TELECOM.'.tcom_status_hist')->row()->final;
        
    }
    
    public function atualizaStatusViabRes(){
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_viab_resp SET idStatusHist = ".$this->input->post('idStatusHist')." WHERE id=".$this->input->post('idViabResp');
        return $this->db->query($sql);
    }
    
    public function pegaUltimoStatus($idResposta){
        
        $this->db->where('idViabResp', $idResposta);
        $this->db->order_by('data_cadastro', 'DESC');
        $this->db->limit(1);
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp_hist')->row()->idStatusHist;
        
    }

}