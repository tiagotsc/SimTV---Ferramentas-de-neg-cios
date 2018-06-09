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
class faturamento_model extends CI_Model{
	
    const tabela = 'tcom_imposto';
    
	/**
	 * Tinterface_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
        
		parent::__construct();
	}
    
    public function testeFat(){
        #echo 'aqui';
        #return $this->notaFiscal->dadosNFTabelaDinamica();
    }
    
    public function gravaFaturamento(){
        
        #$data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        $idSelecionados = $this->input->post('idSelecionados');
        $semDefinicao = (substr($idSelecionados,0,2) == '0,')? 'OR paiOper.id IS NULL': '';
        
        $this->db->trans_begin();
        
        $sql = "SELECT
                    competencia,
                    CONCAT(DATE_FORMAT(competencia,'%m%y'),'1',lpad(idOperCobrFilha, 6, '0')) AS numero,
                    #idContrato,
                    CASE WHEN idOperCobrPai IS NULL THEN '0' ELSE idOperCobrPai END AS idOperCobrPai,
                    idOperCobrFilha,
                    SUM(valor_cobrado) AS valor_cobrado,
                    data_venc,
                    tipo
                FROM ".BANCO_TELECOM.".tcom_delin 
                WHERE status = 'GERADO' 
                AND (idOperCobrPai IN (".$idSelecionados.") ".$semDefinicao.")
                #AND competencia = '".$data."-01'
                GROUP BY 
                competencia,
                #idContrato,
                idOperCobrPai,
                idOperCobrFilha,
                data_venc,
                tipo";
         
        $resultado = $this->db->query($sql)->result();
        
        foreach($resultado as $res){
            
            $idPai[] = $res->idOperCobrPai;
            $campo[] = 'competencia';
            #$valor[] = "'".implode('-',array_reverse(explode('/',$this->input->post('data'))))."-01'";
            #$valor[] = "'".implode('-',array_reverse(explode('/',$this->input->post('competencia')[$res->idOperCobrPai])))."-01'";
            $valor[] = "'".$res->competencia."'";
            $campo[] = 'numero';
            $valor[] = "'".$res->numero."'";
            $campo[] = 'idOperCobrPai';
            $valor[] = ($res->idOperCobrPai=='0')?'':$res->idOperCobrPai;
            $campo[] = 'idOperCobrFilha';
            $valor[] = $res->idOperCobrFilha;
            $campo[] = 'valor_cobrado';
            $valor[] = $this->util->formaValorBanco($res->valor_cobrado);
            $campo[] = 'data_venc';
            $valor[] = $this->util->formaValorBanco($res->data_venc);
            $campo[] = 'tipo';
            $valor[] = "'".$res->tipo."'";
            $campo[] = 'cd_usuario_cadastro';
            $valor[] = $this->session->userdata('cd');
            
            $campos = implode(', ', $campo);
            $valores = str_replace('%','',implode(', ', $valor));
            $sql = "INSERT INTO ".BANCO_TELECOM.".tcom_titulo_fat (".$campos.")\n VALUES(".$valores.");";
            $this->db->query($sql);
            $campo = array();
            $valor = array();
        }
        
        $this->baixaNotas($idSelecionados,$semDefinicao);
        $this->alteraGerarInst($idSelecionados,$semDefinicao);
        
        $_POST['data'] = null;
        $_POST['idGerados'] = implode(',',array_unique($idPai));
        
        $this->delin->alteraStatusDelin('GERADO','FATURADO');
        
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
    
    public function baixaNotas($idSelecionados,$semDefinicao){
        
        $sql = "SELECT 
                    DISTINCT 
                    competencia, idCredito 
                FROM ".BANCO_TELECOM.".tcom_delin 
                WHERE status = 'GERADO' 
                AND (idOperCobrPai IN (".$idSelecionados.") ".$semDefinicao.")
                AND idCredito IS NOT NULL";
        #echo '<pre>'; print_r($sql); exit();
        $baixaNotaCredito = $this->db->query($sql)->result();
        
        foreach($baixaNotaCredito as $bNota){
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_debcred SET competencia = '".$bNota->competencia."', faturado = 'SIM', dataFaturamento = CURRENT_TIMESTAMP WHERE id IN (".$bNota->idCredito.")";
            #echo $sql; echo '<br>';
            $this->db->query($sql);
        }
        #echo '<br>';
        $sql = "SELECT 
                    DISTINCT 
                    competencia, idDebito 
                FROM ".BANCO_TELECOM.".tcom_delin 
                WHERE status = 'GERADO' 
                AND (idOperCobrPai IN (".$idSelecionados.") ".$semDefinicao.")
                AND idDebito IS NOT NULL";
        
        $baixaNotaDebito = $this->db->query($sql)->result();
        
        foreach($baixaNotaDebito as $bNota){
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_debcred SET competencia = '".$bNota->competencia."', faturado = 'SIM', dataFaturamento = CURRENT_TIMESTAMP WHERE id IN (".$bNota->idDebito.")";
            #echo $sql; echo '<br>';
            $this->db->query($sql);
        }
        #exit();
    }
    
    public function alteraGerarInst($idSelecionados,$semDefinicao){
        
        $sql = "SELECT
      		        idContrato
                FROM ".BANCO_TELECOM.".tcom_delin
                WHERE status = 'GERADO' 
                AND (idOperCobrPai IN (".$idSelecionados.") ".$semDefinicao.")
                #AND competencia = '-01'
                AND tipo = 'INST'";
        
        $resultado = $this->db->query($sql)->result();
        
        foreach($resultado as $res){
            $sql = "UPDATE ".BANCO_TELECOM.".tcom_contrato_valor SET gerouInst='S' WHERE idContrato = ".$res->idContrato;
            $this->db->query($sql);
        }
        
    }
    
    /*
    public function dadosFatura(){
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        
        $sql = "SELECT
                    data_canc,
                    tcom_contrato.status,
                    CASE 
                    	WHEN DATEDIFF(data_canc, '".$data."-01') > 0
                    		THEN ROUND((telecom.valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,'ICMS,PIS,COFINS',idServico,faturado_por,'REDUCAO') / 30) * (DATEDIFF(data_canc, '".$data."-01')+1),2)
                    ELSE telecom.valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,'ICMS,PIS,COFINS',idServico,faturado_por,'REDUCAO') END AS valor_cobrado,
                    DATEDIFF(data_canc, '".$data."-01')+1 AS dia,
                    paiOper.titulo AS 'Operadora',
                    tcom_oper_cobr.cnpj AS CNPJ,
                    tcom_circuito.designacao AS 'Designacao dos Servicos',
                    data_inicio AS 'Data Ativacao',
                    data_fim AS 'Data Termino',
                    tcom_contrato_valor.valor AS 'Valor Contrato',
                    tcom_contrato_valor.mens_contratada_sem_imposto AS 'Mensalidade contratada (S/I)',
                    tcom_servico_tipo.nome AS 'Tipo Servico',
                    tcom_contrato_valor.mens_atual_sem_imposto AS 'Mensalidade Atual (S/I)',
                    telecom.valorComImposto(tcom_contrato_valor.mens_atual_sem_imposto,isencao_icms,'ICMS,PIS,COFINS',idServico,faturado_por,'REDUCAO') AS 'Mensalidade Atual (C/I)',
                    CASE WHEN faturado_por IS NULL THEN unidade.nome ELSE (SELECT nome FROM adminti.unidade WHERE cd_unidade = faturado_por) END AS 'Faturado Por'
                FROM telecom.tcom_contrato
                LEFT JOIN telecom.tcom_viab_resp ON tcom_contrato.id = CASE WHEN tcom_viab_resp.idContratoAtual IS NOT NULL THEN tcom_viab_resp.idContratoAtual ELSE tcom_viab_resp.idContrato END
                INNER JOIN telecom.tcom_oper AS filhaOper ON filhaOper.id = tcom_contrato.idOper
                INNER JOIN telecom.tcom_oper AS paiOper ON paiOper.id = filhaOper.pai
                LEFT JOIN telecom.tcom_oper_cobr ON tcom_oper_cobr.idOper = filhaOper.id
                LEFT JOIN telecom.tcom_circuito ON tcom_circuito.id = idCircuito
                LEFT JOIN telecom.tcom_contrato_circuito ON tcom_contrato_circuito.idContrato = tcom_contrato.id AND tcom_contrato_circuito.idCircuito = tcom_contrato.idCircuito
                LEFT JOIN telecom.tcom_contrato_valor ON tcom_contrato_valor.idContrato = tcom_contrato.id
                LEFT JOIN telecom.tcom_interface ON tcom_interface.id = tcom_contrato_circuito.idInterface
                LEFT JOIN telecom.tcom_taxa_digital ON tcom_taxa_digital.id = tcom_contrato_circuito.idTaxaDigital
                LEFT JOIN telecom.tcom_cliente ON tcom_cliente.id = tcom_contrato.idCliente
                LEFT JOIN adminti.unidade ON tcom_contrato.cd_unidade = unidade.cd_unidade
                LEFT JOIN telecom.tcom_servico ON tcom_servico.id = idServico
                LEFT JOIN telecom.tcom_servico_tipo ON tcom_servico_tipo.id = idServicoTipo
                LEFT JOIN telecom.tcom_cliente_tipo ON tcom_cliente_tipo.id = tcom_cliente.idClienteTipo
                WHERE 1=1 
                AND SUBSTR(data_inicio,1,7) <= '".$data."' AND SUBSTR(data_fim,1,7) >= '".$data."'
                AND tcom_contrato.status = 'A'
                OR (SUBSTR(data_canc,1,7) >= '".$data."' AND tcom_contrato.status = 'I')
                ORDER BY paiOper.titulo";
        
            return $this->db->query($sql)->result_array(); 
    }
    */
    public function dadosTabelaDinamica(){
        
        $data = implode('-',array_reverse(explode('/',$this->input->post('data'))));
        $data = date('Y-m', strtotime('-1 months', strtotime($data.'-01')));
        
        if($this->input->post('tipo_acao') == 'delin'){
        
            return $this->delin->dadosDelinTabelaDinamica();
            
        }
            
        if($this->input->post('tipo_acao') == 'faturamento'){
        
            $sql = "SELECT
                    	tcom_oper.id AS idPai, 
                    	'-' AS existe,
                    	'-' AS faturado,
                    	(
                            SELECT DATE_FORMAT(MAX(tcom_delin.competencia),'%m/%Y') FROM telecom.tcom_delin WHERE idOperCobrPai = tcom_oper.id
                    		/*SELECT 
                                DISTINCT 
                                    DATE_FORMAT(DATE_ADD(MAX(tcom_delin.competencia),INTERVAL 1 MONTH),'%m/%Y') 
                            FROM ".BANCO_TELECOM.".tcom_delin 
                            WHERE CASE WHEN tcom_oper.id IS NULL THEN idOperCobrPai IS NULL ELSE idOperCobrPai = tcom_oper.id END AND status = 'FATURADO'*/
                        ) AS proxima,
                    	CASE WHEN tcom_oper.titulo IS NULL THEN 'NAO DEFINIDO' ELSE tcom_oper.titulo END AS grupo, 
                    	COUNT(*) AS qtdOper,
                    	ROUND(SUM(valor_cobrado),2) AS valor_cobrado,
                        '0.00' AS valor_cobrado_inst
                    FROM ".BANCO_TELECOM.".tcom_delin
                    LEFT JOIN ".BANCO_TELECOM.".tcom_oper ON idOperCobrPai = tcom_oper.id
                    WHERE tcom_delin.status IN ('GERADO') #AND competencia = '".$data."-01'
                    AND idOperCobrPai IS NOT NULL
                    GROUP BY tcom_oper.id, tcom_oper.titulo";
                    
            return $this->db->query($sql)->result(); 
                
        }
        
        
        if($this->input->post('tipo_acao') == 'nota_fiscal'){
            
            return $this->notafiscal->dadosNFTabelaDinamica();
            
        }
        
    }
    
