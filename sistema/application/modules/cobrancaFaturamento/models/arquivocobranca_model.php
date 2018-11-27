<?php
/**
 * ArquivoCobranca_model
 * 
 * Classe que manipula a toda a base relacionada a cobraça
 * 
 * @package   
 * @author Tiago Silva Costa
 * @copyright Boomer
 * @version 2014
 * @access public
 */
class ArquivoCobranca_model extends CI_Model{
    
	/**
	 * ArquivoCobranca_model::__construct()
	 * 
	 * @return
	 */
	/**
	 * ArquivoCobranca_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
		$this->load->library('Util', '', 'util');
	}
    
    /**
     * ArquivoCobranca_model::bancoArquivo()
     * 
     * @param mixed $condicao
     * @return
     */
    /*public function bancoArquivo($condicao = null){
        
        if($condicao){
            $this->db->where('cd_banco_arquivo = '.$condicao);
        }
        
        return $this->db->get('banco_arquivo')->result(); # TRANSFORMA O RESULTADO EM OBJETO                
		
	}*/
    
    /*
    TESTE COM CONEXÃO DO ORACLE
    */
    /**
     * ArquivoCobranca_model::teste_oracle()
     * 
     * @return
     */
    public function teste_oracle(){

        $DB1 = $this->load->database('oracle', TRUE);
        
        $sql = "(SELECT
                'RJOP' AS nome,
                (SELECT COUNT(*) FROM supsiga.V_SUPGXV_ASS_DESC_D WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') AND COD_OPERADORA IN (71,72,73,74) AND PRODUTO = 'PTV') as qtd_tv,
                (SELECT COUNT(*) FROM supsiga.V_SUPGXV_ASS_DESC_D WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') AND COD_OPERADORA IN (71,72,73,74) AND PRODUTO = 'CM') as qtd_cm,
                (SELECT COUNT(*) FROM supsiga.V_SUPGXV_ASS_DESC_D WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') AND COD_OPERADORA IN (71,72,73,74) AND PRODUTO = 'COMBO') as qtd_combo
                FROM supsiga.V_SUPGXV_ASS_DESC_D
                WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') and COD_OPERADORA IN (71,72,73,74))                                                                                                                                                                 
                UNION
                (SELECT
                to_char(OPERADORA) AS nome,                                                                                                                                                                      
                (SELECT COUNT(*) FROM supsiga.V_SUPGXV_ASS_DESC_D sse WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') AND ppr.COD_OPERADORA = sse.COD_OPERADORA AND PRODUTO = 'PTV') as qtd_tv,
                (SELECT COUNT(*) FROM supsiga.V_SUPGXV_ASS_DESC_D sse WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') AND ppr.COD_OPERADORA = sse.COD_OPERADORA AND PRODUTO = 'CM') as qtd_cm,
                (SELECT COUNT(*) FROM supsiga.V_SUPGXV_ASS_DESC_D sse WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY') AND ppr.COD_OPERADORA = sse.COD_OPERADORA AND PRODUTO = 'COMBO') as qtd_combo
                FROM supsiga.V_SUPGXV_ASS_DESC_D ppr
                WHERE DT_DESC_ASS BETWEEN TO_DATE('01/05/14', 'DD/MM/YY')  and TO_DATE('02/06/14', 'DD/MM/YY'))";
        $DB1->query($sql);
        $resultado = $DB1->query($sql)->result();
        foreach($resultado as $res){
            
            echo html_entity_decode($res->NOME); echo '<br>';
            
        }
         exit();
        
    }
    
    /**
     * ArquivoCobranca_model::gravaNomeArquivoRetorno()
     * 
     * Grava o nome do arquivo
     * 
     * @param mixed $nome Nome do arquivo que será gravado
     * @param mixed $tipoBoleto Tipos de boletos que serão gravados
     * @param mixed $empresa Sigla da empresa que será grava
     * @param mixed $cdBanco Cd do banco que será gravado
     * 
     * @return O id do arquivo gravado
     */
    /**
     * ArquivoCobranca_model::gravaNomeArquivoRetorno()
     * 
     * @param mixed $nome
     * @param mixed $tipoBoleto
     * @param mixed $empresa
     * @param mixed $cdBanco
     * @return
     */
    public function gravaNomeArquivoRetorno($nome, $tipoBoleto = null, $empresa = null, $cdBanco){
		
        $tipoBoleto = (empty($tipoBoleto))? 'SEM TIPO BOLETO' : "'".$tipoBoleto."'";
        $empresa = (empty($empresa))? "'SEM EMPRESA'" : "'".$empresa."'";
        
		$sql = "INSERT INTO arquivo_retorno (
                    nome_arquivo_retorno, 
                    tipo_arquivo_retorno, 
                    nome_empresa_arquivo_retorno,
                    cd_banco
                )\n VALUES('".$nome."', ".$tipoBoleto.", ".$empresa.", ".$cdBanco.");";
		$this->db->query($sql);

		#return $this->db->affected_rows(); # RETORNA O NÚMERO DE LINHAS AFETADAS     
        
        if($this->db->insert_id()){
            return $this->db->insert_id();
        }else{
            return false;
        }
                
    }
    
    /**
     * ArquivoCobranca_model::registraDadosHeaderArquivo()
     * 
     * Grava dos dados do header do arquivo
     * 
     * @param mixed $idArquivo Id do arquivo para realizar atualização
     * @param mixed $dataArquivo Data do arquivo de retorno
     * 
     */
    /**
     * ArquivoCobranca_model::registraDadosHeaderArquivo()
     * 
     * @param mixed $idArquivo
     * @param mixed $dataArquivo
     * @return
     */
    public function registraDadosHeaderArquivo($idArquivo, $dataArquivo){
        
        if(strlen($dataArquivo) == 6){
        
            $dia = substr($dataArquivo, 0, 2);
            $mes = substr($dataArquivo, 2, 2);
            $ano = (strlen(substr($dataArquivo, 4)) == 2)? '20'.substr($dataArquivo, 4): substr($dataArquivo, 4);
        
        }else{
            
            $dia = substr($dataArquivo, 6, 2);
            $mes = substr($dataArquivo, 4, 2);
            $ano = substr($dataArquivo, 0, 4);
            
        }
        
        $sql = "UPDATE arquivo_retorno SET data_arquivo_retorno = '".$ano."-".$mes."-".$dia."' WHERE cd_arquivo_retorno = ".$idArquivo;
        $this->db->query($sql);
        
    }
    /*
    public function todosArquivos(){
        
        $this->db->join('banco_arquivo', 'banco_arquivo.cd_banco_arquivo = arquivo.cd_banco_arquivo');
        $this->db->order_by("data_arquivo", "desc"); 
		return $this->db->get('arquivo')->result();
        
    }
*/    
    /**
     * ArquivoCobranca_model::dadosArquivoRetorno()
     * 
     * Pega os dados dos arquivos
     * 
     * @param mixed $cdArquivo
     * @return
     */
    public function dadosArquivoRetorno($cdArquivo){
        
        $this->db->join('banco', 'banco.cd_banco = arquivo_retorno.cd_banco');
        $this->db->where('cd_arquivo_retorno', $cdArquivo);
        #$this->db->order_by("data_arquivo", "asc"); 
		return $this->db->get('arquivo_retorno')->result();
        
    }
/*    
    public function buscaBoleto(){
        
        #$this->db->join('banco_arquivo', 'banco_arquivo.cd_banco_arquivo = arquivo.cd_banco_arquivo');
        $this->db->where('cd_arquivo', $this->input->post('cd_arquivo'));
        $this->db->where('boleto_conteudo_arquivo', $this->input->post('boleto'));
        $this->db->order_by("cd_conteudo_arquivo", "asc"); 
		return $this->db->get('conteudo_arquivo')->result();
        
    }*/

    /**
     * ArquivoCobranca_model::gravaLinhasRetorno()
     * 
     * Realiza a gravação das linhas do arquivo
     * 
     * @param mixed $idArquivo Id do arquivo para ser gravado na linha
     * @param mixed $linha A linha do arquivo
     * @param mixed $tipo_linha Tipo de linha
     * @param mixed $boleto Número do boleto
     * @param mixed $agencia Agência
     * @param mixed $conta Conta
     * @param mixed $valorPago Valor pago
     * @param mixed $statusBoleto Status do boleto
     * @param mixed $codInscricao Código da inscrição
     * @param mixed $numeroInscricao Número da inscrição
     * @param mixed $nossoNumero Nosso número + DV
     * @param mixed $dataOcorrencia Data da ocorrência
     * @param mixed $dataVencimento Data de vencimento 
     * @param mixed $valorTitulo Valor do título
     * @param mixed $nossoNumCorresp Utilizado para cobrança no formato correspondente
     * @param mixed $codBanco Código do banco
     * @param mixed $codOcorrencia Código da ocorrência
     * @param mixed $permissor Permissor do título (Identifica o local do boleto)
     * @param mixed $cdOcorrencia Cd da ocorrência na nosso base de dados
     * @param mixed $numeroLinha Sequencia das linhas
     */
    public function gravaLinhasRetorno(
                                        $idArquivo, # Id do arquivo
                                        $linha, # Linha do arquivo
                                        $tipo_linha, # Tipo de linha
                                        $boleto, # Número do título
                                        $agencia, # Agência
                                        $conta, # Conta
                                        $valorPago, # Valor pago 
                                        $codInscricao, # Código de inscrição (Tipo CPF ou CNPJ)
                                        $numeroInscricao, # Número de inscrição (Número do CPF ou CNPJ)
                                        $nossoNumero, # Nosso número (Identifica a remessa com o DV)
                                        $dataOcorrencia, # Data da ocorrência
                                        $dataVencimento, # Data de vencimento do título
                                        $valorTitulo, # Valor do título 
                                        $nossoNumCorresp, # Só é utilizado para cobranças em correspondentes
                                        $codBanco, # Código do banco
                                        $codOcorrencia, # Código da ocorrência
                                        $permissor, # Código do permissor
                                        $cdOcorrencia, # Cd da ocorrência na nossa base de dados
                                        $numeroLinha # Número da linha sequêncial
                                        ){
        
        
        # Data de ocorrência formatada
        $dataOcorFor = '20'.substr($dataOcorrencia, 4, 2).'-'.substr($dataOcorrencia, 2, 2).'-'.substr($dataOcorrencia, 0, 2);
        # Data de vencimento formatada
        $dataVencFor = '20'.substr($dataVencimento, 4, 2).'-'.substr($dataVencimento, 2, 2).'-'.substr($dataVencimento, 0, 2);  

        if($dataVencFor == '2000-00-00'){
            $dataVencFor = '0000-00-00';
        }


        $agencia = (empty($agencia))? 'null': "'".$agencia."'";
        $conta = (empty($conta))? 'null': "'".$conta."'";
        $valorPago = (empty($valorPago))? 'null': "'".$valorPago."'";
        $codInscricao = (empty($codInscricao))? 'null': $codInscricao;
        $numeroInscricao = (empty($numeroInscricao))? 'null': "'".$numeroInscricao."'";
        $nossoNumero = (empty($nossoNumero))? 'null': "'".$nossoNumero."'";
        $dataOcorrencia = (empty($dataOcorrencia))? 'null': "'".$dataOcorFor."'";
        $dataVencimento = (empty($dataVencimento))? 'null': "'".$dataVencFor."'";
        $valorTitulo = (empty($valorTitulo))? 'null': "'".$valorTitulo."'";
        $nossoNumCorresp = (empty($nossoNumCorresp))? 'null': "'".$nossoNumCorresp."'";
        $codBanco = (empty($codBanco))? 'null': "'".$codBanco."'";
        $codOcorrencia = (empty($codOcorrencia))? 'null': "'".$codOcorrencia."'";
        $permissor = (empty($permissor))? 'null': $permissor;
        $boleto = (empty($boleto))? 'null': $boleto;                
        $cdOcorrencia = (empty($cdOcorrencia))? 500: $cdOcorrencia; # 500 = Sem ocorrência (Usado em HEADER / FOOTER)

		$sql = "INSERT INTO conteudo_arquivo_retorno (
                    linha_arquivo_retorno,
                    permissor_arquivo_retorno, 
                    titulo_arquivo_retorno, 
                    cd_tipo_linha_arquivo_retorno,
                    cd_arquivo_retorno,
                    agencia_arquivo_retorno,
                    conta_arquivo_retorno,
                    valor_pago_arquivo_retorno,
                    codigo_inscricao_arquivo_retorno,
                    numero_inscricao_arquivo_retorno,
                    nosso_numero_arquivo_retorno,
                    data_ocorrencia_arquivo_retorno,
                    data_vencimento_arquivo_retorno,
                    valor_titulo_arquivo_retorno,
                    nosso_num_corresp_arquivo_retorno,
                    codigo_banco_arquivo_retorno,
                    codigo_ocorrencia_arquivo_retorno,
                    cd_ocorrencia_arquivo_retorno,
                    numero_linha_arquivo_retorno
                ) VALUES(
                    '".trim($linha)."', 
                    ".$permissor.",
                    ".$boleto.", 
                    ".$tipo_linha.", 
                    ".$idArquivo.", 
                    ".$agencia.", 
                    ".$conta.", 
                    ".$valorPago.",
                    ".$codInscricao.",
                    ".$numeroInscricao.",
                    ".$nossoNumero.",
                    ".$dataOcorrencia.",
                    ".$dataVencimento.",
                    ".$valorTitulo.",
                    ".$nossoNumCorresp.",
                    ".$codBanco.",
                    ".$codOcorrencia.",
                    ".$cdOcorrencia.",
                    ".$numeroLinha."
                );";                

		$this->db->query($sql);
                
    }
    
    /**
     * ArquivoCobranca_model::gravaLinhasRemovidasRetorno()
     * 
     * Grava as linhas removidas do arquivo
     * 
     * @param mixed $idArquivo Id do arquivo para ser gravado
     * @param mixed $linha Linha removida do arquivo
     * @param mixed $numeroLinha Número sequencial da linha que foi removida
     */
    public function gravaLinhasRemovidasRetorno($idArquivo, $linha, $tipoErro, $numeroLinha){
        
        $sql = "INSERT INTO excluido_arquivo_retorno (
                                                        linha_excluido_arquivo_retorno, 
                                                        tipo_excluido_arquivo_retorno,
                                                        numero_linha_excluido_arquivo_retorno, 
                                                        cd_arquivo_retorno
                                                    ) VALUES (
                                                        '".trim($linha)."', 
                                                        '".$tipoErro."',
                                                        ".$numeroLinha.", 
                                                        ".$idArquivo."
                                                    );";
        
        $this->db->query($sql);
        
    }
    
    /**
     * ArquivoCobranca_model::iniciaTrasacao()
     * 
     * Inicia a transação no banco
     * 
     */
    public function iniciaTrasacao(){
        
        $this->db->trans_begin();
        
    }
    
    /**
     * ArquivoCobranca_model::finalizaTransacao()
     * 
     * Finaliza a transação no banco
     * 
     * @return (Bool) de acordo com a execução da transação
     */
    public function finalizaTransacao(){
        
        $this->db->trans_complete();
        
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
    
    /**
     * ArquivoCobranca_model::registrosTelecom()
     * 
     * Pegas os registros de telecom
     * 
     * @return Os registros de telecom
     */
    public function registrosTelecom(){
        
        return $this->db->get('registro_telecom')->result();
        
    }
    
    /**
     * ArquivoCobranca_model::banco()
     * 
     * Pega os bancos existentes
     * 
     * @param mixed $cdBanco Filtra os banco caso seja informado esse parâmetro
     * 
     * @return Retorna os banco encontrados
     */
    public function banco($cdBanco){
        
        if(!empty($cdBanco)){
            $this->db->where('cd_banco', $cdBanco);
        }
        
        return $this->db->get('banco')->result();
        
    }
    
    /**
     * ArquivoCobranca_model::cdOcorrenciaRetorno()
     * 
     * Pega o CD ocorrência no banco de dados
     * 
     * @param mixed $codOcorrencia Código da ocorrência
     * @param mixed $cdBanco Banco para filtragem
     * @param mixed $tipo Tipo para filtragem que pode ser: NORMAL ou DEBAUTO
     * 
     * @return Retorna cd da ocorrência
     */
    public function cdOcorrenciaRetorno($codOcorrencia, $banco, $tipo){
        
        $this->db->where('codigo_ocorrencia_arquivo_retorno', $codOcorrencia);
        $this->db->where('tipo_ocorrencia_arquivo_retorno', $tipo);
        $this->db->where('cd_banco', $banco);
        
        $resultado = $this->db->get('ocorrencia_arquivo_retorno')->result();
        
        # Cd da ocorrência
        return $resultado[0]->cd_ocorrencia_arquivo_retorno;
        
    }
    
    /**
     * ArquivoCobranca_model::psqArquivosRetorno()
     * 
     * lista os dados dos arquivos de retorno
     * 
     * @param mixed $banco Banco para pesquisa
     * @param mixed $dataLote Data do lote para pesquisa
     * @param mixed $pagina Página corrente
     * @param mixed $mostra_por_pagina Quantidade que será mostrada por página
     * 
     * @return A lista dos arquivos
     */
    public function psqArquivosRetorno($banco = null, $dataLote = null, $pagina = null, $mostra_por_pagina = null){
        
        $this->db->select("
                            cd_arquivo_retorno,
                            nome_arquivo_retorno,
                            nome_banco,
                            DATE_FORMAT(data_insercao_arquivo_retorno, '%d/%m/%Y %H:%i:%s') as data_insercao_arquivo_retorno,
                            DATE_FORMAT(data_arquivo_retorno, '%d/%m/%Y') as data_arquivo_retorno,
                            (SELECT COUNT(*) AS excluido FROM excluido_arquivo_retorno AS sec WHERE sec.cd_arquivo_retorno = arquivo_retorno.cd_arquivo_retorno) AS excluido
                            ");        
        
        if($banco <> '0'){
            $this->db->where('arquivo_retorno.cd_banco', $banco);
        }
        
        if($dataLote <> '0'){
            $this->db->like('data_insercao_arquivo_retorno', $dataLote); 
        }
        
        $this->db->join('banco', 'banco.cd_banco = arquivo_retorno.cd_banco');        
        
        #$this->db->get('arquivo_retorno', $mostra_por_pagina, $pagina)->result();        
        #echo '<pre>';
        #echo $this->db->last_query();        
        #exit();
        return $this->db->get('arquivo_retorno', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * ArquivoCobranca_model::psqQtdArquivosRetorno()
     * 
     * Consulta a quantidade de arquivos de retorno
     * 
     * @param $banco Cd do banco para filtrar a consulta
     * @param $dataLote Data do lote para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function psqQtdArquivosRetorno($banco = null, $dataLote = null){
        
        if($banco <> '0'){
            $this->db->where('arquivo_retorno.cd_banco', $banco);
        }
        
        if($dataLote <> '0'){
            $this->db->like('data_insercao_arquivo_retorno', $dataLote); 
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('arquivo_retorno')->result();
    }
    
    /**
     * ArquivoCobranca_model::conteudoArquivoRetorno()
     * 
     * Pega conteúdo do arquivo (Linhas)
     * 
     * @param mixed $cdArquivo Cd o arquivo para filtragem
     * 
     * @return Retorna o conteúdo do arquivo
     */
    public function conteudoArquivoRetorno($cdArquivo){
        
        $this->db->select('linha_arquivo_retorno');
        $this->db->where('cd_arquivo_retorno', $cdArquivo);
        $this->db->order_by("numero_linha_arquivo_retorno", "asc"); 
        return $this->db->get('conteudo_arquivo_retorno')->result();
        
    }
    
    /**
     * ArquivoCobranca_model::dadosConteudoArquivoRetorno()
     * 
     * Pega o conteúdo do arquivo
     * 
     * @param mixed $titulo
     * @param mixed $nossoNumeroCorresp
     * @return Retorna o conteúdo
     */
    public function dadosConteudoArquivoRetorno($titulo = null, $nossoNumero = null, $nossoNumeroCorresp = null){
        
        if($titulo <> ''){
        
            $this->db->where('titulo_arquivo_retorno', $titulo);
        
        }
        
        if($nossoNumero <> ''){
            
            $this->db->where('nosso_numero_arquivo_retorno', $nossoNumero);
            
        }
        
        if($nossoNumeroCorresp <> ''){
            
            $this->db->where('nosso_num_corresp_arquivo_retorno', $nossoNumeroCorresp);
            
        }
        
        $this->db->select("
                            nome_arquivo_retorno AS nome_arquivo, 
                            nome_banco, 
                            linha_arquivo_retorno, 
                            DATE_FORMAT(data_insercao_arquivo_retorno, '%d/%m/%Y %H:%i:%s') AS data_lote,
                            permissor_arquivo_retorno AS permissor,
                            titulo_arquivo_retorno AS boleto,
                            ocorrencia_arquivo_retorno.codigo_ocorrencia_arquivo_retorno AS codigo_ocorrencia,
                            agencia_arquivo_retorno AS agencia,
                            conta_arquivo_retorno AS conta,
                            valor_titulo_arquivo_retorno AS valor_titulo,
                            valor_pago_arquivo_retorno AS valor_pago,
                            DATE_FORMAT(data_vencimento_arquivo_retorno, '%d/%m/%Y') AS data_vencimento,
                            codigo_inscricao_arquivo_retorno AS codigo_inscricao,
                            numero_inscricao_arquivo_retorno AS numero_inscricao,
                            nosso_numero_arquivo_retorno AS nosso_numero,
                            nosso_num_corresp_arquivo_retorno AS numero_corresp,
                            DATE_FORMAT(data_ocorrencia_arquivo_retorno, '%d/%m/%Y') AS data_ocorrencia,
                            codigo_banco_arquivo_retorno AS codigo_banco,
                            nome_ocorrencia_arquivo_retorno AS nome_ocorrencia,
                            numero_linha_arquivo_retorno AS numero_linha
                            ");
        $this->db->join('arquivo_retorno', 'arquivo_retorno.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno');   
        $this->db->join('banco', 'banco.cd_banco = arquivo_retorno.cd_banco');  
        $this->db->join('ocorrencia_arquivo_retorno', 'ocorrencia_arquivo_retorno.cd_ocorrencia_arquivo_retorno = conteudo_arquivo_retorno.cd_ocorrencia_arquivo_retorno'); 
        $this->db->order_by("numero_linha_arquivo_retorno", "asc"); 
        return $this->db->get('conteudo_arquivo_retorno')->result();
        
    }
    
    /**
     * ArquivoCobranca_model::excluidoArquivoRetorno()
     * 
     * Pega as linhas que foram excluídas do arquivo de retorno
     * 
     * @param mixed $cdArquivo Cd o arquivo para filtragem das linhas
     * 
     * @return Retorna as linhas
     */
    public function excluidoArquivoRetorno($cdArquivo){
        
        $this->db->select('linha_excluido_arquivo_retorno');
        $this->db->where('cd_arquivo_retorno', $cdArquivo);
        #$this->db->order_by("numero_linha_excluido_arquivo_retorno", "asc"); 
        $this->db->order_by("tipo_excluido_arquivo_retorno", "asc");
        return $this->db->get('excluido_arquivo_retorno')->result();
        
    }
    
    /**
     * ArquivoCobranca_model::dataInsercaoArquivoRetorno()
     * 
     * Pega as datas de inserção existentes
     * 
     * @return As datas
     */
    public function dataInsercaoArquivoRetorno(){
        
        $this->db->distinct();
        $this->db->select("DATE_FORMAT(data_insercao_arquivo_retorno, '%d/%m/%Y') AS data_formatada, SUBSTR(data_insercao_arquivo_retorno, 1, 10) AS data_banco_insercao");
        $this->db->order_by("data_insercao_arquivo_retorno", "desc"); 
        return $this->db->get('arquivo_retorno')->result();
        
    }
    
    /**
     * ArquivoCobranca_model::qtdArquivosDiarios()
     * 
     * @return
     */
    public function qtdArquivosDiarios(){
        
        /*if($this->input->post('banco') <> ''){
            $banco = 'AND arquivo_retorno.cd_banco = '.$this->input->post('banco');
        }else{
            $banco = '';
        }*/
        
        if($this->input->post('dataPainel')){
            $data = str_replace("'", "", $this->util->formaValorBanco($this->input->post('dataPainel'),'USA'));
        }else{
            $data = date('Y-m-d');
        }
        
        $banco = '';
        
        /*$sql = "SELECT
                	nome_banco,
                	COUNT(*) As qtd_arquivos
                FROM
                	arquivo_retorno
                INNER JOIN banco ON banco.cd_banco = arquivo_retorno.cd_banco
                WHERE
                	cd_arquivo_retorno IN(
                		SELECT
                			arquivo_retorno.cd_arquivo_retorno  
                		FROM
                			conteudo_arquivo_retorno
                		INNER JOIN arquivo_retorno ON arquivo_retorno.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                		WHERE
                			cd_tipo_linha_arquivo_retorno NOT IN(1, 4)
                        ".$banco."
                		AND data_insercao_arquivo_retorno LIKE '".$data."%'
                		GROUP BY
                			cd_arquivo_retorno
                	)
                GROUP BY
                	nome_banco";*/
                    
        /*$sql = "SELECT
                	nome_banco,
                	COUNT(*) As qtd_arquivos
                FROM
                	arquivo_retorno
                INNER JOIN banco ON banco.cd_banco = arquivo_retorno.cd_banco
                WHERE data_insercao_arquivo_retorno LIKE '".$data."%'
                GROUP BY nome_banco";*/
                
        $sql = "SELECT
                	nome_banco,
                	COUNT(*) As qtd_arquivos
                FROM
                	arquivo_retorno
                INNER JOIN banco ON banco.cd_banco = arquivo_retorno.cd_banco
                WHERE data_insercao_arquivo_retorno LIKE '".$data."%'
                AND cd_arquivo_retorno IN (
                	SELECT
                	DISTINCT
                	arquivo_retorno.cd_arquivo_retorno
                	FROM conteudo_arquivo_retorno
                	INNER JOIN arquivo_retorno ON arquivo_retorno.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                	WHERE data_insercao_arquivo_retorno LIKE '".$data."%'
                	AND cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                	#GROUP BY arquivo_retorno.cd_arquivo_retorno
                )
                GROUP BY nome_banco";
                    
        return $this->db->query($sql)->result();
        
    }
    
    /**
     * ArquivoCobranca_model::verificaExistenciaArquivo()
     * 
     * Verifica se existe o arquivo informado
     * 
     * @param mixed $banco Banco para verificação
     * @param mixed $arquivo Arquivo para verificação
     * @return
     */
    public function verificaExistenciaArquivo($banco, $arquivo){
        
        $sql = "SELECT
                    COUNT(*) AS verificacao
                FROM arquivo_retorno
                INNER JOIN banco ON arquivo_retorno.cd_banco = banco.cd_banco
                WHERE nome_arquivo_retorno LIKE '%".$arquivo."%' 
                AND data_arquivo_retorno BETWEEN DATE_ADD(CURDATE(), INTERVAL -7 DAY) AND CURRENT_DATE()
                AND arquivo_retorno.cd_banco = ".$banco;

        $resultado = $this->db->query($sql)->result();
             
        return $resultado[0]->verificacao;
        
    }
    
    /**
    * Função que pega os arquivos duplicados
    * @return os registros encontrados
    */
    public function pegaDuplicados(){
        
        // Pegar o primeiro arquivo de retorno
        /*$sql = "SELECT
                	arquivo_retorno.cd_arquivo_retorno, 
                	nome_arquivo_retorno, 
                	nome_banco,
                	DATE_FORMAT(data_insercao_arquivo_retorno, '%d/%m/%Y') AS data_lote,
                	count(linha_arquivo_retorno) AS qtd_arquvos,
                	count(linha_arquivo_retorno)-1 AS precisa_remocao
                FROM arquivo_retorno
                INNER JOIN banco ON banco.cd_banco = arquivo_retorno.cd_banco
                INNER JOIN conteudo_arquivo_retorno ON arquivo_retorno.cd_arquivo_retorno = conteudo_arquivo_retorno.cd_arquivo_retorno
                WHERE numero_linha_arquivo_retorno = 2
                AND cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                AND data_insercao_arquivo_retorno > DATE_ADD(CURDATE(), INTERVAL -15 DAY)
                GROUP BY linha_arquivo_retorno
                HAVING COUNT(linha_arquivo_retorno) > 1
                ORDER BY arquivo_retorno.cd_arquivo_retorno DESC";*/
                
        // Pega o último arquivo de retorno
        $sql = "SELECT
                	(SELECT sec.cd_arquivo_retorno FROM conteudo_arquivo_retorno AS sec WHERE sec.linha_arquivo_retorno = pri.linha_arquivo_retorno ORDER BY sec.cd_arquivo_retorno DESC LIMIT 1) AS cd_arquivo_retorno, 
                	nome_arquivo_retorno, 
                	nome_banco,
                	DATE_FORMAT(data_insercao_arquivo_retorno, '%d/%m/%Y') AS data_lote,
                	count(pri.linha_arquivo_retorno) AS qtd_arquvos,
                	count(pri.linha_arquivo_retorno)-1 AS precisa_remocao
                FROM arquivo_retorno
                INNER JOIN banco ON banco.cd_banco = arquivo_retorno.cd_banco
                INNER JOIN conteudo_arquivo_retorno AS pri ON arquivo_retorno.cd_arquivo_retorno = pri.cd_arquivo_retorno
                WHERE numero_linha_arquivo_retorno = 2
                AND pri.cd_tipo_linha_arquivo_retorno NOT IN (1,4)
                AND data_insercao_arquivo_retorno > DATE_ADD(CURDATE(), INTERVAL -5 DAY)
                GROUP BY pri.linha_arquivo_retorno
                HAVING COUNT(pri.linha_arquivo_retorno) > 1
                ORDER BY arquivo_retorno.cd_arquivo_retorno DESC";
                
        return $this->db->query($sql)->result();
        
    }
    
    /**
    * ArquivoCobranca_model::apagaRetornoDuplicado()
    * 
    * Função que apaga os arquivos duplicados
    * 
    * @param mixed $cd_arquivo
    * 
    * @return O número de linhas afetadas
    */
    public function apagaRetornoDuplicado($cd_arquivo){
	
		$this->db->where('cd_arquivo_retorno', $cd_arquivo);
        $this->db->delete('arquivo_retorno'); 
        
        return $this->db->affected_rows();
	
	}

}