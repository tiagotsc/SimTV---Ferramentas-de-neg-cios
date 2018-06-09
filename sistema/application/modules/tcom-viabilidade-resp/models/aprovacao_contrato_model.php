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
class Aprovacao_contrato_model extends CI_Model{
    
	/**
	 * ViabilidadeRes_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){ 
		parent::__construct();
	}
    
    public function atualizaContrato(){
        
        // Atualiza Mudança de endereço do cliente
        if($this->input->post('idTipo') == 5){
            
            $status = $this->atualizaEnderecoCliente();
            
        }/*else{
            
            $status = $this->atualizaDadosContrato();
            
        }*/
        return $status;
    }
    
    public function insereContrato(){
        
        $this->load->model('tcom-circuito/circuito_model','circuito');
        
        $designacao = $this->circuito->proximaDesignacao($this->input->post('idUnidade'), $this->input->post('idOperadora'));
        $idCircuito = $this->circuito->insere($designacao);
        
        $dadosContrato = $this->contrato->insereContratoAprovacao($idCircuito);
        
        $idContrato = $dadosContrato['idContrato'];
        
        $this->contrato->inseriContratoCircuito($idContrato, $this->input->post('idCircuito'), $this->input->post('idInterface'), $this->input->post('idTaxaDigital'));
        $this->cliente->atualizaPontaB($idContrato, $this->input->post('idTipo'));
        
        if($dadosContrato['linhasAfetadas']){
            return array('status'=>'ok', 'acao'=>'cadastrar', 'descricao'=>'Contrato gerado com sucesso!', 'idContrato'=>$idContrato, 'contrato'=>$designacao);
        }else{
            return array('status'=>'erro', 'acao'=>'cadastrar', 'descricao'=>'Erro ao gerar contrato, caso o erro persista comunique o administrador.');
        }
    }
    
    public function insereContratoMudanca(){
        #echo '<pre>'; print_r($_POST); exit();
        $this->load->model('tcom-circuito/circuito_model','circuito');
        
        $idBackup = $this->input->post('id');
        #echo 'teste'; 
        #$this->contrato->inativaContratoAnterior($this->input->post('temContrato'));
        #echo 'passou'; exit();
        #$_POST['id'] = $idBackup;
        #echo '<pre>'; print_r($_POST); exit();
        #$this->salvaMsgInativacao($this->input->post('temContrato'));
        $dadosContrato = $this->contrato->insereContratoAprovacao($this->input->post('idCircuito'));
        
        $idContrato = $dadosContrato['idContrato'];
        
        #echo $this->input->post('idCircuito'); exit();
        $this->contrato->inseriContratoCircuito($idContrato, $this->input->post('idCircuito'), $this->input->post('idInterface'), $this->input->post('idTaxaDigital'));
        # MD. Endereço
        $this->cliente->atualizaPontaB($idContrato, $this->input->post('idTipo'));
        $_POST['id'] = $idBackup;
        
        $idCircuito = $this->circuito->atualizaInterfaceVelocidade();
        
        if($dadosContrato['linhasAfetadas']){
            return array('status'=>'ok', 'acao'=>'cadastrar', 'descricao'=>'Contrato gerado com sucesso!', 'idContrato'=>$idContrato, 'contrato'=>$designacao);
        }else{
            return array('status'=>'erro', 'acao'=>'cadastrar', 'descricao'=>'Erro ao gerar contrato, caso o erro persista comunique o administrador.');
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
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_viab_resp_hist(idViabResp, observacao, idStatusHist) ";
        $obs = utf8_encode(":: INATIVAÇÃO POR ".$tipo)."\n\n";
        $obs .= "EM ".date('d/m/Y H:i:s');
        
        $sql .= "VALUES(".$viabResp[$cont]->id.",'".$obs."',5)";

        $this->db->query($sql);
    }
    
    public function dadosRespTecnicaViab($idContrato){
        #$this->db->where('idContratoAtual', $idContrato);
        $this->db->where("ativou = 'S' AND (idContrato = ".$idContrato." OR idContratoAtual = ".$idContrato.")");
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp')->result();
    }
    /*
    public function atualizaDadosContrato(){
        
        $this->load->model('tcom-contrato/log_contrato_model','contratoLog');
        
        $posts = array('idInterface'=>'idInterface', 'idTaxaDigital'=>'idTaxaDigital', 'qtd_circuitos' =>'qtdCircuitos');
        
        $campoValor[] = "data_atualizacao = '".date('Y-m-d H:i:s')."'";
        $campoValor[] = "cd_usuario_atualizacao = ".$this->session->userdata('cd');
        foreach($posts as $campo => $input){
            
 			$valorFormatado = trim($this->util->removeAcentos($this->input->post($input)));
 			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
		
			$campoValor[] = $campo.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE sistema.tcom_contrato SET ".$camposValores." WHERE id = ".$this->input->post('idContrato').";";
		$this->db->query($sql);
        
        if($this->db->affected_rows()){ 
            
            $this->contratoLog->gravaLogGeracao('UPDATE', $posts, $sql);
            
            return array('status'=>'ok', 'acao'=>'atualizar', 'descricao'=>'Dados do contrato atualizado com sucesso!', 'idContrato'=>$this->input->post('idContrato'));
        }else{
            return array('status'=>'erro', 'acao'=>'atualizar', 'descricao'=>'Erro ao atualizar dados do contrato, caso o erro persista comunique o administrador.');
        }
        
    }
    */
    /*public function atualizaEnderecoCliente(){
        
        $novoEnd = $this->dadosMdEndereco($this->input->post('idViab'));
        
        $backupIdViabResp = $this->input->post('id');
        
        $campos = array_keys($novoEnd[0]);
            
            foreach($campos as $campo){            
                if(!in_array($campo, array('cnpj', 'id'))){
				    $_POST[$campo] = $novoEnd[0][$campo];
                } 
			}
            
        $_POST['id'] = $this->input->post('idCliente');
        
        $status = $this->cliente->atualizaDadosEndereco();
        
        $_POST['telefones'] = $this->telMdEndereco($novoEnd[0]['id']);
       
        $this->cliente->salvaTelefone($this->input->post('idCliente'));
         
        $_POST['id'] = $backupIdViabResp;
        
        if($status){
            return array('status'=>'ok', 'acao'=>'atualizar', 'descricao'=>'Endereço do cliente (Ponta B) atualizado com sucesso!', 'idCliente'=>$this->input->post('idCliente'));
        }else{
            return array('status'=>'erro', 'acao'=>'atualizar', 'descricao'=>'Erro ao atualizar o endereço do cliente (Ponta B), caso o erro persista comunique o administrador.');
        }
        
    }
    
    public function dadosMdEndereco($idViab = false){
        
        if(!$idViab){
            return false;
        }
        
        $this->db->where('idViab', $idViab);
        return $this->db->get('sistema.tcom_viab_md_end')->result_array();
        
    }
    
    public function telMdEndereco($idViabMdEnd = false){
        
        if(!$idViabMdEnd){
            return false;
        }
        
        $this->db->where('idViabMdEnd', $idViabMdEnd);
        return $this->db->get('sistema.tcom_viab_md_end_tel')->result();
        
    }*/
}