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
class Viabilidade_resp_model extends CI_Model{
	
    const tabela = 'tcom_viab_resp';
    
	/**
	 * ViabilidadeRes_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){ 
		parent::__construct();
	}
    
    public function vistoriasPendentes($id = false){
        
        if($id){
            $this->db->where('vistoriado', 'N');
            $this->db->or_where('id', $id); 
        }else{
            $this->db->where('vistoriado', 'N');
        }
        
        $this->db->order_by('controle', 'asc');
        return $this->db->get(BANCO_TELECOM.'.tcom_viab')->result();
    }
    
    public function pegaVistoria($id){
        
        $this->db->where('tcom_viab_resp.id', $id);
        $this->db->join(BANCO_TELECOM.'.tcom_viab', 'tcom_viab_resp.idViab = tcom_viab.id');
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp')->row();
        
    }
    
    public function nodes($id = false, $cd_unidade = false){
        
        if($id){
            $this->db->where('id', $id);
        }
        
        if($cd_unidade){
            $this->db->where('cd_unidade', $cd_unidade);
        }
        
        $this->db->order_by('descricao', 'asc');
        #$this->db->limit(5);
        return $this->db->get(BANCO_TELECOM.'.tcom_node')->result();
    }
    
    public function limpaDadosVistoria(){
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_viab SET vistoriado = 'N' WHERE id = ".$this->input->post('idViab_backup');
        $this->db->query($sql);
        
    }
    
    public function atualizaDadosVistoria(){
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_viab SET vistoriado = 'S' WHERE id = ".$this->input->post('idViab');
        $this->db->query($sql);
        
    }
    
    public function qtdRegistros(){
        
        #$this->db->like('controle', $mes, 'after'); 
        $qtd = $this->db->count_all_results(BANCO_TELECOM.'.tcom_viab_resp');
        return $qtd + 1;
        
    }
    
    
    /**
     * 
     * lista os dados existentes de acordo com os parâmetros informados
     * @param $parametros Condições para filtro
     * @param $mostra_por_pagina Página corrente da paginação
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de ordeção do campo (Crescente ou decrescente)
     * @param $pagina Página da paginação
     * 
     * @return A lista dos dados
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        /*
        $this->db->select("
                        	tcom_viab_resp.id,
                            controle,
                            designacao,
                            tcom_viab_tipo.nome AS tipo,
                        	CASE 
                                WHEN viavel = 'S' 
                                    THEN 'Sim' 
                                WHEN viavel = 'N'
                                    THEN 'Nao'
                            ELSE '' END AS viavel,
                            cabo,
                            cordoalha,
                            CASE 
                                WHEN aprovacao = 'S' 
                                    THEN 'Sim' 
                                WHEN aprovacao = 'N'
                                    THEN 'Nao'
                                WHEN aprovacao = 'C'
                                    THEN 'Canc.'
                            ELSE '' END AS aprovacao,
                            (
                            SELECT 
								tcom_status_hist.nome
							FROM sistema.tcom_viab_resp_hist
							INNER JOIN sistema.tcom_status_hist ON tcom_viab_resp_hist.idStatusHist = tcom_status_hist.id
							WHERE idViabResp = tcom_viab_resp.id
							ORDER BY tcom_viab_resp_hist.data_cadastro DESC
							LIMIT 1
                            ) AS status,
                            prazo_ativacao AS prazo,
                            CASE 
                                WHEN viavel = 'N' OR aprovacao = 'N' OR aprovacao = 'C'
                                    THEN 'OK'
                                WHEN viavel = 'S' AND aprovacao IS NULL
                                    THEN 'PENDENTE'
                                WHEN aprovacao = 'S' AND (
                                                                SELECT 
																	idStatusHist
																FROM sistema.tcom_viab_resp_hist
																INNER JOIN sistema.tcom_status_hist ON tcom_viab_resp_hist.idStatusHist = tcom_status_hist.id
																WHERE idViabResp = tcom_viab_resp.id
																AND final = 'S'
																ORDER BY tcom_viab_resp_hist.data_cadastro DESC
																LIMIT 1
                                                            ) IS NOT NULL
                                    THEN 'OK'
                            	WHEN aprovacao = 'S' AND prazo_ativacao >= CURDATE() AND (
                                                                                            SELECT 
																								idStatusHist
																							FROM sistema.tcom_viab_resp_hist
																							INNER JOIN sistema.tcom_status_hist ON tcom_viab_resp_hist.idStatusHist = tcom_status_hist.id
																							WHERE idViabResp = tcom_viab_resp.id
																							AND final = 'S'
																							ORDER BY tcom_viab_resp_hist.data_cadastro DESC
																							LIMIT 1
                                                                                        ) IS NULL 
                            		THEN 'PENDENTE'
                           	ELSE 'ATRASADO' END AS situacao_atual
                            ");
        */
        /*
        $this->db->select("
                        	tcom_viab_resp.id,
                            controle,
                            designacao,
                            tcom_viab_tipo.nome AS tipo,
                        	CASE 
                                WHEN viavel = 'S' 
                                    THEN 'Sim' 
                                WHEN viavel = 'N'
                                    THEN 'Nao'
                            ELSE '' END AS viavel,
                            cabo,
                            cordoalha,
                            CASE 
                                WHEN aprovacao = 'S' 
                                    THEN 'Sim' 
                                WHEN aprovacao = 'N'
                                    THEN 'Nao'
                                WHEN aprovacao = 'C'
                                    THEN 'Canc.'
                            ELSE '' END AS aprovacao,
                            CASE WHEN tcom_status_hist.nome IS NULL THEN 'Vistoriado' ELSE tcom_status_hist.nome END AS status,
                            prazo_ativacao AS prazo,
                            CASE 
                            	WHEN aprovacao = 'S' AND prazo_ativacao < CURDATE() AND tcom_viab_resp.idStatusHist NOT IN (SELECT id FROM sistema.tcom_status_hist WHERE final = 'S') 
                            		THEN CONCAT(DATEDIFF(CURDATE(),prazo_ativacao),' dia(s) de atraso')
                            ELSE '' END AS dias_atraso,
                            CASE 
                                WHEN viavel = 'N' OR aprovacao = 'N' OR aprovacao = 'C'
                                    THEN 'OK'
                                WHEN viavel = 'S' AND aprovacao IS NULL
                                    THEN 'PENDENTE'
                                WHEN aprovacao = 'S' AND tcom_viab_resp.idStatusHist IN (
                                                                SELECT id FROM sistema.tcom_status_hist WHERE final = 'S'
                                                            )
                                    THEN 'OK'
                            	WHEN aprovacao = 'S' AND prazo_ativacao >= CURDATE() AND tcom_viab_resp.idStatusHist NOT IN (
                                                                                            SELECT id FROM sistema.tcom_status_hist WHERE final = 'S'
                                                                                        )
                            		THEN 'PENDENTE'
                                WHEN tcom_status_hist.nome IS NULL
                                    THEN 'PENDENTE'
                           	ELSE 'ATRASADO' END AS situacao_atual
                            ");
        */
        
