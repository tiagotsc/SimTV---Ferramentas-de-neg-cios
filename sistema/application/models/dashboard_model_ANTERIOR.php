<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza consultas genéricas no banco
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
    * Função que pega os anos do retorno
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
    * Função que pega a quantidade de títulos baixados no mês
    * 
    * @param $mes Mês para filtrar a consulta
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
    * Função que pega a soma dos títulos baixados no mês
    * 
    * @param $mes Mês para filtrar a consulta
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
    * Pega todos os meses/ano da rentabilitação para montar a combo
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
        
        $conexao = $this->load->database('siga_bcv', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
    *
    * Dashboard_model::rentabilizacaoTela1() 
    * 
    * Pega os dados a tela 1 da rentabilização
    * 
    * @param $mesAno Mês/ano que será buscado
    * 
    * @return Retorna os dados referentes ao mês e ano
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
    * Pega os dados a tela 2 da rentabilização
    * 
    * @param $mesAno Mês/ano que será buscado
    * 
    * @return Retorna os dados referentes ao mês e ano
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
    * Pega os dados a tela 3 da rentabilização
    * 
    * @param $mesAno Mês/ano que será buscado
    * 
    * @return Retorna os dados referentes ao mês e ano
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
     * Pega os dashboard que o usuário tem permissão para acessar
     * 
     * @param mixed $permissoes Permissões do perfil do usuário
     * @return
     */
    public function dashboardPermitidos($permissoes){
        
        $this->db->select('cd_grafico');
        $this->db->where('status_grafico', 'A');
        $this->db->where('cd_permissao IN ('.implode(',',$permissoes).')');
        $this->db->order_by("nome_grafico", "asc"); 
        
		return $this->db->get('grafico')->result();
        
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
                		AND TO_CHAR(dia, 'YYYY-MM-DD') = '".date('Y-m-d', strtotime("-1 days"))."'
                	ORDER BY AGING_DESAB
                )	
                GROUP BY PRODUTO,ORDEM
                ORDER BY ORDEM";
                
        $conexao = $this->load->database('oracle', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBase()
     * 
     * Pega as saídas das movimentações da base consolidada
     * 
     * @return Os dados
     */
    public function movimentacaoBaseConsolidadoSaida(){
        
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
                    	ELSE '' END AS MOTIVO, 
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO 
                    WHERE DIA = '".strtoupper(date("d-M-y", strtotime("-2 days")))."' AND MOV = 'D' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBase()
     * 
     * Pega as entradas das movimentações da base consolidada
     * 
     * @return Os dados
     */
    public function movimentacaoBaseConsolidadoEntrada(){
        
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
                    			THEN 'Pagamento Corte Fisico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_CORTE_MANUAL'
                    			THEN 'Cancelamento Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico'
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
                    			THEN 'Pagamento Corte Fisico'
                    	ELSE '' END AS MOTIVO,
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO 
                    WHERE DIA = '".strtoupper(date("d-M-y", strtotime("-1 days")))."' AND MOV = 'A' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBase()
     * 
     * Pega as saídas das movimentações da base consolidada
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
                    	ELSE '' END AS MOTIVO, 
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO_TOT 
                    WHERE DIA = '".strtoupper(date("d-M-y", strtotime("-2 days")))."' AND TIPO = '".$tipo."' AND MOV = 'D' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::movimentacaoBaseConsolidadoEntradaTipo()
     * 
     * Pega as entradas das movimentações da base de acordo com o tipo
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
                    			THEN 'Pagamento Corte Fisico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_INAD'
                    			THEN 'Cancelamento Corte F&iacute;sico'
                    		WHEN MOTIVO_DET = 'REAB_CANC_BAIXA_OPCAO'
                    			THEN 'Cancelamento Baixa por Op&ccedil;&atilde;o'
                    		WHEN MOTIVO_DET = 'REAB_CANC_CORTE_MANUAL'
                    			THEN 'Cancelamento Corte L&oacute;gico'
                    		WHEN MOTIVO_DET = 'REAB_PGTO_CORTE_LOG'
                    			THEN 'Pagamento Corte L&oacute;gico'
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
                    			THEN 'Pagamento Corte Fisico'
                    	ELSE '' END AS MOTIVO,        
                    	SUM(qtde) AS QTD FROM SUPSIGA.V_SUPGXV_DASH_DIF_MOTIVO_TOT 
                    WHERE DIA = '".strtoupper(date("d-M-y", strtotime("-1 days")))."' AND TIPO = '".$tipo."' AND MOV = 'A' GROUP BY MOTIVO_DET
                ) GROUP BY MOTIVO";
        
        $conexao = $this->load->database('oracle', TRUE);
        
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
                    AND TO_CHAR(dia, 'YYYY-MM-DD') = '".date('Y-m-d', strtotime("-1 days"))."'
                	ORDER BY AGING_DESAB
                )	
                GROUP BY PRODUTO, ORDEM
                ORDER BY ORDEM";
        
        $conexao = $this->load->database('oracle', TRUE);
        
        return $conexao->query($sql)->result();
        
    }
    
    /**
     * Dashboard_model::titulosPagos()
     * 
     * Pega o valor total de título pagos
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
     * Pega o quantidade de assinantes reajustados por mês
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

}