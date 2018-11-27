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
class delin_model extends faturamento_model{
	
    const tabela = 'tcom_imposto';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function dadosDelin($arquivo, $link){
        
        $this->db->trans_begin();
        
        $this->gravaDelin($arquivo, $link);
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        $idSelecionados = $this->input->post('idSelecionados');
        $semDefinicao = (substr($idSelecionados,0,2) == '0,')? 'OR paiOper.id IS NULL': '';
        
        $sql = "SELECT 
                * 
                FROM (
                SELECT
                    CASE WHEN paiOper.titulo IS NULL THEN 'NAO DEFINIDO' ELSE paiOper.titulo END AS 'Grupo',
                    tcom_oper_cobr.cnpj AS CNPJ,
                    tcom_circuito.designacao AS 'Designacao dos Servicos',
                    data_inicio AS 'Data Ativacao',
                    data_fim AS 'Data Termino',
                    tcom_contrato_valor.valor AS 'Valor Contrato',
                    tcom_contrato_valor.mens_contratada_sem_imposto AS 'Mensalidade contratada',
                    tcom_contrato_valor.mens_atual_sem_imposto AS 'Mensalidade Atual (S/I)',
                    ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) AS 'Mensalidade Atual (C/I)',
                    CASE 
                        WHEN tcom_contrato_valor.gerouInst = 'N'
                            THEN ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.taxa_inst_sem_imposto,isencao_icms,CASE WHEN tcom_contrato.idServico = 6 THEN 6 ELSE 11 END,tcom_contrato.cd_unidade,faturado_por,filhaOper.estatal)
                    ELSE '' END AS 'Taxa Inst (C/I)',
                    CASE 
                    	WHEN DATEDIFF(data_canc, '".$data."-01') > 0 AND SUBSTR(data_canc,1,7) = '".$data."' # TRATAMENTO DATA DE CANCELAMENTO
                    		THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(data_canc, '".$data."-01')+1),2)
                        WHEN DATEDIFF(tcom_contrato.data_fim, '".$data."-01') > 0 AND SUBSTR(tcom_contrato.data_fim,1,7) = '".$data."' # TRATAMENTO DATA DE TERMINO
                    		THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(tcom_contrato.data_fim, '".$data."-01')+1),2)
			            WHEN DATEDIFF(LAST_DAY('".$data."-01'), tcom_contrato.data_inicio) > 0 AND SUBSTR(tcom_contrato.data_inicio,1,7) = '".$data."' # TRATAMENTO DATA DE ATIVAÇÃO
			                THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(LAST_DAY('".$data."-01'), tcom_contrato.data_inicio)+1),2)
                    ELSE ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) END AS valor_cobrado,
                    CASE WHEN SUBSTR(data_canc,1,7) = '".$data."' THEN DATEDIFF(data_canc, '".$data."-01')+1 ELSE NULL END AS dia_cobrados_por_canc,
                    tcom_servico_tipo.nome AS 'Tipo Servico',
                    (SELECT nome FROM adminti.unidade WHERE cd_unidade = faturado_por) AS 'Faturado Por'
                FROM ".BANCO_TELECOM.".tcom_contrato
                LEFT JOIN ".BANCO_TELECOM.".tcom_contrato_valor ON tcom_contrato_valor.idContrato = tcom_contrato.id
                #LEFT JOIN ".BANCO_TELECOM.".tcom_viab_resp ON tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END
                LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS filhaOper ON filhaOper.id = tcom_contrato_valor.idOper
                LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS paiOper ON paiOper.id = filhaOper.pai
                LEFT JOIN ".BANCO_TELECOM.".tcom_oper_cobr ON tcom_oper_cobr.idOper = filhaOper.id
                LEFT JOIN ".BANCO_TELECOM.".tcom_circuito ON tcom_circuito.id = idCircuito
                LEFT JOIN ".BANCO_TELECOM.".tcom_contrato_circuito ON tcom_contrato_circuito.idContrato = tcom_contrato.id AND tcom_contrato_circuito.idCircuito = tcom_contrato.idCircuito
                LEFT JOIN ".BANCO_TELECOM.".tcom_interface ON tcom_interface.id = tcom_contrato_circuito.idInterface
                LEFT JOIN ".BANCO_TELECOM.".tcom_taxa_digital ON tcom_taxa_digital.id = tcom_contrato_circuito.idTaxaDigital
                LEFT JOIN ".BANCO_TELECOM.".tcom_cliente ON tcom_cliente.id = tcom_contrato.idCliente
                LEFT JOIN adminti.unidade ON tcom_contrato.cd_unidade = unidade.cd_unidade
                LEFT JOIN ".BANCO_TELECOM.".tcom_servico ON tcom_servico.id = idServico
                LEFT JOIN ".BANCO_TELECOM.".tcom_servico_tipo ON tcom_servico_tipo.id = idServicoTipo
                LEFT JOIN ".BANCO_TELECOM.".tcom_cliente_tipo ON tcom_cliente_tipo.id = tcom_cliente.idClienteTipo
                WHERE 1=1
                AND faturado_siga = 'NAO'
                AND (
                        (SUBSTR(data_inicio,1,7) <= '".$data."' AND SUBSTR(data_fim,1,7) >= '".$data."'  AND tcom_contrato.status = 'A')
                    OR 
                        (SUBSTR(data_canc,1,7) >= '".$data."' AND tcom_contrato.status = 'I')
                    )
                AND (paiOper.id IN (".$idSelecionados.") ".$semDefinicao.")
                ) AS res
                ORDER BY grupo ASC";
                
        $resultado = $this->db->query($sql)->result_array();
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
    
            return $resultado;
        }
    }
    
    public function gravaDelin($arquivo, $link){
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        $idSelecionados = $this->input->post('idSelecionados');
        $semDefinicao = (substr($idSelecionados,0,2) == '0,')? 'OR paiOper.id IS NULL': '';
        
        $sql = "SELECT 
                * 
                FROM (
                    SELECT
                    	tcom_contrato_circuito.idContrato,
                    	tcom_contrato_circuito.idCircuito,
                    	paiOper.id AS idOperCobrPai,
                    	filhaOper.id AS idOperCobrFilha,
                        tcom_contrato_valor.gerouInst,
                        ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.taxa_inst_sem_imposto,isencao_icms,CASE WHEN tcom_contrato.idServico = 6 THEN 6 ELSE 11 END,tcom_contrato.cd_unidade,faturado_por,filhaOper.estatal) AS taxa_inst_com_imposto,
                        ADDDATE(tcom_contrato.data_inicio, INTERVAL 1 MONTH) AS data_inicio,
                    	CASE 
                    		WHEN DATEDIFF(data_canc, '".$data."-01') > 0 AND SUBSTR(data_canc,1,7) = '".$data."' # TRATAMENTO DATA DE CANCELAMENTO
                    			THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(data_canc, '".$data."-01')+1),2)
                    			WHEN DATEDIFF(tcom_contrato.data_fim, '".$data."-01') > 0 AND SUBSTR(tcom_contrato.data_fim,1,7) = '".$data."' # TRATAMENTO DATA DE TERMINO
                    			THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(tcom_contrato.data_fim, '".$data."-01')+1),2)
                    					WHEN DATEDIFF(LAST_DAY('".$data."-01'), tcom_contrato.data_inicio) > 0 AND SUBSTR(tcom_contrato.data_inicio,1,7) = '".$data."' # TRATAMENTO DATA DE ATIVAÇÃO
                    				THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(LAST_DAY('".$data."-01'), tcom_contrato.data_inicio)+1),2)
                    	ELSE ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) END AS valor_cobrado,
                        '' AS data_vencimento,
                        ADDDATE(tcom_contrato.data_inicio, INTERVAL 1 MONTH) AS data_venc_inst
                    FROM ".BANCO_TELECOM.".tcom_contrato
                    LEFT JOIN ".BANCO_TELECOM.".tcom_contrato_valor ON tcom_contrato_valor.idContrato = tcom_contrato.id
                    #LEFT JOIN ".BANCO_TELECOM.".tcom_viab_resp ON tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END
                    LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS filhaOper ON filhaOper.id = tcom_contrato_valor.idOper
                    LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS paiOper ON paiOper.id = filhaOper.pai
                    LEFT JOIN ".BANCO_TELECOM.".tcom_oper_cobr ON tcom_oper_cobr.idOper = filhaOper.id
                    LEFT JOIN ".BANCO_TELECOM.".tcom_contrato_circuito ON tcom_contrato_circuito.idContrato = tcom_contrato.id AND tcom_contrato_circuito.idCircuito = tcom_contrato.idCircuito
                    WHERE 1=1
                    AND faturado_siga = 'NAO'
                    AND (
                        (SUBSTR(data_inicio,1,7) <= '".$data."' AND SUBSTR(data_fim,1,7) >= '".$data."'  AND tcom_contrato.status = 'A')
                    OR 
                        (SUBSTR(data_canc,1,7) >= '".$data."' AND tcom_contrato.status = 'I')
                    )
                    AND (paiOper.id IN (".$idSelecionados.") ".$semDefinicao.")
                    #AND tcom_contrato_circuito.idContrato IN (1598, 1383)
                ) AS res
                ORDER BY idOperCobrPai ASC";
        
        $resultado = $this->db->query($sql)->result();
        
        $this->alteraStatusDelin('GERADO','ALTERADO');
        
        foreach($resultado as $res){
            
            $campo[] = 'competencia';
            $valor[] = "'".implode('-',array_reverse(explode('/',$this->input->post('data'))))."-01'";
            $campo[] = 'arquivo';
            $valor[] = "'".$arquivo."'";
            $campo[] = 'link';
            $valor[] = "'".$link."'";
            $campo[] = 'idContrato';
            $valor[] = $this->util->formaValorBanco($res->idContrato);
            $campo[] = 'idOperCobrPai';
            $valor[] = $this->util->formaValorBanco($res->idOperCobrPai);
            $campo[] = 'idOperCobrFilha';
            $valor[] = $this->util->formaValorBanco($res->idOperCobrFilha);
            $campo[] = 'valor_cobrado';
            $valor[] = $this->util->formaValorBanco($res->valor_cobrado);
            $campo[] = 'data_venc';
            $valor[] = $this->util->formaValorBanco($res->data_vencimento);
            $campo[] = 'tipo';
            $valor[] = "'MENS'";
            $campo[] = 'cd_usuario';
            $valor[] = $this->session->userdata('cd');
            
            $campos = implode(', ', $campo);
            $valores = str_replace('%','',implode(', ', $valor));
            $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_delin (".$campos.")\n VALUES(".$valores.");";
            $this->db->query($sql);
            $campo = array();
            $valor = array();
            
            if($res->gerouInst == 'N'){
                $this->gravaInst($res, $arquivo, $link);
            }
            
        }
    
    }
    
    public function gravaInst($res, $arquivo, $link){
        
        $campo[] = 'competencia';
        $valor[] = "'".implode('-',array_reverse(explode('/',$this->input->post('data'))))."-01'";
        $campo[] = 'arquivo';
        $valor[] = "'".$arquivo."'";
        $campo[] = 'link';
        $valor[] = "'".$link."'";
        $campo[] = 'idContrato';
        $valor[] = $this->util->formaValorBanco($res->idContrato);
        $campo[] = 'idOperCobrPai';
        $valor[] = $this->util->formaValorBanco($res->idOperCobrPai);
        $campo[] = 'idOperCobrFilha';
        $valor[] = $this->util->formaValorBanco($res->idOperCobrFilha);
        $campo[] = 'valor_cobrado';
        $valor[] = $this->util->formaValorBanco($res->taxa_inst_com_imposto);
        $campo[] = 'data_venc';
        $valor[] = $this->util->formaValorBanco($res->data_venc_inst);
        $campo[] = 'tipo';
        $valor[] = "'INST'";
        $campo[] = 'cd_usuario';
        $valor[] = $this->session->userdata('cd');
        
        $campos = implode(', ', $campo);
        $valores = str_replace('%','',implode(', ', $valor));
        $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_delin (".$campos.")\n VALUES(".$valores.");";
        $this->db->query($sql);
        $campo = array();
            $valor = array();
    }
    
    public function alteraStatusDelin($statusAntigo, $statusNovo){
        
        if($this->input->post('idGerados')){
            
            if($this->input->post('data')){
                $data = "AND competencia = '".implode('-',array_reverse(explode('/',$this->input->post('data'))))."-01'";
            }else{
                $data = "";
            }
            
            $semDefinicao = (substr($this->input->post('idGerados'),0,2) == '0,')? 'OR idOperCobrPai IS NULL': '';
            
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_delin SET status = '".$statusNovo."' WHERE status='".$statusAntigo."' ".$data." AND idOperCobrPai IN (".$this->input->post('idGerados').") ".$semDefinicao;
            $this->db->query($sql);
        }
        
    }
    
    public function dadosDelinTabelaDinamica(){
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        
        $sql = "SELECT
                	idPai, 
                	(
                	SELECT COUNT(*) FROM ".BANCO_TELECOM.".tcom_delin WHERE CASE WHEN idPai = '' THEN idOperCobrPai IS NULL ELSE idOperCobrPai = idPai END AND competencia = '".$data."-01'
                	) AS existe,
                	(
                	SELECT COUNT(*) FROM ".BANCO_TELECOM.".tcom_delin WHERE CASE WHEN idPai = '' THEN idOperCobrPai IS NULL ELSE idOperCobrPai = idPai END AND competencia = '".$data."-01' AND status = 'FATURADO'
                    #SELECT COUNT(*) FROM ".BANCO_TELECOM.".tcom_delin WHERE CASE WHEN idPai = '' THEN idOperCobrPai IS NULL ELSE idOperCobrPai = idPai END AND competencia = DATE_ADD('".$data."-01',INTERVAL -1 MONTH) AND status IN('GERADO','FATURADO')
                	) AS faturado,
                    (
                	SELECT DATE_FORMAT(MAX(competencia),'%m/%Y') FROM ".BANCO_TELECOM.".tcom_delin WHERE CASE WHEN idPai = '' THEN idOperCobrPai IS NULL ELSE idOperCobrPai = idPai END AND status = 'GERADO'
                	) AS competencia,
                	(
                		SELECT DISTINCT DATE_FORMAT(DATE_ADD(MAX(competencia),INTERVAL 1 MONTH),'%m/%Y') FROM ".BANCO_TELECOM.".tcom_delin WHERE CASE WHEN idPai = '' THEN idOperCobrPai IS NULL ELSE idOperCobrPai = idPai END AND status IN ('FATURADO')
                	) AS proxima,
                    (
                		SELECT DISTINCT arquivo FROM ".BANCO_TELECOM.".tcom_delin WHERE CASE WHEN idPai = '' THEN idOperCobrPai IS NULL ELSE idOperCobrPai = idPai END AND competencia = '".$data."-01' ORDER BY tcom_delin.data_cadastro DESC LIMIT 1
                	) AS arquivo,
                	grupo, 
                    SUM(tot) AS qtdOper,
                	SUM(valor_contrato) AS valor_contrato,
                	SUM(mens_contratada) AS mens_contratada, 
                	ROUND(SUM(mens_atual_si),2) AS mens_atual_si, 
                	ROUND(SUM(mens_atual_ci),2) AS mens_atual_ci, 
                	ROUND(SUM(valor_cobrado),2) AS valor_cobrado
                FROM (
                	SELECT
            		CASE WHEN paiOper.id IS NULL THEN 0 ELSE paiOper.id END AS idPai,
            		CASE WHEN paiOper.titulo IS NULL THEN 'NAO DEFINIDO' ELSE paiOper.titulo END AS 'Grupo',
            		tcom_oper_cobr.cnpj AS CNPJ,
                    designacao,
                    COUNT(*) AS tot,
            		SUM(tcom_contrato_valor.valor) AS valor_contrato,
            		SUM(tcom_contrato_valor.mens_contratada_sem_imposto) AS mens_contratada,
            		SUM(tcom_contrato_valor.mens_atual_sem_imposto) AS mens_atual_si,
            		SUM(".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal)) AS mens_atual_ci,
            		/*CASE 
            			WHEN DATEDIFF(data_canc, '".$data."-01') > 0 AND SUBSTR(data_canc,1,7) = '".$data."'
            				THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,faturado_por,filhaOper.estatal) / 30) * (DATEDIFF(data_canc, '".$data."-01')+1),2)
        	        ELSE ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,faturado_por,filhaOper.estatal) END AS valor_cobrado*/
                	SUM(
                    CASE 
               		   WHEN DATEDIFF(data_canc, '".$data."-01') > 0 AND SUBSTR(data_canc,1,7) = '".$data."' # TRATAMENTO DATA DE CANCELAMENTO
           			      THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(data_canc, '".$data."-01')+1),2)
                       WHEN DATEDIFF(tcom_contrato.data_fim, '".$data."-01') > 0 AND SUBSTR(tcom_contrato.data_fim,1,7) = '".$data."' # TRATAMENTO DATA DE TERMINO
   			              THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(tcom_contrato.data_fim, '".$data."-01')+1),2)
               		   WHEN DATEDIFF(LAST_DAY('".$data."-01'), tcom_contrato.data_inicio) > 0 AND SUBSTR(tcom_contrato.data_inicio,1,7) = '".$data."' # TRATAMENTO DATA DE ATIVAÇÃO
           			      THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) / 30) * (DATEDIFF(LAST_DAY('".$data."-01'), tcom_contrato.data_inicio)+1),2)
                	ELSE ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal) END
                    ) AS valor_cobrado
                    FROM ".BANCO_TELECOM.".tcom_contrato
                    LEFT JOIN ".BANCO_TELECOM.".tcom_circuito ON tcom_circuito.id = idCircuito
                	LEFT JOIN ".BANCO_TELECOM.".tcom_contrato_valor ON tcom_contrato_valor.idContrato = tcom_contrato.id
                	LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS filhaOper ON filhaOper.id = tcom_contrato_valor.idOper
                	LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS paiOper ON paiOper.id = filhaOper.pai
                	LEFT JOIN ".BANCO_TELECOM.".tcom_oper_cobr ON tcom_oper_cobr.idOper = filhaOper.id
                	LEFT JOIN adminti.unidade ON tcom_contrato.cd_unidade = unidade.cd_unidade
                	WHERE 1=1
                    AND faturado_siga = 'NAO'
                	AND (
                        (SUBSTR(data_inicio,1,7) <= '".$data."' AND SUBSTR(data_fim,1,7) >= '".$data."'  AND tcom_contrato.status = 'A')
                	OR (SUBSTR(data_canc,1,7) >= '".$data."' AND tcom_contrato.status = 'I')
                    )
                    GROUP BY paiOper.id, paiOper.titulo, CNPJ, designacao
                	#ORDER BY paiOper.titulo ASC
                    UNION ALL
                    SELECT
            		CASE WHEN paiOper.id IS NULL THEN 0 ELSE paiOper.id END AS idPai,
            		CASE WHEN paiOper.titulo IS NULL THEN 'NAO DEFINIDO' ELSE paiOper.titulo END AS 'Grupo',
            		tcom_oper_cobr.cnpj AS CNPJ,
                    designacao,
                    0 AS tot,
            		SUM(tcom_contrato_valor.valor) AS valor_contrato,
            		SUM(tcom_contrato_valor.mens_contratada_sem_imposto) AS mens_contratada,
            		SUM(tcom_contrato_valor.mens_atual_sem_imposto) AS mens_atual_si,
            		SUM(".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,tcom_contrato.cd_unidade,tcom_contrato_valor.faturado_por, filhaOper.estatal)) AS mens_atual_ci,
            		/*CASE 
            			WHEN DATEDIFF(data_canc, '".$data."-01') > 0 AND SUBSTR(data_canc,1,7) = '".$data."'
            				THEN ROUND((".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,faturado_por,filhaOper.estatal) / 30) * (DATEDIFF(data_canc, '".$data."-01')+1),2)
        	        ELSE ".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,idServico,faturado_por,filhaOper.estatal) END AS valor_cobrado*/
                	
                    SUM(".BANCO_TELECOM.".valorComImposto(tcom_contrato_valor.taxa_inst_sem_imposto,isencao_icms,CASE WHEN tcom_contrato.idServico = 6 THEN 6 ELSE 11 END,tcom_contrato.cd_unidade,faturado_por,filhaOper.estatal))
 AS valor_cobrado
                    FROM ".BANCO_TELECOM.".tcom_contrato
                    LEFT JOIN ".BANCO_TELECOM.".tcom_circuito ON tcom_circuito.id = idCircuito
                	LEFT JOIN ".BANCO_TELECOM.".tcom_contrato_valor ON tcom_contrato_valor.idContrato = tcom_contrato.id
                	LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS filhaOper ON filhaOper.id = tcom_contrato_valor.idOper
                	LEFT JOIN ".BANCO_TELECOM.".tcom_oper AS paiOper ON paiOper.id = filhaOper.pai
                	LEFT JOIN ".BANCO_TELECOM.".tcom_oper_cobr ON tcom_oper_cobr.idOper = filhaOper.id
                	LEFT JOIN adminti.unidade ON tcom_contrato.cd_unidade = unidade.cd_unidade
                	WHERE 1=1
                    AND faturado_siga = 'NAO'
                    AND tcom_contrato_valor.gerouInst = 'N'
                	AND (
                        (SUBSTR(data_inicio,1,7) <= '".$data."' AND SUBSTR(data_fim,1,7) >= '".$data."'  AND tcom_contrato.status = 'A')
                	OR (SUBSTR(data_canc,1,7) >= '".$data."' AND tcom_contrato.status = 'I')
                    )
                    GROUP BY paiOper.id, paiOper.titulo, CNPJ, designacao
                	#ORDER BY paiOper.titulo ASC
                ) AS res
                GROUP BY idPai, grupo";
                #echo '<pre>'.$sql; exit();
            
        return $this->db->query($sql)->result(); 
        
    }
    
    public function delinAno(){
        
        $this->db->distinct();
        $this->db->select("SUBSTR(competencia,1,4) AS ano");
        $this->db->order_by('competencia', 'desc');
        return $this->db->get(BANCO_TELECOM.'.tcom_delin')->result();
        
    }
    
    public function delinArquivos(){
        
        $this->db->distinct();
        $this->db->select("arquivo, link, status, DATE_FORMAT(data_cadastro,'%d/%m/%Y %H:%i') AS data_cadastro");
        $this->db->where('SUBSTR(competencia,1,4)', $this->input->post('ano'));
        $this->db->order_by('data_cadastro', 'desc');
        return $this->db->get(BANCO_TELECOM.'.tcom_delin')->result();
        
    }

}