    public function GrupoOperadora($idPai){
        if($idPai){
            $this->db->where('id', $idPai);
        }
        $this->db->where('pai', '0');
        $this->db->where('status', 'A');
        $this->db->order_by('titulo', 'asc');
        if($idPai){
            return $this->db->get(BANCO_TELECOM.'.tcom_oper')->row();
        }else{
            return $this->db->get(BANCO_TELECOM.'.tcom_oper')->result();
        }
    }
    
    public function gruposFaturados(){
        
        $this->db->distinct();
        $this->db->select("tcom_oper.id, tcom_oper.titulo");
        $this->db->order_by('tcom_oper.titulo', 'asc');
        $this->db->join(BANCO_TELECOM.'.tcom_oper', 'tcom_oper.id = idOperCobrPai'); 
        return $this->db->get(BANCO_TELECOM.'.tcom_titulo_fat')->result();
        
    }
    
    public function competenciasFaturadas(){
        
        $this->db->distinct();
        $this->db->select("competencia, DATE_FORMAT(competencia,'%m/%Y') AS competencia_formatada");
        $this->db->order_by('competencia', 'desc');
        return $this->db->get(BANCO_TELECOM.'.tcom_titulo_fat')->result();
        
    }
    
    public function dadosFaturamento($idPai, $status){
     
        $this->db->select("
                            tcom_titulo_fat.id,
                            DATE_FORMAT(tcom_titulo_fat.competencia,'%m/%Y') AS competencia,
                            titulo,
                            cnpj,
                            tcom_titulo_fat.nota_fiscal,
                            tcom_titulo_fat.tipo,
                            tcom_titulo_fat.valor_cobrado,
                            tcom_titulo_fat.valor_pago,
                            DATE_FORMAT(tcom_titulo_fat.data_venc,'%d/%m/%Y') AS data_venc,
                            DATE_FORMAT(tcom_titulo_fat.data_pagamento,'%d/%m/%Y') AS data_pagamento,
                            CASE 
                            	WHEN tcom_titulo_fat.status = 'ABERTO' AND tcom_titulo_fat.data_venc < CURDATE()
                            		THEN 'ATRASADO'
                            ELSE '' END AS situacao,
                            tcom_titulo_fat.status
                            ");
                            
        $this->db->where('idOperCobrPai', $idPai);
        $this->db->where('tcom_titulo_fat.status', $status);
        $this->db->join(BANCO_TELECOM.'.tcom_oper', 'tcom_titulo_fat.idOperCobrFilha = tcom_oper.id');
        $this->db->join(BANCO_TELECOM.'.tcom_oper_cobr', 'tcom_oper_cobr.idOper = tcom_oper.id');
        $this->db->order_by('competencia', 'asc');
        
        return $this->db->get(BANCO_TELECOM.'.tcom_titulo_fat')->result();
        
    }
    
    
    /**
     * 
     * lista os dados existentes de acordo com os par�metros informados
     * @param $parametros Condi��es para filtro
     * @param $mostra_por_pagina P�gina corrente da pagina��o
     * @param $sort_by Campo que vai ser ordenado
     * @param $sort_order Tipo de orde��o do campo (Crescente ou decrescente)
     * @param $pagina P�gina da pagina��o
     * 
     * @return A lista das dados
     */
    public function pesquisa($parametros, $mostra_por_pagina, $sort_by, $sort_order, $pagina){
        #$this->delin->teste();
        $this->db->select("
                        	(SELECT id FROM telecom.tcom_oper WHERE tcom_oper.id = idOperCobrPai) AS id,
                            (SELECT titulo FROM ".BANCO_TELECOM.".tcom_oper WHERE tcom_oper.id = idOperCobrPai) AS grupo,
                            DATE_FORMAT(tcom_titulo_fat.competencia,'%m/%Y') AS competencia,
                            DATE_FORMAT(tcom_titulo_fat.competencia,'%m_%Y') AS comp_banco,
                            SUM(tcom_titulo_fat.valor_cobrado) AS valor_cobrado
                            ");

        if($sort_by != '1'){
            $this->db->order_by($sort_by, $sort_order);
        }

        if($parametros){
            $post = explode('|', $parametros);
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('competencia','tipo','status'))){
                        $this->db->where('tcom_titulo_fat.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('idOperCobrPai'))){ 
                        if($res[1] == 'N'){
                            $this->db->where('tcom_titulo_fat.'.$res[0].' IS NULL');
                        }else{
                            $this->db->where('tcom_titulo_fat.'.$res[0], $res[1]);
                        }
                    }else{
                        $this->db->like('tcom_titulo_fat.'.$res[0], $res[1]);
                    }
                }
                
            }
        }  
        