        $this->db->select("
                        	tcom_viab_resp.id,
                            controle,
                            n_solicitacao,
                            tcom_contrato.numero,
                            designacao,
                            tcom_viab_tipo.nome AS tipo,
                        	CASE 
                                WHEN viavel = 'S' 
                                    THEN 'Sim' 
                                WHEN viavel = 'N'
                                    THEN 'Nao'
                            ELSE '' END AS viavel,
                            cabo,
                            cordoalha,
                            CASE 
                                WHEN aprovacao = 'S' 
                                    THEN 'Sim' 
                                WHEN aprovacao = 'N'
                                    THEN 'Nao'
                                WHEN aprovacao = 'C'
                                    THEN 'Canc.'
                            ELSE '' END AS aprovacao,
                            CASE WHEN tcom_status_hist.nome IS NULL THEN 'Vistoriado' ELSE tcom_status_hist.nome END AS status,
                            prazo_ativacao AS prazo,
                            CASE 
                            	WHEN aprovacao = 'S' AND prazo_ativacao < CURDATE() AND tcom_viab_resp.idStatusHist NOT IN (SELECT id FROM ".BANCO_TELECOM.".tcom_status_hist WHERE final = 'S') 
                            		THEN CONCAT(DATEDIFF(CURDATE(),prazo_ativacao),' dia(s) de atraso')
                            ELSE '' END AS dias_atraso,
                            CASE 
                                WHEN viavel = 'N' OR aprovacao IS NOT NULL
                                    THEN 'OK'
                                WHEN viavel = 'S' AND aprovacao IS NULL
                                    THEN 'PENDENTE'
                           	ELSE 'ATRASADO' END AS situacao_atual
                            ");
        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('cd_unidade','status','andamento'))){
                        
                        if($res[0] == 'andamento'){
                            
                            $where = $this->pegaWhereAndamento($res[1]);
                            
                            if($where){
                                $this->db->where($where);
                            }
                            
                        }else{
                            $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                        }
                        
                    }else{ 
                        if(in_array($res[0], array('controle'))){ 
                            $this->db->like('tcom_viab.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('numero'))){
                            $this->db->like('tcom_contrato.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('designacao'))){
                            $this->db->like('tcom_circuito.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('n_solicitacao'))){
                            $this->db->like('tcom_viab.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('endereco'))){
                            $this->db->like('tcom_cliente_end.'.$res[0], $res[1]);
                        }else{
                            if(in_array($res[0], array('aprovacao'))){
                                if($res[1] == 'P'){
                                    $this->db->where(self::tabela.".viavel = 'S' AND ".self::tabela.".aprovacao IS NULL");
                                }else{
                                    $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                                }
                            }else{
                                $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                            }
                        }
                    }
                }
            }
        } 
        
        $this->db->join(BANCO_TELECOM.'.tcom_viab', 'tcom_viab.id = tcom_viab_resp.idViab', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_viab_resp.idStatusHist = tcom_status_hist.id', 'left');
        #$this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = tcom_viab_resp.idContrato', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = tcom_contrato.idCircuito', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_cliente', 'tcom_viab.idCliente = tcom_cliente.id', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'tcom_cliente_end.idCliente = tcom_cliente.id', 'left'); 
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();   
        #echo '<pre>'; print_r($this->db->last_query()); exit();      
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('n_solicitacao' => 'Nº pedido', 'numero' => 'Nº contrato');
        $dados['campos'] = array('id', 'n_solicitacao', 'numero', 'designacao', 'tipo', 'viavel', 'prazo', 'aprovacao', 'status');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condições para filtro
     * 
     * @return A quantidade de linhas
     */
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('cd_unidade','status','andamento'))){
                        
                        if($res[0] == 'andamento'){
                            
                            $where = $this->pegaWhereAndamento($res[1]);
                            
                            if($where){
                                $this->db->where($where);
                            }
                            
                        }else{
                            $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                        }
                    }else{ 
                        if(in_array($res[0], array('controle'))){
                            $this->db->like('tcom_viab.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('numero'))){
                            $this->db->like('tcom_contrato.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('designacao'))){
                            $this->db->like('tcom_circuito.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('n_solicitacao'))){
                            $this->db->like('tcom_viab.'.$res[0], $res[1]);
                        }elseif(in_array($res[0], array('endereco'))){
                            $this->db->like('tcom_cliente_end.'.$res[0], $res[1]);
                        }else{
                            if(in_array($res[0], array('aprovacao'))){
                                if($res[1] == 'P'){
                                    $this->db->where(self::tabela.".viavel = 'S' AND ".self::tabela.".aprovacao IS NULL");
                                }else{
                                    $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                                }
                            }else{
                                $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                            }
                        }
                    }
                }
            }
        }
        $this->db->join(BANCO_TELECOM.'.tcom_viab', 'tcom_viab.id = tcom_viab_resp.idViab', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_status_hist', 'tcom_viab_resp.idStatusHist = tcom_status_hist.id', 'left');
        #$this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = tcom_viab_resp.idContrato', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_contrato', 'tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = tcom_contrato.idCircuito', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_cliente', 'tcom_viab.idCliente = tcom_cliente.id', 'left'); 
        $this->db->join(BANCO_TELECOM.'.tcom_cliente_end', 'tcom_cliente_end.idCliente = tcom_cliente.id', 'left'); 
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function pegaWhereAndamento($valor){
        
        switch ($valor) {
            case 'C': # Concluído
                /*$where = "aprovacao = 'S' AND (
                                                                SELECT 
																	idStatusHist
																FROM sistema.tcom_viab_resp_hist
																INNER JOIN sistema.tcom_status_hist ON tcom_viab_resp_hist.idStatusHist = tcom_status_hist.id
																WHERE idViabResp = tcom_viab_resp.id
																AND final = 'S'
																ORDER BY tcom_viab_resp_hist.data_cadastro DESC
																LIMIT 1
                                                            ) IS NOT NULL OR (viavel = 'N' OR aprovacao = 'N' OR aprovacao = 'C')";*/
                                                            
                /*$where = "viavel = 'N' OR aprovacao = 'N' OR aprovacao = 'C' OR tcom_viab_resp.idStatusHist IN (
                                                                SELECT id FROM sistema.tcom_status_hist WHERE final = 'S'
                                                            )";*/
                                                            
                $where = "viavel = 'N' OR aprovacao IS NOT NULL";
                                                            
                break;
            case 'P': # Pendente
                /*$where = "aprovacao = 'S' AND prazo_ativacao >= CURDATE() AND (
                                                                                            SELECT 
																								idStatusHist
																							FROM sistema.tcom_viab_resp_hist
																							INNER JOIN sistema.tcom_status_hist ON tcom_viab_resp_hist.idStatusHist = tcom_status_hist.id
																							WHERE idViabResp = tcom_viab_resp.id
																							AND final = 'S'
																							ORDER BY tcom_viab_resp_hist.data_cadastro DESC
																							LIMIT 1
                                                                                        ) IS NULL OR (viavel = 'S' AND aprovacao IS NULL) OR tcom_status_hist.nome IS NULL";*/
                
                /*$where = "aprovacao = 'S' AND prazo_ativacao >= CURDATE() AND tcom_viab_resp.idStatusHist NOT IN (
                                                                SELECT id FROM sistema.tcom_status_hist WHERE final = 'S'
                                                            ) OR (viavel = 'S' AND aprovacao IS NULL) OR tcom_status_hist.nome IS NULL";*/
                $where = "viavel = 'S' AND aprovacao IS NULL";
                                                                                                        
                break;
            case 'A': # Atrasado
                /*$where = "prazo_ativacao <= CURDATE() AND aprovacao = 'S' AND (
                                                                                            SELECT 
																								idStatusHist
																							FROM sistema.tcom_viab_resp_hist
																							INNER JOIN sistema.tcom_status_hist ON tcom_viab_resp_hist.idStatusHist = tcom_status_hist.id
																							WHERE idViabResp = tcom_viab_resp.id
																							AND final = 'S'
																							ORDER BY tcom_viab_resp_hist.data_cadastro DESC
																							LIMIT 1
                                                                                        ) IS NULL";*/
                
                $where = "prazo_ativacao <= CURDATE() AND aprovacao = 'S' AND tcom_viab_resp.idStatusHist NOT IN (
                                                                SELECT id FROM ".BANCO_TELECOM.".tcom_status_hist WHERE final = 'S'
                                                            )";
                
                break;
            default:
                $where = false;
        }
        
        return $where;
        
    }
    
    public function dadosViabilidadeResp($id = false){
        
        if(!$id){
            return false;
        }
        
        $this->db->where('id', $id);
        $this->db->join('adminti.usuario', 'usuario.cd_usuario = tcom_viab_resp.cd_usuario', 'left'); 
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->row(); 
        
    }
    
    public function dadosViabRespPorViabilidade($id = false){

        if(!$id){
            return false;
        }
        
        $this->db->where('idViab', $id);
        $this->db->join('adminti.usuario', 'usuario.cd_usuario = tcom_viab_resp.cd_usuario','left'); 
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->row(); 
        
    }
    
    public function atualizaAprovacao(){
        
        $this->db->trans_begin();
        
        $contratoGerado = false;
        if($this->input->post('aprovacao') == 'S'){
            if($this->input->post('idTipo') == 1){ # Ativação
                if(!$this->input->post('temContrato')){
                    $status = $this->aprovacaoContrato->insereContrato();
                    if($status){
                        $contratoGerado = true;
                        $idContrato = $status['idContrato'];
                    }
                }else{
                    $status = array('status'=>'alerta','acao'=>'cadastrar', 'descricao'=>'Esse contrato já foi gerado!');
                }
            }
            if($this->input->post('idTipo') == 1 and trim($this->input->post('nome_cliente')) != ''){
                $this->atualizaNomeCliente();
            }
            
            if(in_array($this->input->post('idTipo'),array(2,3,5))){ # UPGRADE, DOWNGRADE, MUDANÇA DE ENDEREÇO
                $status = $this->aprovacaoContrato->insereContratoMudanca();
                if($status){
                    $contratoGerado = true;
                    $idContrato = $this->input->post('temContrato');
                }else{
                    $status = array('status'=>'alerta','acao'=>'cadastrar', 'descricao'=>'Esse contrato já foi gerado!');
                }
            }
            
        }
        
        $posts = array('aprovacao', 'prazo_ativacao');
        
        if($contratoGerado){ // Se aprovação gerou contrato
            $campoValor[] = "gerou_contrato = 'S'";
            $campoValor[] = "idContrato = ".$idContrato;
            
            if(in_array($this->input->post('idTipo'),array(2,3,5))){
                $campoValor[] = "idContratoAtual = ".$status['idContrato'];
            }
        }else{
            $campoValor[] = "idContrato = ".$idContrato;
        }
        
        $campoValor[] = "data_aprovacao = '".date('Y-m-d H:i:s')."'";
        $campoValor[] = "cd_usuario_aprovacao = ".$this->session->userdata('cd');
        
        foreach($posts as $p){
 			$valorFormatado = trim($this->util->removeAcentos($this->input->post($p)));
 			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			$campoValor[] = $p.' = '.$valorFormatado;
		}
        
        $camposValores = implode(', ', $campoValor);
        
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_viab_resp SET ".$camposValores." WHERE id = ".$this->input->post('id').";";
        
		$this->db->query($sql);
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            /*if(in_array($this->input->post('idTipo'),array(2,3,5))){ # Upgrade - Downgrade - Md. Endereço
                $status = array('status'=>'ok', 'acao'=>'atualizar', 'descricao'=>'Dados salvo com sucesso!');
            }
            if(!isset($status)){
                $status = array('status' =>'erro', 'acao'=>'atualizar', 'descricao'=>'');
            }*/
            return array('id' => $this->input->post('id'), 'contratoDados' => $status);
        }
        
    }
    
    public function atualizaNomeCliente(){
        
        $valorFormatado = trim($this->util->removeAcentos($this->input->post('nome_cliente')));
	    $valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_cliente SET titulo = ".$valorFormatado." WHERE id = ".$this->input->post('idCliente').";";
        return $this->db->query($sql);
        
    }
    
    /**
    * DadosBanco_model::unidade()
    * 
    * Função que pega as unidades
    * @return Retorna as unidades
    */
	public function unidade($cd_unidade = null){
        
        if($cd_unidade != null){
            $this->db->where("cd_unidade", $cd_unidade);
        }
        
		return $this->db->get('adminti.unidade')->row();
        
	}
    
    public function dadosCircuitoContrato($idContrato = false){
        
        $this->db->select("tcom_circuito.*, tcom_contrato.numero, tcom_interface.nome AS interface, CONCAT(velocidade,' ', tipo) AS velocidade");
        $this->db->where("tcom_contrato.id", $idContrato);
        
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = tcom_contrato.idCircuito');
        $this->db->join(BANCO_TELECOM.'.tcom_interface', 'tcom_interface.id = tcom_circuito.idInterface','left');
        $this->db->join(BANCO_TELECOM.'.tcom_taxa_digital', 'tcom_taxa_digital.id = tcom_circuito.idTaxaDigital','left');
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato')->row();
    }

}