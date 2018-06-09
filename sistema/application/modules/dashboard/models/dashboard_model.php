<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza consultas gen�ricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class Dashboard_model extends CI_Model{
	
	/**
	 * Dashboard_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    public function gravaAcesso(){
        
        $this->db->trans_begin();
        
		$sql = "INSERT INTO grafico_acesso (cd_grafico, cd_usuario)\n VALUES(".$this->input->post('cd_grafico').",".$this->session->userdata('cd').");";
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return $cd;
        }        
        
    }
    
    /**
    * 
    * Dashboard_model::anosArquivosRetorno()
    * 
    * Fun��o que pega os anos do retorno
    * @return Retorna os anos
    */
    public function anosArquivosRetorno(){
        
        $this->db->distinct();
        $this->db->select('SUBSTR(data_arquivo_retorno, 1 , 4) AS anos');
        $this->db->order_by('SUBSTR(data_arquivo_retorno, 1 , 4)', 'desc'); 
        return $this->db->get('arquivo_retorno')->result();
        
    }
    
    /**
    * 
    * Dashboard_model::qtdTitulos()
    * 
    * Fun��o que pega a quantidade de t�tulos baixados no m�s
    * 
    * @param $mes M�s para filtrar a consulta
    * @param $ano Ano para filtrar a consulta
    * 
    * @return Retorna as quantidades
    */
    public function qtdTitulos($ano, $cdBanco = null){
        
        if($cdBanco <> ''){
            #$this->db->where('arquivo_retorno.cd_banco', $cdBanco);
            $bancoPri = "AND pri.cd_banco = ".$cdBanco;
            $bancoSec = "AND arquiSecu.cd_banco = ".$cdBanco;
        }else{
            $bancoPri = '';
            $bancoSec = '';
        } 
        
        $sql = "SELECT 
                CASE 
                	WHEN SUBSTR(data_arquivo_retorno,6,2) = 1 
                		THEN 'Jan'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 2) 
                		THEN 'Fev'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 3)
                		THEN 'Mar'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 4)
                		THEN 'Abr'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 5)
                		THEN 'Mai'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 6)
                		THEN 'Jun'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 7)
                		THEN 'Jul'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 8)
                		THEN 'Ago'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 9)
                		THEN 'Set'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 10)
                		THEN 'Out'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 11)
                		THEN 'Nov'
                ELSE 'Dez'END AS mes,
                COUNT(*) AS qtd_baixado,
                (
                	SELECT 
                	COUNT(*) AS qtd_titulos
                	FROM conteudo_arquivo_retorno
                	INNER JOIN arquivo_retorno AS arquiSecu ON arquiSecu.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                	WHERE cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                	AND cd_ocorrencia_arquivo_retorno IN (610,619,620,634,651,653,657,730)
                	AND SUBSTR(arquiSecu.data_arquivo_retorno,6,2) = SUBSTR(pri.data_arquivo_retorno,6,2)
                	AND SUBSTR(arquiSecu.data_arquivo_retorno, 1, 4) = ".$ano."
                    ".$bancoSec."
                	GROUP BY SUBSTR(arquiSecu.data_arquivo_retorno,6,2)
                ) AS qtd_rejeitado
                FROM conteudo_arquivo_retorno
                INNER JOIN arquivo_retorno AS pri ON pri.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                WHERE cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                AND cd_ocorrencia_arquivo_retorno IN (612,635,731,643,644,740,831,848,867,887,902,920,781,843,862,878,903,934)
                AND pri.data_arquivo_retorno LIKE '".$ano."%'
                ".$bancoPri."
                GROUP BY SUBSTR(pri.data_arquivo_retorno,6,2)
                ORDER BY SUBSTR(pri.data_arquivo_retorno,6,2) ASC";  
                
        return $this->db->query($sql)->result();    
        
    }
    
    
    /**
    *
    * Dashboard_model::valorTotalTitulos() 
    * 
    * Fun��o que pega a soma dos t�tulos baixados no m�s
    * 
    * @param $mes M�s para filtrar a consulta
    * @param $ano Ano para filtrar a consulta
    * 
    * @return Retorna o valor
    */
    public function valorTotalTitulos($ano, $cdBanco = null){
        
        if($cdBanco <> ''){
            #$this->db->where('arquivo_retorno.cd_banco', $cdBanco);
            $bancoPri = "AND pri.cd_banco = ".$cdBanco;
            $bancoSec = "AND arquiSecu.cd_banco = ".$cdBanco;
        }else{
            $bancoPri = '';
            $bancoSec = '';
        } 
        
        $sql = "SELECT 
                CASE 
                	WHEN SUBSTR(data_arquivo_retorno,6,2) = 1 
                		THEN 'Jan'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 2) 
                		THEN 'Fev'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 3)
                		THEN 'Mar'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 4)
                		THEN 'Abr'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 5)
                		THEN 'Mai'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 6)
                		THEN 'Jun'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 7)
                		THEN 'Jul'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 8)
                		THEN 'Ago'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 9)
                		THEN 'Set'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 10)
                		THEN 'Out'
                	WHEN (SUBSTR(data_arquivo_retorno,6,2) = 11)
                		THEN 'Nov'
                ELSE 'Dez'END AS mes,
                SUM(valor_pago_arquivo_retorno) AS total_baixado,
                (
                	SELECT 
                	SUM(valor_pago_arquivo_retorno) AS total_rejeitado
                	FROM conteudo_arquivo_retorno
                	INNER JOIN arquivo_retorno AS arquiSecu ON arquiSecu.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                	WHERE cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                	AND cd_ocorrencia_arquivo_retorno IN (610,619,620,634,651,653,657,730)
                	AND SUBSTR(arquiSecu.data_arquivo_retorno,6,2) = SUBSTR(pri.data_arquivo_retorno,6,2)
                	AND SUBSTR(arquiSecu.data_arquivo_retorno, 1, 4) = ".$ano."
                    ".$bancoSec."
                	GROUP BY SUBSTR(arquiSecu.data_arquivo_retorno,6,2)
                ) AS total_rejeitado
                FROM conteudo_arquivo_retorno
                INNER JOIN arquivo_retorno AS pri ON pri.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                WHERE cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                AND cd_ocorrencia_arquivo_retorno IN (612,635,731,643,644,740,831,848,867,887,902,920,781,843,862,878,903,934)
                AND pri.data_arquivo_retorno LIKE '".$ano."%'
                ".$bancoPri."
                GROUP BY SUBSTR(pri.data_arquivo_retorno,6,2)
                ORDER BY SUBSTR(pri.data_arquivo_retorno,6,2) ASC";  
                
        return $this->db->query($sql)->result(); 
        
    }
    
    /**
    *
    * Dashboard_model::comboRentabilizacao() 
    * 
    * Pega todos os meses/ano da rentabilita��o para montar a combo
    * 
    * @return Retorna os meses / ano
    */
    public function comboRentabilizacao(){
        
        $sql = "SELECT 
                	DISTINCT
                	TO_CHAR(DIA, 'MM/YYYY') AS exibicao,
                    TO_CHAR(DIA, 'MM-YYYY') AS valor,
                	SUBSTR(DIA, 4, 6) AS formato_banco
                FROM supsiga.V_SUPGXV_RENTABILIZACAO
                ORDER BY SUBSTR(DIA, 4, 6) DESC";
        
        $conexao = $this->load->database('oracle', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
    *
    * Dashboard_model::rentabilizacaoTela1() 
    * 
    * Pega os dados a tela 1 da rentabiliza��o
    * 
    * @param $mesAno M�s/ano que ser� buscado
    * 
    * @return Retorna os dados referentes ao m�s e ano
    */
    public function rentabilizacaoTela1($mesAno){
        
        $sql = "SELECT 
                		TO_CHAR(DIA, 'DD') AS dia,
                		VLR_TOT AS rentabilizacao
                FROM supsiga.V_SUPGXV_RENTABILIZACAO
                WHERE TO_CHAR(DIA, 'MM-YYYY') = '".$mesAno."'";
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
    *
    * Dashboard_model::rentabilizacaoTela2() 
    * 
    * Pega os dados a tela 2 da rentabiliza��o
    * 
    * @param $mesAno M�s/ano que ser� buscado
    * 
    * @return Retorna os dados referentes ao m�s e ano
    */
    public function rentabilizacaoTela2($mesAno){
        
        $sql = "SELECT 
                		*
                FROM supsiga.V_SUPGXV_RENTABILIZACAO_PRD
                WHERE TO_CHAR(PERIODO, 'MM-YYYY') = '".$mesAno."'";
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
    *
    * Dashboard_model::rentabilizacaoTela3() 
    * 
    * Pega os dados a tela 3 da rentabiliza��o
    * 
    * @param $mesAno M�s/ano que ser� buscado
    * 
    * @return Retorna os dados referentes ao m�s e ano
    */
    public function rentabilizacaoTela3($mesAno){
        
        $sql = "SELECT 
                	CASE 
                		WHEN TO_CHAR(MES, 'MM') = '01'
                			THEN 'JANEIRO'
                		WHEN TO_CHAR(MES, 'MM') = '02'
                			THEN 'FEVEREIRO'
                		WHEN TO_CHAR(MES, 'MM') = '03'
                			THEN 'MAR&Ccedil;O'
                		WHEN TO_CHAR(MES, 'MM') = '04'
                			THEN 'ABRIL'
                		WHEN TO_CHAR(MES, 'MM') = '05'
                			THEN 'MAIO'
                		WHEN TO_CHAR(MES, 'MM') = '06'
                			THEN 'JUNHO'
                		WHEN TO_CHAR(MES, 'MM') = '07'
                			THEN 'JULHO'
                		WHEN TO_CHAR(MES, 'MM') = '08'
                			THEN 'AGOSTO'
                		WHEN TO_CHAR(MES, 'MM') = '09'
                			THEN 'SETEMBRO'
                		WHEN TO_CHAR(MES, 'MM') = '10'
                			THEN 'OUTUBRO'
                		WHEN TO_CHAR(MES, 'MM') = '11'
                			THEN 'NOVEMBRO'
                	ELSE 'DEZEMBRO' END AS MES,
                	TO_CHAR(PERIODO_INICIAL, 'DD/MM/YYYY') AS PERIODO_INICIAL,
                	TO_CHAR(PERIODO_FINAL, 'DD/MM/YYYY') AS PERIODO_FINAL,
                	TOT_RENTAB,
                	PROJECAO
                FROM supsiga.V_SUPGXV_RENTABILIZACAO_TOT
                WHERE TO_CHAR(MES, 'MM-YYYY') = '".$mesAno."'";
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::dashboardPermitidos()
     * 
     * Pega os dashboard que o usu�rio tem permiss�o para acessar
     * 
     * @param mixed $permissoes Permiss�es do perfil do usu�rio
     * @return
     */
    public function dashboardPermitidos($permissoes, $tipo = 'objeto'){
        
        $this->db->select('cd_grafico');
        $this->db->where('status_grafico', 'A');
        $this->db->where('cd_permissao IN ('.implode(',',$permissoes).')');
        $this->db->order_by("nome_grafico", "asc"); 
        
        if($tipo == 'objeto'){
            return $this->db->get('grafico')->result();
        }else{
            return $this->db->get('grafico')->result_array();
        }
        
    }
    
    /**
     * Dashboard_model::baseAssinantesConsolidadePermissor()
     * 
     * Pega a base de assinantes consolidado por permissor
     * 
     * @return Os dados
     */
    public function baseAssinantesConsolidadePermissor(){
        
        $sql = "SELECT
                *
                FROM (
                SELECT 
                	LINHA,
                	COD_OPERADORA,
                	OPERADORA,
                	CASE 
                	WHEN PRODUTO = 'PTV'
                		THEN 'TV'
                	WHEN PRODUTO = 'CM'
                		THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	BASE_COM_SINAL,
                	DIAS_ATRAS_5,
                	DIAS_ATRAS_4,
                	DIF_4,
                	DIF_PORC_4,
                	DIAS_ATRAS_3,
                	DIF_3,
                	DIF_PORC_3,
                	DIAS_ATRAS_2,
                	DIF_2,
                	DIF_PORC_2,
                	DIAS_ATRAS_1,
                	DIF_1,
                	DIF_PORC_1,
                	CASE 
                		WHEN COD_OPERADORA IN (63, 61, 62)
                			THEN 'RJ'
                		WHEN COD_OPERADORA IN (91)
                			THEN 'MT'
                		WHEN COD_OPERADORA IN (53, 51)
                			THEN 'BH'
                		WHEN COD_OPERADORA IN (74)
                			THEN 'SP'
                		WHEN COD_OPERADORA IN (73, 72, 71)
                			THEN 'PE'
                		WHEN COD_OPERADORA IN (82)
                			THEN 'RS'
                		WHEN COD_OPERADORA IN (52)
                			THEN 'SE'
                		WHEN COD_OPERADORA IN (64)
                			THEN 'MG'
                	END AS estado
                FROM V_SUPGXV_DASH_TOT_OPER
                )
                --WHERE COD_OPERADORA = 72
                ORDER BY ESTADO, COD_OPERADORA, LINHA, PRODUTO";
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on();
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::baseAssinantesConsolidadoComSinal()
     * 
     * Pega a base de assinantes consolidado com sinal
     * 
     * @return Os dados
     */
    public function baseAssinantesConsolidadoComSinal(){
        
        $sql = "SELECT 
                	CASE 
                	WHEN PRODUTO = 'PTV'
                		THEN 'TV'
                	WHEN PRODUTO = 'CM'
                		THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	DIAS_ATRAS_5,
                	DIAS_ATRAS_4,
                	DIF_4,
                	DIF_PORC_4,
                	DIAS_ATRAS_3,
                	DIF_3,
                	DIF_PORC_3,
                	DIAS_ATRAS_2,
                	DIF_2,
                	DIF_PORC_2,
                	DIAS_ATRAS_1,
                	DIF_1,
                	DIF_PORC_1
                FROM SUPSIGA.V_SUPGXV_DASH_TOT
                ORDER BY
                CASE 
                	WHEN PRODUTO = 'TV'
                		THEN 1
                	WHEN PRODUTO = 'INTERNET'
                		THEN 2
                    WHEN PRODUTO = 'COMBO'
                		THEN 3
                ELSE 4 END";
                
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on();
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::baseAssinantesConsolidadoSemSinal()
     * 
     * Pega a base de assinantes consolidado sem sinal
     * 
     * @return Os dados
     */
    public function baseAssinantesConsolidadoSemSinal(){
        
        /*$sql = "SELECT
                	PRODUTO,
                	SUM(DECODE (AGING_DESAB, 'ATE 5 DIAS', QTDE, NULL)) ate5dias,
                	SUM(DECODE (AGING_DESAB, 'DE 6 a 15 DIAS', QTDE, NULL)) de6a15dias,
                	SUM(DECODE (AGING_DESAB, 'DE 16 a 59 DIAS', QTDE, NULL)) de16a59dias,
                	SUM(DECODE (AGING_DESAB, 'ACIMA DE 60 DIAS', QTDE, NULL)) acima60dias,
                	ORDEM
                FROM (
                	SELECT 
                	CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 'TV'
                		WHEN PRODUTO = 'CM'
                			THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	AGING_DESAB,
                	QTDE,
                	CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 1
                		WHEN PRODUTO = 'CM'
                			THEN 2
                	ELSE 3 END AS ORDEM
                	FROM V_SUPGXV_DASH_TOT_AGING
                	WHERE AGING_DESAB IS NOT NULL
                		AND TO_CHAR(dia, 'YYYY-MM-DD') = '".date('Y-m-d', strtotime("-1 days"))."'
                	ORDER BY AGING_DESAB
                )	
                GROUP BY PRODUTO,ORDEM
                ORDER BY ORDEM";*/
               
        $sql = "SELECT
                	PRODUTO,
                	SUM(DECODE (AGING_DESAB, 'ATE 5 DIAS', QTDE, NULL)) ate5dias,
                	SUM(DECODE (AGING_DESAB, 'DE 6 a 15 DIAS', QTDE, NULL)) de6a15dias,
                	SUM(DECODE (AGING_DESAB, 'DE 16 a 59 DIAS', QTDE, NULL)) de16a59dias,
                	SUM(DECODE (AGING_DESAB, 'ACIMA DE 60 DIAS', QTDE, NULL)) acima60dias,
                	ORDEM
                FROM (
                	SELECT 
                	CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 'TV'
                		WHEN PRODUTO = 'CM'
                			THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	AGING_DESAB,
                	QTDE,
                	CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 1
                		WHEN PRODUTO = 'CM'
                			THEN 2
                	ELSE 3 END AS ORDEM
                	FROM V_SUPGXV_DASH_TOT_AGING
                	WHERE AGING_DESAB IS NOT NULL
                		AND TO_CHAR(dia, 'YYYY-MM-DD') = TO_CHAR(SYSDATE-1, 'YYYY-MM-DD')
                	ORDER BY AGING_DESAB
                )	
                GROUP BY PRODUTO,ORDEM
                ORDER BY ORDEM";
                
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on(); 
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBase()
     * 
     * Pega as sa�das das movimenta��es da base consolidada
     * 
     * @return Os dados
     */
    public function movimentacaoBaseConsolidadoSaida(){
        
        /*$sql = "SELECT 
                	MOTIVO, 
                	SUM(QTD) AS QTD
                FROM (
                    SELECT 
          		        CASE
                    		WHEN MOTIVO_DET = 'DESAB_BAIXA_INAD'
                    			THEN 'Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'DESAB_CORTE_LOG'
                    			THEN 'Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'DESAB_PROM_VENC'
                    			THEN 'Promessa Pagto Vencida'
                            WHEN MOTIVO_DET = 'DESC_BAIXA_INAD'
                                THEN 'Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'DESC_BAIXA_OPCAO'
                    			THEN 'Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'DESC_MUD_END'
                    			THEN 'Mudan&ccedil;a de endere&ccedil;o'
                    	ELSE '' END AS MOTIVO, 
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO 
                    WHERE DIA = '".strtoupper(date("d-M-y", strtotime("-2 days")))."' AND MOV = 'D' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";*/
               
        $sql = "SELECT 
                	MOTIVO, 
                	SUM(QTD) AS QTD
                FROM (
                		SELECT 
                			CASE
                				WHEN MOTIVO_DET = 'DESAB_BAIXA_INAD'
                					THEN 'Corte F&iacute;sico'
                				WHEN MOTIVO_DET = 'DESAB_CORTE_LOG'
                					THEN 'Corte L&oacute;gico'
                				WHEN MOTIVO_DET = 'DESAB_PROM_VENC'
                					THEN 'Promessa Pagto Vencida'
                						WHEN MOTIVO_DET = 'DESC_BAIXA_INAD'
                								THEN 'Corte F&iacute;sico'
                				WHEN MOTIVO_DET = 'DESC_BAIXA_OPCAO'
                					THEN 'Baixa por Op&ccedil;&atilde;o'
                				WHEN MOTIVO_DET = 'DESC_MUD_END'
                					THEN 'Mudan&ccedil;a de endere&ccedil;o'
                                WHEN MOTIVO_DET = 'DESC_MUD_NIV' 
                                    THEN 'Mudan&ccedil;a de Nivel'
                                WHEN MOTIVO_DET = 'RECON_MUD_NIVEL' 
                                    THEN 'Mudan&ccedil;a de Nivel'
                			ELSE '' END AS MOTIVO, 
                			SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO 
                		WHERE DIA = SUBSTR(SYSDATE-2, 1,10) AND MOV = 'D' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on(); 
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBase()
     * 
     * Pega as entradas das movimenta��es da base consolidada
     * 
     * @return Os dados
     */
    public function movimentacaoBaseConsolidadoEntrada(){
        
        /*$sql = "SELECT 
                	MOTIVO, 
                	SUM(QTD) AS QTD
                FROM (
                    SELECT 
          		        CASE
                            WHEN MOTIVO_DET = 'INSTAL'
                    			THEN 'Instala&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_MANUAL'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico  - $'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_CORTE_MANUAL'
                    			THEN 'Cancelamento Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico - $'
                    		WHEN MOTIVO_DET = 'REAB_PROMESSA'
                    			THEN 'Promessa Pagamento'
                    		WHEN MOTIVO_DET = 'RECON_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'RECON_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'RECON_CANC_MUD_END'
                    			THEN 'Mudan&ccedil;a de Endere&ccedil;o'
                    		WHEN MOTIVO_DET = 'RECONEXAO'
                    			THEN 'Reconex&atilde;o'
                    		WHEN MOTIVO_DET = 'RECON_MUD_END'
                    			THEN 'Mudan&ccedil;a de Endere&ccedil;o'
                    		WHEN MOTIVO_DET = 'RECON_PGTO_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico - $'
                            WHEN MOTIVO_DET = 'REAB_PGTO_NC_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico - NC'
                            WHEN MOTIVO_DET = 'REAB_PGTO_NC_BAIXA_INAD' OR MOTIVO_DET = 'RECON_PGTO_NC_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico - NC'
                    	ELSE '' END AS MOTIVO,
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO 
                    WHERE DIA = '".strtoupper(date("d-M-y", strtotime("-1 days")))."' AND MOV = 'A' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";*/
               
        $sql = "SELECT 
                	MOTIVO, 
                	SUM(QTD) AS QTD
                FROM (
                    SELECT 
          		        CASE
                            WHEN MOTIVO_DET = 'INSTAL'
                    			THEN 'Instala&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_MANUAL'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico  - $'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_CORTE_MANUAL'
                    			THEN 'Cancelamento Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico - $'
                    		WHEN MOTIVO_DET = 'REAB_PROMESSA'
                    			THEN 'Promessa Pagamento'
                    		WHEN MOTIVO_DET = 'RECON_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'RECON_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'RECON_CANC_MUD_END'
                    			THEN 'Mudan&ccedil;a de Endere&ccedil;o'
                    		WHEN MOTIVO_DET = 'RECONEXAO' OR MOTIVO_DET = 'RECON_MUD_NIVEL'
                    			THEN 'Reconex&atilde;o'
                    		WHEN MOTIVO_DET = 'RECON_MUD_END'
                    			THEN 'Mudan&ccedil;a de Endere&ccedil;o'
                    		WHEN MOTIVO_DET = 'RECON_PGTO_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico - $'
                            WHEN MOTIVO_DET = 'REAB_PGTO_NC_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico - NC'
                            WHEN MOTIVO_DET = 'REAB_PGTO_NC_BAIXA_INAD' OR MOTIVO_DET = 'RECON_PGTO_NC_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico - NC'
                    	ELSE '' END AS MOTIVO,
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO 
                    WHERE DIA = SUBSTR(SYSDATE-1, 1,10) AND MOV = 'A' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on(); 
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBase()
     * 
     * Pega as sa�das das movimenta��es da base consolidada
     * 
     * @return Os dados
     */
    public function movimentacaoBaseSaidaTipo($tipo = 'INDIVIDUAL'){
        
        $sql = "SELECT 
                	MOTIVO, 
                	SUM(QTD) AS QTD
                FROM (
                    SELECT 
          		        CASE
                    		WHEN MOTIVO_DET = 'DESAB_BAIXA_INAD'
                    			THEN 'Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'DESAB_CORTE_LOG'
                    			THEN 'Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'DESAB_PROM_VENC'
                    			THEN 'Promessa Pagto Vencida'
                            WHEN MOTIVO_DET = 'DESC_BAIXA_INAD'
                                THEN 'Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'DESC_BAIXA_OPCAO'
                    			THEN 'Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'DESC_MUD_END'
                    			THEN 'Mudan&ccedil;a de endere&ccedil;o'
                            WHEN MOTIVO_DET = 'DESC_MUD_NIV' 
                                THEN 'Mudan&ccedil;a de Nivel'
                            WHEN MOTIVO_DET = 'RECON_MUD_NIVEL' 
                                THEN 'Mudan&ccedil;a de Nivel'
                    	ELSE '' END AS MOTIVO, 
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO_TOT 
                    WHERE DIA = SUBSTR(SYSDATE-2, 1,10) AND TIPO = '".$tipo."' AND MOV = 'D' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on();
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBaseConsolidadoEntradaTipo()
     * 
     * Pega as entradas das movimenta��es da base de acordo com o tipo
     * 
     * @return Os dados
     */
    public function movimentacaoBaseEntradaTipo($tipo = 'INDIVIDUAL'){
        
        $sql = "SELECT 
                	MOTIVO, 
                	SUM(QTD) AS QTD
                FROM (
                    SELECT 
          		        CASE
                            WHEN MOTIVO_DET = 'INSTAL'
                    			THEN 'Instala&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_MANUAL'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico  - $'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_CORTE_MANUAL'
                    			THEN 'Cancelamento Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico  - $'
                    		WHEN MOTIVO_DET = 'REAB_PROMESSA'
                    			THEN 'Promessa Pagamento'
                    		WHEN MOTIVO_DET = 'RECON_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'RECON_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'RECON_CANC_MUD_END'
                    			THEN 'Mudan&ccedil;a de Endere&ccedil;o'
                    		WHEN MOTIVO_DET = 'RECONEXAO' OR MOTIVO_DET = 'RECON_MUD_NIVEL'
                    			THEN 'Reconex&atilde;o'
                    		WHEN MOTIVO_DET = 'RECON_MUD_END'
                    			THEN 'Mudan&ccedil;a de Endere&ccedil;o'
                    		WHEN MOTIVO_DET = 'RECON_PGTO_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico  - $'
                            WHEN MOTIVO_DET = 'REAB_PGTO_NC_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico - NC'
                            WHEN MOTIVO_DET = 'REAB_PGTO_NC_BAIXA_INAD' OR MOTIVO_DET = 'RECON_PGTO_NC_BAIXA_INAD'
                    			THEN 'Pagamento Corte F&iacute;sico - NC'
                    	ELSE '' END AS MOTIVO,        
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO_TOT 
                    WHERE DIA = SUBSTR(SYSDATE-1, 1,10) AND TIPO = '".$tipo."' AND MOV = 'A' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on();
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::baseAssinantesComSinalTipo()
     * 
     * Pega a base de assinantes com sinal de acordo com o tipo
     * 
     * @param $tipo Pode ser 'INDIVIDUAL' ou 'FILIADO'
     * 
     * @return Os dados
     */
    public function baseAssinantesComSinalTipo($tipo = 'INDIVIDUAL'){
        
        $sql = "SELECT
                    CASE 
                	WHEN PRODUTO = 'PTV'
                		THEN 'TV'
                	WHEN PRODUTO = 'CM'
                		THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	DIAS_ATRAS_5,
                	DIAS_ATRAS_4,
                	DIF_4,
                	DIF_PORC_4,
                	DIAS_ATRAS_3,
                	DIF_3,
                	DIF_PORC_3,
                	DIAS_ATRAS_2,
                	DIF_2,
                	DIF_PORC_2,
                	DIAS_ATRAS_1,
                	DIF_1,
                	DIF_PORC_1
                FROM SUPSIGA.V_SUPGXV_DASH_TOT_TIPO
                WHERE TIPO = '".$tipo."'
                ORDER BY
                CASE 
                	WHEN PRODUTO = 'TV'
                		THEN 1
                	WHEN PRODUTO = 'INTERNET'
                		THEN 2
                    WHEN PRODUTO = 'COMBO'
                		THEN 3
                ELSE 4 END";
                
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on();
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::baseAssinantesSemSinalTipo()
     * 
     * Pega a base de assinantes sem sinal de acordo com o tipo
     * 
     * @param $tipo Pode ser 'INDIVIDUAL' ou 'FILIADO'
     * 
     * @return Os dados
     */
    public function baseAssinantesSemSinalTipo($tipo = 'INDIVIDUAL'){
        
        /*$sql = "SELECT
                	PRODUTO,
                	SUM(DECODE (AGING_DESAB, 'ATE 5 DIAS', QTDE, NULL)) ate5dias,
                	SUM(DECODE (AGING_DESAB, 'DE 6 a 15 DIAS', QTDE, NULL)) de6a15dias,
                	SUM(DECODE (AGING_DESAB, 'DE 16 a 59 DIAS', QTDE, NULL)) de16a59dias,
                	SUM(DECODE (AGING_DESAB, 'ACIMA DE 60 DIAS', QTDE, NULL)) acima60dias
                FROM (
                	SELECT 
                	CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 'TV'
                		WHEN PRODUTO = 'CM'
                			THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	AGING_DESAB,
                	QTDE,
                    CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 1
                		WHEN PRODUTO = 'CM'
                			THEN 2
                        WHEN PRODUTO = 'COMBO'
                			THEN 3                            
                	ELSE 4 END AS ORDEM
                	FROM V_SUPGXV_DASH_TOT_AGING_TIPO
                	WHERE TIPO = '".$tipo."'
                	AND AGING_DESAB IS NOT NULL
                    AND TO_CHAR(dia, 'YYYY-MM-DD') = '".date('Y-m-d', strtotime("-1 days"))."'
                	ORDER BY AGING_DESAB
                )	
                GROUP BY PRODUTO, ORDEM
                ORDER BY ORDEM";*/
                
        $sql = "SELECT
                	PRODUTO,
                	SUM(DECODE (AGING_DESAB, 'ATE 5 DIAS', QTDE, NULL)) ate5dias,
                	SUM(DECODE (AGING_DESAB, 'DE 6 a 15 DIAS', QTDE, NULL)) de6a15dias,
                	SUM(DECODE (AGING_DESAB, 'DE 16 a 59 DIAS', QTDE, NULL)) de16a59dias,
                	SUM(DECODE (AGING_DESAB, 'ACIMA DE 60 DIAS', QTDE, NULL)) acima60dias
                FROM (
                	SELECT 
                	CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 'TV'
                		WHEN PRODUTO = 'CM'
                			THEN 'INTERNET'
                	ELSE PRODUTO END AS PRODUTO,
                	AGING_DESAB,
                	QTDE,
                    CASE 
                		WHEN PRODUTO = 'PTV'
                			THEN 1
                		WHEN PRODUTO = 'CM'
                			THEN 2
                        WHEN PRODUTO = 'COMBO'
                			THEN 3                            
                	ELSE 4 END AS ORDEM
                	FROM V_SUPGXV_DASH_TOT_AGING_TIPO
                	WHERE TIPO = '".$tipo."'
                	AND AGING_DESAB IS NOT NULL
                    AND TO_CHAR(dia, 'YYYY-MM-DD') = TO_CHAR(SYSDATE-1, 'YYYY-MM-DD')
                	ORDER BY AGING_DESAB
                )	
                GROUP BY PRODUTO, ORDEM
                ORDER BY ORDEM";
        
        $conexao = $this->load->database('oracle', TRUE);
        #$conexao->cache_on();
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::titulosPagos()
     * 
     * Pega o valor total de t�tulo pagos
     * 
     * @return Os dados
     */
    public function titulosPagos(){
        
        $sql = "SELECT 
                	VENCTO,
                	CASE 
                		WHEN TO_CHAR(VENCTO, 'MM') = '01'
                			THEN 'Jan'
                		WHEN TO_CHAR(VENCTO, 'MM') = '02'
                			THEN 'Fev'
                		WHEN TO_CHAR(VENCTO, 'MM') = '03'
                			THEN 'Mar'
                		WHEN TO_CHAR(VENCTO, 'MM') = '04'
                			THEN 'Abr'
                		WHEN TO_CHAR(VENCTO, 'MM') = '05'
                			THEN 'Mai'
                		WHEN TO_CHAR(VENCTO, 'MM') = '06'
                			THEN 'Jun'
                		WHEN TO_CHAR(VENCTO, 'MM') = '07'
                			THEN 'Jul'
                		WHEN TO_CHAR(VENCTO, 'MM') = '08'
                			THEN 'Ago'
                		WHEN TO_CHAR(VENCTO, 'MM') = '09'
                			THEN 'Set'
                		WHEN TO_CHAR(VENCTO, 'MM') = '10'
                			THEN 'Out'
                		WHEN TO_CHAR(VENCTO, 'MM') = '11'
                			THEN 'Nov'
                	ELSE 'Dez' END AS MES,
                	VLR_TOT,
                	VLR_PAGO,
                	VLR_PAGO_OUTROS 
                FROM V_SUPGXV_TIT_PAGO
                ORDER BY VENCTO";
                
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::reajustadosAssinantes()
     * 
     * Pega o quantidade de assinantes reajustados por m�s
     * 
     * @return Os dados
     */
    public function reajustadosAssinantes(){
        
        $sql = "SELECT 
                	MES_REAJ_ASS,
                	TO_CHAR(MES_REAJ_ASS, 'MM/YYYY') AS MES,
                    QTDE
                FROM SUPSIGA.V_SUPGXV_PROD_REAJU_1
                ORDER BY MES_REAJ_ASS";
                
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::metaCorrente()
     * 
     * Pega a meta estimada para o m�s corrente
     * 
     * @return Os dados
     */
    public function metaCorrente(){
        
        $sql = "SELECT 
                	numero
                FROM meta_dados 
                WHERE cd_meta_tipo = 4 AND SUBSTR(data,1,7) = SUBSTR(CURDATE(),1,7)";
        
        return $this->db->query($sql)->row()->numero;
        
    }
    
    /** NEW
     * Dashboard_model::comboMesesCobranca()
     * 
     * Pega os meses/anos da cobrança
     * 
     * @return Os dados
     */
    public function comboMesesCobranca(){
        
        $sql = "SELECT
                 TO_CHAR(EMFCHVTO, 'MM/YYYY') AS exibicao,
                 TO_CHAR(EMFCHVTO, 'MM-YYYY') AS valor
                FROM (
                	SELECT DISTINCT(TRUNC(EMFCHVTO,'MM')) EMFCHVTO FROM SUPSIGA.V_SUPGXV_ENVIO_2_A ORDER BY TRUNC(EMFCHVTO,'MM') DESC
                )";
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /** NEW
     * Dashboard_model::comboCicloCobranca()
     * 
     * Pega os ciclos da cobrança
     * 
     * @return Os dados
     */
    public function comboCicloCobranca($mesAno, $tipo){
        
        $sql = "SELECT 
                	DISTINCT TO_CHAR(EMFCHVTO, 'DD') AS ciclo
                FROM supsiga.V_SUPGXV_ENVIO_2_A 
                WHERE TO_CHAR(EMFCHVTO, 'MM-YYYY') = '".$mesAno."'
                AND MEDCOBTPO = '".$tipo."'";
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    public function bancosCobranca($mesAno, $tipo){
        
        $sql = "SELECT 
            	DISTINCT 
            	TRIM(BCODSC) AS BANCO_BD,
            	CASE 
            		WHEN TRIM(BCODSC) = 'BANCO BRADESCO S.A.'
            			THEN 'Bradesco'
            		WHEN TRIM(BCODSC) = 'BANCO DAYCOVAL'
            			THEN 'Daycoval'
            		WHEN TRIM(BCODSC) = 'BANCO DO BRASIL S.A.'
            			THEN 'BB'
            		WHEN TRIM(BCODSC) = 'BANCO HSBC S.A.'
            			THEN 'HSBC'
            		WHEN TRIM(BCODSC) = 'BANCO ITAU S.A.' OR TRIM(BCODSC) = 'BANCO ITAU SA'
            			THEN 'Itau'
            		WHEN TRIM(BCODSC) = 'BANCO SANTANDER S.A.'
            			THEN 'Santander'
            		WHEN TRIM(BCODSC) = 'CAIXA ECONOMICA FEDERAL'
            			THEN 'CEF'
            	ELSE 'Desconhecido' END AS BANCO
            FROM supsiga.V_SUPGXV_ENVIO_2_A
            WHERE TO_CHAR(EMFCHVTO, 'MM-YYYY') = '".$mesAno."'
            AND MEDCOBTPO = '".$tipo."'";
            
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    public function statusCobranca($mesAno, $ciclo, $tipo){
        
        $sql = "SELECT
                	ciclo,
                	SUM(DECODE (status, 'PAGO', VLR, 0)) pago,
               		SUM(DECODE (status, 'REGISTRADO', VLR, 0)) registrado,
                	SUM(DECODE (status, 'ENVIADO', VLR, 0)) enviado,
                    SUM(DECODE (status, 'NAO ENVIADO', VLR, 0)) nao_enviado,
                	SUM(DECODE (status, 'REJEITADO', VLR, 0)) rejeitado,
                	SUM(DECODE (status, 'NAO_GERADO', VLR, 0)) nao_gerado,
                		SUM(DECODE (status, 'NAO RETORNADO', VLR, 0)) nao_retornado
                FROM (
                	SELECT 
                    	CASE 
                    		WHEN TRIM(BCODSC) = 'BANCO BRADESCO S.A.'
                    			THEN 'Bradesco'
                    		WHEN TRIM(BCODSC) = 'BANCO DAYCOVAL'
                    			THEN 'Daycoval'
                    		WHEN TRIM(BCODSC) = 'BANCO DO BRASIL S.A.'
                    			THEN 'BB'
                    		WHEN TRIM(BCODSC) = 'BANCO HSBC S.A.'
                    			THEN 'HSBC'
                    		WHEN TRIM(BCODSC) = 'BANCO ITAU S.A.'
                    			THEN 'Itau'
                    		WHEN TRIM(BCODSC) = 'BANCO SANTANDER S.A.'
                    			THEN 'Santander'
                    		WHEN TRIM(BCODSC) = 'CAIXA ECONOMICA FEDERAL'
                    			THEN 'CEF'
                    	ELSE 'Desconhecido' END AS banco,
                		TO_CHAR(EMFCHVTO, 'DD') AS ciclo,
                		CASE 
    						WHEN status IS NULL
    					       THEN 'NAO_GERADO'
                		ELSE status END AS status,
                		qtde,
                		vlr
                	FROM supsiga.v_supgxv_envio_2_a
                	WHERE TO_CHAR(EMFCHVTO, 'MM-YYYY') = '".$mesAno."'
                	AND TO_CHAR(EMFCHVTO, 'DD') = ".$ciclo."
                    AND MEDCOBTPO = '".$tipo."'
                    /*AND MEDCOBTPO = 'B'  Debito em Conta */
                    /*AND MEDCOBTPO = 'O'  Boleto */
                    /*AND MEDCOBTPO = 'T'  Cartão */
                	--AND status IS NOT NULL
                )
                GROUP BY ciclo
                ORDER BY ciclo";
     
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    public function statusCicloCobranca($mesAno, $ciclo, $banco, $tipo){
        
        if($banco == ''){
            $whereBanco = "AND BCODSC IS NULL";
        }else{
            $whereBanco = "AND TRIM(BCODSC) = '".$banco."'";
        }
        
        $sql = "SELECT
                	ciclo,                	
                	banco,
                    --banco_bd,
                	SUM(DECODE (status, 'PAGO', VLR, 0)) pago,
                    SUM(DECODE (status, 'REGISTRADO', VLR, 0)) registrado,
                	SUM(DECODE (status, 'ENVIADO', VLR, 0)) enviado,
                    SUM(DECODE (status, 'NAO ENVIADO', VLR, 0)) nao_enviado,
                	SUM(DECODE (status, 'REJEITADO', VLR, 0)) rejeitado,
                	SUM(DECODE (status, 'NAO_GERADO', VLR, 0)) nao_gerado,
                		SUM(DECODE (status, 'NAO RETORNADO', VLR, 0)) nao_retornado
                FROM (
                	SELECT 
                			TRIM(BCODSC) AS banco_bd,
                			CASE 
                				WHEN TRIM(BCODSC) = 'BANCO BRADESCO S.A.'
                					THEN 'Bradesco'
                				WHEN TRIM(BCODSC) = 'BANCO DAYCOVAL'
                					THEN 'Daycoval'
                				WHEN TRIM(BCODSC) = 'BANCO DO BRASIL S.A.'
                					THEN 'BB'
                				WHEN TRIM(BCODSC) = 'BANCO HSBC S.A.'
                					THEN 'HSBC'
                				WHEN TRIM(BCODSC) = 'BANCO ITAU S.A.'
                					THEN 'Itau'
                				WHEN TRIM(BCODSC) = 'BANCO SANTANDER S.A.'
                					THEN 'Santander'
                				WHEN TRIM(BCODSC) = 'CAIXA ECONOMICA FEDERAL'
                					THEN 'CEF'
                			ELSE 'Desconhecido' END AS banco,
                		TO_CHAR(EMFCHVTO, 'DD') AS ciclo,
                		CASE 
                						WHEN status IS NULL
                					 THEN 'NAO_GERADO'
                		ELSE status END AS status,
                		qtde,
                		vlr
                	FROM supsiga.v_supgxv_envio_2_a
                	WHERE TO_CHAR(EMFCHVTO, 'MM-YYYY') = '".$mesAno."'
                	AND TO_CHAR(EMFCHVTO, 'DD') = ".$ciclo."
                		AND MEDCOBTPO = '".$tipo."'
                		".$whereBanco."
                		/*AND MEDCOBTPO = 'B'  Debito em Conta */
                		/*AND MEDCOBTPO = 'O'  Boleto */
                		/*AND MEDCOBTPO = 'T'  Cartão */
                	--AND status IS NOT NULL
                )
                GROUP BY ciclo, banco_bd, banco
                ORDER BY ciclo";
     
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::comboMesStatusBoleto()
     * 
     * Pega os meses/anos que possuem status
     * 
     * @return Os dados
     */
    public function comboMesStatusBoleto(){
        
        $sql = "SELECT
                 TO_CHAR(EMFCHVTO, 'MM/YYYY') AS exibicao,
                 TO_CHAR(EMFCHVTO, 'MM-YYYY') AS valor
                FROM (
                	SELECT DISTINCT(TRUNC(EMFCHVTO,'MM')) EMFCHVTO FROM SUPSIGA.V_SUPGXV_ENVIO_2_A ORDER BY TRUNC(EMFCHVTO,'MM') DESC
                )";
                
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::comboStatusCobranca()
     * 
     * Pega os meses/anos que possuem status
     * 
     * @return Os dados
     */
    public function comboStatusCobranca($tipo){
        
        $sql = "SELECT
                 TO_CHAR(EMFCHVTO, 'MM/YYYY') AS exibicao,
                 TO_CHAR(EMFCHVTO, 'MM-YYYY') AS valor
                FROM (
                	SELECT 
                        DISTINCT(TRUNC(EMFCHVTO,'MM')) EMFCHVTO 
                    FROM SUPSIGA.V_SUPGXV_ENVIO_2_A 
                    WHERE MEDCOBTPO = '".$tipo."'
                    ORDER BY TRUNC(EMFCHVTO,'MM') DESC
                )";
                
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::comboCicloStatusCobranca()
     * 
     * Pega os ciclos do m�s informado
     * 
     * @param $mesAno para consulta
     * 

     */
    public function comboCicloStatusCobranca($mesAno, $tipo){
        
        $sql = "SELECT 
                	DISTINCT TO_CHAR(EMFCHVTO, 'DD') AS ciclo
                FROM supsiga.v_supgxv_envio_2_A 
                WHERE TO_CHAR(EMFCHVTO, 'MM-YYYY') = '".$mesAno."'
                AND MEDCOBTPO = '".$tipo."'";
                
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
    * DadosBanco_model::unidade()
    * 
    * Fun��o que pega as unidades
    * @return Retorna as unidades
    */
	public function unidade($cd_unidade = null){
        
        if($cd_unidade != null){
            $this->db->where("permissor", $cd_unidade);
        }
        
        $this->db->where("status =  'A'");
        $this->db->where("cd_unidade NOT IN(7)");
        $this->db->order_by("nome", "asc"); 
        
		return $this->db->get('adminti.unidade')->result();
        
	}
    
    /**
     * Dashboard_model::pendenciasInstalacaoDezDias()
     * 
     * Pega as pend�ncias de instala��o dos �ltimos 10 dias
     * 
     * @return Os dados
     */    
    public function pendenciasInstalacaoDezDias($permissor = '0', $servico = '0'){
        
        /*$sql = "SELECT 
                    TO_CHAR(DATA, 'DD/MM') DIA_MES,
                    QTDE_VENDAS,
                    QTDE_INSTAL,
                    QTDE_CANCEL,
                    QTDE_PEND 
                FROM V_SUPGXV_VENDA_PRE_CAD_DASH_D";*/
        
        $condicao = array();
        
        if($permissor <> '0' and $permissor <> 'undefined'){
            $condicao[0] = 'COD_OPERADORA = '.$permissor;
        }
        
        if($servico <> '0' and $servico <> 'undefined'){
            $condicao[1] = "SERVICO = '".$servico."'";
        }
        
        $where = (count($condicao) > 0)? 'WHERE '.implode(' AND ', $condicao): '';

        $sql = "SELECT 
                	TO_CHAR(DATA, 'DD/MM') DIA_MES,
                	SUM(QTDE_VENDAS) AS QTDE_VENDAS,
                	SUM(QTDE_INSTAL) AS QTDE_INSTAL,
                	SUM(QTDE_CANCEL) AS QTDE_CANCEL,
                	SUM(QTDE_PEND) AS QTDE_PEND
                FROM V_SUPGXV_VENDAPRECADPRD_DASH_D
                ".$where."
                GROUP BY DATA
                ORDER BY DATA";
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::pendenciasInstalacaoMesAmes()
     * 
     * Pega as pend�ncias de instala��o m�s � m�s
     * 
     * @return Os dados
     */    
    public function pendenciasInstalacaoMesAmes($permissor = '0', $servico = '0'){
        
        /*$sql = "SELECT 
                	CASE 
                		WHEN TO_CHAR(DATA, 'MM') = '01'
                			THEN 'Jan/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '02'
                			THEN 'Fev/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '03'
                			THEN 'Mar/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '04'
                			THEN 'Abr/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '05'
                			THEN 'Mai/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '06'
                			THEN 'Jun/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '07'
                			THEN 'Jul/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '08'
                			THEN 'Ago/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '09'
                			THEN 'Set'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '10'
                			THEN 'Out/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '11'
                			THEN 'Nov/'||TO_CHAR(DATA, 'YY')
                	ELSE 'Dez/'||TO_CHAR(DATA, 'YY') END AS MES_ANO,
                	QTDE_VENDAS,
                	QTDE_INSTAL,
                	QTDE_CANCEL,
                	QTDE_PEND 
                FROM V_SUPGXV_VENDA_PRE_CAD_DASH_M";*/
                
        $condicao = array();
        
        if($permissor <> '0' and $permissor <> 'undefined'){
            $condicao[0] = 'COD_OPERADORA = '.$permissor;
        }
        
        if($servico <> '0' and $servico <> 'undefined'){
            $condicao[1] = "SERVICO = '".$servico."'";
        }
        
        $where = (count($condicao) > 0)? 'WHERE '.implode(' AND ', $condicao): '';
                
        $sql = "SELECT 
                	CASE 
                		WHEN TO_CHAR(DATA, 'MM') = '01'
                			THEN 'Jan/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '02'
                			THEN 'Fev/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '03'
                			THEN 'Mar/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '04'
                			THEN 'Abr/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '05'
                			THEN 'Mai/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '06'
                			THEN 'Jun/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '07'
                			THEN 'Jul/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '08'
                			THEN 'Ago/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '09'
                			THEN 'Set'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '10'
                			THEN 'Out/'||TO_CHAR(DATA, 'YY')
                		WHEN TO_CHAR(DATA, 'MM') = '11'
                			THEN 'Nov/'||TO_CHAR(DATA, 'YY')
                	ELSE 'Dez/'||TO_CHAR(DATA, 'YY') END AS MES_ANO,
                	SUM(QTDE_VENDAS) AS QTDE_VENDAS,
                	SUM(QTDE_INSTAL) AS QTDE_INSTAL,
                	SUM(QTDE_CANCEL) AS QTDE_CANCEL,
                	SUM(QTDE_PEND) AS QTDE_PEND
                FROM V_SUPGXV_VENDAPRECADPRD_DASH_M
                ".$where."
                GROUP BY DATA
                ORDER BY DATA";
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::telefonia()
     * 
     * Dados da telefonia 1
     * 
     * @return Os dados
     */   
    public function telefonia1(){
        
        /*$sql = 'SELECT 
                	src,
                	dst,
                	dstchannel,
                	billsec,
                	disposition
                FROM cdr
                WHERE
                disposition = \'ANSWERED\'
                AND dst RLIKE "^[2-5][0-9]{7}" # FIXO
                #AND dst RLIKE "^[6-9][0-9]{8}" # CELULAR
                AND DATE_FORMAT(calldate, \'%Y-%m-%d\') >= ADDDATE(CURDATE(), INTERVAL -7 DAY)
                AND DATE_FORMAT(calldate, \'%Y-%m-%d\') <= CURDATE()';*/
                
        $sql = 'SELECT 
                	COUNT(*) AS qtd_liga_fixo,
                	SUM(pri.billsec)/60 AS segundos_fixo, #minutos
                (
                SELECT 
                	COUNT(*) AS qtd_liga_celular
                FROM cdr AS sec
                WHERE
                sec.disposition = \'ANSWERED\'
                AND sec.dst RLIKE "^[6-9][0-9]{8}" # CELULAR
                AND SUBSTR(sec.calldate, 1, 10) = SUBSTR(pri.calldate, 1, 10)
                ) AS qtd_liga_celular,
                (
                SELECT 
                	SUM(billsec)/60 AS segundos_fixo #minutos
                FROM cdr AS sec
                WHERE
                sec.disposition = \'ANSWERED\'
                AND sec.dst RLIKE "^[6-9][0-9]{8}" # CELULAR
                AND SUBSTR(sec.calldate, 1, 10) = SUBSTR(pri.calldate, 1, 10)
                ) AS segundos_celular,
                DATE_FORMAT(pri.calldate, \'%d/%m/%Y\') AS data
                FROM cdr AS pri
                WHERE
                pri.disposition = \'ANSWERED\'
                AND pri.dst RLIKE "^[2-5][0-9]{7}" # FIXO
                #AND dst RLIKE "^[6-9][0-9]{8}" # CELULAR
                AND DATE_FORMAT(pri.calldate, \'%Y-%m-%d\') >= ADDDATE(CURDATE(), INTERVAL -100 DAY)
                AND DATE_FORMAT(pri.calldate, \'%Y-%m-%d\') <= CURDATE()
                GROUP BY DATE_FORMAT(pri.calldate, \'%d/%m/%Y\')
                ORDER BY pri.calldate';
        
        $conexao = $this->load->database('telefonia', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    
    public function telefonia2(){
        
        $sql = 'SELECT
                	COUNT(*) AS qtd,
                	ROUND(SUM(pri.billsec)/60,2) AS tempo, 
                	nome_departamento AS departamento
                FROM adminti.telefonia_cdr AS pri
                INNER JOIN adminti.telefonia_ramal ON pri.src = numero
                INNER JOIN adminti.telefonia_ramal_usuario ON telefonia_ramal_usuario.cd_telefonia_ramal = telefonia_ramal.cd_telefonia_ramal
                INNER JOIN adminti.usuario ON usuario.cd_usuario = telefonia_ramal_usuario.cd_usuario
                INNER JOIN adminti.departamento ON usuario.cd_departamento = departamento.cd_departamento
                WHERE
                	#pri.dst RLIKE "^[2-5][0-9]{7}" # FIXO
                	pri.disposition = \'ANSWERED\'
                	AND DATE_FORMAT(pri.calldate, \'%Y-%m-%d\') >= ADDDATE(CURDATE(), INTERVAL -360 DAY)
                  AND DATE_FORMAT(pri.calldate, \'%Y-%m-%d\') <= CURDATE()
                GROUP BY nome_departamento
                HAVING COUNT(*) > 0
                #HAVING SUM(pri.billsec) > 0
                ORDER BY pri.calldate';
              
        return $this->db->query($sql)->result();  
        
    }
    
    /**
     * Dashboard_model::telefonia3()
     * 
     * Call Center chamadas detalhadas
     * 
     * @return Os dados
     */ 
    public function telefonia3($tipo = 'detalhado'){
        
        if($tipo == 'detalhado'){
            #$condicao = 'inicio >= ADDDATE(CURDATE(), INTERVAL -30 DAY) AND inicio <= CURDATE()';
            $condicao = ' inicio >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY) AND inicio <= CURDATE()';            
        }elseif($tipo == 'consolidado'){
            $condicao = ' DATE_SUB(inicio, INTERVAL DAYOFMONTH(inicio)-1 DAY) >= ADDDATE(CURDATE(), INTERVAL -12 MONTH) AND inicio <= CURDATE()';
        }
        
        $sql = "SELECT 
                \n	DATE_FORMAT(fim, '%m/%Y') AS mes_ano,
                	tipo,
                    CASE 
                    	WHEN tipo = 'celularLocal'
                    		THEN 'Celular Local'
                    	WHEN tipo = 'fixoLDN'
                    		THEN 'Fixo DDD'
                    	WHEN tipo = 'celularLDN'
                    		THEN 'Celular DDD'
                    ELSE 'Fixo local' END AS titulo,
            		CASE 
            			WHEN tipo = 'Celular'        
            				THEN 'celular'
            			WHEN tipo = 'DDD'       
        				    THEN 'fixoddd'
                        WHEN tipo = 'DDDCel'    
            				THEN 'celularddd'
                    ELSE 'fixo' END AS tipoligacao,                    
                	COUNT(*) AS qtd,
                \n	ROUND(SUM(segundos) / 60, 2) AS minutos,
                		SUM(segundos) AS segundos,
                  ROUND(SUM(custo), 2) AS custo
            \n    FROM adminti.telefonia_chamadas
                WHERE
            \n    ".$condicao."
                GROUP BY tipo, DATE_FORMAT(inicio, '%m/%Y')
            \n    ORDER BY inicio";
        #echo '<pre>'; print_r($sql); exit();
        return $this->db->query($sql)->result();  
        
    }
    
    /**
     * Dashboard_model::telefonia3()
     * 
     * Call Center chamadas consolidado
     * 
     * @return Os dados
     */
    public function telefonia4(){
        
        $sql = "SELECT 
                	DATE_FORMAT(inicio, '%m/%Y') AS mes_ano,
                	COUNT(*) AS qtd,
                	ROUND(SUM(segundos) / 60, 2) AS minutos,
                	SUM(segundos) AS segundos
                FROM telefonia_chamadas
                WHERE
                DATE_SUB(inicio, INTERVAL DAYOFMONTH(inicio)-1 DAY) >= ADDDATE(CURDATE(), INTERVAL -12 MONTH)
                AND inicio <= CURDATE()
                GROUP BY DATE_FORMAT(inicio, '%m/%Y')
                ORDER BY DATE_FORMAT(inicio, '%m/%Y')";
        
        return $this->db->query($sql)->result(); 
        
    }
    
    /**
     * Dashboard_model::telefonia5()
     * 
     * Call Center chamadas detalhadas
     * 
     * @return Os dados
     */ 
    public function telefonia5($periodo = 'N', $zoom = 'N', $fonte = 'ATIVO'){
        
        #$condicao = ' fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-4 DAY) AND fim <= DATE_SUB(ADDDATE(CURDATE(), INTERVAL +1 MONTH), INTERVAL DAYOFMONTH(ADDDATE(CURDATE(), INTERVAL +1 MONTH))-3 DAY)';
        
        # Sem Zoom
        #$condicao = ' fim >= ADDDATE(CURDATE(), INTERVAL -7 DAY)';
        
        # Com Zoom
        #$condicao = ' fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY)';
        
        if($periodo == 'N'){
            if($zoom == 'in'){
                $condicao = ' fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE()';
            }else{
                #$condicao = ' fim >= ADDDATE(CURDATE(), INTERVAL -7 DAY)'; 
                $condicao = ' fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE()';
            }
        }else{
            $data = explode(' ', $this->util->telefoniaPeriodo($periodo));
            $condicao = " fim BETWEEN '".$data[0]."' AND '".$data[1]."'";
        }
        
        if($fonte == 'ATIVO'){
            $condiFonte = "AND fonte IN ('CALLCENTER - ATIVO', 'SERVIDOR ASTERISK') ";
        }elseif($fonte == 'RECEPTIVO'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO') ";
        }else{
            $condiFonte = "";
        }
        
        $sql = "SELECT 
                \n	#DATE_FORMAT(fim, '%d/%m') AS mes_ano,
                    DATE_FORMAT(fim, '%d/%c') AS mes_ano,
                    CASE 
                    	WHEN tipo = 'celularLocal'
                    		THEN 'Celular Local'
                    	WHEN tipo = 'fixoLDN'
                    		THEN 'Fixo DDD'
                    	WHEN tipo = 'celularLDN'
                    		THEN 'Celular DDD'
                    ELSE 'Fixo local' END AS titulo,                  
                	COUNT(*) AS qtd,
                \n	ROUND(SUM(segundos) / 60, 2) AS minutos,
                		SUM(segundos) AS segundos,
                  ROUND(SUM(custo), 2) AS custo,
                  fonte
            \n    FROM adminti.telefonia_chamadas
                INNER JOIN adminti.log_arquivo ON adminti.log_arquivo.cd_log_arquivo = adminti.telefonia_chamadas.cd_log_arquivo
                WHERE
            \n     #fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE()
            #fim BETWEEN '2015-09-03' AND '2015-10-02'
                ".$condicao."
                ".$condiFonte."
                GROUP BY DATE_FORMAT(fim, '%d/%m/%Y'),fonte
            \n    ORDER BY fim";
        #echo '<pre>'; print_r($sql); exit();
        return $this->db->query($sql)->result();  
        
    }
    
    public function telefoniaCustoTotal($periodo = 'N', $zoom = 'N', $fonte = 'ATIVO'){
        
        if($periodo == 'N'){
            if($zoom == 'in'){
                $condicao = ' fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE() ';
            }else{
                #$condicao = ' fim >= ADDDATE(CURDATE(), INTERVAL -7 DAY)'; 
                $condicao = ' fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE() ';
            }
        }else{
                                    
            $data = explode(' ', $this->util->telefoniaPeriodo($periodo));
                                    
            $condicao = " fim BETWEEN '".$data[0]."' AND '".$data[1]."'";
        }
        
        if($fonte == 'ATIVO'){
            $condiFonte = "AND fonte IN ('CALLCENTER - ATIVO', 'SERVIDOR ASTERISK') ";
        }elseif($fonte == '0800'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 0800') ";
        }elseif($fonte == '4004'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 4004') ";
        }else{
            $condiFonte = "";
        }
                                
        $sql = "SELECT 
                		ROUND(SUM(custo), 2) AS custo_total
                FROM adminti.telefonia_chamadas
                INNER JOIN adminti.log_arquivo ON adminti.log_arquivo.cd_log_arquivo = adminti.telefonia_chamadas.cd_log_arquivo
                WHERE
                ".$condicao." 
                ".$condiFonte."                              
                #fim BETWEEN '2015-09-03' AND '2015-10-02'
                #fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE()";
                                                
        return $this->db->query($sql)->result();
        
    }
    
    public function diasMesesLigacao($mes = 'N', $fonte = 'ATIVO', $dataMenos1 = 'nao'){
        
        if($mes == 'N'){
            $mes = date('m-Y');
        }
        
        $data = explode(' ', $this->util->telefoniaPeriodo($mes));
                                    
        $condicao = " fim BETWEEN '".$data[0]."' AND '".$data[1]."'";
        
        if($fonte == 'ATIVO'){
            $condiFonte = "AND fonte IN ('CALLCENTER - ATIVO', 'SERVIDOR ASTERISK') ";
        }elseif($fonte == '0800'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 0800') ";
        }elseif($fonte == '4004'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 4004') ";
        }else{
            $condiFonte = "";
        }
        
        if($dataMenos1 == 'sim'){
            $condicaoData = " AND fim < CURDATE() ";
        }else{
            $condicaoData = "";
        }
        
        $sql = "SELECT 
      		        DISTINCT
      		        DATE_FORMAT(fim, '%d/%c') AS data_formatada,
                    DATE_FORMAT(fim, '%d-%m-%Y') AS data_parametro
                FROM adminti.telefonia_chamadas
                INNER JOIN adminti.log_arquivo ON adminti.log_arquivo.cd_log_arquivo = adminti.telefonia_chamadas.cd_log_arquivo
                WHERE
                ".$condicao."
                ".$condiFonte."
                ".$condicaoData."
                #fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE() 
                #AND fonte IN ('CALLCENTER - RECEPTIVO')
                ORDER BY fim";
                
        return $this->db->query($sql)->result();
        
    }
    
    public function dadosTipoLigacao($dia = null, $fonte = 'holding', $tipo = "'celularLDN', 'celularLocal'"){
        
        #$data = explode(' ', $this->util->telefoniaPeriodo($mes));
                                    
        #$condicao = " fim BETWEEN '".$data[0]."' AND '".$data[1]."'";
        
        if($fonte == 'holding'){
            $condiFonte = "AND fonte IN ('SERVIDOR ASTERISK') ";
        }elseif($fonte == 'callcenter'){
            $condiFonte = "AND fonte IN ('CALLCENTER - ATIVO') ";
        }elseif($fonte == '0800'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 0800') ";
        }elseif($fonte == '4004'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 4004') ";
        }else{
            $condiFonte = "";
        }
        
        $sql = "SELECT 
                	COUNT(*) AS qtd,
                	ROUND(SUM(segundos) / 60, 2) AS minutos,
                	ROUND(SUM(custo), 2) AS custo
                FROM adminti.telefonia_chamadas
                INNER JOIN adminti.log_arquivo ON adminti.log_arquivo.cd_log_arquivo = adminti.telefonia_chamadas.cd_log_arquivo
                WHERE
                DATE_FORMAT(fim, '%d-%m-%Y') = '".$dia."'
                ".$condiFonte."
                AND tipo IN (".$tipo.")
                #fim >= DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-3 DAY) and fim < CURDATE() 
                #fim BETWEEN '2015-10-03' AND '2015-11-03'
                #AND tipo IN ('celularLDN', 'celularLocal')
                #AND tipo IN ('fixoLocal', 'fixoLDN')
                #AND fonte IN ('CALLCENTER - RECEPTIVO') ";
                
        return $this->db->query($sql)->result();
        
    }
    
    public function dashboardTelefoniaAtivo($mes){ 
        
        if($mes == 'N'){
            $mes = date('m-Y');
        }
        
        $data = explode(' ', $this->util->telefoniaPeriodo($mes));
        
        $sql = "SELECT 
                	data,
                	data_banco,
                	MAX(IF(fonte = 'SERVIDOR ASTERISK', qtd, 0)) AS 'holding_qtd',
                	MAX(IF(fonte = 'SERVIDOR ASTERISK', minutos, 0)) AS 'holding_minutos',
                	MAX(IF(fonte = 'SERVIDOR ASTERISK', custo, 0)) AS 'holding_custo',
                	MAX(IF(fonte = 'CALLCENTER - ATIVO', qtd, 0)) AS 'callcenter_qtd',
                	MAX(IF(fonte = 'CALLCENTER - ATIVO', minutos, 0)) AS 'callcenter_minutos',
                	MAX(IF(fonte = 'CALLCENTER - ATIVO', custo, 0)) AS 'callcenter_custo'
                FROM (
                SELECT
                	DATE_FORMAT(fim, '%d/%c') AS data,
                	DATE_FORMAT(fim, '%Y-%m-%d') AS data_banco,
                	COUNT(*) AS qtd,
                	ROUND(SUM(segundos) / 60, 2) AS minutos,
                	ROUND(SUM(custo), 2) AS custo,
                	fonte
                FROM adminti.telefonia_chamadas
                INNER JOIN adminti.log_arquivo ON log_arquivo.cd_log_arquivo = telefonia_chamadas.cd_log_arquivo
                WHERE 1=1 
                AND fonte IN ('SERVIDOR ASTERISK', 'CALLCENTER - ATIVO') 
                AND fim BETWEEN '".$data[0]."' AND '".$data[1]."'
                GROUP BY 
                	DATE_FORMAT(fim, '%d/%c'), 
                	DATE_FORMAT(fim, '%Y-%m-%d'),
                	fonte 
                ) AS res
                GROUP BY 
                	data,
                	data_banco
                ORDER BY res.data_banco";
                    
        return $this->db->query($sql)->result();
        
    }
    
    public function dashboardTelefoniaReceptivo($mes, $fonte){ 
        
        if($mes == 'N'){
            $mes = date('m-Y');
        }
        
        $data = explode(' ', $this->util->telefoniaPeriodo($mes));
        
        if($fonte == '0800'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 0800') ";
        }elseif($fonte == '4004'){
            $condiFonte = "AND fonte IN ('CALLCENTER - RECEPTIVO - 4004') ";
        }else{
            $condiFonte = "";
        }
        
        $sql = "SELECT 
                	data,
                	MAX(IF(tipo = 'Celular', qtd, 0)) AS 'celular_qtd',
                	MAX(IF(tipo = 'Celular', minutos, 0)) AS 'celular_minutos',
                	MAX(IF(tipo = 'Celular', custo, 0)) AS 'celular_custo',
                	MAX(IF(tipo = 'Fixo Local', qtd, 0)) AS 'fixo_local_qtd',
                	MAX(IF(tipo = 'Fixo Local', minutos, 0)) AS 'fixo_local_minutos',
                	MAX(IF(tipo = 'Fixo Local', custo, 0)) AS 'fixo_local_custo',
                	MAX(IF(tipo = 'Fixo LDN', qtd, 0)) AS 'fixo_LDN_qtd',
                	MAX(IF(tipo = 'Fixo LDN', minutos, 0)) AS 'fixo_LDN_minutos',
                	MAX(IF(tipo = 'Fixo LDN', custo, 0)) AS 'fixo_LDN_custo'
                FROM (
                SELECT
                	DATE_FORMAT(fim, '%d/%c') AS data,
                	COUNT(*) AS qtd,
                	ROUND(SUM(segundos) / 60, 2) AS minutos,
                	ROUND(SUM(custo), 2) AS custo,
                	CASE 
                		WHEN tipo IN ('celularLDN', 'celularLocal')
                			THEN 'Celular'
                		WHEN tipo IN ('fixoLocal')
                			THEN 'Fixo Local'
                	ELSE 'Fixo LDN' END AS tipo
                FROM adminti.telefonia_chamadas
                INNER JOIN adminti.log_arquivo ON log_arquivo.cd_log_arquivo = telefonia_chamadas.cd_log_arquivo
                WHERE 1=1 
                ".$condiFonte."
                AND fim BETWEEN '".$data[0]."' AND '".$data[1]."'
                GROUP BY 
                	DATE_FORMAT(fim, '%d/%c'), 
                	CASE 
                		WHEN tipo IN ('celularLDN', 'celularLocal')
                			THEN 'Celular'
                		WHEN tipo IN ('fixoLocal')
                			THEN 'Fixo Local'
                	ELSE 'Fixo LDN' END
                ) AS res
                GROUP BY 
                	data";
                    
        return $this->db->query($sql)->result();
        
    }
    

}