        #$this->db->join(BANCO_TELECOM.'.tcom_servico', 'tcom_servico.id = idServico'); 
        #$this->db->join('adminti.estado', 'estado.cd_estado = tcom_imposto.cd_estado'); 
        $this->db->group_by(array('(SELECT id FROM '.BANCO_TELECOM.'.tcom_oper WHERE tcom_oper.id = idOperCobrPai)','tcom_titulo_fat.competencia'));
        
        $dados['id'] = 'id';
        $dados['dados'] = $this->db->get(BANCO_TELECOM.'.tcom_titulo_fat', $mostra_por_pagina, $pagina)->result();      #echo '<pre>';echo  $this->db->last_query(); exit();  
        $dados['qtd'] = $this->qtdLinhas($parametros);
        #$dados['camposLabel'] = array('efetiva' => 'Efetiva%', 'nome_estado' => 'Estado', 'base_calculo' => 'Base c&aacute;lculo%', 'reducao' => 'Redu&ccedil;&atilde;o%');
        $dados['campos'] = array('id', 'grupo', 'competencia', 'valor_cobrado');

        return $dados;
    }
    
    /**
     * 
     * Quantidade de linhas da consulta
     * @param $parametros Condi��es para filtro
     * 
     * @return A quantidade de linhas
     */
    public function qtdLinhas($parametros = null){
        
        if($parametros){
            $post = explode('|', $parametros);
            
            foreach($post as $campoValor){
                $res = explode('=', $campoValor);
                
                if($res[1] != ''){
                    if(in_array($res[0], array('competencia','tipo','status'))){
                        $this->db->where('tcom_titulo_fat.'.$res[0], $res[1]);
                    }elseif(in_array($res[0], array('idOperCobrPai'))){ 
                        if($res[1] == 'N'){
                            $this->db->where('tcom_titulo_fat.'.$res[0].' IS NULL');
                        }else{
                            $this->db->where('tcom_titulo_fat.'.$res[0], $res[1]);
                        }
                    }else{
                        $this->db->like('tcom_titulo_fat.'.$res[0], $res[1]);
                    }
                }
            }
        }
        
        #$this->db->join(BANCO_TELECOM.'.tcom_servico', 'tcom_servico.id = idServico');
        #$this->db->join('adminti.estado', 'estado.cd_estado = tcom_imposto.cd_estado'); 
        
        $this->db->group_by(array('(SELECT id FROM telecom.tcom_oper WHERE tcom_oper.id = idOperCobrPai)','tcom_titulo_fat.competencia'));
        
        return $this->db->get(BANCO_TELECOM.'.tcom_titulo_fat')->num_rows(); 
        
    }

}