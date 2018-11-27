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
class Contrato_model extends CI_Model{
	
    const tabela = 'tcom_contrato';
    
	/**
	 * Contrato_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
        
        $this->load->model('tcom-contrato/log_contrato_model','contratoLog');
        
	}
    
    public function contratos($id = false, $status = false){
    
        if($id){
            $this->db->where('id', $id);
        }
        
        if($status){
            $this->db->where('status', $status);
        }
        $this->db->order_by('numero', 'asc');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->result();
    }
    
    public function contratoCircuito($idContrato){
        
        $this->db->select("
                        tcom_circuito.id,
                        designacao
                        ");
        $this->db->where('idContrato', $idContrato);
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = idCircuito');
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato_circuito')->row();
        
    }
    
    public function dadosContratoValores($id){
        
        $this->db->select("
                        tcom_contrato.id,
                        tcom_contrato.numero,
                        unidade.cd_unidade,
                        unidade.nome AS unidade,
                        DATE_FORMAT(tcom_contrato.data_inicio, '%d/%m/%Y') AS data_inicio,
                        DATE_FORMAT(tcom_contrato.data_fim, '%d/%m/%Y') AS data_fim,
                        tcom_contrato.duracao_mes,
                        usuario.cd_usuario,
                        usuario.matricula_usuario,
                        usuario.nome_usuario,
                        usuario.email_usuario
                    ");
        $this->db->where('MD5(tcom_contrato.id)', $id);
        $this->db->join(BANCO_TELECOM.'.tcom_contrato_valor', 'idContrato = tcom_contrato.id', 'left');
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_contrato.cd_unidade');
        $this->db->join('adminti.usuario', 'usuario.cd_usuario = tcom_contrato_valor.cd_usuario', 'left');
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->row();
        
    }
    
    public function estado($cdEstado = false){
        
        if($cdEstado){
            $this->db->where('cd_estado', $cdEstado);
        }
        
        return $this->db->get('adminti.estado')->row();
        
    }
    
    public function equipamentosAssociados($id = false){
        
        if($id){
            $this->db->where('tcom_contrato_equip.idContrato', $id);
        }
        
        $this->db->select('
                            tcom_contrato_equip.id AS idContEquip,
                            tcom_equip_modelo_codigo.id, 
                            tcom_equip_marca.nome AS marca, 
                            tcom_equip_modelo.nome AS modelo,
                            tcom_equip_modelo_codigo.identificacao,
                            tcom_equip_modelo_codigo.codigo
                            ');
        $this->db->join(BANCO_TELECOM.'.tcom_equip_modelo_codigo', 'tcom_equip_modelo_codigo.id = tcom_contrato_equip.idEquipModCod');
        $this->db->join(BANCO_TELECOM.'.tcom_equip_modelo', 'tcom_equip_modelo_codigo.idEquipModelo = tcom_equip_modelo.id');
        $this->db->join(BANCO_TELECOM.'.tcom_equip_marca', 'tcom_equip_marca.id = tcom_equip_modelo.idEquipMarca');
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato_equip')->result();
        
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
        
        $this->db->select("
                        	tcom_contrato.id,
                            numero,
                        	designacao,
                            qtd_circuitos,
                            duracao_mes,
                            DATE_FORMAT(data_inicio, '%d/%m/%Y') AS data_inicio,
                            DATE_FORMAT(data_fim, '%d/%m/%Y') AS data_fim,
                            titulo,
                            unidade.cd_unidade,
                            CONCAT(unidade.permissor,' - ',unidade.nome) AS permissor,
                            anexo,
                            CASE 
                                WHEN tcom_contrato.status = 'A'
                                    THEN 'Ativo'
                                WHEN tcom_contrato.status = 'I'
                                    THEN 'Inativo'
                                WHEN tcom_contrato.status = 'C'
                                    THEN 'Cancelado'
                                WHEN tcom_contrato.status = 'P'
                                    THEN 'Pendente'                                                                        
                            ELSE '' END AS status
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                if($res[1] != ''){
                    if(in_array($res[0], array('status','cd_unidade'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }if(in_array($res[0], array(''))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('designacao'))){
                        $this->db->like('tcom_circuito.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        
        $this->db->join(BANCO_TELECOM.'.tcom_oper', 'tcom_oper.id = tcom_contrato.idOper', 'left'); 
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_contrato.cd_unidade', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = tcom_contrato.idCircuito');
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.'.self::tabela, $mostra_por_pagina, $pagina)->result();           
        $dados['qtd'] = $this->qtdLinhas($parametros);
        $dados['camposLabel'] = array('titulo'=>'ponta A');
        $dados['campos'] = array('id', 'numero', 'designacao', 'titulo', 'permissor', 'status');

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
                    if(in_array($res[0], array('status','cd_unidade'))){
                        $this->db->where(self::tabela.'.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('designacao'))){
                        $this->db->like('tcom_circuito.'.$res[0], $res[1]);
                    }else{
                        $this->db->like(self::tabela.'.'.$res[0], $res[1]);
                    }
                }
            }
        } 
        
        $this->db->join(BANCO_TELECOM.'.tcom_oper', 'tcom_oper.id = tcom_contrato.idOper', 'left'); 
        $this->db->join('adminti.unidade', 'unidade.cd_unidade = tcom_contrato.cd_unidade', 'left');
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = tcom_contrato.idCircuito');
        
        return $this->db->get(BANCO_TELECOM.'.'.self::tabela)->num_rows(); 
        
    }
    
    public function anexos($idContrato){
        
        $this->db->select("
                            id,
                            idContrato,
                            cd_usuario,
                            anexo_label,
                            anexo,
                            DATE_FORMAT(data_cadastro, '%d/%m/%Y %H:%i:%s') AS data_cadastro                            
                        ");
        $this->db->where("idContrato", $idContrato);
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato_anexo')->result();
        
    }
    
    public function apagaAnexo($idContrato,$arquivo){
        
        $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_contrato_anexo WHERE idContrato = ".$idContrato." AND anexo='".$arquivo."'";
        $this->db->query($sql);        
        return $this->db->affected_rows();
        
    }
    
    public function insereContratoAprovacao($idCircuito){
        
        $posts = array(
                        'cd_unidade'=>'idUnidade',
                        'numero'=>'numero', 
                        'idOper'=>'idOperadora', 
                        'idCliente'=>'idCliente', 
                        #'idInterface'=>'idInterface', 
                        #'idTaxaDigital'=>'idTaxaDigital', 
                        'qtd_circuitos'=>'qtdCircuitos'/*,
                        'data_fim'=>'data_fim'*/);
        
        $campo[] = 'data_cadastro';
		$valor[] = "'".date('Y-m-d H:i:s')."'";
        $campo[] = 'cd_usuario_cadastro';
		$valor[] = $this->session->userdata('cd');
        $campo[] = 'idCircuito';
        $valor[] = $idCircuito;
                        
        foreach($posts as $campoTb => $post){
            
			$valorFormatado = trim($this->util->removeAcentos($this->input->post($post)));
			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
			$campo[] = $campoTb;
			$valor[] = $valorFormatado;
            
		}

		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_contrato (".$campos.")\n VALUES(".$valores.");";
        
		$this->db->query($sql);
        $dados['idContrato'] = $this->db->insert_id(); 
        $dados['linhasAfetadas'] = $this->db->affected_rows();
        
        $_POST['idContrato'] = $dados['idContrato'];
        $posts['idContrato'] = 'idContrato';
        $_POST['idCircuito'] = $idCircuito;
        $posts['idCircuito'] = 'idCircuito';
        $this->contratoLog->gravaLogGeracao('INSERT', $posts, $sql);
        
        return $dados;
    }
    
    public function clonaValores($idContratoAntigo, $idContratoNovo = false){
        /*$this->db->select("
                        tcom_circuito.id,
                        designacao
                        ");*/
        $this->db->where('idContrato', $idContratoAntigo);
        $dados = $this->db->get(BANCO_TELECOM.'.tcom_contrato_valor')->row();
        if($dados){
            unset($dados->id);
            unset($dados->data_cadastro);
            $dados->idContrato = $idContratoNovo;
            $dados->cd_usuario = $this->session->userdata('cd');
            
            foreach($dados as $campo => $valor){
                $campos[] = $campo;
                $valores[] = $this->util->formaValorBanco($valor);
            }
            
            $sql = "INSERT ".BANCO_TELECOM.".tcom_contrato_valor(".implode(",",$campos).") VALUES(".implode(",",$valores).")";
            
            $this->db->query($sql);
        }

    }
    
    public function inseriContratoCircuito($idContrato, $idCircuito, $idInterface, $idVelocidade){
        
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_contrato_circuito(
                                                                    idContrato, 
                                                                    idCircuito, 
                                                                    idInterface, 
                                                                    idTaxaDigital, 
                                                                    cd_usuario) ";
        $sql .= "\n VALUES(".$idContrato.",";
        $sql .= $idCircuito.",";
        $sql .= $idInterface.",";
        $sql .= $idVelocidade.",";
        $sql .= $this->session->userdata('cd').");";
        $this->db->query($sql);
        
    }
    
    public function inativaContratoAnterior($idContrato){
        
        switch($this->input->post('idTipo')){
            case 2: 
                $tipoAcao = utf8_encode('Desativação por upgrade');
                break;
            case 3:
                $tipoAcao = utf8_encode('Desativação por Downgrade');
                break;
            case 5:
                $tipoAcao = utf8_encode('Desativação por outras mudanças');
                break;
            case 6:
                $tipoAcao = utf8_encode('Desativação por mudança de endereço');
                break;
        }
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato ";
        $sql .= "SET status = 'I', data_canc = ADDDATE(CURDATE(), INTERVAL -1 DAY), acaoObs = '".$tipoAcao."', cd_usuario_atualizacao = '".$this->session->userdata('cd')."' WHERE id = ".$idContrato;
        $this->db->query($sql);
        
        $_POST['id'] = $idContrato;
        $_POST['status'] = 'I';
        
        $this->contratoLog->gravaLogStatus('UPDATE_STATUS_INATIVO_SISTEMA', $sql);
        
    }
    
    public function execAtivacao(){
        
        $msg = utf8_encode('Ativação');
        
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato SET data_inicio = CURDATE(), acaoObs = '".$msg."', cd_usuario_atualizacao = '".$this->session->userdata('cd')."', status = 'A' WHERE id = ".$this->input->post('idContrato');
        $this->db->query($sql);
        
        $logEquip = $this->salvarEquipamentoAtivacao();
        
        $this->contratoLog->gravaLogExecAtiv('ATIVACAO_ATIVA_EXEC' ,$sql, $logEquip);
        
    }
    
    public function execAtivacaoUpgradeDowngrade($queryCircuito){
        
        $posts = array('qtd_circuitos' =>'qtdCircuitos');
        
        $campoValor[] = "data_atualizacao = '".date('Y-m-d H:i:s')."'";
        $campoValor[] = "cd_usuario_atualizacao = ".$this->session->userdata('cd');
        foreach($posts as $campo => $input){
            
 			$valorFormatado = trim($this->util->removeAcentos($this->input->post($input)));
 			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
		
			$campoValor[] = $campo.' = '.$valorFormatado;
                
                
		}
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato SET ".$camposValores." WHERE id = ".$this->input->post('idContrato').";";
		$this->db->query($sql);
        
        $queryEquip = $this->salvarEquipamentoAtivacao();
        
        $this->contratoLog->gravaLogExecAtiv('ATIVACAO_UPDATE_EXEC', $sql, $queryEquip, $queryCircuito);
        
    }
    
    public function salvarEquipamentoAtivacao(){
        
        $dadosLog = '';
        if($this->input->post('equip')){
            foreach(array_unique($this->input->post('equip')) as $idEquipModCod){
                $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_contrato_equip(idContrato, idEquipModCod, cd_usuario_cadastro) VALUES(".$this->input->post('idContrato').",".$idEquipModCod.",".$this->session->userdata('cd').")";
                $this->db->query($sql);
                $dadosLog .= $sql."\n";
            }
        }
        
        return $dadosLog;
        
    }
    
    public function listaHistoricos($id){
        
        $this->db->select("tcom_viab_resp.id,
                            tcom_viab_tipo.nome,
                            CASE 
                            	WHEN (
                                        SELECT 
                                            COUNT(*) 
                                        FROM ".BANCO_TELECOM.".tcom_viab_resp_hist 
                                        WHERE idViabResp = tcom_viab_resp.id) > 0
                            		THEN 
                            			(
                            			SELECT 
                            				tcom_status_hist.nome
                            			FROM ".BANCO_TELECOM.".tcom_viab_resp_hist 
                            			INNER JOIN ".BANCO_TELECOM.".tcom_status_hist ON tcom_status_hist.id = idStatusHist
                            			WHERE idViabResp = tcom_viab_resp.id
                            			ORDER BY tcom_viab_resp_hist.data_cadastro DESC LIMIT 1
                            			) 
                            ELSE '-' END AS status,
                            DATE_FORMAT(tcom_viab_resp.data_cadastro,'%d/%m/%Y') AS data_cadastro");
        
        $this->db->join(BANCO_TELECOM.'.tcom_viab_tipo', 'tcom_viab_tipo.id = tcom_viab.idViabTipo');
        $this->db->join(BANCO_TELECOM.'.tcom_viab_resp', 'tcom_viab_resp.idViab = tcom_viab.id');
        
        #$this->db->where("tcom_viab_resp.idContrato", $id);
        $this->db->where("CASE WHEN idContratoAtual IS NOT NULL THEN idContratoAtual = ".$id." ELSE tcom_viab_resp.idContrato =  ".$id." END");
        
        $this->db->order_by('tcom_viab_resp.data_cadastro', 'asc');
            
        return $this->db->get(BANCO_TELECOM.'.tcom_viab')->result();  
        
    }
    
    public function valoresContrato($id){
        
        $this->db->select("
                            tcom_contrato.id,
                            idContrato,
                            FORMAT(valor,2,'de_DE') AS valor,
                            DATE_FORMAT(data_pri_fatura,'%m/%Y') AS data_pri_fatura,
                            DATE_FORMAT(data_ult_fatura,'%d/%m/%Y') AS data_ult_fatura,
                            DATE_FORMAT(proximo_reajuste,'%d/%m/%Y') AS proximo_reajuste,
                            FORMAT(mens_contratada_sem_imposto,2,'de_DE') AS mens_contratada_sem_imposto,
                            FORMAT(mens_atual_sem_imposto,2,'de_DE') AS mens_atual_sem_imposto,
                            FORMAT(mens_atual_com_imposto,2,'de_DE') AS mens_atual_com_imposto,
                            FORMAT(taxa_inst_com_imposto,2,'de_DE') AS taxa_inst_com_imposto,
                            FORMAT(taxa_inst_sem_imposto,2,'de_DE') AS taxa_inst_sem_imposto,
                            FORMAT(mao_obra_empreiteira,2,'de_DE') AS mao_obra_empreiteira,
                            FORMAT(aquisicao_equipamento,2,'de_DE') AS aquisicao_equipamento,
                            FORMAT(sub_total,2,'de_DE') AS sub_total,
                            FORMAT(receita_total_sem_imposto,2,'de_DE') AS receita_total_sem_imposto,
                            FORMAT(receita_total_com_imposto,2,'de_DE') AS receita_total_com_imposto,
                            FORMAT(primeira_mensalidade,2,'de_DE') AS primeira_mensalidade,
                            idRegraReajuste,
                            mes_reajuste,
                            faturado_por,
                            multa_porc,
                            multa_nao_dev_equip,
                            dia_vencimento,
                            dia_pro_rata_pri_mes,
                            idIndiceReajuste,
                            isencao_icms,
                            faturado_siga,
                            (SELECT pai FROM ".BANCO_TELECOM.".tcom_oper WHERE tcom_oper.id = tcom_contrato_valor.idOper) AS idGrupo,
                            tcom_contrato_valor.idOper                            
                        ");
        $this->db->where("tcom_contrato.id", $id);
        $this->db->join(BANCO_TELECOM.'.tcom_contrato_valor', 'idContrato = tcom_contrato.id', 'left');
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato')->row(); 
        
    }
    
    public function salvaValores(){
        
        $this->db->trans_begin();
        $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_contrato_valor WHERE idContrato = ".$this->input->post('valor_id');
        $this->db->query($sql);
        
        $campo = array();
		$valor = array();
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        $campo[] = 'idContrato';
        $valor[] = $this->input->post('valor_id');
		foreach($_POST as $c => $v){
			
            if(!in_array($c, array('nome_contrato_valor','valor_id', 'email', 'grupo'))){
                if($this->input->post($c) != ''){
                    
                        if(in_array($c, array('data_pri_fatura'))){
                            $_POST[$c] = '01/'.$_POST[$c];
                        }
                        
                        $valorFormatado = $this->util->formaValorBanco( addslashes ( str_replace(",", ".", str_replace(".", "", trim($this->input->post($c)))) ));
        			
        			$campo[] = $c;
        			$valor[] = $valorFormatado;
                }
            }
            
		}
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);

		$sql = "INSERT INTO ".BANCO_TELECOM.".tcom_contrato_valor (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato_valor (".$campos.")\n VALUES(".$valores.");";
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
    
    public function dadosViabResp($idContrato){
        
        $this->db->where("idContrato", $idContrato);
        return $this->db->get(BANCO_TELECOM.'.tcom_viab_resp')->row(); 
        
    }
    
    public function deleta(){
        
        $dados = $this->contratos($this->input->post('apg_id'));
        
        $qtdCircuitoHist = $this->qtdCircuitoHistorico($dados[0]->idCircuito);
        
        $this->db->trans_begin();
        
        $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_contrato WHERE id = ".$this->input->post('apg_id');
        $sqlContrato = $sql;
        $this->db->query($sql);
        
        $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_contrato_circuito WHERE idContrato = ".$this->input->post('apg_id')." AND idCircuito = ".$dados[0]->idCircuito;
        $sqlCircuito = $sql;
        $this->db->query($sql);
        
        if($qtdCircuitoHist == 1){ 
            $sql = "DELETE FROM ".BANCO_TELECOM.".tcom_circuito WHERE id = ".$dados[0]->idCircuito;
            $sqlCircuito .= "\n".$sql;
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
            $this->contratoLog->gravaLogDeleta('DELETE', $sqlContrato, $sqlCircuito);
            return true;
        }
        
    }
    
    public function qtdCircuitoHistorico($idCircuito){
        
        $this->db->where('idCircuito', $idCircuito);
        $this->db->from(BANCO_TELECOM.'.tcom_contrato_circuito');
        return $this->db->count_all_results();
        
    }
    
    public function usuarioEnviaEmail($emailEnvia, $cd_unidade = false, $tipoRetorno = 'objeto'){

        $sql = "SELECT 
                		DISTINCT
                        usuario.cd_usuario,
                        login_usuario,
                		nome_usuario, 
                		email_usuario
                FROM adminti.usuario
                INNER JOIN sistema.email_recebe ON email_recebe.cd_usuario = usuario.cd_usuario
                WHERE 
                	status_usuario = 'A'
                	AND email_usuario IS NOT NULL
               		AND email_recebe.idEmailEnvia = ".$emailEnvia."
               	UNION
                SELECT 
                	DISTINCT
                        '' AS cd_usuario,
                		'' AS login_usuario,
                		'' AS nome_usuario,
                		email AS email_usuario
                FROM sistema.email_grupo
                INNER JOIN sistema.email_grupo_recebe ON email_grupo_recebe.idEmailGrupo = email_grupo.id
                WHERE 
                	email_grupo.status = 'A'
                	AND email_grupo_recebe.idEmailEnvia = ".$emailEnvia."
                ";
        if($tipoRetorno == 'objeto'){            
            return $this->db->query($sql)->result();
        }else{
            return $this->db->query($sql)->result_array();
        }
    }
    
    public function gravaPrimeiroEnvioAnalise($idContrato, $usuarios){
        
        $existe = $this->existeAnaliseEmail($idContrato);
        
        if(!$existe){
            foreach($usuarios as $usu){
                $sql = "INSERT INTO telecom.tcom_analise_financ(idContrato,cd_usuario,cd_usuario_disparador) ";
                $sql .= "VALUES(".$idContrato.",".$usu->cd_usuario.",".$this->session->userdata('cd').")";
                $this->db->query($sql);
            }
        }
         
    }
    
    public function existeAnaliseEmail($idContrato){
        
        $this->db->where('idContrato', $idContrato);
        $this->db->from(BANCO_TELECOM.'.tcom_analise_financ');
        return $this->db->count_all_results();
        
    }
    
    public function alterarVigencia(){
        $grava = false;
        $dataInicio = $this->util->formataData($this->input->post('data_inicio'), 'USA');
        $dataFim = $this->util->formataData($this->input->post('data_fim'), 'USA');
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato SET ";
        if($this->input->post('duracao_mes')!= ''){
            
            $sql .= " data_inicio='".$dataInicio."' ";
            
            if($this->input->post('calcular_hoje')){
                $grava = true;
                $dataInicio = date('Y-m-d');
            }else{
                #if($this->input->post('duracao_mes') != $this->input->post('backup_mes')){      
                    $grava = true;
                #}
            }
            $_POST['data_fim'] = date('Y-m-d', strtotime("+".$this->input->post('duracao_mes')." months",strtotime($dataInicio)));
            $sql .=", duracao_mes=".$this->input->post('duracao_mes')." ";
            $sql .= ", data_fim='".$this->input->post('data_fim')."' ";
            
        }else{
            if($this->input->post('data_inicio') and $this->input->post('data_fim')){
                $grava = true;
                $sql .= " data_inicio='".$dataInicio."', data_fim='".$dataFim."', duracao_mes=null ";
                $_POST['duracao_mes'] = '0';
            }
        }  
        
        if($grava){
            $sql .= ", cd_usuario_atualizacao = ".$this->session->userdata('cd')." ";
            $sql .= "WHERE id = ".$this->input->post('id');
            $status = $this->db->query($sql);
            $this->contratoLog->gravaLogVigencia('UPDATE_VIGENCIA', $sql);
            return $status;
        }else{
            return true;
        }      
        
    }
    
    public function alterarStatus(){
        $grava = false;
        
        #if($this->input->post('status') != $this->input->post('backup_status')){ 
            
            $dataSolCanc = 'null';
            $dataCanc = 'null';
            if(in_array($this->input->post('status'), array('C','I'))){
                if($this->input->post('data_solic_canc')){
                    $dataSolCanc = "'".$this->util->formataData($this->input->post('data_solic_canc'), 'USA')."'";
                }
                if($this->input->post('data_canc')){
                    $dataCanc = "'".$this->util->formataData($this->input->post('data_canc'), 'USA')."'";
                    $this->alteraDataUltimoFaturamento();
                }else{
                    $dataCanc = "'".date('Y-m-d')."'"; 
                    $dataSolCanc = "'".date('Y-m-d')."'"; 
                }
                
            }
        
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato SET ";
            $sql .= "status = '".$this->input->post('status')."' ";
            $sql .= ", data_solic_canc = ".$dataSolCanc;
            $sql .= ", data_canc = ".$dataCanc;
            $sql .= ", cd_usuario_atualizacao = ".$this->session->userdata('cd')." ";
            $sql .= "WHERE id = ".$this->input->post('id');
            
            if($this->input->post('status') == 'I'){
                $_POST['idViab'] = 'null';
                $_POST['idViabResp'] = 'null';
                $_POST['idTipo'] = 6;
                $this->ordem->geraOrdem($this->input->post('id'));
            }
            
            $status = $this->db->query($sql);
            $this->contratoLog->gravaLogStatus('UPDATE_STATUS', $sql);
            
        return $status;
        
        #}else{
            #return true;
        #}
        
    }
    
    public function alteraDataUltimoFaturamento(){
        
        $data = $this->util->formataData($this->input->post('data_canc'), 'USA'); 
        #$dataDb = ($data)? "'".$data."'" : "'".date("Y-m-d")."'";        
        $dataDb = ($data)? $data : date("Y-m-d");
        $dataDb = "'".date('Y-m-d', strtotime("+1 months",strtotime($data)))."'"; 
        
        $this->db->where('idContrato', $this->input->post('id'));
        $this->db->from(BANCO_TELECOM.'.tcom_contrato_valor');
        $count = $this->db->count_all_results();

        if($count > 0){
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato_valor SET ";
            $sql .= "data_ult_fatura = ".$dataDb.", cd_usuario=".$this->session->userdata('cd');
            $sql .= " WHERE idContrato = ".$this->input->post('id');
            $this->db->query($sql);
            $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato_valor(idContrato, data_ult_fatura,cd_usuario) VALUES(".$this->input->post('id').",".$dataDb.",".$this->session->userdata('cd').")";
            $this->db->query($sql);
        }else{
            $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_contrato_valor(idContrato, data_ult_fatura,cd_usuario) VALUES(".$this->input->post('id').",".$dataDb.",".$this->session->userdata('cd').")";
            $this->db->query($sql);
            $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_log_contrato_valor(idContrato, data_ult_fatura,cd_usuario) VALUES(".$this->input->post('id').",".$dataDb.",".$this->session->userdata('cd').")";
            $this->db->query($sql);
        }
    }
    
    public function alterarServicos(){
        
        $servicoTipo = 'null';
        $servico = 'null';
        $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato SET ";
        if($this->input->post('idServicoTipo')){
            $servicoTipo = $this->input->post('idServicoTipo');
        }
        $sql .= " idServicoTipo = ".$servicoTipo;
        if($this->input->post('idServico')){
            $servico = $this->input->post('idServico');
        }
        $sql .= ", idServico = ".$servico;
        $sql .= ", cd_usuario_atualizacao = ".$this->session->userdata('cd')." ";
        $sql .= "WHERE id = ".$this->input->post('id');
        
        $status = $this->db->query($sql);
        $this->contratoLog->gravaLogServicos('UPDATE_SERVICO', $sql);
            
        return $status;
        
    }
    
    public function dadosContratoCircuito($idContrato, $idCircuito){
        
        $this->db->select("
                        idCircuito, 
                        idContrato, 
                        idCliente, 
                        tcom_cliente.titulo AS tituloPontaB,
                        designacao,
                        tcom_contrato_circuito.idTaxaDigital, 
                        tcom_contrato_circuito.idInterface, 
                        cep, 
                        endereco, 
                        numero, 
                        bairro, 
                        cidade, 
                        cd_estado, 
                        complemento, 
                        telefones
                        ");
        
        $this->db->where("idContrato", $idContrato);
        if($idCircuito){
            $this->db->where("idCircuito", $idCircuito);
        }
        
        $this->db->join(BANCO_TELECOM.'.tcom_cliente', 'tcom_cliente.id = idCliente');
        $this->db->join(BANCO_TELECOM.'.tcom_circuito', 'tcom_circuito.id = idCircuito');
        return $this->db->get(BANCO_TELECOM.'.tcom_contrato_circuito')->row(); 
        
    }
    
    public function alterarNumero(){
        
        if($this->input->post('numero')){
            
            $numero = strtoupper(trim($this->util->removeAcentos($this->input->post('numero'))));
            
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato SET ";
            $sql .= "numero = '".$numero."' ";
            $sql .= ", cd_usuario_atualizacao = ".$this->session->userdata('cd')." ";
            $sql .= "WHERE id = ".$this->input->post('id');
            
            $status = $this->db->query($sql);
            $this->contratoLog->gravaLogNumero('UPDATE_NUMERO', $sql);
        
            return $status;
        
        }else{
            return true;
        }
        
    }
    
    public function alteraPontabEndereco(){
        
        $_POST['numero'] = $_POST['numero_end_cliente'];
        $posts = array('cep', 'cd_estado', 'cidade', 'endereco', 'numero', 'bairro', 'complemento');
        
        foreach($posts as $p){
            
            if(isset($_POST[$p])){
            
    			$valorFormatado = trim($this->util->removeAcentos( addslashes ( $this->input->post($p) ) ));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    		
    			$campoValor[] = $p.' = '.$valorFormatado;
            
            }
                
		}
        
        if($this->input->post('telefone')){
            $campoValor[] = "telefones = '".implode(' / ', $this->input->post('telefone'))."'";
        }
        
        $camposValores = implode(', ', $campoValor);
     
		$sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato_circuito SET ".$camposValores." WHERE idCircuito = ".$this->input->post('idCircuito')." AND idContrato = ".$this->input->post('idContrato').";";
		
        $this->db->query($sql);
        $_POST['status'] = 'A';
        $this->contratoLog->gravaLogCircuito('UPDATE_ENDERECO_PONTA_B', $sql);
        
        return $this->db->affected_rows();
        
    }
    
    public function indiceReajuste(){
        
        return $this->db->get(BANCO_TELECOM.'.tcom_indice_reajuste')->result();  
        
    }
    
    public function servicosTipos(){
        
        return $this->db->get(BANCO_TELECOM.'.tcom_servico_tipo')->result();  
        
    }
    
    public function servicos($idServico = false){
        
        $servicoCondicao = ($idServico)? 'OR tcom_servico.id='.$idServico: '';
        
        $sql = "SELECT 
            	DISTINCT tcom_servico.*
            FROM telecom.tcom_servico
            JOIN telecom.tcom_imposto ON tcom_imposto.idServico = tcom_servico.id OR tcom_servico.id = 6 ".$servicoCondicao."
            WHERE tcom_servico.id != 11
            ORDER BY nome asc";   
            
        return $this->db->query($sql)->result();    
        
    }
    
    public function servicosAtivos($idServico = false){
        
        $servicoCondicao = ($idServico)? 'OR tcom_servico.id='.$idServico: '';
        
        $sql = "SELECT 
            	DISTINCT tcom_servico.*
            FROM telecom.tcom_servico
            JOIN telecom.tcom_imposto ON tcom_imposto.idServico = tcom_servico.id OR tcom_servico.id = 6 ".$servicoCondicao."
            WHERE tcom_servico.id != 11
            AND tcom_servico.status = 'A'
            ORDER BY nome asc";   
        return $this->db->query($sql)->result();     
        
    }

}